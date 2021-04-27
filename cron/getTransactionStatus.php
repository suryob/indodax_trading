<?php
    require dirname(__FILE__) . "/../lib/" . "config.php";
    include dirname(__FILE__) . "/../lib/" . "db.php";
    include dirname(__FILE__) . "/../lib/" . "common.php";
    $common = new common();
    $db = new db(DBHOST, DBUSER, DBPASS, DBNAME);
    $common->logit("check status mulai");
    $sql = "SELECT transaksi.id, koin.kode, transaksi.reff_beli,transaksi.reff_jual,transaksi.status 
    FROM transaksi JOIN koin ON transaksi.koin_id = koin.id
    WHERE transaksi.status IN(0,2)";
    $transactions = $db->query($sql)->fetchAll();
    $param="";

    foreach($transactions as $transaction){
        if($transaction["status"] ==0){
            $reff = $transaction["reff_beli"];
        }elseif($transaction["status"] ==2){
            $reff = $transaction["reff_jual"];
        }
        $data = [
			'method' => 'getOrder',
			'timestamp' => $common->getTimeStamp(),
			'pair' => strtolower($transaction["kode"]).'_idr',
			'order_id' => $reff
		];
        $param = http_build_query($data, '', '&');
        $headers = $common->createHeaders($param);
        $response = $common->curlPost(PRIVATEURL,$param,$headers);
        if($common->isjson($response)){
            $arrResponse = json_decode($response,true);
            if($arrResponse["success"]){
                $sqlUpdate = "";
                $tipe = $arrResponse["return"]["order"]["type"];
                $status = $arrResponse["return"]["order"]["status"];
                if(trim(strtolower($status))=="filled"){
                    $sqlUpdate = "UPDATE transaksi set `status` = ? WHERE id = ?";
                    if(trim(strtolower($tipe))=="buy"){
                        $updateStatus = 1;
                    }elseif(trim(strtolower($tipe))=="sell"){
                        $updateStatus = 3;
                    }
                    $db->query($sqlUpdate,$updateStatus,$transaction["id"]);
                }
            }
        }else{
            $common->logit("error got ".$response." from ".PRIVATEURL);
        }
    }
    $common->logit("check status selesai");
?>