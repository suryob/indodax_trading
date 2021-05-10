-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.36-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             10.1.0.5464
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for koinkoin
CREATE DATABASE IF NOT EXISTS `koinkoin` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `koinkoin`;

-- Dumping structure for table koinkoin.config
CREATE TABLE IF NOT EXISTS `config` (
  `key` char(25) NOT NULL,
  `value` char(25) DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.config: ~1 rows (approximately)
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`key`, `value`) VALUES
	('nonce', '0');


-- Dumping structure for table koinkoin.countgl
CREATE TABLE IF NOT EXISTS `countgl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tgl` date DEFAULT NULL,
  `koin_id` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0' COMMENT '0 gain; 1 loss',
  PRIMARY KEY (`id`),
  KEY `FK_countgl_koin` (`koin_id`),
  CONSTRAINT `FK_countgl_koin` FOREIGN KEY (`koin_id`) REFERENCES `koin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.countgl: ~0 rows (approximately)
/*!40000 ALTER TABLE `countgl` DISABLE KEYS */;

-- Dumping structure for table koinkoin.harga
CREATE TABLE IF NOT EXISTS `harga` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `koin_id` int(11) DEFAULT '0',
  `tgl` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `harga` double DEFAULT '0',
  `harga_beli` double DEFAULT '0',
  `harga_jual` double DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_harga_koin` (`koin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.harga: ~0 rows (approximately)
/*!40000 ALTER TABLE `harga` DISABLE KEYS */;

-- Dumping structure for procedure koinkoin.insert_harga
DELIMITER //
CREATE PROCEDURE `insert_harga`(
	IN `p_koin_id` INT,
	IN `p_harga` DOUBLE,
	IN `p_harga_beli` DOUBLE,
	IN `p_harga_jual` DOUBLE

)
BEGIN
	INSERT INTO harga(koin_id,tgl,harga,harga_beli,harga_jual) VALUES(p_koin_id,NOW(),p_harga,p_harga_beli,p_harga_jual);
END//
DELIMITER ;

-- Dumping structure for table koinkoin.journal
CREATE TABLE IF NOT EXISTS `journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0: Saldo awal\r\n1: Pembelian\r\n2: Penjualan\r\n3: Saldo Akhir',
  `tgl` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `koin_id` int(11) NOT NULL DEFAULT '0',
  `qty` double NOT NULL DEFAULT '0',
  `harga` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_journal_koin` (`koin_id`),
  CONSTRAINT `FK_journal_koin` FOREIGN KEY (`koin_id`) REFERENCES `koin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.journal: ~0 rows (approximately)
/*!40000 ALTER TABLE `journal` DISABLE KEYS */;


-- Dumping structure for table koinkoin.koin
CREATE TABLE IF NOT EXISTS `koin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` char(6) DEFAULT NULL,
  `nama` char(50) DEFAULT NULL,
  `harga_maksimum` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.koin: ~25 rows (approximately)
/*!40000 ALTER TABLE `koin` DISABLE KEYS */;
INSERT INTO `koin` (`id`, `kode`, `nama`, `harga_maksimum`, `status`) VALUES
	(0, 'IDR', 'RUPIAH', 0, 1),
	(1, 'BTC', 'Bitcoin', 1000000, 0),
	(2, 'ETH', 'Ethereum', NULL, 0),
	(3, 'BNB', 'Binance Coin', NULL, 0),
	(4, 'XRP', 'XRP', NULL, 0),
	(5, 'USDT', 'Tether', NULL, 0),
	(6, 'ADA', 'Cardano', NULL, 1),
	(7, 'DOT', 'Polkadot', NULL, 1),
	(8, 'DOGE', 'Dogecoin', NULL, 1),
	(9, 'LTC', 'Litecoin', NULL, 0),
	(10, 'UNI', 'Uniswap', NULL, 0),
	(11, 'LINK', 'Chainlink', NULL, 0),
	(12, 'BCH', 'Bitcoin Cash', NULL, 0),
	(13, 'XLM', 'Stellar', NULL, 1),
	(14, 'THETA', 'Theta Network', NULL, 0),
	(15, 'USDC', 'USD Coin', NULL, 0),
	(17, 'TEN', 'TOKENOMY MARKET', 0, 0),
	(18, '1INCH', '1INCH MARKET', 0, 0),
	(19, 'IOST', 'IOST MARKET', 0, 0),
	(20, 'AAVE', 'AAVE MARKET', 0, 0),
	(21, 'ATOM', 'COSMOS MARKET', 0, 0),
	(22, 'AOA', 'AURORA MARKET', 0, 0),
	(23, 'CELO', 'CELO MARKET', 0, 0),
	(24, 'BTG', 'BITCOIN GOLD MARKET', 0, 0),
	(25, 'ATT', 'ATT MARKET', 0, 0);


-- Dumping structure for table koinkoin.saldo
CREATE TABLE IF NOT EXISTS `saldo` (
  `key` char(3) NOT NULL,
  `saldo` double DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.saldo: ~0 rows (approximately)
/*!40000 ALTER TABLE `saldo` DISABLE KEYS */;
INSERT INTO `saldo` (`key`, `saldo`) VALUES
	('IDR', 0);


-- Dumping structure for table koinkoin.stoped_koin
CREATE TABLE IF NOT EXISTS `stoped_koin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tgl` datetime DEFAULT CURRENT_TIMESTAMP,
  `koin_id` int(11) DEFAULT NULL,
  `alasan` char(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_stoped_koin_koin` (`koin_id`),
  CONSTRAINT `FK_stoped_koin_koin` FOREIGN KEY (`koin_id`) REFERENCES `koin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.stoped_koin: ~0 rows (approximately)
/*!40000 ALTER TABLE `stoped_koin` DISABLE KEYS */;

-- Dumping structure for table koinkoin.transaksi
CREATE TABLE IF NOT EXISTS `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `koin_id` int(11) DEFAULT '0',
  `tgl_beli` datetime DEFAULT CURRENT_TIMESTAMP,
  `qty` double DEFAULT NULL,
  `harga_beli` double DEFAULT '0',
  `reff_beli` char(50) DEFAULT '0',
  `fee_beli` double DEFAULT '0',
  `tgl_jual` datetime DEFAULT NULL,
  `harga_jual` double DEFAULT NULL,
  `reff_jual` char(50) DEFAULT NULL,
  `fee_jual` double DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '0 Order beli\r\n1 Terbeli\r\n2 Order Jual\r\n3 Terjual',
  PRIMARY KEY (`id`),
  KEY `FK_transaksi_koin` (`koin_id`),
  CONSTRAINT `FK_transaksi_koin` FOREIGN KEY (`koin_id`) REFERENCES `koin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table koinkoin.transaksi: ~0 rows (approximately)
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40000 ALTER TABLE `countgl` ENABLE KEYS */;
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
/*!40000 ALTER TABLE `stoped_koin` ENABLE KEYS */;
/*!40000 ALTER TABLE `saldo` ENABLE KEYS */;
/*!40000 ALTER TABLE `koin` ENABLE KEYS */;
/*!40000 ALTER TABLE `journal` ENABLE KEYS */;
/*!40000 ALTER TABLE `harga` ENABLE KEYS */;
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
