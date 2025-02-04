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
CREATE DATABASE IF NOT EXISTS `electre` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `electre`;

-- Dumping structure for table electre.alternatif
CREATE TABLE IF NOT EXISTS `alternatif` (
  `kode_alternatif` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_alternatif` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ID` int DEFAULT NULL,
  PRIMARY KEY (`kode_alternatif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table electre.alternatif: ~20 rows (approximately)
INSERT INTO `alternatif` (`kode_alternatif`, `nama_alternatif`, `ID`) VALUES
	('A1', 'Muhammad Rasya', 1),
	('A10', 'Mhd.Ahyar Faturrahim', 10),
	('A11', 'Renita Br.Tarigan', 11),
	('A12', 'Doni Dwi Sutrisno', 12),
	('A13', 'Novia Auliani', 13),
	('A14', 'Ridho Fernanda', 14),
	('A15', 'Dimas Syahputra', 15),
	('A16', 'Putri Ramadhani', 16),
	('A17', 'Septi Herlina Wati Hulu', 17),
	('A18', 'Rohit Setiawan', 18),
	('A19', 'Erly Putri Zefani', 19),
	('A2', 'Muhammad Alif Alfarino', 2),
	('A20', 'Rizky Pratama', 20),
	('A3', 'M.Yusri Hafizd', 3),
	('A4', 'Hamdan Prayoga', 4),
	('A5', 'Reynold Satria Mahendra', 5),
	('A6', 'Putri Shyfa Khairani', 6),
	('A7', 'Muhammad Zaki', 7),
	('A8', 'Tiara Fransiska Br.Sihole', 8),
	('A9', 'Siti Fatimah', 9);

-- Dumping structure for table electre.kriteria
CREATE TABLE IF NOT EXISTS `kriteria` (
  `kode_kriteria` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_kriteria` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `bobot` int NOT NULL,
  `jenis` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'cost',
  `id` int DEFAULT NULL,
  PRIMARY KEY (`kode_kriteria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table electre.kriteria: ~10 rows (approximately)
INSERT INTO `kriteria` (`kode_kriteria`, `nama_kriteria`, `bobot`, `jenis`, `id`) VALUES
	('C1', 'Administrasi', 3, '', 1),
	('C10', 'Kepemilikan Rumah', 2, 'cost', 10),
	('C2', 'Ujian Saringan Masuk', 5, 'cost', 2),
	('C3', 'Wawancara', 3, 'benefit', 3),
	('C4', 'Prestasi Akademik', 5, 'cost', 4),
	('C5', 'Pestasi Non Akademe', 3, 'cost', 5),
	('C6', 'Pekerjaan Orang Tua', 4, 'cost', 6),
	('C7', 'Penghasilan Orang Tua', 5, 'cost', 7),
	('C8', 'Status Orang Tua', 2, 'cost', 8),
	('C9', 'Tanggungan Orang Tua', 4, 'cost', 9);

-- Dumping structure for table electre.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `level` enum('admin','user') COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table electre.pengguna: ~3 rows (approximately)
INSERT INTO `pengguna` (`id`, `email`, `nama`, `level`, `password`) VALUES
	(1, 'a@gmail.com', 'a', 'admin', '1'),
	(2, 'reno@gmail.com', 'reno', 'admin', '123'),
	(3, 'u@gmail.com', 'reno prasasti', 'user', '1');

-- Dumping structure for table electre.penilaian
CREATE TABLE IF NOT EXISTS `penilaian` (
  `kode_alternatif` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `kode_kriteria` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `nilai` int NOT NULL,
  `id_penilaian` int DEFAULT NULL,
  PRIMARY KEY (`kode_alternatif`,`kode_kriteria`),
  KEY `kode_kriteria` (`kode_kriteria`),
  CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`kode_alternatif`) REFERENCES `alternatif` (`kode_alternatif`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`kode_kriteria`) REFERENCES `kriteria` (`kode_kriteria`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table electre.penilaian: ~200 rows (approximately)
INSERT INTO `penilaian` (`kode_alternatif`, `kode_kriteria`, `nilai`, `id_penilaian`) VALUES
	('A1', 'C1', 30, NULL),
	('A1', 'C10', 25, NULL),
	('A1', 'C2', 550, NULL),
	('A1', 'C3', 80, NULL),
	('A1', 'C4', 20, NULL),
	('A1', 'C5', 10, NULL),
	('A1', 'C6', 25, NULL),
	('A1', 'C7', 20, NULL),
	('A1', 'C8', 10, NULL),
	('A1', 'C9', 20, NULL),
	('A10', 'C1', 30, NULL),
	('A10', 'C10', 40, NULL),
	('A10', 'C2', 320, NULL),
	('A10', 'C3', 98, NULL),
	('A10', 'C4', 20, NULL),
	('A10', 'C5', 10, NULL),
	('A10', 'C6', 10, NULL),
	('A10', 'C7', 15, NULL),
	('A10', 'C8', 10, NULL),
	('A10', 'C9', 15, NULL),
	('A11', 'C1', 30, NULL),
	('A11', 'C10', 25, NULL),
	('A11', 'C2', 300, NULL),
	('A11', 'C3', 75, NULL),
	('A11', 'C4', 20, NULL),
	('A11', 'C5', 15, NULL),
	('A11', 'C6', 10, NULL),
	('A11', 'C7', 10, NULL),
	('A11', 'C8', 10, NULL),
	('A11', 'C9', 20, NULL),
	('A12', 'C1', 30, NULL),
	('A12', 'C10', 25, NULL),
	('A12', 'C2', 290, NULL),
	('A12', 'C3', 96, NULL),
	('A12', 'C4', 20, NULL),
	('A12', 'C5', 25, NULL),
	('A12', 'C6', 10, NULL),
	('A12', 'C7', 10, NULL),
	('A12', 'C8', 10, NULL),
	('A12', 'C9', 15, NULL),
	('A13', 'C1', 20, NULL),
	('A13', 'C10', 25, NULL),
	('A13', 'C2', 280, NULL),
	('A13', 'C3', 88, NULL),
	('A13', 'C4', 20, NULL),
	('A13', 'C5', 15, NULL),
	('A13', 'C6', 10, NULL),
	('A13', 'C7', 10, NULL),
	('A13', 'C8', 10, NULL),
	('A13', 'C9', 20, NULL),
	('A14', 'C1', 30, NULL),
	('A14', 'C10', 25, NULL),
	('A14', 'C2', 250, NULL),
	('A14', 'C3', 75, NULL),
	('A14', 'C4', 15, NULL),
	('A14', 'C5', 10, NULL),
	('A14', 'C6', 10, NULL),
	('A14', 'C7', 10, NULL),
	('A14', 'C8', 10, NULL),
	('A14', 'C9', 20, NULL),
	('A15', 'C1', 30, NULL),
	('A15', 'C10', 40, NULL),
	('A15', 'C2', 190, NULL),
	('A15', 'C3', 75, NULL),
	('A15', 'C4', 25, NULL),
	('A15', 'C5', 10, NULL),
	('A15', 'C6', 10, NULL),
	('A15', 'C7', 10, NULL),
	('A15', 'C8', 10, NULL),
	('A15', 'C9', 20, NULL),
	('A16', 'C1', 30, NULL),
	('A16', 'C10', 25, NULL),
	('A16', 'C2', 580, NULL),
	('A16', 'C3', 70, NULL),
	('A16', 'C4', 20, NULL),
	('A16', 'C5', 10, NULL),
	('A16', 'C6', 10, NULL),
	('A16', 'C7', 10, NULL),
	('A16', 'C8', 10, NULL),
	('A16', 'C9', 25, NULL),
	('A17', 'C1', 30, NULL),
	('A17', 'C10', 40, NULL),
	('A17', 'C2', 170, NULL),
	('A17', 'C3', 85, NULL),
	('A17', 'C4', 20, NULL),
	('A17', 'C5', 15, NULL),
	('A17', 'C6', 10, NULL),
	('A17', 'C7', 10, NULL),
	('A17', 'C8', 10, NULL),
	('A17', 'C9', 25, NULL),
	('A18', 'C1', 30, NULL),
	('A18', 'C10', 25, NULL),
	('A18', 'C2', 530, NULL),
	('A18', 'C3', 60, NULL),
	('A18', 'C4', 20, NULL),
	('A18', 'C5', 10, NULL),
	('A18', 'C6', 25, NULL),
	('A18', 'C7', 30, NULL),
	('A18', 'C8', 30, NULL),
	('A18', 'C9', 20, NULL),
	('A19', 'C1', 10, NULL),
	('A19', 'C10', 25, NULL),
	('A19', 'C2', 500, NULL),
	('A19', 'C3', 93, NULL),
	('A19', 'C4', 20, NULL),
	('A19', 'C5', 10, NULL),
	('A19', 'C6', 10, NULL),
	('A19', 'C7', 10, NULL),
	('A19', 'C8', 10, NULL),
	('A19', 'C9', 25, NULL),
	('A2', 'C1', 10, NULL),
	('A2', 'C10', 35, NULL),
	('A2', 'C2', 500, NULL),
	('A2', 'C3', 90, NULL),
	('A2', 'C4', 20, NULL),
	('A2', 'C5', 15, NULL),
	('A2', 'C6', 25, NULL),
	('A2', 'C7', 10, NULL),
	('A2', 'C8', 10, NULL),
	('A2', 'C9', 10, NULL),
	('A20', 'C1', 30, NULL),
	('A20', 'C10', 25, NULL),
	('A20', 'C2', 500, NULL),
	('A20', 'C3', 70, NULL),
	('A20', 'C4', 20, NULL),
	('A20', 'C5', 10, NULL),
	('A20', 'C6', 25, NULL),
	('A20', 'C7', 20, NULL),
	('A20', 'C8', 25, NULL),
	('A20', 'C9', 15, NULL),
	('A3', 'C1', 10, NULL),
	('A3', 'C10', 25, NULL),
	('A3', 'C2', 500, NULL),
	('A3', 'C3', 90, NULL),
	('A3', 'C4', 20, NULL),
	('A3', 'C5', 15, NULL),
	('A3', 'C6', 25, NULL),
	('A3', 'C7', 10, NULL),
	('A3', 'C8', 30, NULL),
	('A3', 'C9', 20, NULL),
	('A4', 'C1', 30, NULL),
	('A4', 'C10', 25, NULL),
	('A4', 'C2', 470, NULL),
	('A4', 'C3', 75, NULL),
	('A4', 'C4', 20, NULL),
	('A4', 'C5', 10, NULL),
	('A4', 'C6', 30, NULL),
	('A4', 'C7', 15, NULL),
	('A4', 'C8', 25, NULL),
	('A4', 'C9', 20, NULL),
	('A5', 'C1', 25, NULL),
	('A5', 'C10', 25, NULL),
	('A5', 'C2', 420, NULL),
	('A5', 'C3', 70, NULL),
	('A5', 'C4', 10, NULL),
	('A5', 'C5', 15, NULL),
	('A5', 'C6', 10, NULL),
	('A5', 'C7', 10, NULL),
	('A5', 'C8', 25, NULL),
	('A5', 'C9', 20, NULL),
	('A6', 'C1', 30, NULL),
	('A6', 'C10', 25, NULL),
	('A6', 'C2', 400, NULL),
	('A6', 'C3', 80, NULL),
	('A6', 'C4', 15, NULL),
	('A6', 'C5', 10, NULL),
	('A6', 'C6', 10, NULL),
	('A6', 'C7', 10, NULL),
	('A6', 'C8', 10, NULL),
	('A6', 'C9', 25, NULL),
	('A7', 'C1', 30, NULL),
	('A7', 'C10', 25, NULL),
	('A7', 'C2', 380, NULL),
	('A7', 'C3', 85, NULL),
	('A7', 'C4', 25, NULL),
	('A7', 'C5', 10, NULL),
	('A7', 'C6', 25, NULL),
	('A7', 'C7', 15, NULL),
	('A7', 'C8', 10, NULL),
	('A7', 'C9', 15, NULL),
	('A8', 'C1', 30, NULL),
	('A8', 'C10', 25, NULL),
	('A8', 'C2', 320, NULL),
	('A8', 'C3', 70, NULL),
	('A8', 'C4', 15, NULL),
	('A8', 'C5', 25, NULL),
	('A8', 'C6', 25, NULL),
	('A8', 'C7', 30, NULL),
	('A8', 'C8', 25, NULL),
	('A8', 'C9', 15, NULL),
	('A9', 'C1', 30, NULL),
	('A9', 'C10', 25, NULL),
	('A9', 'C2', 320, NULL),
	('A9', 'C3', 80, NULL),
	('A9', 'C4', 25, NULL),
	('A9', 'C5', 10, NULL),
	('A9', 'C6', 25, NULL),
	('A9', 'C7', 10, NULL),
	('A9', 'C8', 10, NULL),
	('A9', 'C9', 15, NULL);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
