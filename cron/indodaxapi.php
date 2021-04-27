<?php
require "../lib/config.php";

include dirname(__FILE__) . "/../lib/" . "db.php";
include dirname(__FILE__) . "/../lib/" . "common.php";
$common = new common();
$common->logit("started on " . date('YmdHis'));
$db = new db(DBHOST, DBUSER, DBPASS, DBNAME);
$info = $common->getInfo();
if ($common->isjson($info)) {
    $arrInfo = json_decode($info, true);
    if ($arrInfo["success"]) {
        $saldo = isset($arrInfo["return"]["balance"]["idr"]) ? $arrInfo["return"]["balance"]["idr"] : 0;
        $common->logit("saldo idr : " . $saldo);
        $sql = "UPDATE saldo SET saldo = ? WHERE `key`=?";
        $db->query($sql, $saldo, 'IDR');
    } else {
        $common->logit("Failed to get balance from server (response success)");
    }
} else {
    $common->logit("Failed to get balance from server (response not json)");
}

$koins = $db->query('SELECT id,kode,status FROM koin WHERE id <>0')->fetchAll();
foreach ($koins as $koin) {
    $common->logit("processing " . $koin["kode"]);
    $url = DAXBASEURL . "ticker/" . strtolower($koin["kode"] . "idr");
    $jresult = $common->curlPost($url);
    if ($common->isjson($jresult)) {
        $arrResult = json_decode($jresult, true);
        if (isset($arrResult['ticker']['last'])) {
            $sql = "CALL insert_harga(?,?,?,?)";
            $insert = $db->query($sql, $koin["id"], $arrResult['ticker']['last'], $arrResult['ticker']['buy'], $arrResult['ticker']['sell']);
            if ($insert->affectedRows() > 0) {
                if ($koin["status"] == 1) {
                    /* ========================Beli============================== */
                    $common->logit("Start Buy Order");
                    $qty = $common->checkOpenCoin($koin["id"]);
                    if ($qty <= 0) {
                        $sql = "SELECT harga.id, koin.kode,koin.nama, harga.harga,harga.harga_beli,harga.harga_jual, ifnull(koin.harga_maksimum,0) as harga_maksimum   
FROM koin JOIN harga ON koin.id = harga.koin_id 
WHERE koin.STATUS = 1 and koin.kode = ? 
ORDER BY harga.id DESC 
LIMIT 10";
                        $harga2 = $db->query($sql, $koin["kode"])->fetchAll();

                        $cekHarga = array();
                        $angkaJalan = 0;
                        $hitungSemua = 0;
                        $hitungMinus = 0;
                        $hitungPlus = 0;
                        $hargaTerakhir = 0;
                        $hargaMaximum = 0;
                        for ($i = count($harga2) - 1; $i >= 0; $i--) {
                            if ($i > 0) {
                                $hitungSelisih = $harga2[$i - 1]["harga"] - $harga2[$i]["harga"];
                                $cekHarga[$angkaJalan] = $hitungSelisih;
                                $hitungSemua += $hitungSelisih;
                                $angkaJalan += 1;
                                if ($hitungSelisih > 0) {
                                    $hitungPlus += 1;
                                } else {
                                    $hitungMinus += 1;
                                }
                            } else {
                                $hargaTerakhir = $harga2[$i]["harga_jual"];
                                $hargaMaximum = $harga2[$i]["harga_maksimum"];
                            }
                        }
                        if ($hargaTerakhir < $hargaMaximum || $hargaMaximum == 0) {
                            if ($hitungPlus > $hitungMinus) {
                                if ($hitungSemua > 0) {
                                    $nominalBeli = ceil((MAKSPENGGUNAANDEPOSIT / 100) * $saldo);
                                    $common->logit("beli " . $koin["kode"]);
                                    $reqBeli = $common->getTradeRequest($koin["kode"], "buy", $harga2[0]["harga_jual"], $nominalBeli, 0);
                                    $headers = $common->createHeaders($reqBeli);
                                    $common->logit($reqBeli);
                                    $response = $common->curlpost(PRIVATEURL, $reqBeli, $headers);
                                    $common->logit($response);
                                    if ($common->isjson($response)) {
                                        $arrresponse = json_decode($response, true);
                                        if ($arrresponse["success"]) {
                                            $tag = "receive_" . trim(strtolower($koin["kode"]));
                                            $common->logit($tag);
                                            $qtyJson = $arrresponse["return"][$tag];
                                            $common->logit($qtyJson);
                                            $fee = $arrresponse["return"]["fee"];
                                            $orderid = $arrresponse["return"]["order_id"];
                                            if ($qtyJson > 0) {
                                                $qty = $qtyJson;
                                            } else {
                                                $common->logit("qtyJson 0, hitung sendiri");
                                                $qty = round(($nominalBeli / $harga2[0]["harga_jual"]), 8);
                                            }
                                            $common->logit($qty);
                                            $sql = "insert into transaksi(koin_id,qty,harga_beli,reff_beli,fee_beli,status) values(?,?,?,?,?,?);";
                                            $db->query($sql, $koin["id"], $qty, $harga2[0]["harga_jual"], $orderid, $fee, 0);
                                        }
                                    }
                                }
                            }
                        } else {
                            $common->logit("harga " . $koin["kode"] . " melebihi batas harga maximum");
                        }
                    } else {
                        $common->logit($koin["kode"] . " masih ada open balance(" . $qty . ")");
                    }
                    $common->logit("End Buy Order");
                    /* ========================================================== */
                    /* ========================Jual============================== */
                    $common->logit("Start SELL Order");
                    $sql = "SELECT transaksi.id, transaksi.koin_id,koin.kode, transaksi.harga_beli,transaksi.fee_beli, transaksi.qty
    FROM transaksi JOIN koin ON transaksi.koin_id = koin.id
    WHERE transaksi.status =1 AND transaksi.koin_id=?";
                    $transaction = $db->query($sql, $koin["id"])->fetchArray();
                    if (isset($transaction["id"])) {
                        $sql = 'SELECT harga.harga_beli FROM harga WHERE harga.id = (SELECT MAX(id) FROM harga WHERE koin_id = ?)';
                        $harga = $db->query($sql, $transaction["koin_id"])->fetchArray();
                        $selisih = $harga["harga_beli"] - $transaction["harga_beli"];
                        $common->logit("harga beli " . number_format($transaction["harga_beli"], 0, ",", ".") . " harga sekarang " . number_format($harga["harga_beli"], 0, ",", "."));
                        $common->logit("selisih " . number_format($selisih, 0, ",", "."));
                        $persen = ($selisih / $transaction["harga_beli"]) * 100;
                        $absPersen = abs($persen);
                        $common->logit("selisih persen " . $persen . " abs " . $absPersen);
                        $common->logit("batas atas " . BATASATAS . " batas bawah " . BATASBAWAH);
                        $totalJual = $transaction["harga_beli"] * $transaction["qty"];
                        $response = "";
                        $batasAtas = BATASATAS;

                        if (($persen >= $batasAtas)) {
                            $common->logit("JUAL " . $transaction["kode"] . " Batas atas ");
                            $reqJual = $common->getTradeRequest($transaction["kode"], "sell", $harga["harga_beli"], 0, (float)$transaction["qty"]);
                            // $reqJual = $common->getTradeRequest($transaction["kode"],"sell",$harga["harga_beli"],0,(float)$transaction["qty"]);
                            $common->logit($reqJual);
                            $headers = $common->createHeaders($reqJual);
                            $response = $common->curlPost(PRIVATEURL, $reqJual, $headers);
                            $common->logit($response);

                            //kalau sudah margin 5x sstop transaksi untuk koin itu
                            $sql = 'INSERT INTO countgl(tgl,koin_id,status) VALUES(?,?,?)';
                            $db->query($sql, date('Y-m-d'), $transaction['koin_id'], 0);
                        }
                        if ($response == "") {
                            if ($persen < 0 && ($absPersen >= BATASBAWAH)) {
                                $common->logit("JUAL " . $transaction["kode"] . " Batas bawah ");
                                $reqJual = $common->getTradeRequest($transaction["kode"], "sell", $harga["harga_beli"], 0, $transaction["qty"]);
                                $common->logit($reqJual);
                                $common->logit($reqJual);
                                $headers = $common->createHeaders($reqJual);
                                $response = $common->curlPost(PRIVATEURL, $reqJual, $headers);
                                $common->logit($response);
                                //kalau sudah loss 3x sstop transaksi untuk koin itu
                                $sql = 'INSERT INTO countgl(tgl,koin_id,status) VALUES(?,?,?)';
                                $db->query($sql, date('Y-m-d'), $transaction['koin_id'], 1);
                            }
                        }
                        if ($response != "") {
                            if ($common->isjson($response)) {
                                $arrResponse = json_decode($response, true);
                                if ($arrResponse["success"]) {
                                    $fee = $arrResponse["return"]["fee"];
                                    $reff_jual = $arrResponse["return"]["order_id"];
                                    $sql = "UPDATE transaksi SET ";
                                    $sql .= "tgl_jual=?, ";
                                    $sql .= "harga_jual=?, ";
                                    $sql .= "reff_jual=?, ";
                                    $sql .= "fee_jual=?, ";
                                    $sql .= "status=? ";
                                    $sql .= "WHERE id =? ";
                                    $db->query($sql, date('Y-m-d H:i:s'), $harga["harga_beli"], $reff_jual, $fee, 2, $transaction["id"]);
                                }
                            }
                        }
                        $sql = 'SELECT koin.id, ifnull(x.gain,0) AS gain ,ifnull(y.loss,0) AS loss FROM koin LEFT JOIN (SELECT koin_id,COUNT(koin_id) AS gain from countgl WHERE status = 0 AND tgl = ? GROUP BY koin_id) AS x ON koin.id = x.koin_id
                    left join (select koin_id,ifnull(count(koin_id),0) as loss from countgl where status = 1 AND tgl = ? GROUP BY koin_id) as y on koin.id = y.koin_id 
                    where koin.id = ?';
                        $countgl = $db->query($sql, date("Y-m-d"), date("Y-m-d"), $transaction["koin_id"])->fetchArray();
                        $sql = "";
                        $alasan = "";
                        if ($countgl["gain"] >= MAXGAIN || $countgl["loss"] >= MAXLOSS) {
                            // $sql = "UPDATE koin SET status  = 0 WHERE id = ?";
                            if ($countgl["gain"] >= MAXGAIN) {
                                $alasan = "Maksimum gain sudah tercapai";
                                $common->logit($transaction["kode"] . " sudah mencapai maksimum gain,");
                            }
                            if ($countgl["loss"] >= MAXLOSS) {
                                $alasan = "Maksimum loss sudah tercapai";
                                $common->logit($transaction["kode"] . " sudah mencapai maksimum loss,");
                            }
                        }
                        if ($alasan != "") {
                            $sql = "INSERT into stoped_koin(koin_id,alasan) VALUES(?,?)";
                            $db->query($sql, $transaction["koin_id"], $alasan);
                            $sql = "UPDATE koin SET status = 0 WHERE id = ?";
                            $db->query($sql, $transaction["koin_id"]);
                        }
                    }
                    $common->logit("End SELL Order");
                    /* ========================================================== */
                }
            } else {
                $common->logit("Failed insert new price of " . $koin["kode"] . "(" . $arrResult['ticker']['last'] . ") on " . date('Y-m-d H:i:s'));
            }
        }
    } else {
        $common->logit("error got " . $jresult . " from " . DAXBASEURL);
    }
}
$common->logit("done on " . date('YmdHis'));
$db->close();
