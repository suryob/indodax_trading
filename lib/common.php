<?php
class common
{
	protected $logdir;
	protected $uniqid;
	protected $trxDir = 'trx';
	
	public function __construct($logdir = "logs")
	{
		date_default_timezone_set("Asia/Jakarta");
		$this->logdir = $logdir;
		$this->uniqid = uniqid();
	}
	function EOD($saldo){
		global $db;
		$date = strtotime("-1 day");
		$sql = "SELECT stoped_koin.koin_id,koin.kode FROM stoped_koin JOIN koin ON stoped_koin.koin_id = koin.id WHERE DATE(stoped_koin.tgl) = ?";
		$stopeds = $db->query($sql,date('Y-m-d',$date))->fetchAll();
		foreach($stopeds as $stoped){
			$this->logit("Re-enabling ".$stoped["kode"]);
			$sql = "UPDATE koin set status = 1 WHERE id = ?";
			$db->query($sql,$stoped["koin_id"]);
		}
		$sql = "SELECT id FROM journal WHERE tipe = 3 AND DATE(tgl) =?";
		$journal = $db->query($sql,date('Y-m-d',$date))->fetchArray();
		if(!isset($journal["id"])){
			/*insert saldo idr*/
			$this->logit("Writting Journal");
			$sql = "INSERT INTO journal(tipe,tgl,koin_id,qty,harga) VALUES(?,?,?,?,?)";
			$db->query($sql,3,date('Y-m-d',$date).' 23:59:59',0,1,$saldo);
			$sql = "INSERT INTO journal(tipe,tgl,koin_id,qty,harga) VALUES(?,?,?,?,?)";
			$db->query($sql,0,date('Y-m-d H:i:s'),0,1,$saldo);
			
			$sql = "select koin_id,qty,harga_beli from transaksi where status = 0 ";
			$sql.= "union ";
			$sql.= "select koin_id,qty,harga_beli from transaksi where status = 1 ";
			$sql.= "union ";
			$sql.= "select koin_id,qty,harga_jual from transaksi where status = 2";
			
			$koins = $db->query($sql)->fetchAll();
			foreach($koins as $koin){
				$sql = "INSERT INTO journal(tipe,tgl,koin_id,qty,harga) VALUES(?,?,?,?,?)";
				$db->query($sql,3,date('Y-m-d',$date).' 23:59:59',$koin["koin_id"],$koin["qty"],$koin["harga_beli"]);
				$sql = "INSERT INTO journal(tipe,tgl,koin_id,qty,harga) VALUES(?,?,?,?,?)";
				$db->query($sql,0,date('Y-m-d H:i:s'),$koin["koin_id"],$koin["qty"],$koin["harga_beli"]);
			}
			$this->logit("Writting Journal DONE");
		}
	}
	function checkOpenCoin($coinId){
		global $db;
		$sql = "SELECT transaksi.id, transaksi.qty,transaksi.status
		FROM transaksi 
		WHERE transaksi.status <>3 AND transaksi.status <>4 AND transaksi.koin_id = ?";
		$transaksi = $db->query($sql,$coinId)->fetchArray();
		$qty = isset($transaksi["qty"])?$transaksi["qty"]:0;
		return $qty;
	}
	function getTimeStamp(){
		return round(microtime(true) * 1000);
	}
	function getNonce(){
		global $db;
		$sql = "SELECT `value` FROM config WHERE `key`='nonce'";
		$config = $db->query($sql)->fetchArray();
		$nonce = $config["value"] + 1;
		$sql = "UPDATE config set `value` = ? WHERE `key`=?";
		$db->query($sql,$nonce,'nonce');
		return $nonce;
	}
	function createHeaders($post_data){
		$sign = hash_hmac('sha512', $post_data, PRIVATESECRET);
		$headers = ['Key:'.PRIVATEKEY,'Sign:'.$sign];
		return $headers;
	}
	function getTradeRequest($kodeKoin,$jenis,$harga,$nominalPembelian,$jmlKoin){
		$data["method"]="trade";
		$data["timestamp"]=$this->getTimeStamp();
		$data["pair"]=strtolower($kodeKoin).'_idr';
		$data["type"]=$jenis;
		$data["price"]=$harga;
		if($nominalPembelian>0){
			$data["idr"]=$nominalPembelian;
		}
		if($jmlKoin>0){
			$data[strtolower($kodeKoin)]=$jmlKoin;
		}
		return http_build_query($data, '', '&');
	}
	function getInfo(){
		$data = [
			'method' => 'getInfo',
			'timestamp' => round(microtime(true) * 1000),
			'nonce'=>$this->getNonce()
		];
		
		$post_data = http_build_query($data, '', '&');
		
		$headers = $this->createHeaders($post_data); 
		$response = $this->curlPost(PRIVATEURL,$post_data,$headers);
		return $response;
	}
	public function logit($str)
	{

		if (!is_dir($this->logdir)) {
			mkdir($this->logdir);
		}
		$logfile = date('Ymd') . ".log";
		$file_name = $this->logdir . "/" . $logfile;
		
		$log = fopen($file_name, "a");
		$write = date('Y-m-d H:i:s') . " - " . $this->uniqid . " - " . $str . PHP_EOL;
		fwrite($log, $write);
		fclose($log);
	}
	function isjson($string)
	{
		return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
	function is_xml($xml)
	{
		$doc = @simplexml_load_string($xml);
		if ($doc) {
			return true; //this is valid
		} else {
			return false; //this is not valid
		}
	}
	function curlPost($url, $param = "", $header = "", $timeout = 90, $bypassSSL = "N")
	{
		$ch = curl_init();
		if ($ch == false) {
			die('Failed to create curl object');
		}
		if ($header != "") {
			if (is_array($header)) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			}
		}
		if ($bypassSSL != "N") {
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		//execute post
		// $this->logit("sending ".$param." to ".$url);
		$result = curl_exec($ch);
		$err = curl_error($ch);

		//close connection
		curl_close($ch);
		if ($err) {
			$result = "cURL Error #:" . $err;
			$this->logit("got ".$result);
		}
		
		return $result;
	}
}
