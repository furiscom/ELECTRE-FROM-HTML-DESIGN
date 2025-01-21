-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for electre
CREATE DATABASE IF NOT EXISTS `electre` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `electre`;

-- Dumping structure for table electre.alternatif
CREATE TABLE IF NOT EXISTS `alternatif` (
  `kode_alternatif` varchar(10) NOT NULL,
  `nama_alternatif` varchar(255) NOT NULL,
  PRIMARY KEY (`kode_alternatif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table electre.alternatif: ~9 rows (approximately)
INSERT INTO `alternatif` (`kode_alternatif`, `nama_alternatif`) VALUES
	('A01', 'Mahasiswa 1'),
	('A02', 'Mahasiswa 2'),
	('A03', 'Mahasiswa 3'),
	('A04', 'Mahasiswa 4'),
	('A05', 'Mahasiswa 5'),
	('A06', 'Mahasiswa 6'),
	('A07', 'Mahasiswa 7'),
	('A08', 'Mahasiswa 8'),
	('A09', 'Mahasiswa 9'),
	('a11', 'mhs10');

-- Dumping structure for table electre.kriteria
CREATE TABLE IF NOT EXISTS `kriteria` (
  `kode_kriteria` varchar(10) NOT NULL,
  `nama_kriteria` varchar(255) NOT NULL,
  `bobot` int NOT NULL,
  `jenis` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'cost',
  PRIMARY KEY (`kode_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table electre.kriteria: ~4 rows (approximately)
INSERT INTO `kriteria` (`kode_kriteria`, `nama_kriteria`, `bobot`, `jenis`) VALUES
	('C01', 'Nilai Mata Pelajaran', 1, ''),
	('C02', 'Peringkat Siswa', 3, 'cost'),
	('C03', 'Nilai UN', 5, 'benefit');

-- Dumping structure for table electre.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `level` enum('admin','user') NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table electre.pengguna: ~0 rows (approximately)
INSERT INTO `pengguna` (`id`, `email`, `nama`, `level`, `password`) VALUES
	(1, 'a@gmail.com', 'a', 'admin', '1');

-- Dumping structure for table electre.penilaian
CREATE TABLE IF NOT EXISTS `penilaian` (
  `kode_alternatif` varchar(10) NOT NULL,
  `kode_kriteria` varchar(10) NOT NULL,
  `nilai` int NOT NULL,
  `id_penilaian` int DEFAULT NULL,
  PRIMARY KEY (`kode_alternatif`,`kode_kriteria`),
  KEY `kode_kriteria` (`kode_kriteria`),
  CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`kode_alternatif`) REFERENCES `alternatif` (`kode_alternatif`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`kode_kriteria`) REFERENCES `kriteria` (`kode_kriteria`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table electre.penilaian: ~27 rows (approximately)
INSERT INTO `penilaian` (`kode_alternatif`, `kode_kriteria`, `nilai`, `id_penilaian`) VALUES
	('A01', 'C01', 80, 1),
	('A01', 'C02', 2, 2),
	('A01', 'C03', 91, 3),
	('A02', 'C01', 70, 4),
	('A02', 'C02', 4, 5),
	('A02', 'C03', 85, 6),
	('A03', 'C01', 90, 7),
	('A03', 'C02', 5, 8),
	('A03', 'C03', 78, 9),
	('A04', 'C01', 76, 10),
	('A04', 'C02', 3, 11),
	('A04', 'C03', 82, 12),
	('A05', 'C01', 85, 13),
	('A05', 'C02', 1, 14),
	('A05', 'C03', 92, 15),
	('A06', 'C01', 78, 16),
	('A06', 'C02', 3, 17),
	('A06', 'C03', 88, 18),
	('A07', 'C01', 95, 19),
	('A07', 'C02', 1, 20),
	('A07', 'C03', 94, 21),
	('A08', 'C01', 88, 22),
	('A08', 'C02', 2, 23),
	('A08', 'C03', 80, 24),
	('A09', 'C01', 75, 25),
	('A09', 'C02', 4, 26),
	('A09', 'C03', 85, 27);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
