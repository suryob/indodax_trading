<?php
	define('MODE','DEV'); /*accepted value = DEV / PROD*/
	define('APPNAME','KOIN');
	define('LIBPATH','lib/');
	define('DBHOST','127.0.0.1');
	define('DBUSER','root');
	define('DBPASS','');
	define('DBNAME','koinkoin');
	define('DAXBASEURL','https://indodax.com/api/');
	
	define('PRIVATEURL','https://indodax.com/tapi/');
	define('MAKSPENGGUNAANDEPOSIT',100);	/* dalam persen */
	define('BATASATAS',0.75);	/* dalam persen */
	define('BATASBAWAH',5.6);	/* dalam persen */
	
	define('MAXGAIN',8); /*Maksimum berapa kali gain per koin*/
	define('MAXLOSS',2); /*Maksimum berapa kali loss per koin*/
	include "cred.php";
?>
