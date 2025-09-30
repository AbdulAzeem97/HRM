-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: u902429527_ttphrm
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `u902429527_ttphrm`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `u902429527_ttphrm` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `u902429527_ttphrm`;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `added_by` varchar(40) DEFAULT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `announcements_company_id_foreign` (`company_id`),
  KEY `announcements_department_id_foreign` (`department_id`),
  CONSTRAINT `announcements_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcements_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appraisals`
--

DROP TABLE IF EXISTS `appraisals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appraisals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `designation_id` bigint(20) unsigned NOT NULL,
  `customer_experience` varchar(191) NOT NULL,
  `marketing` varchar(191) DEFAULT NULL,
  `administration` varchar(191) DEFAULT NULL,
  `professionalism` varchar(191) DEFAULT NULL,
  `integrity` varchar(191) DEFAULT NULL,
  `attendance` varchar(191) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appraisals_company_id_foreign` (`company_id`),
  KEY `appraisals_employee_id_foreign` (`employee_id`),
  KEY `appraisals_department_id_foreign` (`department_id`),
  KEY `appraisals_designation_id_foreign` (`designation_id`),
  CONSTRAINT `appraisals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appraisals_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appraisals`
--

LOCK TABLES `appraisals` WRITE;
/*!40000 ALTER TABLE `appraisals` DISABLE KEYS */;
/*!40000 ALTER TABLE `appraisals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_categories`
--

DROP TABLE IF EXISTS `asset_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_categories_company_id_foreign` (`company_id`),
  CONSTRAINT `asset_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_categories`
--

LOCK TABLES `asset_categories` WRITE;
/*!40000 ALTER TABLE `asset_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(50) NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `asset_code` varchar(80) NOT NULL,
  `assets_category_id` bigint(20) unsigned NOT NULL,
  `Asset_note` mediumtext DEFAULT NULL,
  `manufacturer` varchar(191) NOT NULL,
  `serial_number` varchar(191) NOT NULL,
  `invoice_number` varchar(191) NOT NULL,
  `asset_image` varchar(191) DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `warranty_date` date NOT NULL,
  `status` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assets_company_id_foreign` (`company_id`),
  KEY `assets_employee_id_foreign` (`employee_id`),
  KEY `assets_assets_category_id_foreign` (`assets_category_id`),
  CONSTRAINT `assets_assets_category_id_foreign` FOREIGN KEY (`assets_category_id`) REFERENCES `asset_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attendances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `office_shift_id` bigint(20) unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `clock_in` varchar(191) NOT NULL,
  `clock_in_ip` varchar(45) NOT NULL,
  `clock_out` varchar(191) NOT NULL,
  `clock_out_ip` varchar(45) NOT NULL,
  `clock_in_out` tinyint(4) NOT NULL,
  `time_late` varchar(191) NOT NULL DEFAULT '00:00',
  `early_leaving` varchar(191) NOT NULL DEFAULT '00:00',
  `overtime` varchar(191) NOT NULL DEFAULT '00:00',
  `total_work` varchar(191) NOT NULL DEFAULT '00:00',
  `total_rest` varchar(191) NOT NULL DEFAULT '00:00',
  `attendance_status` varchar(191) NOT NULL DEFAULT 'present',
  PRIMARY KEY (`id`),
  KEY `attendances_employee_id_foreign` (`employee_id`),
  KEY `attendances_office_shift_id_index` (`office_shift_id`),
  CONSTRAINT `attendances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_office_shift_id_foreign` FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (28,64,1,'2025-05-01','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(29,64,1,'2025-05-02','08:10:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:20','00:40','Present'),(30,64,1,'2025-05-03','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(31,64,1,'2025-05-05','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(32,64,1,'2025-05-06','08:15:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:30','00:30','Present'),(33,64,1,'2025-05-07','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(34,64,1,'2025-05-08','07:45:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:15','00:00','Present'),(35,64,1,'2025-05-09','08:30:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:30','00:15','00:00','08:30','01:30','Present'),(36,64,1,'2025-05-10','08:00:00','127.0.0.1','14:00:00','127.0.0.1',0,'00:00','03:15','00:00','06:00','00:00','Half Day'),(37,64,1,'2025-05-12','08:00:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:45','00:00','08:30','01:30','Early Leave'),(38,64,1,'2025-05-13','08:05:00','127.0.0.1','17:20:00','127.0.0.1',0,'00:00','00:00','00:05','09:15','00:45','Present'),(39,64,1,'2025-05-14','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(40,64,1,'2025-05-15','08:20:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:20','00:00','00:55','10:10','00:00','Present'),(41,64,1,'2025-05-16','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(42,64,1,'2025-05-17','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(43,64,1,'2025-05-19','08:00:00','127.0.0.1','19:00:00','127.0.0.1',0,'00:00','00:00','01:45','11:00','00:00','Present'),(44,64,1,'2025-05-20','08:45:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:45','00:15','00:00','08:15','01:45','Present'),(45,64,1,'2025-05-21','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(46,64,1,'2025-05-22','08:10:00','127.0.0.1','17:10:00','127.0.0.1',0,'00:00','00:05','00:00','09:00','01:00','Present'),(47,64,1,'2025-05-23','08:00:00','127.0.0.1','12:30:00','127.0.0.1',0,'00:00','04:45','00:00','04:30','00:00','Half Day'),(48,64,1,'2025-05-24','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(49,64,1,'2025-05-26','07:50:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:10','00:00','Present'),(50,64,1,'2025-05-27','08:15:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:45','01:15','Present'),(51,64,1,'2025-05-28','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(52,64,1,'2025-05-29','08:00:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:00','00:00','01:15','10:30','00:00','Present'),(53,64,1,'2025-05-30','08:05:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:55','01:05','Present'),(54,64,1,'2025-05-31','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(75,65,1,'2025-05-01','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(76,65,1,'2025-05-02','08:10:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:35','00:25','Present'),(77,65,1,'2025-05-05','08:05:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:10','00:50','Present'),(78,65,1,'2025-05-06','08:00:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:00','00:00','Present'),(79,65,1,'2025-05-07','08:20:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:20','00:00','00:00','08:55','01:05','Present'),(80,65,1,'2025-05-08','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(81,65,1,'2025-05-09','08:30:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:30','00:00','00:00','08:45','01:15','Present'),(82,65,1,'2025-05-12','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(83,65,1,'2025-05-13','08:15:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:00','01:00','Present'),(84,65,1,'2025-05-14','08:00:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:45','00:15','Present'),(85,65,2,'2025-05-15','07:00:00','127.0.0.1','16:00:00','127.0.0.1',0,'00:00','00:00','00:15','09:00','01:00','Present'),(86,65,2,'2025-05-16','07:10:00','127.0.0.1','16:15:00','127.0.0.1',0,'00:00','00:00','00:30','09:05','00:55','Present'),(87,65,2,'2025-05-19','07:00:00','127.0.0.1','15:45:00','127.0.0.1',0,'00:00','00:00','00:00','08:45','01:15','Present'),(88,65,2,'2025-05-20','07:05:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:00','00:45','09:25','00:35','Present'),(89,65,2,'2025-05-21','07:20:00','127.0.0.1','15:45:00','127.0.0.1',0,'00:20','00:00','00:00','08:25','01:35','Present'),(90,65,2,'2025-05-22','07:00:00','127.0.0.1','16:00:00','127.0.0.1',0,'00:00','00:00','00:15','09:00','01:00','Present'),(91,65,2,'2025-05-23','07:30:00','127.0.0.1','15:45:00','127.0.0.1',0,'00:30','00:00','00:00','08:15','01:45','Present'),(92,65,2,'2025-05-26','07:00:00','127.0.0.1','16:15:00','127.0.0.1',0,'00:00','00:00','00:30','09:15','00:45','Present'),(93,65,2,'2025-05-27','07:15:00','127.0.0.1','15:45:00','127.0.0.1',0,'00:00','00:00','00:00','08:30','01:30','Present'),(94,65,2,'2025-05-28','07:00:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:00','00:45','09:30','00:30','Present'),(95,63,NULL,'2025-05-01','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(96,63,NULL,'2025-05-02','08:10:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:20','00:40','Present'),(97,63,NULL,'2025-05-03','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(98,63,NULL,'2025-05-05','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(99,63,NULL,'2025-05-06','08:15:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:30','00:30','Present'),(100,63,NULL,'2025-05-07','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(101,63,NULL,'2025-05-08','07:45:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:15','00:00','Present'),(102,63,NULL,'2025-05-09','08:30:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:30','00:15','00:00','08:30','01:30','Present'),(103,63,NULL,'2025-05-10','08:00:00','127.0.0.1','14:00:00','127.0.0.1',0,'00:00','03:15','00:00','06:00','00:00','Half Day'),(104,63,NULL,'2025-05-12','08:00:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:45','00:00','08:30','01:30','Early Leave'),(105,63,NULL,'2025-05-13','08:05:00','127.0.0.1','17:20:00','127.0.0.1',0,'00:00','00:00','00:05','09:15','00:45','Present'),(106,63,NULL,'2025-05-14','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(107,63,NULL,'2025-05-15','08:20:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:20','00:00','00:55','10:10','00:00','Present'),(108,63,NULL,'2025-05-16','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(109,63,NULL,'2025-05-17','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(110,63,NULL,'2025-05-19','08:00:00','127.0.0.1','19:00:00','127.0.0.1',0,'00:00','00:00','01:45','11:00','00:00','Present'),(111,63,NULL,'2025-05-20','08:45:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:45','00:15','00:00','08:15','01:45','Present'),(112,63,NULL,'2025-05-21','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(113,63,NULL,'2025-05-22','08:10:00','127.0.0.1','17:10:00','127.0.0.1',0,'00:00','00:05','00:00','09:00','01:00','Present'),(114,63,NULL,'2025-05-23','08:00:00','127.0.0.1','12:30:00','127.0.0.1',0,'00:00','04:45','00:00','04:30','00:00','Half Day'),(115,63,NULL,'2025-05-24','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(116,63,NULL,'2025-05-26','07:50:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:10','00:00','Present'),(117,63,NULL,'2025-05-27','08:15:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:45','01:15','Present'),(118,63,NULL,'2025-05-28','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(119,63,NULL,'2025-05-29','08:00:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:00','00:00','01:15','10:30','00:00','Present'),(120,63,NULL,'2025-05-30','08:05:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:55','01:05','Present'),(121,63,NULL,'2025-05-31','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(122,62,NULL,'2025-05-01','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(123,62,NULL,'2025-05-02','08:10:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:20','00:40','Present'),(124,62,NULL,'2025-05-03','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(125,62,NULL,'2025-05-05','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(126,62,NULL,'2025-05-06','08:15:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:30','00:30','Present'),(127,62,NULL,'2025-05-07','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(128,62,NULL,'2025-05-08','07:45:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:15','00:00','Present'),(129,62,NULL,'2025-05-09','08:30:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:30','00:15','00:00','08:30','01:30','Present'),(130,62,NULL,'2025-05-10','08:00:00','127.0.0.1','14:00:00','127.0.0.1',0,'00:00','03:15','00:00','06:00','00:00','Half Day'),(131,62,NULL,'2025-05-12','08:00:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:45','00:00','08:30','01:30','Early Leave'),(132,62,NULL,'2025-05-13','08:05:00','127.0.0.1','17:20:00','127.0.0.1',0,'00:00','00:00','00:05','09:15','00:45','Present'),(133,62,NULL,'2025-05-14','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(134,62,NULL,'2025-05-15','08:20:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:20','00:00','00:55','10:10','00:00','Present'),(135,62,NULL,'2025-05-16','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(136,62,NULL,'2025-05-17','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(137,62,NULL,'2025-05-19','08:00:00','127.0.0.1','19:00:00','127.0.0.1',0,'00:00','00:00','01:45','11:00','00:00','Present'),(138,62,NULL,'2025-05-20','08:45:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:45','00:15','00:00','08:15','01:45','Present'),(139,62,NULL,'2025-05-21','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(140,62,NULL,'2025-05-22','08:10:00','127.0.0.1','17:10:00','127.0.0.1',0,'00:00','00:05','00:00','09:00','01:00','Present'),(141,62,NULL,'2025-05-23','08:00:00','127.0.0.1','12:30:00','127.0.0.1',0,'00:00','04:45','00:00','04:30','00:00','Half Day'),(142,62,NULL,'2025-05-24','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(143,62,NULL,'2025-05-26','07:50:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:10','00:00','Present'),(144,62,NULL,'2025-05-27','08:15:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:45','01:15','Present'),(145,62,NULL,'2025-05-28','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(146,62,NULL,'2025-05-29','08:00:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:00','00:00','01:15','10:30','00:00','Present'),(147,62,NULL,'2025-05-30','08:05:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:55','01:05','Present'),(148,62,NULL,'2025-05-31','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(149,62,NULL,'2025-05-01','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(150,62,NULL,'2025-05-02','08:10:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:20','00:40','Present'),(151,62,NULL,'2025-05-03','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(152,62,NULL,'2025-05-05','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(153,62,NULL,'2025-05-06','08:15:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:30','00:30','Present'),(154,62,NULL,'2025-05-07','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(155,62,NULL,'2025-05-08','07:45:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:15','00:00','Present'),(156,62,NULL,'2025-05-09','08:30:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:30','00:15','00:00','08:30','01:30','Present'),(157,62,NULL,'2025-05-10','08:00:00','127.0.0.1','14:00:00','127.0.0.1',0,'00:00','03:15','00:00','06:00','00:00','Half Day'),(158,62,NULL,'2025-05-12','08:00:00','127.0.0.1','16:30:00','127.0.0.1',0,'00:00','00:45','00:00','08:30','01:30','Early Leave'),(159,62,NULL,'2025-05-13','08:05:00','127.0.0.1','17:20:00','127.0.0.1',0,'00:00','00:00','00:05','09:15','00:45','Present'),(160,62,NULL,'2025-05-14','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(161,62,NULL,'2025-05-15','08:20:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:20','00:00','00:55','10:10','00:00','Present'),(162,62,NULL,'2025-05-16','08:00:00','127.0.0.1','13:00:00','127.0.0.1',0,'00:00','04:15','00:00','05:00','00:00','Half Day'),(163,62,NULL,'2025-05-17','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(164,62,NULL,'2025-05-19','08:00:00','127.0.0.1','19:00:00','127.0.0.1',0,'00:00','00:00','01:45','11:00','00:00','Present'),(165,62,NULL,'2025-05-20','08:45:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:45','00:15','00:00','08:15','01:45','Present'),(166,62,NULL,'2025-05-21','08:00:00','127.0.0.1','17:30:00','127.0.0.1',0,'00:00','00:00','00:15','09:30','00:30','Present'),(167,62,NULL,'2025-05-22','08:10:00','127.0.0.1','17:10:00','127.0.0.1',0,'00:00','00:05','00:00','09:00','01:00','Present'),(168,62,NULL,'2025-05-23','08:00:00','127.0.0.1','12:30:00','127.0.0.1',0,'00:00','04:45','00:00','04:30','00:00','Half Day'),(169,62,NULL,'2025-05-24','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(170,62,NULL,'2025-05-26','07:50:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:10','00:00','Present'),(171,62,NULL,'2025-05-27','08:15:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:45','01:15','Present'),(172,62,NULL,'2025-05-28','08:00:00','127.0.0.1','17:15:00','127.0.0.1',0,'00:00','00:00','00:00','09:15','00:45','Present'),(173,62,NULL,'2025-05-29','08:00:00','127.0.0.1','18:30:00','127.0.0.1',0,'00:00','00:00','01:15','10:30','00:00','Present'),(174,62,NULL,'2025-05-30','08:05:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','08:55','01:05','Present'),(175,62,NULL,'2025-05-31','08:00:00','127.0.0.1','17:00:00','127.0.0.1',0,'00:00','00:15','00:00','09:00','01:00','Present'),(181,65,1,'2025-06-01','08:10:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:35','00:00','Present'),(182,65,1,'2025-06-02','08:20:00','127.0.0.1','18:15:00','127.0.0.1',0,'00:20','00:00','00:40','09:55','00:00','Present'),(183,65,1,'2025-06-03','08:30:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:30','00:00','00:00','09:15','00:00','Present'),(184,65,1,'2025-06-04','08:00:00','127.0.0.1','18:00:00','127.0.0.1',0,'00:00','00:00','00:45','10:00','00:00','Present'),(185,65,1,'2025-06-05','08:15:00','127.0.0.1','17:45:00','127.0.0.1',0,'00:00','00:00','00:30','09:30','00:00','Present');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `award_types`
--

DROP TABLE IF EXISTS `award_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `award_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `award_name` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `award_types`
--

LOCK TABLES `award_types` WRITE;
/*!40000 ALTER TABLE `award_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `award_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `awards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `award_information` mediumtext DEFAULT NULL,
  `award_date` date NOT NULL,
  `gift` varchar(40) DEFAULT NULL,
  `cash` varchar(40) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `award_type_id` bigint(20) unsigned DEFAULT NULL,
  `award_photo` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `awards_company_id_foreign` (`company_id`),
  KEY `awards_department_id_foreign` (`department_id`),
  KEY `awards_employee_id_foreign` (`employee_id`),
  KEY `awards_award_type_id_foreign` (`award_type_id`),
  CONSTRAINT `awards_award_type_id_foreign` FOREIGN KEY (`award_type_id`) REFERENCES `award_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `awards_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `awards_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `awards_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `awards`
--

LOCK TABLES `awards` WRITE;
/*!40000 ALTER TABLE `awards` DISABLE KEYS */;
/*!40000 ALTER TABLE `awards` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `c_m_s`
--

DROP TABLE IF EXISTS `c_m_s`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `c_m_s` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `home` longtext DEFAULT NULL,
  `about` longtext DEFAULT NULL,
  `contact` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `c_m_s`
--

LOCK TABLES `c_m_s` WRITE;
/*!40000 ALTER TABLE `c_m_s` DISABLE KEYS */;
/*!40000 ALTER TABLE `c_m_s` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendarables`
--

DROP TABLE IF EXISTS `calendarables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calendarables` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendarables`
--

LOCK TABLES `calendarables` WRITE;
/*!40000 ALTER TABLE `calendarables` DISABLE KEYS */;
/*!40000 ALTER TABLE `calendarables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `candidate_interview`
--

DROP TABLE IF EXISTS `candidate_interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate_interview` (
  `interview_id` bigint(20) unsigned NOT NULL,
  `candidate_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`interview_id`,`candidate_id`),
  KEY `candidate_interview_candidate_id_foreign` (`candidate_id`),
  CONSTRAINT `candidate_interview_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `job_candidates` (`id`),
  CONSTRAINT `candidate_interview_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `job_interviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate_interview`
--

LOCK TABLES `candidate_interview` WRITE;
/*!40000 ALTER TABLE `candidate_interview` DISABLE KEYS */;
/*!40000 ALTER TABLE `candidate_interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `contact_no` varchar(15) NOT NULL,
  `username` varchar(64) NOT NULL,
  `profile` varchar(191) DEFAULT NULL,
  `company_name` varchar(191) NOT NULL,
  `gender` varchar(40) NOT NULL,
  `website` varchar(40) DEFAULT NULL,
  `address1` mediumtext DEFAULT NULL,
  `address2` mediumtext DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `zip` varchar(191) DEFAULT NULL,
  `country` tinyint(4) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `clients_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_name` varchar(191) NOT NULL,
  `company_type_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `trading_name` varchar(191) DEFAULT NULL,
  `registration_no` varchar(191) DEFAULT NULL,
  `contact_no` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `website` varchar(191) DEFAULT NULL,
  `tax_no` varchar(191) DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `company_logo` varchar(191) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_location_id_foreign` (`location_id`),
  KEY `companies_company_type_id_foreign` (`company_type_id`),
  CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`),
  CONSTRAINT `companies_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'TRIMS TECH PACKAGING',1,'','','1234567','info@cubetechwiz.com','','1234',1,NULL,NULL,'2025-05-26 17:30:37','2025-05-26 17:30:37');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_types`
--

DROP TABLE IF EXISTS `company_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_types`
--

LOCK TABLES `company_types` WRITE;
/*!40000 ALTER TABLE `company_types` DISABLE KEYS */;
INSERT INTO `company_types` VALUES (1,'Private','2025-05-26 17:29:58','2025-05-26 17:29:58',NULL);
/*!40000 ALTER TABLE `company_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaints` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `complaint_title` varchar(40) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `complaint_from` bigint(20) unsigned NOT NULL,
  `complaint_against` bigint(20) unsigned NOT NULL,
  `complaint_date` date NOT NULL,
  `status` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `complaints_company_id_foreign` (`company_id`),
  KEY `complaints_complaint_from_foreign` (`complaint_from`),
  KEY `complaints_complaint_against_foreign` (`complaint_against`),
  CONSTRAINT `complaints_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaints_complaint_against_foreign` FOREIGN KEY (`complaint_against`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `complaints_complaint_from_foreign` FOREIGN KEY (`complaint_from`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'US','United States'),(2,'CA','Canada'),(3,'AF','Afghanistan'),(4,'AL','Albania'),(5,'DZ','Algeria'),(6,'DS','American Samoa'),(7,'AD','Andorra'),(8,'AO','Angola'),(9,'AI','Anguilla'),(10,'AQ','Antarctica'),(11,'AG','Antigua and/or Barbuda'),(12,'AR','Argentina'),(13,'AM','Armenia'),(14,'AW','Aruba'),(15,'AU','Australia'),(16,'AT','Austria'),(17,'AZ','Azerbaijan'),(18,'BS','Bahamas'),(19,'BH','Bahrain'),(20,'BD','Bangladesh'),(21,'BB','Barbados'),(22,'BY','Belarus'),(23,'BE','Belgium'),(24,'BZ','Belize'),(25,'BJ','Benin'),(26,'BM','Bermuda'),(27,'BT','Bhutan'),(28,'BO','Bolivia'),(29,'BA','Bosnia and Herzegovina'),(30,'BW','Botswana'),(31,'BV','Bouvet Island'),(32,'BR','Brazil'),(33,'IO','British lndian Ocean Territory'),(34,'BN','Brunei Darussalam'),(35,'BG','Bulgaria'),(36,'BF','Burkina Faso'),(37,'BI','Burundi'),(38,'KH','Cambodia'),(39,'CM','Cameroon'),(40,'CV','Cape Verde'),(41,'KY','Cayman Islands'),(42,'CF','Central African Republic'),(43,'TD','Chad'),(44,'CL','Chile'),(45,'CN','China'),(46,'CX','Christmas Island'),(47,'CC','Cocos (Keeling) Islands'),(48,'CO','Colombia'),(49,'KM','Comoros'),(50,'CG','Congo'),(51,'CK','Cook Islands'),(52,'CR','Costa Rica'),(53,'HR','Croatia (Hrvatska)'),(54,'CU','Cuba'),(55,'CY','Cyprus'),(56,'CZ','Czech Republic'),(57,'DK','Denmark'),(58,'DJ','Djibouti'),(59,'DM','Dominica'),(60,'DO','Dominican Republic'),(61,'TP','East Timor'),(62,'EC','Ecudaor'),(63,'EG','Egypt'),(64,'SV','El Salvador'),(65,'GQ','Equatorial Guinea'),(66,'ER','Eritrea'),(67,'EE','Estonia'),(68,'ET','Ethiopia'),(69,'FK','Falkland Islands (Malvinas)'),(70,'FO','Faroe Islands'),(71,'FJ','Fiji'),(72,'FI','Finland'),(73,'FR','France'),(74,'FX','France, Metropolitan'),(75,'GF','French Guiana'),(76,'PF','French Polynesia'),(77,'TF','French Southern Territories'),(78,'GA','Gabon'),(79,'GM','Gambia'),(80,'GE','Georgia'),(81,'DE','Germany'),(82,'GH','Ghana'),(83,'GI','Gibraltar'),(84,'GR','Greece'),(85,'GL','Greenland'),(86,'GD','Grenada'),(87,'GP','Guadeloupe'),(88,'GU','Guam'),(89,'GT','Guatemala'),(90,'GN','Guinea'),(91,'GW','Guinea-Bissau'),(92,'GY','Guyana'),(93,'HT','Haiti'),(94,'HM','Heard and Mc Donald Islands'),(95,'HN','Honduras'),(96,'HK','Hong Kong'),(97,'HU','Hungary'),(98,'IS','Iceland'),(99,'IN','India'),(100,'ID','Indonesia'),(101,'IR','Iran (Islamic Republic of)'),(102,'IQ','Iraq'),(103,'IE','Ireland'),(104,'IL','Israel'),(105,'IT','Italy'),(106,'CI','Ivory Coast'),(107,'JM','Jamaica'),(108,'JP','Japan'),(109,'JO','Jordan'),(110,'KZ','Kazakhstan'),(111,'KE','Kenya'),(112,'KI','Kiribati'),(113,'KP','Korea, Democratic People\'s Republic of'),(114,'KR','Korea, Republic of'),(115,'KW','Kuwait'),(116,'KG','Kyrgyzstan'),(117,'LA','Lao People\'s Democratic Republic'),(118,'LV','Latvia'),(119,'LB','Lebanon'),(120,'LS','Lesotho'),(121,'LR','Liberia'),(122,'LY','Libyan Arab Jamahiriya'),(123,'LI','Liechtenstein'),(124,'LT','Lithuania'),(125,'LU','Luxembourg'),(126,'MO','Macau'),(127,'MK','Macedonia'),(128,'MG','Madagascar'),(129,'MW','Malawi'),(130,'MY','Malaysia'),(131,'MV','Maldives'),(132,'ML','Mali'),(133,'MT','Malta'),(134,'MH','Marshall Islands'),(135,'MQ','Martinique'),(136,'MR','Mauritania'),(137,'MU','Mauritius'),(138,'TY','Mayotte'),(139,'MX','Mexico'),(140,'FM','Micronesia, Federated States of'),(141,'MD','Moldova, Republic of'),(142,'MC','Monaco'),(143,'MN','Mongolia'),(144,'MS','Montserrat'),(145,'MA','Morocco'),(146,'MZ','Mozambique'),(147,'MM','Myanmar'),(148,'NA','Namibia'),(149,'NR','Nauru'),(150,'NP','Nepal'),(151,'NL','Netherlands'),(152,'AN','Netherlands Antilles'),(153,'NC','New Caledonia'),(154,'NZ','New Zealand'),(155,'NI','Nicaragua'),(156,'NE','Niger'),(157,'NG','Nigeria'),(158,'NU','Niue'),(159,'NF','Norfork Island'),(160,'MP','Northern Mariana Islands'),(161,'NO','Norway'),(162,'OM','Oman'),(163,'PK','Pakistan'),(164,'PW','Palau'),(165,'PA','Panama'),(166,'PG','Papua New Guinea'),(167,'PY','Paraguay'),(168,'PE','Peru'),(169,'PH','Philippines'),(170,'PN','Pitcairn'),(171,'PL','Poland'),(172,'PT','Portugal'),(173,'PR','Puerto Rico'),(174,'QA','Qatar'),(175,'RE','Reunion'),(176,'RO','Romania'),(177,'RU','Russian Federation'),(178,'RW','Rwanda'),(179,'KN','Saint Kitts and Nevis'),(180,'LC','Saint Lucia'),(181,'VC','Saint Vincent and the Grenadines'),(182,'WS','Samoa'),(183,'SM','San Marino'),(184,'ST','Sao Tome and Principe'),(185,'SA','Saudi Arabia'),(186,'SN','Senegal'),(187,'SC','Seychelles'),(188,'SL','Sierra Leone'),(189,'SG','Singapore'),(190,'SK','Slovakia'),(191,'SI','Slovenia'),(192,'SB','Solomon Islands'),(193,'SO','Somalia'),(194,'ZA','South Africa'),(195,'GS','South Georgia South Sandwich Islands'),(196,'ES','Spain'),(197,'LK','Sri Lanka'),(198,'SH','St. Helena'),(199,'PM','St. Pierre and Miquelon'),(200,'SD','Sudan'),(201,'SR','Suriname'),(202,'SJ','Svalbarn and Jan Mayen Islands'),(203,'SZ','Swaziland'),(204,'SE','Sweden'),(205,'CH','Switzerland'),(206,'SY','Syrian Arab Republic'),(207,'TW','Taiwan'),(208,'TJ','Tajikistan'),(209,'TZ','Tanzania, United Republic of'),(210,'TH','Thailand'),(211,'TG','Togo'),(212,'TK','Tokelau'),(213,'TO','Tonga'),(214,'TT','Trinidad and Tobago'),(215,'TN','Tunisia'),(216,'TR','Turkey'),(217,'TM','Turkmenistan'),(218,'TC','Turks and Caicos Islands'),(219,'TV','Tuvalu'),(220,'UG','Uganda'),(221,'UA','Ukraine'),(222,'AE','United Arab Emirates'),(223,'GB','United Kingdom'),(224,'UM','United States minor outlying islands'),(225,'UY','Uruguay'),(226,'UZ','Uzbekistan'),(227,'VU','Vanuatu'),(228,'VA','Vatican City State'),(229,'VE','Venezuela'),(230,'VN','Vietnam'),(231,'VG','Virigan Islands (British)'),(232,'VI','Virgin Islands (U.S.)'),(233,'WF','Wallis and Futuna Islands'),(234,'EH','Western Sahara'),(235,'YE','Yemen'),(236,'YU','Yugoslavia'),(237,'ZR','Zaire'),(238,'ZM','Zambia'),(239,'ZW','Zimbabwe');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deduction_types`
--

DROP TABLE IF EXISTS `deduction_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deduction_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deduction_types`
--

LOCK TABLES `deduction_types` WRITE;
/*!40000 ALTER TABLE `deduction_types` DISABLE KEYS */;
INSERT INTO `deduction_types` VALUES (1,'Government','2025-09-27 09:56:23','2025-09-27 09:56:23',NULL);
/*!40000 ALTER TABLE `deduction_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(191) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_head` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_company_id_foreign` (`company_id`),
  KEY `departments_department_head_foreign` (`department_head`),
  CONSTRAINT `departments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `departments_department_head_foreign` FOREIGN KEY (`department_head`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'ADMIN/HR',1,NULL,NULL,'2025-05-26 17:32:09','2025-05-26 17:32:09'),(2,'CUTTING',1,NULL,NULL,'2025-05-26 17:32:33','2025-05-26 17:32:33'),(3,'DIE CUTTING',1,NULL,NULL,'2025-05-26 17:32:39','2025-05-26 17:32:39'),(4,'FLEXO CUTTING',1,NULL,NULL,'2025-05-26 17:32:46','2025-05-26 17:32:46'),(5,'FLEXO PACKING',1,NULL,NULL,'2025-05-26 17:32:52','2025-05-26 17:32:52'),(6,'NEEDLE LOOM',1,NULL,NULL,'2025-05-26 17:32:59','2025-05-26 17:32:59'),(7,'FLEXO PRINTING',1,NULL,NULL,'2025-05-26 17:33:08','2025-05-26 17:33:08'),(8,'MERCHANDISING',1,NULL,NULL,'2025-05-26 17:33:14','2025-05-26 17:33:14'),(9,'HOUSE KEEPING',1,NULL,NULL,'2025-05-26 17:33:20','2025-05-26 17:33:20'),(10,'THERMAL',1,NULL,NULL,'2025-05-26 17:33:28','2025-05-26 17:33:28'),(11,'PRINTING G.T.O',1,NULL,NULL,'2025-05-26 17:33:35','2025-05-26 17:33:35'),(12,'PRINTING OFFSET',1,NULL,NULL,'2025-05-26 17:33:41','2025-05-26 17:33:41'),(13,'SECURITY',1,NULL,NULL,'2025-05-26 17:33:46','2025-05-26 17:33:46'),(14,'QUALITY',1,NULL,NULL,'2025-05-26 17:33:54','2025-05-26 17:33:54'),(15,'PRODUCT DEVELOPMENT',1,NULL,NULL,'2025-05-26 17:34:00','2025-05-26 17:34:00'),(16,'PACKING 2',1,NULL,NULL,'2025-05-26 17:34:08','2025-05-26 17:35:18'),(17,'FLEXCO PACKING',1,NULL,NULL,'2025-05-26 17:47:47','2025-05-26 17:47:47');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deposit_categories`
--

DROP TABLE IF EXISTS `deposit_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deposit_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deposit_categories`
--

LOCK TABLES `deposit_categories` WRITE;
/*!40000 ALTER TABLE `deposit_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `deposit_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `designations`
--

DROP TABLE IF EXISTS `designations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `designations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `designation_name` varchar(191) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `designations_company_id_foreign` (`company_id`),
  KEY `designations_department_id_foreign` (`department_id`),
  CONSTRAINT `designations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `designations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `designations`
--

LOCK TABLES `designations` WRITE;
/*!40000 ALTER TABLE `designations` DISABLE KEYS */;
INSERT INTO `designations` VALUES (1,'BLOCK MAKER',1,17,NULL,'2025-05-26 17:36:04','2025-05-26 17:48:12'),(2,'CLEANER',1,9,NULL,'2025-05-26 17:36:17','2025-05-26 17:36:17'),(3,'HELPER',1,7,NULL,'2025-05-26 17:37:10','2025-05-26 17:37:10'),(4,'HELPER',1,16,NULL,'2025-05-26 17:37:21','2025-05-26 17:37:21'),(5,'SORTOR',1,16,NULL,'2025-05-26 17:37:32','2025-05-26 17:37:32'),(6,'PACKER',1,5,NULL,'2025-05-26 17:37:46','2025-05-26 17:37:46'),(7,'ASSISTANT',1,15,NULL,'2025-05-26 17:37:54','2025-05-26 17:37:54'),(8,'Q.C',1,14,NULL,'2025-05-26 17:38:02','2025-05-26 17:38:02'),(9,'SORTOR',1,5,NULL,'2025-05-26 17:38:14','2025-05-26 17:38:14'),(10,'WATCH MAN',1,13,NULL,'2025-05-26 17:38:24','2025-05-26 17:38:35'),(11,'HELPER',1,12,NULL,'2025-05-26 17:38:48','2025-05-26 17:38:48'),(12,'HELPER',1,11,NULL,'2025-05-26 17:38:58','2025-05-26 17:38:58'),(13,'MACHINE MAN',1,11,NULL,'2025-05-26 17:39:09','2025-05-26 17:39:09'),(14,'ASSISTANT',1,10,NULL,'2025-05-26 17:39:18','2025-05-26 17:39:18'),(15,'MERCHANDISER',1,8,NULL,'2025-05-26 17:39:27','2025-05-26 17:39:27'),(16,'ASSISTANT',1,8,NULL,'2025-05-26 17:39:36','2025-05-26 17:39:36'),(17,'MACHINE MAN',1,7,NULL,'2025-05-26 17:39:56','2025-05-26 17:39:56'),(18,'SORTOR',1,7,NULL,'2025-05-26 17:40:16','2025-05-26 17:40:16'),(19,'OPERATOR',1,6,NULL,'2025-05-26 17:41:59','2025-05-26 17:41:59'),(20,'MACHINE MAN',1,4,NULL,'2025-05-26 17:42:28','2025-05-26 17:42:28'),(21,'HELPER',1,4,NULL,'2025-05-26 17:42:37','2025-05-26 17:42:37'),(22,'SENIOR MACHINE MAN',1,4,NULL,'2025-05-26 17:42:52','2025-05-26 17:42:52'),(23,'HELPER',1,3,NULL,'2025-05-26 17:43:12','2025-05-26 17:43:12'),(24,'MACHINE MAN',1,3,NULL,'2025-05-26 17:43:22','2025-05-26 17:43:22'),(25,'ADMIN/COMPLIANCE EXECUTIVE',1,1,NULL,'2025-05-26 17:43:33','2025-05-26 17:43:33'),(26,'MACHINE MAN',1,2,NULL,'2025-05-26 17:43:41','2025-05-26 17:43:41');
/*!40000 ALTER TABLE `designations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `document_types`
--

DROP TABLE IF EXISTS `document_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `document_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `document_type` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `document_types`
--

LOCK TABLES `document_types` WRITE;
/*!40000 ALTER TABLE `document_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `document_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_bank_accounts`
--

DROP TABLE IF EXISTS `employee_bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_bank_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `account_title` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `bank_name` varchar(191) NOT NULL,
  `bank_code` varchar(191) NOT NULL,
  `bank_branch` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_bank_accounts_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_bank_accounts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_bank_accounts`
--

LOCK TABLES `employee_bank_accounts` WRITE;
/*!40000 ALTER TABLE `employee_bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_contacts`
--

DROP TABLE IF EXISTS `employee_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_contacts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `relation_type_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `is_primary` tinyint(4) DEFAULT 0,
  `is_dependent` tinyint(4) DEFAULT 0,
  `contact_name` varchar(191) NOT NULL,
  `work_phone` varchar(191) DEFAULT NULL,
  `work_phone_ext` varchar(191) DEFAULT NULL,
  `personal_phone` varchar(191) DEFAULT NULL,
  `home_phone` varchar(191) DEFAULT NULL,
  `work_email` varchar(191) DEFAULT NULL,
  `personal_email` varchar(191) DEFAULT NULL,
  `address1` varchar(191) DEFAULT NULL,
  `address2` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `zip` varchar(191) DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_contacts_employee_id_foreign` (`employee_id`),
  KEY `employee_contacts_relation_type_id_foreign` (`relation_type_id`),
  CONSTRAINT `employee_contacts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_contacts_relation_type_id_foreign` FOREIGN KEY (`relation_type_id`) REFERENCES `relation_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_contacts`
--

LOCK TABLES `employee_contacts` WRITE;
/*!40000 ALTER TABLE `employee_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_documents`
--

DROP TABLE IF EXISTS `employee_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned DEFAULT NULL,
  `document_title` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `document_file` varchar(191) DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_documents_employee_id_foreign` (`employee_id`),
  KEY `employee_documents_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `employee_documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_documents_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_documents`
--

LOCK TABLES `employee_documents` WRITE;
/*!40000 ALTER TABLE `employee_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_immigrations`
--

DROP TABLE IF EXISTS `employee_immigrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_immigrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `document_type_id` bigint(20) unsigned DEFAULT NULL,
  `document_number` varchar(191) NOT NULL,
  `document_file` varchar(191) DEFAULT NULL,
  `issue_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `eligible_review_date` date DEFAULT NULL,
  `country_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_immigrations_employee_id_foreign` (`employee_id`),
  KEY `employee_immigrations_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `employee_immigrations_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_immigrations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_immigrations`
--

LOCK TABLES `employee_immigrations` WRITE;
/*!40000 ALTER TABLE `employee_immigrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_immigrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_interview`
--

DROP TABLE IF EXISTS `employee_interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_interview` (
  `interview_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`interview_id`,`employee_id`),
  KEY `employee_interview_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_interview_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `employee_interview_interview_id_foreign` FOREIGN KEY (`interview_id`) REFERENCES `job_interviews` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_interview`
--

LOCK TABLES `employee_interview` WRITE;
/*!40000 ALTER TABLE `employee_interview` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_leave_type_details`
--

DROP TABLE IF EXISTS `employee_leave_type_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_leave_type_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type_detail` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_leave_type_details_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_leave_type_details_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_leave_type_details`
--

LOCK TABLES `employee_leave_type_details` WRITE;
/*!40000 ALTER TABLE `employee_leave_type_details` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_leave_type_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_meeting`
--

DROP TABLE IF EXISTS `employee_meeting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_meeting` (
  `employee_id` bigint(20) unsigned NOT NULL,
  `meeting_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employee_id`,`meeting_id`),
  KEY `employee_meeting_meeting_id_foreign` (`meeting_id`),
  CONSTRAINT `employee_meeting_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_meeting_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_meeting`
--

LOCK TABLES `employee_meeting` WRITE;
/*!40000 ALTER TABLE `employee_meeting` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_meeting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_project`
--

DROP TABLE IF EXISTS `employee_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_project` (
  `employee_id` bigint(20) unsigned NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employee_id`,`project_id`),
  KEY `employee_project_project_id_foreign` (`project_id`),
  CONSTRAINT `employee_project_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_project_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_project`
--

LOCK TABLES `employee_project` WRITE;
/*!40000 ALTER TABLE `employee_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_qualificaitons`
--

DROP TABLE IF EXISTS `employee_qualificaitons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_qualificaitons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `education_level_id` bigint(20) unsigned DEFAULT NULL,
  `institution_name` varchar(191) NOT NULL,
  `from_year` date DEFAULT NULL,
  `to_year` date DEFAULT NULL,
  `language_skill_id` bigint(20) unsigned DEFAULT NULL,
  `general_skill_id` bigint(20) unsigned DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_qualificaitons_employee_id_foreign` (`employee_id`),
  KEY `employee_qualificaitons_education_level_id_foreign` (`education_level_id`),
  KEY `employee_qualificaitons_language_skill_id_foreign` (`language_skill_id`),
  KEY `employee_qualificaitons_general_skill_id_foreign` (`general_skill_id`),
  CONSTRAINT `employee_qualificaitons_education_level_id_foreign` FOREIGN KEY (`education_level_id`) REFERENCES `qualification_education_levels` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_qualificaitons_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_qualificaitons_general_skill_id_foreign` FOREIGN KEY (`general_skill_id`) REFERENCES `qualification_skills` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employee_qualificaitons_language_skill_id_foreign` FOREIGN KEY (`language_skill_id`) REFERENCES `qualification_languages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_qualificaitons`
--

LOCK TABLES `employee_qualificaitons` WRITE;
/*!40000 ALTER TABLE `employee_qualificaitons` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_qualificaitons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_shift_changes`
--

DROP TABLE IF EXISTS `employee_shift_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_shift_changes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `old_shift_id` bigint(20) unsigned DEFAULT NULL,
  `new_shift_id` bigint(20) unsigned NOT NULL,
  `effective_date` date NOT NULL COMMENT 'Date when new shift becomes effective',
  `changed_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User who made the change',
  `reason` varchar(255) DEFAULT NULL COMMENT 'Reason for shift change',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_shift_changes_employee_id_index` (`employee_id`),
  KEY `employee_shift_changes_effective_date_index` (`effective_date`),
  KEY `employee_shift_changes_old_shift_id_foreign` (`old_shift_id`),
  KEY `employee_shift_changes_new_shift_id_foreign` (`new_shift_id`),
  CONSTRAINT `employee_shift_changes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_shift_changes_new_shift_id_foreign` FOREIGN KEY (`new_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_shift_changes_old_shift_id_foreign` FOREIGN KEY (`old_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_shift_changes`
--

LOCK TABLES `employee_shift_changes` WRITE;
/*!40000 ALTER TABLE `employee_shift_changes` DISABLE KEYS */;
INSERT INTO `employee_shift_changes` VALUES (1,65,1,2,'2025-05-15',NULL,'Promoted to early morning shift for better productivity','2025-09-27 11:34:14','2025-09-27 11:34:14'),(2,65,1,2,'2025-05-15',NULL,'Promoted to early morning shift for better productivity','2025-09-27 11:35:01','2025-09-27 11:35:01');
/*!40000 ALTER TABLE `employee_shift_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_support_ticket`
--

DROP TABLE IF EXISTS `employee_support_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_support_ticket` (
  `employee_id` bigint(20) unsigned NOT NULL,
  `support_ticket_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employee_id`,`support_ticket_id`),
  KEY `employee_support_ticket_support_ticket_id_foreign` (`support_ticket_id`),
  CONSTRAINT `employee_support_ticket_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_support_ticket_support_ticket_id_foreign` FOREIGN KEY (`support_ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_support_ticket`
--

LOCK TABLES `employee_support_ticket` WRITE;
/*!40000 ALTER TABLE `employee_support_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_support_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_task`
--

DROP TABLE IF EXISTS `employee_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_task` (
  `employee_id` bigint(20) unsigned NOT NULL,
  `task_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employee_id`,`task_id`),
  KEY `employee_task_task_id_foreign` (`task_id`),
  CONSTRAINT `employee_task_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_task_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_task`
--

LOCK TABLES `employee_task` WRITE;
/*!40000 ALTER TABLE `employee_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_training_list`
--

DROP TABLE IF EXISTS `employee_training_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_training_list` (
  `employee_id` bigint(20) unsigned NOT NULL,
  `training_list_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`employee_id`,`training_list_id`),
  KEY `employee_training_list_training_list_id_foreign` (`training_list_id`),
  CONSTRAINT `employee_training_list_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_training_list_training_list_id_foreign` FOREIGN KEY (`training_list_id`) REFERENCES `training_lists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_training_list`
--

LOCK TABLES `employee_training_list` WRITE;
/*!40000 ALTER TABLE `employee_training_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_training_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_work_experience`
--

DROP TABLE IF EXISTS `employee_work_experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employee_work_experience` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `company_name` varchar(191) NOT NULL,
  `from_year` date DEFAULT NULL,
  `to_year` date DEFAULT NULL,
  `post` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_work_experience_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_work_experience_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_work_experience`
--

LOCK TABLES `employee_work_experience` WRITE;
/*!40000 ALTER TABLE `employee_work_experience` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_work_experience` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `staff_id` varchar(191) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `date_of_birth` varchar(10) DEFAULT NULL COMMENT 'Date format: dd-mm-yyyy',
  `gender` varchar(191) DEFAULT NULL,
  `office_shift_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `designation_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `role_users_id` bigint(20) unsigned DEFAULT NULL,
  `status_id` bigint(20) unsigned DEFAULT NULL,
  `joining_date` varchar(10) DEFAULT NULL COMMENT 'Date format: dd-mm-yyyy',
  `exit_date` varchar(10) DEFAULT NULL COMMENT 'Date format: dd-mm-yyyy',
  `marital_status` varchar(191) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `nic` varchar(64) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `nic_expiry` varchar(24) DEFAULT NULL,
  `cv` varchar(64) DEFAULT NULL,
  `skype_id` varchar(64) DEFAULT NULL,
  `fb_id` varchar(64) DEFAULT NULL,
  `twitter_id` varchar(64) DEFAULT NULL,
  `linkedIn_id` varchar(64) DEFAULT NULL,
  `whatsapp_id` varchar(64) DEFAULT NULL,
  `basic_salary` double DEFAULT 0,
  `payslip_type` varchar(191) DEFAULT NULL,
  `attendance_type` varchar(191) DEFAULT NULL,
  `pension_type` varchar(50) DEFAULT NULL,
  `pension_amount` double(8,2) DEFAULT 0.00,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_labor_employee` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Labor/Contract employee flag',
  `overtime_allowed` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Overtime eligibility',
  `required_hours_per_day` int(11) NOT NULL DEFAULT 9 COMMENT 'Required working hours per day',
  PRIMARY KEY (`id`),
  KEY `employees_office_shift_id_foreign` (`office_shift_id`),
  KEY `employees_company_id_foreign` (`company_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_designation_id_foreign` (`designation_id`),
  KEY `employees_location_id_foreign` (`location_id`),
  KEY `employees_role_users_id_foreign` (`role_users_id`),
  KEY `employees_status_id_foreign` (`status_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_office_shift_id_foreign` FOREIGN KEY (`office_shift_id`) REFERENCES `office_shifts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_role_users_id_foreign` FOREIGN KEY (`role_users_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (61,'MUHAMMAD UZAIR SIDDIQUI','MUHAMMAD NAFEES SIDDIQUI','1',NULL,'0331-2700598','1986-03-17','Male',1,1,1,25,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # R-185, AREA GULSHAN MEHMAR K.D.A MALIR KARACHI','Karachi','45504-2093073-7','163','28-02-2027',NULL,NULL,NULL,NULL,NULL,NULL,50000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 17:57:57',0,1,9),(62,'M.SAEED KHAN','M.IDREES KHAN','2',NULL,NULL,'1975-08-23','Male',1,1,2,26,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSEE NO; D-15 JAT COLONY KHERO BAD KARACHI','Karachi','42201-0712192-7','163','29-08-2026',NULL,NULL,NULL,NULL,NULL,NULL,41440,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 17:58:39',0,1,9),(63,'M.ASIF','LIAQUAT ULLAH','3',NULL,'0313-8169680','1970-07-11','Male',1,1,3,24,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # H-141/1 SAOUDABAD MALIR COLONY KRACHI','Karachi','42201-0812614-9','163','25-12-2031',NULL,NULL,NULL,NULL,NULL,NULL,40700,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 17:58:58',0,1,9),(64,'M.TASLEEM','M.SALEEM QURESHI','4',NULL,'0317-2275112','1985-08-13','Male',1,1,3,23,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # S-49 KORANGI # 01 KARACHI','Karachi','42201-2570594-9','163','11-10-2029',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 17:59:19',0,1,9),(65,'HASEEB AHMED','SAEED AHMED','5',NULL,'0304-9771988','1998-03-01','Male',2,1,4,22,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 18 STREET KORANGI KARACHI.','Karachi','36302-8462065-9','163','23-03-2029',NULL,NULL,NULL,NULL,NULL,NULL,42750,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-09-27 09:58:53',0,1,9),(66,'M HUZAIFA','NOOR MUHAMMAD','6',NULL,'0311-1207843','2002-03-01','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # K - 276 AREA K KORANGI KARACHI','Karachi','42604-0370902-5','163','01-12-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 17:59:56',0,1,9),(67,'M.ZAYAN','M.IMRAN KHAN','7',NULL,'0316-2412400','2005-10-29','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # N-184 SEC 50-A AREA MADINA COLONY KORANGI KARACHI','Karachi','42201-5806756-3','163','09-12-2033',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:00:21',0,1,9),(68,'ADAN ISHAAQ','ISHAAQ MASEEH','8',NULL,'0341-2256280','2004-04-21','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE# 1223 ST#01 AREA ESSA NAGRI SULIMAN ROAD KARACHI.','Karachi','42603-0368706-9','163','13-06-2033',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:00:36',0,1,9),(69,'AHMED','M.ISMAIL','9',NULL,'0311-2188976','2005-12-09','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HAZRAT ALI ROAD HOUSE # D-436 AREA KORANGI # 51/2 KARACHI','Karachi','42603-0343531-5','163','01-01-2034',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:00:50',0,1,9),(70,'M.ALI','M.IQBAL','10',NULL,'0310-2065288','1983-02-16','Male',1,1,4,21,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 195 SEC 50/A KORANGI # 04 GHOS PAK ROAD KARACHI','Karachi','42401-8600678-7','163','03-02-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:01:09',0,1,9),(71,'SAQIB ALI','M YOUSUF','11',NULL,'0314-2092182','1996-06-14','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 753 AREA LABOUR SQUARE COLONY KORANGI # 21/2 KARACHI','Karachi','42201-6828437-7','163','17-04-2025',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:01:34',0,1,9),(72,'M.RASHID','M.HANIF','12',NULL,'0318-2068041','1993-10-15','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 74 SECTOR 34/2 KORANGI # 03 KARACHI','Karachi','45504-6927664-1','163','17-09-2025',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:01:49',0,1,9),(73,'M.AZEEM','M.ASLAM','13',NULL,'0319-4626158','1999-05-12','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO: B-251 KORANGI NO: 4, SECTOR 50-A KARACHI','Karachi','42604-0347839-7','163','22-04-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:01:57',0,1,9),(74,'SHERYAR NOOR','M.NOOR','14',NULL,'0313-2146283','1994-11-25','Male',1,1,4,20,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 5/2108 AREA SHAH FAISAL COLONY 05 KARACHI','Karachi','42201-9501564-7','163','20-08-2025',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:02:21',0,1,9),(75,'SYED JUNAID ALI','SYED RAHAT ALI','15',NULL,'0315-3752590','1988-07-09','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO 18/ST NO: 35 AREA 36-B LANDHI KARACHI','Karachi','42201-8023571-5','163','25-07-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:02:29',0,1,9),(76,'ASAD','AZHAR HUSSAIN','16',NULL,'0310-0307673','2002-08-03','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 107 SEC 35-A AREA GULSHAN MUHAMMADI KARACHI','Karachi','42201-6836774-9','163','16-10-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:02:37',0,1,9),(77,'ASIF NOMAN','ABDUL SAMAD','17',NULL,'0312-8135715','1984-12-26','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # F/123 AREA KORANGI # 04 KARACHI','Karachi','42201-3244974-3','163','04-07-2026',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:03:02',0,1,9),(78,'KASHAN HUSSAIN','NAVEED HUSSAIN','18',NULL,'0312-3931273','2000-05-27','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'H C-109  FRIEND MALL KORANGI # 05 KARACHI','Karachi','42201-4774857-1','163','09-08-2028',NULL,NULL,NULL,NULL,NULL,NULL,38500,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:03:14',0,1,9),(79,'S.ASGHAR HUSSAIN JAFFRI','S.ARSHAD HUSSAIN','19',NULL,'0335-0310659','2005-11-15','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # N-06 ST # 08C.G SEC 50-B KORANGI # 51/2 KARACHI','Karachi','42101-0890678-7','163','22-12-2033',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:03:26',0,1,9),(80,'M.AHSAN','M.SALEEM KHAN','20',NULL,'0311-3128071','1993-05-21','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 6/16 AREA-CLIAQUAT ABAD KARACHI','Karachi','42101-9876009-1','163','28-06-2033',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:03:37',0,1,9),(81,'M.WAQAR','FAHEEM UDDIN','21',NULL,'0310-2618101','2003-01-05','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 17 ST # 05 AREA 36-C LANDHI # 06 KARACHI','Karachi','42201-9018911-9','163','01-03-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:04',0,1,9),(82,'ABDULLAH','MUHAMMAD SALEEM','22',NULL,'0300-9208815','2001-08-11','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'AREA 4/D-6 HOUSE NO: 42/6 LANDHI NO:06 KARACHI','Karachi','42201-2583951-9','163','14-12-2029',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:11',0,1,9),(83,'MUHIB HUSSAIN','NAVEED HUSSAIN','23',NULL,'0312-3605883','2004-08-17','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # A-382 AREA P.N.T SOCIETY KORANGI KARACHI','Karachi','42201-4785402-1','163','11-09-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:17',0,1,9),(84,'MUHAMMAD JAWWAD AHMED','MUHAMMAD IRFAN','24',NULL,'0311-2353777','2002-12-07','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # M-375 AREA KORANGI # 31/2 KARACHI','Karachi','42604-0373117-9','163','28-12-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:25',0,1,9),(85,'FARHAN ','MUHAMMAD SAMI','25',NULL,'0311-7765671','1989-08-28','Male',1,1,6,19,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 236 AREA QASIM KHANI COLONY BALDIA TOWN KARACHI ','Karachi','42401-6391527-9','163','13-07-2030',NULL,NULL,NULL,NULL,NULL,NULL,42000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:45',0,1,9),(86,'MUHAMMAD SAQIB','MUHAMMAD RAFIQ','26',NULL,'0311-3243045','1984-12-11','Male',1,1,6,19,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE# 411 SECTOR 35-C AREA RAZA COLONY KORANGI NO: 4 KARACHI ','Karachi','42201-4864835-5','163','18-07-2031',NULL,NULL,NULL,NULL,NULL,NULL,40000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:04:56',0,1,9),(87,'DANISH','M.SAGHEER','27',NULL,'0343-1854910','2002-04-25','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # K-14 AREA LOBOUR SQUARE SEC 32-A KORANGI KRACHI','Karachi','42604-0365381-9','163','02-10-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:05:06',0,1,9),(88,'M.ARSHAD HUSSAIN','KHADIM HUSSAIN','28',NULL,'0318-3755795','1987-06-20','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # G-542 AREA GHOS-PAK-ROAD KORANGI # 51/2 KARACHI','Karachi','42201-1512061-9','163','06-04-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:05:29',0,1,9),(89,'USAMA SIDDIQUI','SAMI ULLAH','29',NULL,'0304-2109579','2001-06-14','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'AREA 4/D HOUSE NO: 4 ST NO : 43 LANDHI KARACHI','Karachi','42201-0487520-1','163','26-12-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:05:43',0,1,9),(90,'TOUSEEF AHMED','FAREED AHMED','30',NULL,'0318-2018162','1999-07-25','Male',1,1,7,17,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE K-280 K-AREA KORANGI KARACHI','Karachi','42604-0429946-5','163','31-10-2032',NULL,NULL,NULL,NULL,NULL,NULL,45910,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:05:53',0,1,9),(91,'M.AYAZ','ABDUL REHMAN','31',NULL,'0317-2301015','2001-06-25','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 251 ST # 11 BLOCK 34/3 KORANGI # 03 KARACHI','Karachi','42604-0411804-7','163','01-05-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:06:12',0,1,9),(92,'HASSAN HUSSAIN','AZHAR HUSSAIN','32',NULL,'0317-1837975','2000-08-05','Male',1,1,7,17,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # C-3/4 CHAMAN COLONY MALIR KARACHI','Karachi','42501-5771915-1','163','06-02-2029',NULL,NULL,NULL,NULL,NULL,NULL,40000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:06:22',0,1,9),(93,'FAHAD KHAN','ABDUL HAFEEZ KHAN','33',NULL,'0310-2639542','2000-07-26','Male',1,1,7,3,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO 41/12 SECTOR 5-F AREA NEW KARACHI','Karachi','42101-0979101-1','163','22-03-2029',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:06:30',0,1,9),(94,'M.USAMA','M.RASHEED','34',NULL,'0316-2297511','2003-07-17','Male',1,1,7,17,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # L-508 AREA KORANGI # 21/2 DOUBLE ROAD SEC 34/1 KARACHI','Karachi','42603-0360561-7','163','29-09-2031',NULL,NULL,NULL,NULL,NULL,NULL,39000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:06:38',0,1,9),(95,'M.ABDUL WASEEM','M.ABDUL AZEEM','35',NULL,'0315-2140152','1988-02-23','Male',1,1,7,17,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 16/24 AREA - A FLAT # 303 3RD FLOOR LIQUATABAD KARACHI','Karachi','42201-9489802-1','163','07-02-2029',NULL,NULL,NULL,NULL,NULL,NULL,56000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:07:08',0,1,9),(96,'M SAJID','M SADIQ','36',NULL,'0312-2303960','2001-05-25','Male',1,1,7,17,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # N-A-6 AREA NASHTER ABAD MALIR TOUSEE COLONY KARACHI','Karachi','42501-1951839-5','163','23-03-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:07:21',0,1,9),(97,'SHEMUS','ZAFAR MASIH','37',NULL,NULL,'1996-07-14','Male',1,1,9,2,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 164 ST # 9, 1/2-2 SECTOR 48 F MOHALA JOZAF TOWN KORANGI','Karachi','42201-7096150-3','163','20-04-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:07:28',0,1,9),(98,'MIRZA YASIR BAIG','MIRZA ANEES BAIG','38',NULL,'0345-6074340','1985-03-20','Male',1,1,8,15,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO; A-1408/419 LATIF PARK','Karachi','45504-8741443-1','163','13-03-2029',NULL,NULL,NULL,NULL,NULL,NULL,92160,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:07:39',0,1,9),(99,'M HAMMAD','M EJAZ','39',NULL,NULL,'2000-04-02','Male',1,1,8,16,NULL,2,NULL,'2025-03-01',NULL,NULL,'House No L-181 Korangi No :03 Sector 34/2 Karahi','Karachi','42201-7461324-5','163','08-10-2029',NULL,NULL,NULL,NULL,NULL,NULL,45910,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:01',0,1,9),(100,'M MUSTAFA','ALTAF HUSSAIN','40',NULL,'0336-1843366','1996-08-23','Male',1,1,8,16,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 40 ST # 11 SEC 35-B G AREA KORANGI # 04 KARACHI','Karachi','42201-7076176-5','163','06-02-2029',NULL,NULL,NULL,NULL,NULL,NULL,40000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:10',0,1,9),(101,'M.NAVEED','NOOR M.ABBASI','41',NULL,'0310-0273594','1992-01-12','Male',1,1,8,16,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # L-20 SEC 33-D AREA KORANGI # 21/2 KARACHI','Karachi','42201-2310342-1','163','27-06-2034',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:18',0,1,9),(102,'M. AHTISHAM','M. SALEEM','42',NULL,'0317-8564846','1999-01-24','Male',1,1,8,15,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO # 934 SECTOR 33-D KORANGI # 2 1/2 KARACHI','Karachi','42201-8917244-1','163','11-12-2027',NULL,NULL,NULL,NULL,NULL,NULL,45910,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:26',0,1,9),(103,'USAMA SIDDIQUI','MUHAMMAD ASLAM SIDDIQUI','43',NULL,'0316-2406469','1997-06-24','Male',1,1,8,15,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE N; 249 AREA 35/A KORANGI NO 04','Karachi','42000-0267360-1','163','17-04-2027',NULL,NULL,NULL,NULL,NULL,NULL,45910,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:49',0,1,9),(104,'HASNAIN ALI','REHMAT ALI','44',NULL,'0318-2149535','2003-06-04','Male',1,1,10,14,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 481 SEC 34-1 AREA KORANGI KARACHI','Karachi','42201-7807901-9','163','24-06-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:08:57',0,1,9),(105,'M.FAZEEL','M.SABIR','45',NULL,'0310-1188282','1995-03-13','Male',1,1,11,13,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # Y/400 AREA KORANGI # 11/2 SEC - Y KARACHI','Karachi','42201-9272283-5','163','24-12-2025',NULL,NULL,NULL,NULL,NULL,NULL,41440,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:05',0,1,9),(106,'M.KAMRAN','M.RAMZAN','46',NULL,'0315-8391635','1979-09-10','Male',1,1,11,12,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # 68 AREA F KORANGI # 04 KARACHI','Karachi','42201-2776973-3','163','02-07-2024',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:12',0,1,9),(107,'M.IMRAN','BABU KHAN','47',NULL,'0310-2388576','1984-04-12','Male',1,1,12,11,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # 43 51-D 100 - QUATOR KORANGI # 51/2','Karachi','42201-1153179-1','163','02-07-2027',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:29',0,1,9),(108,'M.BASHEER','AZMAT','48',NULL,'0335-0214680','1965-07-25','Male',1,1,13,10,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO 1454, GALI #03 AREA AWAMI COLONY KORANGI NO 5 KARACHI','Karachi','71201-0875345-5','163','20-01-2050',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:39',0,1,9),(109,'M.ASLAM','AZAR KHAN','49',NULL,'0311-3823918','1969-07-04','Male',1,1,13,10,NULL,2,NULL,'2025-03-01',NULL,NULL,'KORANGI1-1/2','Karachi','71201-2474065-5','163','23-01-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:45',0,1,9),(110,'S.GHULAM ABBAS','S.RAHBER HUSSAIN','50',NULL,'0348-2314565','2000-04-02','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # J-1 484 K-AREA KORANGI # 05 KARACHI','Karachi','42201-4418633-9','163','02-05-2028',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:09:58',0,1,9),(111,'ASIM KHAN','M.MAQSOOD KHAN','51',NULL,'0310-2384677','1993-12-18','Male',1,1,5,9,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # 07 ST # 35 AREA 4-D LANDHI # 06 KARACHI','Karachi','42201-3663560-3','163','18-02-2033',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:10:18',0,1,9),(112,'ATIF MEHMOOD','PARVAIZ MAHMOOD','52',NULL,'0314-2798256','1996-04-19','Male',1,1,14,8,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE NO: N-H 182 SEC 35 ARE A LABOUR SQUARE','Karachi','42201-0314666-7','163','30-04-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:10:26',0,1,9),(113,'ASAD HASNAIN','M.ARSHAD','53',NULL,'0370-1001653','1996-11-09','Male',1,1,15,7,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # R-166, SEC 35-E KORANGI KARACHI','Karachi','38201-5788416-3','163','20-03-2027',NULL,NULL,NULL,NULL,NULL,NULL,55000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:10:35',0,1,9),(114,'M.ARHAM','M.ASIF','54',NULL,'0312-8972200','2001-05-28','Male',1,1,5,6,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # 147 AREA-D KORANGI # 51/2 KARACHI','Karachi','42501-0211637-1','163','23-03-2030',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:10:53',0,1,9),(115,'MUHAMMAD AHMED RAZA','ZAKIR HUSSAIN','55',NULL,'0312-2798674','2001-06-18','Male',1,1,16,5,NULL,2,NULL,'2025-03-01',NULL,NULL,'H # 97 GALI # 81 AREA 5-C BILALABAD LANDHI # 06 KARACHI','Karachi','4260-30377075-9','163','20-04-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:11:00',0,1,9),(116,'HASHAM RAZA','NAFEES AHMED','56',NULL,'0311-1069305','2002-12-01','Male',1,1,16,4,NULL,2,NULL,'2025-03-01',NULL,NULL,'HOUSE # G-366 AREA-G KORANGI KARACHI','Karachi','42201-1667028-3','163','04-10-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:11:09',0,1,9),(117,'WAJAHAT ALI','LIAQUAT ALI','57',NULL,'0315-8489339','2003-02-04','Male',1,1,7,3,NULL,2,NULL,'2025-03-01',NULL,NULL,'KIA AWAMI COLONY KARACHI','Karachi','44204-7198914-5','163','31-10-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:11:17',0,1,9),(118,'ABDUL RAHEEM ','A','58',NULL,NULL,NULL,'Male',1,1,14,8,NULL,2,NULL,'2025-03-01',NULL,NULL,NULL,'Karachi',NULL,'163',NULL,NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:11:34',0,1,9),(119,'NASEER ','GULAM RASOOL','59',NULL,'0316-8531748','1989-04-10','Male',1,1,9,2,NULL,2,NULL,'2025-03-01',NULL,NULL,'MUHALLA MALIR COLONY SAHAB DAD GOTH , MALIR ZILA KARACHI','Karachi','42501-9206839-7','163','14-01-2032',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:14:04',0,1,9),(120,'MOHAMMAD MOHEUDDIN QADRI','Muhammad Shakeel','60',NULL,'0333-3304842','2005-11-17','Male',1,1,17,1,NULL,2,NULL,'2025-03-01',NULL,NULL,'House # 5/530 Shah Faisal Colony # 5 Karachi','Karachi','42201-4541212-9','163','19-03-2031',NULL,NULL,NULL,NULL,NULL,NULL,37000,'Monthly','general',NULL,0.00,1,'2025-05-26 17:53:56','2025-05-26 18:14:21',0,1,9);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `event_title` varchar(191) NOT NULL,
  `event_note` mediumtext NOT NULL,
  `event_date` date NOT NULL,
  `event_time` varchar(191) NOT NULL,
  `status` varchar(30) NOT NULL,
  `is_notify` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_company_id_foreign` (`company_id`),
  KEY `events_department_id_foreign` (`department_id`),
  CONSTRAINT `events_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_types`
--

DROP TABLE IF EXISTS `expense_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `expense_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expense_types_company_id_foreign` (`company_id`),
  CONSTRAINT `expense_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_types`
--

LOCK TABLES `expense_types` WRITE;
/*!40000 ALTER TABLE `expense_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `expense_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_manager_settings`
--

DROP TABLE IF EXISTS `file_manager_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_manager_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `allowed_extensions` mediumtext NOT NULL,
  `max_file_size` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_manager_settings`
--

LOCK TABLES `file_manager_settings` WRITE;
/*!40000 ALTER TABLE `file_manager_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_manager_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file_managers`
--

DROP TABLE IF EXISTS `file_managers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file_managers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `added_by` bigint(20) unsigned DEFAULT NULL,
  `file_name` varchar(191) NOT NULL,
  `file_size` varchar(191) DEFAULT NULL,
  `file_extension` varchar(191) DEFAULT NULL,
  `external_link` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `file_managers_department_id_foreign` (`department_id`),
  KEY `file_managers_added_by_foreign` (`added_by`),
  CONSTRAINT `file_managers_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `file_managers_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file_managers`
--

LOCK TABLES `file_managers` WRITE;
/*!40000 ALTER TABLE `file_managers` DISABLE KEYS */;
/*!40000 ALTER TABLE `file_managers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_bank_cashes`
--

DROP TABLE IF EXISTS `finance_bank_cashes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_bank_cashes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(50) NOT NULL,
  `account_balance` varchar(191) NOT NULL,
  `initial_balance` varchar(191) NOT NULL,
  `account_number` varchar(191) NOT NULL,
  `branch_code` varchar(191) NOT NULL,
  `bank_branch` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_bank_cashes`
--

LOCK TABLES `finance_bank_cashes` WRITE;
/*!40000 ALTER TABLE `finance_bank_cashes` DISABLE KEYS */;
INSERT INTO `finance_bank_cashes` VALUES (1,'Meezan Bank','9918558','10000000','1234567','1234','Korangi','2025-05-26 18:57:48','2025-09-27 11:46:48');
/*!40000 ALTER TABLE `finance_bank_cashes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_deposits`
--

DROP TABLE IF EXISTS `finance_deposits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_deposits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` varchar(30) NOT NULL,
  `deposit_category_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `description` mediumtext DEFAULT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `payer_id` bigint(20) unsigned DEFAULT NULL,
  `deposit_reference` varchar(191) NOT NULL,
  `deposit_file` varchar(191) DEFAULT NULL,
  `deposit_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_deposits_company_id_foreign` (`company_id`),
  KEY `finance_deposits_account_id_foreign` (`account_id`),
  KEY `finance_deposits_payment_method_id_foreign` (`payment_method_id`),
  KEY `finance_deposits_payer_id_foreign` (`payer_id`),
  KEY `finance_deposits_deposit_category_id_foreign` (`deposit_category_id`),
  CONSTRAINT `finance_deposits_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `finance_bank_cashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_deposits_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_deposits_deposit_category_id_foreign` FOREIGN KEY (`deposit_category_id`) REFERENCES `deposit_categories` (`id`),
  CONSTRAINT `finance_deposits_payer_id_foreign` FOREIGN KEY (`payer_id`) REFERENCES `finance_payers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_deposits_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_deposits`
--

LOCK TABLES `finance_deposits` WRITE;
/*!40000 ALTER TABLE `finance_deposits` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_deposits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_expenses`
--

DROP TABLE IF EXISTS `finance_expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` varchar(30) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `payee_id` bigint(20) unsigned DEFAULT NULL,
  `expense_reference` varchar(191) NOT NULL,
  `expense_file` varchar(191) DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_expenses_company_id_foreign` (`company_id`),
  KEY `finance_expenses_account_id_foreign` (`account_id`),
  KEY `finance_expenses_payment_method_id_foreign` (`payment_method_id`),
  KEY `finance_expenses_payee_id_foreign` (`payee_id`),
  KEY `finance_expenses_category_id_foreign` (`category_id`),
  CONSTRAINT `finance_expenses_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `finance_bank_cashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_expenses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_expenses_payee_id_foreign` FOREIGN KEY (`payee_id`) REFERENCES `finance_payees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_expenses_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_expenses`
--

LOCK TABLES `finance_expenses` WRITE;
/*!40000 ALTER TABLE `finance_expenses` DISABLE KEYS */;
INSERT INTO `finance_expenses` VALUES (1,NULL,1,'46942.31',NULL,NULL,NULL,NULL,'Payroll',NULL,'0000-00-00','2025-09-27 10:16:20','2025-09-27 10:16:20'),(2,NULL,1,'34500.00',NULL,NULL,NULL,NULL,'Payroll',NULL,'0000-00-00','2025-09-27 11:46:48','2025-09-27 11:46:48');
/*!40000 ALTER TABLE `finance_expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_payees`
--

DROP TABLE IF EXISTS `finance_payees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_payees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payee_name` varchar(50) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_payees`
--

LOCK TABLES `finance_payees` WRITE;
/*!40000 ALTER TABLE `finance_payees` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_payees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_payers`
--

DROP TABLE IF EXISTS `finance_payers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_payers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payer_name` varchar(50) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_payers`
--

LOCK TABLES `finance_payers` WRITE;
/*!40000 ALTER TABLE `finance_payers` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_payers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_transactions`
--

DROP TABLE IF EXISTS `finance_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` varchar(30) NOT NULL,
  `category` varchar(30) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `payee_id` bigint(20) unsigned DEFAULT NULL,
  `payer_id` bigint(20) unsigned DEFAULT NULL,
  `expense_reference` varchar(191) DEFAULT NULL,
  `expense_file` varchar(191) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `deposit_reference` varchar(191) DEFAULT NULL,
  `deposit_file` varchar(191) DEFAULT NULL,
  `deposit_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_transactions_company_id_foreign` (`company_id`),
  KEY `finance_transactions_account_id_foreign` (`account_id`),
  KEY `finance_transactions_payment_method_id_foreign` (`payment_method_id`),
  KEY `finance_transactions_payee_id_foreign` (`payee_id`),
  KEY `finance_transactions_payer_id_foreign` (`payer_id`),
  KEY `finance_transactions_category_id_foreign` (`category_id`),
  CONSTRAINT `finance_transactions_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `finance_bank_cashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transactions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `expense_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transactions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transactions_payee_id_foreign` FOREIGN KEY (`payee_id`) REFERENCES `finance_payees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transactions_payer_id_foreign` FOREIGN KEY (`payer_id`) REFERENCES `finance_payers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transactions_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_transactions`
--

LOCK TABLES `finance_transactions` WRITE;
/*!40000 ALTER TABLE `finance_transactions` DISABLE KEYS */;
INSERT INTO `finance_transactions` VALUES (1,NULL,1,'46942.31','',NULL,NULL,NULL,NULL,NULL,'Payroll',NULL,'0000-00-00',NULL,NULL,NULL,'2025-09-27 10:16:20','2025-09-27 10:16:20'),(2,NULL,1,'34500.00','',NULL,NULL,NULL,NULL,NULL,'Payroll',NULL,'0000-00-00',NULL,NULL,NULL,'2025-09-27 11:46:48','2025-09-27 11:46:48');
/*!40000 ALTER TABLE `finance_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `finance_transfers`
--

DROP TABLE IF EXISTS `finance_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finance_transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `from_account_id` bigint(20) unsigned DEFAULT NULL,
  `to_account_id` bigint(20) unsigned DEFAULT NULL,
  `amount` varchar(30) NOT NULL,
  `reference` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `payment_method_id` bigint(20) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `finance_transfers_company_id_foreign` (`company_id`),
  KEY `finance_transfers_from_account_id_foreign` (`from_account_id`),
  KEY `finance_transfers_to_account_id_foreign` (`to_account_id`),
  KEY `finance_transfers_payment_method_id_foreign` (`payment_method_id`),
  CONSTRAINT `finance_transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transfers_from_account_id_foreign` FOREIGN KEY (`from_account_id`) REFERENCES `finance_bank_cashes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transfers_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `finance_transfers_to_account_id_foreign` FOREIGN KEY (`to_account_id`) REFERENCES `finance_bank_cashes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `finance_transfers`
--

LOCK TABLES `finance_transfers` WRITE;
/*!40000 ALTER TABLE `finance_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `finance_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `general_settings`
--

DROP TABLE IF EXISTS `general_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `site_title` varchar(191) NOT NULL,
  `site_logo` varchar(191) DEFAULT NULL,
  `time_zone` varchar(191) DEFAULT NULL,
  `currency` varchar(191) DEFAULT NULL,
  `currency_format` varchar(191) DEFAULT NULL,
  `default_payment_bank` varchar(191) DEFAULT NULL,
  `date_format` varchar(191) DEFAULT NULL,
  `theme` varchar(191) DEFAULT NULL,
  `footer` varchar(191) DEFAULT NULL,
  `footer_link` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_settings`
--

LOCK TABLES `general_settings` WRITE;
/*!40000 ALTER TABLE `general_settings` DISABLE KEYS */;
INSERT INTO `general_settings` VALUES (1,'TTP HRM','logo.jpg','Asia/Karachi','Rs.','prefix','1','d-m-Y','default.css','Cube Techwiz','https://www.cubetechwiz.com','2020-07-25 19:00:00','2025-05-26 18:59:12');
/*!40000 ALTER TABLE `general_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goal_trackings`
--

DROP TABLE IF EXISTS `goal_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goal_trackings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `goal_type_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(191) NOT NULL,
  `target_achievement` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` varchar(191) NOT NULL,
  `end_date` varchar(191) NOT NULL,
  `progress` int(11) NOT NULL,
  `status` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_trackings_company_id_foreign` (`company_id`),
  KEY `goal_trackings_goal_type_id_foreign` (`goal_type_id`),
  CONSTRAINT `goal_trackings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goal_trackings_goal_type_id_foreign` FOREIGN KEY (`goal_type_id`) REFERENCES `goal_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goal_trackings`
--

LOCK TABLES `goal_trackings` WRITE;
/*!40000 ALTER TABLE `goal_trackings` DISABLE KEYS */;
/*!40000 ALTER TABLE `goal_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goal_types`
--

DROP TABLE IF EXISTS `goal_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goal_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `goal_type` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goal_types`
--

LOCK TABLES `goal_types` WRITE;
/*!40000 ALTER TABLE `goal_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `goal_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `holidays` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_name` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_publish` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `holidays_company_id_foreign` (`company_id`),
  KEY `holidays_department_id_foreign` (`department_id`),
  CONSTRAINT `holidays_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `holidays_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `holidays`
--

LOCK TABLES `holidays` WRITE;
/*!40000 ALTER TABLE `holidays` DISABLE KEYS */;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `indicators`
--

DROP TABLE IF EXISTS `indicators`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `indicators` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `designation_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `customer_experience` varchar(191) NOT NULL,
  `marketing` varchar(191) NOT NULL,
  `administrator` varchar(191) NOT NULL,
  `professionalism` varchar(191) NOT NULL,
  `integrity` varchar(191) NOT NULL,
  `attendance` varchar(191) NOT NULL,
  `added_by` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `indicators_company_id_foreign` (`company_id`),
  KEY `indicators_department_id_foreign` (`department_id`),
  KEY `indicators_designation_id_foreign` (`designation_id`),
  CONSTRAINT `indicators_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `indicators_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `indicators_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `indicators`
--

LOCK TABLES `indicators` WRITE;
/*!40000 ALTER TABLE `indicators` DISABLE KEYS */;
/*!40000 ALTER TABLE `indicators` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `item_name` varchar(191) NOT NULL,
  `item_tax_type` int(11) NOT NULL DEFAULT 0,
  `item_tax_rate` decimal(5,2) NOT NULL,
  `item_qty` int(11) NOT NULL DEFAULT 0,
  `item_unit_price` decimal(10,2) NOT NULL,
  `item_sub_total` double NOT NULL,
  `sub_total` double NOT NULL,
  `discount_type` tinyint(4) DEFAULT NULL,
  `discount_figure` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_discount` double NOT NULL,
  `grand_total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_items_project_id_foreign` (`project_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(191) NOT NULL,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `invoice_due_date` date NOT NULL,
  `sub_total` double NOT NULL,
  `discount_type` tinyint(4) DEFAULT NULL,
  `discount_figure` double NOT NULL,
  `total_tax` double NOT NULL,
  `total_discount` double NOT NULL,
  `grand_total` double NOT NULL,
  `invoice_note` mediumtext DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoices_client_id_foreign` (`client_id`),
  KEY `invoices_project_id_foreign` (`project_id`),
  CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_settings`
--

DROP TABLE IF EXISTS `ip_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `ip_address` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_settings`
--

LOCK TABLES `ip_settings` WRITE;
/*!40000 ALTER TABLE `ip_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_candidates`
--

DROP TABLE IF EXISTS `job_candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_candidates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` bigint(20) unsigned NOT NULL,
  `full_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(191) NOT NULL,
  `address` text DEFAULT NULL,
  `cover_letter` longtext NOT NULL,
  `fb_id` varchar(191) DEFAULT NULL,
  `linkedin_id` varchar(191) DEFAULT NULL,
  `cv` varchar(191) NOT NULL,
  `status` varchar(191) NOT NULL,
  `remarks` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_candidates_job_id_foreign` (`job_id`),
  CONSTRAINT `job_candidates_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_candidates`
--

LOCK TABLES `job_candidates` WRITE;
/*!40000 ALTER TABLE `job_candidates` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_candidates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_categories`
--

DROP TABLE IF EXISTS `job_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_category` mediumtext NOT NULL,
  `url` char(36) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_categories`
--

LOCK TABLES `job_categories` WRITE;
/*!40000 ALTER TABLE `job_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_experiences`
--

DROP TABLE IF EXISTS `job_experiences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_experiences` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_experiences`
--

LOCK TABLES `job_experiences` WRITE;
/*!40000 ALTER TABLE `job_experiences` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_experiences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_interviews`
--

DROP TABLE IF EXISTS `job_interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_interviews` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` bigint(20) unsigned NOT NULL,
  `added_by` bigint(20) unsigned DEFAULT NULL,
  `interview_place` varchar(191) NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `description` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_interviews_job_id_foreign` (`job_id`),
  KEY `job_interviews_added_by_foreign` (`added_by`),
  CONSTRAINT `job_interviews_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `job_interviews_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `job_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_interviews`
--

LOCK TABLES `job_interviews` WRITE;
/*!40000 ALTER TABLE `job_interviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_interviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_posts`
--

DROP TABLE IF EXISTS `job_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `job_category_id` bigint(20) unsigned NOT NULL,
  `job_title` varchar(191) NOT NULL,
  `job_type` varchar(191) NOT NULL,
  `no_of_vacancy` int(11) NOT NULL,
  `job_url` varchar(191) NOT NULL,
  `gender` varchar(30) NOT NULL,
  `job_experience_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `short_description` mediumtext NOT NULL,
  `long_description` longtext NOT NULL,
  `closing_date` date NOT NULL,
  `status` tinyint(4) NOT NULL,
  `is_featured` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `job_posts_job_category_id_foreign` (`job_category_id`),
  KEY `job_posts_company_id_foreign` (`company_id`),
  KEY `job_posts_job_experience_id_foreign` (`job_experience_id`),
  CONSTRAINT `job_posts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_posts_job_category_id_foreign` FOREIGN KEY (`job_category_id`) REFERENCES `job_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `job_posts_job_experience_id_foreign` FOREIGN KEY (`job_experience_id`) REFERENCES `job_experiences` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_posts`
--

LOCK TABLES `job_posts` WRITE;
/*!40000 ALTER TABLE `job_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) DEFAULT NULL,
  `language` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `languages`
--

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leave_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type` varchar(50) NOT NULL,
  `allocated_day` int(11) DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leave_types_company_id_foreign` (`company_id`),
  CONSTRAINT `leave_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leave_types`
--

LOCK TABLES `leave_types` WRITE;
/*!40000 ALTER TABLE `leave_types` DISABLE KEYS */;
INSERT INTO `leave_types` VALUES (1,'Others',0,NULL,'2025-05-26 17:12:41','2025-05-26 17:12:41');
/*!40000 ALTER TABLE `leave_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `leaves` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `leave_type_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `department_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `leave_reason` mediumtext DEFAULT NULL,
  `remarks` varchar(191) DEFAULT NULL,
  `status` varchar(40) NOT NULL,
  `is_notify` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_company_id_foreign` (`company_id`),
  KEY `leaves_employee_id_foreign` (`employee_id`),
  KEY `leaves_leave_type_id_foreign` (`leave_type_id`),
  KEY `leaves_department_id_foreign` (`department_id`),
  CONSTRAINT `leaves_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loan_types`
--

DROP TABLE IF EXISTS `loan_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `loan_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loan_types`
--

LOCK TABLES `loan_types` WRITE;
/*!40000 ALTER TABLE `loan_types` DISABLE KEYS */;
INSERT INTO `loan_types` VALUES (1,'Asset','2025-09-27 09:59:37','2025-09-27 09:59:37',NULL),(2,'Cash','2025-09-27 09:59:40','2025-09-27 09:59:40',NULL);
/*!40000 ALTER TABLE `loan_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(191) NOT NULL,
  `location_head` bigint(20) unsigned DEFAULT NULL,
  `address1` varchar(191) DEFAULT NULL,
  `address2` varchar(191) DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `state` varchar(191) DEFAULT NULL,
  `country` int(10) unsigned DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `locations_location_head_foreign` (`location_head`),
  KEY `locations_country_foreign` (`country`),
  CONSTRAINT `locations_country_foreign` FOREIGN KEY (`country`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `locations_location_head_foreign` FOREIGN KEY (`location_head`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locations`
--

LOCK TABLES `locations` WRITE;
/*!40000 ALTER TABLE `locations` DISABLE KEYS */;
INSERT INTO `locations` VALUES (1,'Korangi Industrial Area',NULL,'Plot#25, Sector 26','KIA','Karachi','Sindh',163,75160,'2025-05-26 17:28:49','2025-05-26 17:29:19');
/*!40000 ALTER TABLE `locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `meetings`
--

DROP TABLE IF EXISTS `meetings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `meetings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned NOT NULL,
  `meeting_title` varchar(191) NOT NULL,
  `meeting_note` mediumtext NOT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` varchar(191) NOT NULL,
  `status` varchar(30) NOT NULL,
  `is_notify` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `meetings_company_id_foreign` (`company_id`),
  CONSTRAINT `meetings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `meetings`
--

LOCK TABLES `meetings` WRITE;
/*!40000 ALTER TABLE `meetings` DISABLE KEYS */;
/*!40000 ALTER TABLE `meetings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (106,'2023_05_06_053210_create_countries_table',1),(107,'2023_05_06_053211_create_locations_table',1),(108,'2023_05_06_053212_create_companies_table',1),(109,'2023_05_06_053213_create_departments_table',1),(110,'2023_05_06_053214_create_designations_table',1),(111,'2023_05_06_053215_create_roles_table',1),(112,'2023_05_06_053217_create_users_table',1),(113,'2023_05_06_053218_create_office_shifts_table',1),(114,'2023_05_06_053219_create_statuses_table',1),(115,'2023_05_06_053220_create_employees_table',1),(116,'2023_05_06_053221_create_announcements_table',1),(117,'2023_05_06_053222_create_appraisals_table',1),(118,'2023_05_06_053223_create_asset_categories_table',1),(119,'2023_05_06_053224_create_assets_table',1),(120,'2023_05_06_053225_create_attendances_table',1),(121,'2023_05_06_053226_create_award_types_table',1),(122,'2023_05_06_053227_create_awards_table',1),(123,'2023_05_06_053228_create_c_m_s_table',1),(124,'2023_05_06_053229_create_calendarables_table',1),(125,'2023_05_06_053231_create_clients_table',1),(126,'2023_05_06_053232_create_complaints_table',1),(127,'2023_05_06_053233_create_document_types_table',1),(128,'2023_05_06_053234_create_employee_bank_accounts_table',1),(129,'2023_05_06_053235_create_employee_contacts_table',1),(130,'2023_05_06_053236_create_employee_documents_table',1),(131,'2023_05_06_053237_create_employee_immigrations_table',1),(132,'2023_05_06_053238_create_employee_leave_type_details_table',1),(133,'2023_05_06_053239_create_job_categories_table',1),(134,'2023_05_06_053240_create_job_posts_table',1),(135,'2023_05_06_053241_create_job_interviews_table',1),(136,'2023_05_06_053242_create_employee_interview_table',1),(137,'2023_05_06_053243_create_meetings_table',1),(138,'2023_05_06_053244_create_employee_meeting_table',1),(139,'2023_05_06_053245_create_projects_table',1),(140,'2023_05_06_053246_create_employee_project_table',1),(141,'2023_05_06_053247_create_qualification_languages_table',1),(142,'2023_05_06_053248_create_qualification_skills_table',1),(143,'2023_05_06_053249_create_qualification_education_levels_table',1),(144,'2023_05_06_053250_create_employee_qualificaitons_table',1),(145,'2023_05_06_053251_create_support_tickets_table',1),(146,'2023_05_06_053252_create_employee_support_ticket_table',1),(147,'2023_05_06_053253_create_tasks_table',1),(148,'2023_05_06_053254_create_employee_task_table',1),(149,'2023_05_06_053255_create_trainers_table',1),(150,'2023_05_06_053256_create_training_types_table',1),(151,'2023_05_06_053257_create_training_lists_table',1),(152,'2023_05_06_053258_create_employee_training_list_table',1),(153,'2023_05_06_053259_create_employee_work_experience_table',1),(154,'2023_05_06_053260_create_events_table',1),(155,'2023_05_06_053261_create_expense_types_table',1),(156,'2023_05_06_053262_create_failed_jobs_table',1),(157,'2023_05_06_053263_create_file_manager_settings_table',1),(158,'2023_05_06_053265_create_file_managers_table',1),(159,'2023_05_06_053265_create_finance_bank_cashes_table',1),(160,'2023_05_06_053266_create_finance_payers_table',1),(161,'2023_05_06_053267_create_payment_methods_table',1),(162,'2023_05_06_053268_create_finance_deposits_table',1),(163,'2023_05_06_053269_create_finance_payees_table',1),(164,'2023_05_06_053270_create_finance_expenses_table',1),(165,'2023_05_06_053271_create_finance_transactions_table',1),(166,'2023_05_06_053272_create_finance_transfers_table',1),(167,'2023_05_06_053273_create_general_settings_table',1),(168,'2023_05_06_053274_create_goal_types_table',1),(169,'2023_05_06_053275_create_goal_trackings_table',1),(170,'2023_05_06_053276_create_holidays_table',1),(171,'2023_05_06_053277_create_indicators_table',1),(172,'2023_05_06_053278_create_invoices_table',1),(173,'2023_05_06_053279_create_invoice_items_table',1),(174,'2023_05_06_053280_create_ip_settings_table',1),(175,'2023_05_06_053281_create_job_candidates_table',1),(176,'2023_05_06_053282_create_leave_types_table',1),(177,'2023_05_06_053283_create_leaves_table',1),(178,'2023_05_06_053284_create_permissions_table',1),(179,'2023_05_06_053285_create_model_has_permissions_table',1),(180,'2023_05_06_053287_create_model_has_roles_table',1),(181,'2023_05_06_053288_create_notifications_table',1),(182,'2023_05_06_053289_create_official_documents_table',1),(183,'2023_05_06_053290_create_password_resets_table',1),(184,'2023_05_06_053291_create_payslips_table',1),(185,'2023_05_06_053292_create_policies_table',1),(186,'2023_05_06_053293_create_project_bugs_table',1),(187,'2023_05_06_053294_create_project_discussions_table',1),(188,'2023_05_06_053295_create_project_files_table',1),(189,'2023_05_06_053296_create_promotions_table',1),(190,'2023_05_06_053297_create_resignations_table',1),(191,'2023_05_06_053298_create_role_has_permissions_table',1),(192,'2023_05_06_053299_create_salary_allowances_table',1),(193,'2023_05_06_053300_create_salary_basics_table',1),(194,'2023_05_06_053301_create_salary_commissions_table',1),(195,'2023_05_06_053302_create_salary_deductions_table',1),(196,'2023_05_06_053303_create_salary_loans_table',1),(197,'2023_05_06_053304_create_salary_other_payments_table',1),(198,'2023_05_06_053305_create_salary_overtimes_table',1),(199,'2023_05_06_053306_create_task_discussions_table',1),(200,'2023_05_06_053307_create_task_files_table',1),(201,'2023_05_06_053308_create_tax_types_table',1),(202,'2023_05_06_053309_create_termination_types_table',1),(203,'2023_05_06_053310_create_terminations_table',1),(204,'2023_05_06_053311_create_ticket_comments_table',1),(205,'2023_05_06_053312_create_transfers_table',1),(206,'2023_05_06_053313_create_travel_types_table',1),(207,'2023_05_06_053314_create_travels_table',1),(208,'2023_05_06_053315_create_warnings_type_table',1),(209,'2023_05_06_053316_create_warnings_table',1),(210,'2023_05_06_053317_create_candidate_interview_table',1),(211,'2023_05_06_151650_delete_column_from_document_types_table',2),(212,'2023_05_06_153653_update_foreign_key_to_employees_table',2),(213,'2023_05_07_163304_update_foreign_key_to_projects_table',2),(214,'2023_05_08_004033_update_foreign_key_to_support_tickets_table',2),(215,'2023_05_08_094247_update_foreign_key_to_tasks_table',2),(216,'2023_05_08_101326_add_foreign_key_to_training_types_table',2),(217,'2023_05_08_152355_update_foreign_key_to_file_managers_table',2),(218,'2023_05_08_165246_update_foreign_key_to_payment_methods_table',2),(219,'2023_05_08_165419_update_foreign_key_to_finance_deposits_table',2),(220,'2023_05_09_112302_update_foreign_key_to_finance_expenses_table',2),(221,'2023_05_09_115226_update_foreign_key_to_finance_transactions_table',2),(222,'2023_05_09_122727_update_foreign_key_to_finance_transfers_table',2),(223,'2023_05_09_130054_add_foreign_key_to_goal_trackings_table',2),(224,'2023_05_09_134538_update_foreign_key_to_holidays_table',2),(225,'2023_05_09_134626_add_foreign_key_to_indicators_table',2),(226,'2023_05_09_134702_update_foreign_key_to_invoices_table',2),(227,'2023_05_09_134831_update_foreign_key_to_invoice_items_table',2),(228,'2023_05_09_162641_update_foreign_key_to_leaves_table',2),(229,'2023_05_09_181324_update_foreign_key_to_official_documents_table',2),(230,'2023_05_09_190434_update_foreign_key_to_policies_table',2),(231,'2023_05_09_193007_update_foreign_key_to_terminations_table',2),(232,'2023_05_09_195431_update_foreign_key_to_travel_types_table',2),(233,'2023_05_09_195925_update_foreign_key_to_travels_table',2),(234,'2023_05_09_200229_update_foreign_key_to_warnings_table',2),(235,'2023_08_02_113953_delete_company_id_column_to_training_types_table',2),(236,'2024_04_15_143831_add_column_to_users_table',3),(238,'2024_06_09_114933_create_company_types_table',4),(239,'2018_08_08_100000_create_telescope_entries_table',5),(240,'2024_05_13_123951_update_column_type_to_invoice_items_table',6),(243,'2024_07_22_184915_add_company_type_id_column_to_companies_table',7),(244,'2024_07_22_201241_create_relation_types_table',8),(245,'2024_07_22_230352_add_relation_type_id_column_to_employee_contacts_table',9),(246,'2024_07_23_091947_create_loan_types_table',10),(248,'2024_07_22_230356_add_loan_type_id_column_to_salary_loans_table',11),(249,'2024_07_23_113537_create_deduction_types_table',12),(250,'2024_07_22_230367_add_deduction_type_id_column_to_salary_loans_table',13),(251,'2024_07_23_201134_create_deposit_categories_table',14),(252,'2024_07_22_230368_add_deposit_category_id_column_to_finance_deposits_table',15),(255,'2024_07_22_230370_add_job_experience_id_column_to_job_posts_table',16),(256,'2024_07_28_140520_create_job_experiences_table',17);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1),(1,'App\\Models\\User',8),(1,'App\\Models\\User',28),(1,'App\\Models\\User',29),(1,'App\\Models\\User',30),(1,'App\\Models\\User',31),(1,'App\\Models\\User',36),(1,'App\\Models\\User',40),(1,'App\\Models\\User',44),(1,'App\\Models\\User',46),(1,'App\\Models\\User',47),(1,'App\\Models\\User',48),(1,'App\\Models\\User',51),(1,'App\\Models\\User',59),(1,'App\\Models\\User',60),(1,'App\\Models\\User',61),(1,'App\\Models\\User',62),(1,'App\\Models\\User',66),(1,'App\\Models\\User',67),(1,'App\\Models\\User',68),(2,'App\\Models\\User',9),(2,'App\\Models\\User',12),(2,'App\\Models\\User',13),(2,'App\\Models\\User',14),(2,'App\\Models\\User',15),(2,'App\\Models\\User',24),(2,'App\\Models\\User',25),(2,'App\\Models\\User',27),(2,'App\\Models\\User',32),(2,'App\\Models\\User',33),(2,'App\\Models\\User',34),(2,'App\\Models\\User',37),(2,'App\\Models\\User',38),(2,'App\\Models\\User',41),(2,'App\\Models\\User',42),(2,'App\\Models\\User',43),(2,'App\\Models\\User',49),(2,'App\\Models\\User',50),(2,'App\\Models\\User',54),(2,'App\\Models\\User',56),(2,'App\\Models\\User',57),(2,'App\\Models\\User',58),(3,'App\\Models\\User',52),(3,'App\\Models\\User',53),(4,'App\\Models\\User',23),(4,'App\\Models\\User',26),(4,'App\\Models\\User',45),(5,'App\\Models\\User',21),(5,'App\\Models\\User',22),(6,'App\\Models\\User',10),(6,'App\\Models\\User',11);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(191) NOT NULL,
  `notifiable_type` varchar(191) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `office_shifts`
--

DROP TABLE IF EXISTS `office_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `office_shifts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `shift_name` varchar(191) NOT NULL,
  `default_shift` varchar(191) DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `sunday_in` varchar(191) DEFAULT NULL,
  `sunday_out` varchar(191) DEFAULT NULL,
  `saturday_in` varchar(191) DEFAULT NULL,
  `saturday_out` varchar(191) DEFAULT NULL,
  `friday_in` varchar(191) DEFAULT NULL,
  `friday_out` varchar(191) DEFAULT NULL,
  `thursday_in` varchar(191) DEFAULT NULL,
  `thursday_out` varchar(191) DEFAULT NULL,
  `wednesday_in` varchar(191) DEFAULT NULL,
  `wednesday_out` varchar(191) DEFAULT NULL,
  `tuesday_in` varchar(191) DEFAULT NULL,
  `tuesday_out` varchar(191) DEFAULT NULL,
  `monday_in` varchar(191) DEFAULT NULL,
  `monday_out` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `office_shifts_company_id_foreign` (`company_id`),
  CONSTRAINT `office_shifts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `office_shifts`
--

LOCK TABLES `office_shifts` WRITE;
/*!40000 ALTER TABLE `office_shifts` DISABLE KEYS */;
INSERT INTO `office_shifts` VALUES (1,'GENERAL',NULL,1,'','','08:00:00','17:15:00','08:00:00','17:15:00','08:00:00','17:15:00','08:00:00','17:15:00','08:00:00','17:15:00','08:00:00','17:15:00','2025-05-26 17:53:09','2025-09-27 11:21:18'),(2,'SHIFT-A','Normal',1,NULL,NULL,'07:00:00','15:45:00','07:00:00','15:45:00','07:00:00','15:45:00','07:00:00','15:45:00','07:00:00','15:45:00','07:00:00','15:45:00','2025-09-27 11:21:18','2025-09-27 11:21:18'),(3,'11:00 TO 20:15','Normal',1,NULL,NULL,'11:00:00','20:15:00','11:00:00','20:15:00','11:00:00','20:15:00','11:00:00','20:15:00','11:00:00','20:15:00','11:00:00','20:15:00','2025-09-27 11:21:18','2025-09-27 11:21:18'),(4,'SHIFT-B','Normal',1,NULL,NULL,'15:00:00','23:45:00','15:00:00','23:45:00','15:00:00','23:45:00','15:00:00','23:45:00','15:00:00','23:45:00','15:00:00','23:45:00','2025-09-27 11:21:18','2025-09-27 11:21:18'),(5,'19:00 TO 04:15','Night',1,NULL,NULL,'19:00:00','04:15:00','19:00:00','04:15:00','19:00:00','04:15:00','19:00:00','04:15:00','19:00:00','04:15:00','19:00:00','04:15:00','2025-09-27 11:21:18','2025-09-27 11:21:18'),(6,'SHIFT-C','Night',1,NULL,NULL,'23:00:00','07:15:00','23:00:00','07:15:00','23:00:00','07:15:00','23:00:00','07:15:00','23:00:00','07:15:00','23:00:00','07:15:00','2025-09-27 11:21:18','2025-09-27 11:21:18');
/*!40000 ALTER TABLE `office_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `official_documents`
--

DROP TABLE IF EXISTS `official_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `official_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `document_type_id` bigint(20) unsigned DEFAULT NULL,
  `added_by` bigint(20) unsigned DEFAULT NULL,
  `document_title` varchar(191) NOT NULL,
  `identification_number` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `document_file` varchar(191) DEFAULT NULL,
  `expiry_date` date NOT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `official_documents_company_id_foreign` (`company_id`),
  KEY `official_documents_document_type_id_foreign` (`document_type_id`),
  KEY `official_documents_added_by_foreign` (`added_by`),
  CONSTRAINT `official_documents_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `official_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `official_documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `official_documents`
--

LOCK TABLES `official_documents` WRITE;
/*!40000 ALTER TABLE `official_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `official_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `overtime_calculations`
--

DROP TABLE IF EXISTS `overtime_calculations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `overtime_calculations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `attendance_date` date NOT NULL,
  `clock_in` time NOT NULL,
  `clock_out` time NOT NULL,
  `shift_start_time` time NOT NULL,
  `shift_end_time` time NOT NULL,
  `working_minutes` int(11) NOT NULL COMMENT 'Total working time in minutes',
  `shift_minutes` int(11) NOT NULL COMMENT 'Expected shift duration in minutes',
  `late_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Late arrival in minutes',
  `overtime_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Gross overtime before adjustments',
  `net_overtime_minutes` int(11) NOT NULL DEFAULT 0 COMMENT 'Net overtime after late deduction',
  `hourly_rate` decimal(10,2) NOT NULL COMMENT 'Basic hourly rate',
  `overtime_rate` decimal(10,2) NOT NULL COMMENT 'Overtime rate (usually 2x)',
  `overtime_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Final overtime pay',
  `overtime_eligible` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Employee OT eligibility',
  `required_hours_per_day` int(11) NOT NULL DEFAULT 9 COMMENT 'Employee required hours',
  `basic_salary` decimal(10,2) NOT NULL COMMENT 'Employee basic salary for calculation',
  `calculation_notes` varchar(255) DEFAULT NULL COMMENT 'Any special notes',
  `shift_name` varchar(100) DEFAULT NULL COMMENT 'Shift name for this attendance',
  `status` enum('calculated','verified','paid') NOT NULL DEFAULT 'calculated',
  `calculated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_date_ot` (`employee_id`,`attendance_date`),
  KEY `overtime_calculations_employee_id_attendance_date_index` (`employee_id`,`attendance_date`),
  KEY `overtime_calculations_attendance_date_index` (`attendance_date`),
  KEY `overtime_calculations_status_index` (`status`),
  KEY `idx_overtime_calc_amount` (`overtime_amount`),
  KEY `idx_overtime_calc_status_date` (`status`,`attendance_date`),
  CONSTRAINT `overtime_calculations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `overtime_calculations`
--

LOCK TABLES `overtime_calculations` WRITE;
/*!40000 ALTER TABLE `overtime_calculations` DISABLE KEYS */;
INSERT INTO `overtime_calculations` VALUES (6,65,'2025-05-01','08:00:00','17:30:00','08:00:00','17:15:00',570,555,0,15,15,182.69,365.38,91.35,1,9,42750.00,'GENERAL shift: No lateness, 15 min overtime','GENERAL','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(7,65,'2025-05-02','08:10:00','17:45:00','08:00:00','17:15:00',575,555,0,30,30,182.69,365.38,182.69,1,9,42750.00,'GENERAL shift: 10 min late (within grace), full 30 min overtime','GENERAL','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(8,65,'2025-05-06','08:00:00','18:00:00','08:00:00','17:15:00',600,555,0,45,45,182.69,365.38,274.04,1,9,42750.00,'GENERAL shift: No lateness, 45 min overtime','GENERAL','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(9,65,'2025-05-07','08:20:00','17:15:00','08:00:00','17:15:00',535,555,20,0,0,182.69,365.38,0.00,1,9,42750.00,'GENERAL shift: 20 min late (beyond grace), no overtime','GENERAL','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(10,65,'2025-05-09','08:30:00','17:15:00','08:00:00','17:15:00',525,555,30,0,0,182.69,365.38,0.00,1,9,42750.00,'GENERAL shift: 30 min late (beyond grace), no overtime','GENERAL','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(11,65,'2025-05-15','07:00:00','16:00:00','07:00:00','15:45:00',540,525,0,15,15,182.69,365.38,91.35,1,9,42750.00,'SHIFT-A: No lateness, 15 min overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(12,65,'2025-05-16','07:10:00','16:15:00','07:00:00','15:45:00',545,525,0,30,30,182.69,365.38,182.69,1,9,42750.00,'SHIFT-A: 10 min late (within grace), full 30 min overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(13,65,'2025-05-20','07:05:00','16:30:00','07:00:00','15:45:00',565,525,0,45,45,182.69,365.38,274.04,1,9,42750.00,'SHIFT-A: 5 min late (within grace), full 45 min overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(14,65,'2025-05-21','07:20:00','15:45:00','07:00:00','15:45:00',505,525,20,0,0,182.69,365.38,0.00,1,9,42750.00,'SHIFT-A: 20 min late (beyond grace), no overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(15,65,'2025-05-23','07:30:00','15:45:00','07:00:00','15:45:00',495,525,30,0,0,182.69,365.38,0.00,1,9,42750.00,'SHIFT-A: 30 min late (beyond grace), no overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05'),(16,65,'2025-05-28','07:00:00','16:30:00','07:00:00','15:45:00',570,525,0,45,45,182.69,365.38,274.04,1,9,42750.00,'SHIFT-A: No lateness, 45 min overtime','SHIFT-A','calculated','2025-09-27 11:36:05','2025-09-27 11:36:05','2025-09-27 11:36:05');
/*!40000 ALTER TABLE `overtime_calculations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES ('new@gmail.com','$2y$10$day4AQ4g8sFvMwhMrTxpJuLXZCpVF2IK9kVS.6qZxeR6b7CVt2eGy','2020-10-06 04:22:35'),('irfanchowdhury80@gmail.com','$2y$10$3Opuz3k6NY0WRJbDgHO8gOz2UlR4GOumTVsQgu61.mPbmzt8DnYi6','2024-01-14 08:13:48'),('irfanchowdhury434@gmail.com','$2y$10$Xzahp1UflMrb3lzE/C9lQeGQ0z.E9UoA.1XjW7KRLIuuwTGdfp.NS','2024-03-31 07:18:22');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_methods` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `method_name` varchar(40) NOT NULL,
  `payment_percentage` varchar(100) DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_methods_company_id_foreign` (`company_id`),
  CONSTRAINT `payment_methods_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payslips`
--

DROP TABLE IF EXISTS `payslips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payslips` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payslip_key` char(36) NOT NULL,
  `payslip_number` varchar(191) DEFAULT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `payment_type` varchar(191) NOT NULL,
  `basic_salary` double NOT NULL,
  `net_salary` double NOT NULL,
  `allowances` text NOT NULL,
  `commissions` text NOT NULL,
  `loans` text NOT NULL,
  `deductions` text NOT NULL,
  `overtimes` text NOT NULL,
  `other_payments` text NOT NULL,
  `pension_type` varchar(50) DEFAULT NULL,
  `pension_amount` double NOT NULL,
  `hours_worked` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `month_year` varchar(15) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payslips_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payslips_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payslips`
--

LOCK TABLES `payslips` WRITE;
/*!40000 ALTER TABLE `payslips` DISABLE KEYS */;
INSERT INTO `payslips` VALUES (1,'HChEcR82aqYCF76qTT0g','1759583542',65,1,'Monthly',42750,46942.31,'[{\"id\":1,\"employee_id\":65,\"month_year\":\"May-2025\",\"first_date\":\"2025-05-01\",\"allowance_title\":\"Fuel\",\"allowance_amount\":\"5000\",\"is_taxable\":0,\"created_at\":\"2025-09-27T09:55:47.000000Z\",\"updated_at\":\"2025-09-27T09:55:47.000000Z\"}]','[]','[{\"id\":1,\"employee_id\":65,\"loan_title\":\"Health\",\"loan_amount\":10000,\"time_remaining\":3,\"amount_remaining\":7500,\"monthly_payable\":2500}]','[{\"id\":1,\"employee_id\":65,\"month_year\":\"May-2025\",\"first_date\":\"2025-05-01\",\"deduction_title\":\"EOBI\",\"deduction_amount\":\"500\",\"deduction_type_id\":1,\"created_at\":\"2025-09-27T10:01:21.000000Z\",\"updated_at\":\"2025-09-27T10:01:21.000000Z\"}]','[]','[]',NULL,0,0,1,'May-2025','2025-09-27 10:16:20','2025-09-27 10:16:20'),(2,'nuoOI8QiCFsW985ZuTwO','3190395454',64,1,'Monthly',37000,34500,'[]','[]','[{\"id\":2,\"employee_id\":64,\"loan_title\":\"Car\",\"loan_amount\":10000,\"time_remaining\":4,\"amount_remaining\":8000,\"monthly_payable\":2000}]','[{\"id\":2,\"employee_id\":64,\"month_year\":\"May-2025\",\"first_date\":\"2025-05-01\",\"deduction_title\":\"EOBI\",\"deduction_amount\":\"500\",\"deduction_type_id\":1,\"created_at\":\"2025-09-27T11:05:59.000000Z\",\"updated_at\":\"2025-09-27T11:05:59.000000Z\"}]','[]','[]',NULL,0,0,1,'May-2025','2025-09-27 11:46:48','2025-09-27 11:46:48');
/*!40000 ALTER TABLE `payslips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=298 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user','web',NULL,NULL),(2,'view-user','web',NULL,NULL),(3,'edit-user','web',NULL,NULL),(4,'delete-user','web',NULL,NULL),(5,'last-login-user','web',NULL,NULL),(6,'role-access-user','web',NULL,NULL),(7,'details-employee','web',NULL,NULL),(8,'view-details-employee','web',NULL,NULL),(9,'store-details-employee','web',NULL,NULL),(10,'modify-details-employee','web',NULL,NULL),(11,'customize-setting','web',NULL,NULL),(12,'role-access','web',NULL,NULL),(13,'general-setting','web',NULL,NULL),(14,'view-general-setting','web',NULL,NULL),(15,'store-general-setting','web',NULL,NULL),(16,'mail-setting','web',NULL,NULL),(17,'view-mail-setting','web',NULL,NULL),(18,'store-mail-setting','web',NULL,NULL),(19,'language-setting','web',NULL,NULL),(20,'core_hr','web',NULL,NULL),(21,'view-calendar','web',NULL,NULL),(22,'promotion','web',NULL,NULL),(23,'view-promotion','web',NULL,NULL),(24,'store-promotion','web',NULL,NULL),(25,'edit-promotion','web',NULL,NULL),(26,'delete-promotion','web',NULL,NULL),(27,'award','web',NULL,NULL),(28,'view-award','web',NULL,NULL),(29,'store-award','web',NULL,NULL),(30,'edit-award','web',NULL,NULL),(31,'delete-award','web',NULL,NULL),(32,'transfer','web',NULL,NULL),(33,'view-transfer','web',NULL,NULL),(34,'store-transfer','web',NULL,NULL),(35,'edit-transfer','web',NULL,NULL),(36,'delete-transfer','web',NULL,NULL),(37,'travel','web',NULL,NULL),(38,'view-travel','web',NULL,NULL),(39,'store-travel','web',NULL,NULL),(40,'edit-travel','web',NULL,NULL),(41,'delete-travel','web',NULL,NULL),(42,'resignation','web',NULL,NULL),(43,'view-resignation','web',NULL,NULL),(44,'store-resignation','web',NULL,NULL),(45,'edit-resignation','web',NULL,NULL),(46,'delete-resignation','web',NULL,NULL),(47,'complaint','web',NULL,NULL),(48,'view-complaint','web',NULL,NULL),(49,'store-complaint','web',NULL,NULL),(50,'edit-complaint','web',NULL,NULL),(51,'delete-complaint','web',NULL,NULL),(52,'warning','web',NULL,NULL),(53,'view-warning','web',NULL,NULL),(54,'store-warning','web',NULL,NULL),(55,'edit-warning','web',NULL,NULL),(56,'delete-warning','web',NULL,NULL),(57,'termination','web',NULL,NULL),(58,'view-termination','web',NULL,NULL),(59,'store-termination','web',NULL,NULL),(60,'edit-termination','web',NULL,NULL),(61,'delete-termination','web',NULL,NULL),(62,'timesheet','web',NULL,NULL),(63,'attendance','web',NULL,NULL),(64,'view-attendance','web',NULL,NULL),(65,'edit-attendance','web',NULL,NULL),(66,'office_shift','web',NULL,NULL),(67,'view-office_shift','web',NULL,NULL),(68,'store-office_shift','web',NULL,NULL),(69,'edit-office_shift','web',NULL,NULL),(70,'delete-office_shift','web',NULL,NULL),(71,'holiday','web',NULL,NULL),(72,'view-holiday','web',NULL,NULL),(73,'store-holiday','web',NULL,NULL),(74,'edit-holiday','web',NULL,NULL),(75,'delete-holiday','web',NULL,NULL),(76,'leave','web',NULL,NULL),(77,'view-holiday','web',NULL,NULL),(78,'store-holiday','web',NULL,NULL),(79,'edit-holiday','web',NULL,NULL),(80,'delete-holiday','web',NULL,NULL),(81,'payment-module','web',NULL,NULL),(82,'view-payslip','web',NULL,NULL),(83,'make-payment','web',NULL,NULL),(84,'make-bulk_payment','web',NULL,NULL),(85,'view-paylist','web',NULL,NULL),(86,'set-salary','web',NULL,NULL),(87,'hr_report','web',NULL,NULL),(88,'report-payslip','web',NULL,NULL),(89,'report-attendance','web',NULL,NULL),(90,'report-training','web',NULL,NULL),(91,'report-project','web',NULL,NULL),(92,'report-task','web',NULL,NULL),(93,'report-employee','web',NULL,NULL),(94,'report-account','web',NULL,NULL),(95,'report-deposit','web',NULL,NULL),(96,'report-expense','web',NULL,NULL),(97,'report-transaction','web',NULL,NULL),(98,'recruitment','web',NULL,NULL),(99,'job_employer','web',NULL,NULL),(100,'view-job_employer','web',NULL,NULL),(101,'store-job_employer','web',NULL,NULL),(102,'edit-job_employer','web',NULL,NULL),(103,'delete-job_employer','web',NULL,NULL),(104,'job_post','web',NULL,NULL),(105,'view-job_post','web',NULL,NULL),(106,'store-job_post','web',NULL,NULL),(107,'edit-job_post','web',NULL,NULL),(108,'delete-job_post','web',NULL,NULL),(109,'job_candidate','web',NULL,NULL),(110,'view-job_candidate','web',NULL,NULL),(111,'store-job_candidate','web',NULL,NULL),(112,'delete-job_candidate','web',NULL,NULL),(113,'job_interview','web',NULL,NULL),(114,'view-job_interview','web',NULL,NULL),(115,'store-job_interview','web',NULL,NULL),(116,'delete-job_interview','web',NULL,NULL),(117,'project-management','web',NULL,NULL),(118,'project','web',NULL,NULL),(119,'view-project','web',NULL,NULL),(120,'store-project','web',NULL,NULL),(121,'edit-project','web',NULL,NULL),(122,'delete-project','web',NULL,NULL),(123,'task','web',NULL,NULL),(124,'view-task','web',NULL,NULL),(125,'store-task','web',NULL,NULL),(126,'edit-task','web',NULL,NULL),(127,'delete-task','web',NULL,NULL),(128,'client','web',NULL,NULL),(129,'view-client','web',NULL,NULL),(130,'store-client','web',NULL,NULL),(131,'edit-client','web',NULL,NULL),(132,'delete-client','web',NULL,NULL),(133,'invoice','web',NULL,NULL),(134,'view-invoice','web',NULL,NULL),(135,'store-invoice','web',NULL,NULL),(136,'edit-invoice','web',NULL,NULL),(137,'delete-invoice','web',NULL,NULL),(138,'ticket','web',NULL,NULL),(139,'view-ticket','web',NULL,NULL),(140,'store-ticket','web',NULL,NULL),(141,'edit-ticket','web',NULL,NULL),(142,'delete-ticket','web',NULL,NULL),(143,'import-module','web',NULL,NULL),(144,'import-attendance','web',NULL,NULL),(145,'import-employee','web',NULL,NULL),(146,'file_module','web',NULL,NULL),(147,'file_manager','web',NULL,NULL),(148,'view-file_manager','web',NULL,NULL),(149,'store-file_manager','web',NULL,NULL),(150,'edit-file_manager','web',NULL,NULL),(151,'delete-file_manager','web',NULL,NULL),(152,'view-file_config','web',NULL,NULL),(153,'official_document','web',NULL,NULL),(154,'view-official_document','web',NULL,NULL),(155,'store-official_document','web',NULL,NULL),(156,'edit-official_document','web',NULL,NULL),(157,'delete-official_document','web',NULL,NULL),(158,'event-meeting','web',NULL,NULL),(159,'meeting','web',NULL,NULL),(160,'view-meeting','web',NULL,NULL),(161,'store-meeting','web',NULL,NULL),(162,'edit-meeting','web',NULL,NULL),(163,'delete-meeting','web',NULL,NULL),(164,'event','web',NULL,NULL),(165,'view-event','web',NULL,NULL),(166,'store-event','web',NULL,NULL),(167,'edit-event','web',NULL,NULL),(168,'delete-event','web',NULL,NULL),(169,'role','web',NULL,NULL),(170,'view-role','web',NULL,NULL),(171,'store-role','web',NULL,NULL),(172,'edit-role','web',NULL,NULL),(173,'delete-role','web',NULL,NULL),(174,'assign-module','web',NULL,NULL),(175,'assign-role','web',NULL,NULL),(176,'assign-ticket','web',NULL,NULL),(177,'assign-project','web',NULL,NULL),(178,'assign-task','web',NULL,NULL),(179,'finance','web',NULL,NULL),(180,'account','web',NULL,NULL),(181,'view-account','web',NULL,NULL),(182,'store-account','web',NULL,NULL),(183,'edit-account','web',NULL,NULL),(184,'delete-account','web',NULL,NULL),(185,'view-transaction','web',NULL,NULL),(186,'view-balance_transfer','web',NULL,NULL),(187,'store-balance_transfer','web',NULL,NULL),(188,'expense','web',NULL,NULL),(189,'view-expense','web',NULL,NULL),(190,'store-expense','web',NULL,NULL),(191,'edit-expense','web',NULL,NULL),(192,'delete-expense','web',NULL,NULL),(193,'deposit','web',NULL,NULL),(194,'view-deposit','web',NULL,NULL),(195,'store-deposit','web',NULL,NULL),(196,'edit-deposit','web',NULL,NULL),(197,'delete-deposit','web',NULL,NULL),(198,'payer','web',NULL,NULL),(199,'view-payer','web',NULL,NULL),(200,'store-payer','web',NULL,NULL),(201,'edit-payer','web',NULL,NULL),(202,'delete-payer','web',NULL,NULL),(203,'payee','web',NULL,NULL),(204,'view-payee','web',NULL,NULL),(205,'store-payee','web',NULL,NULL),(206,'edit-payee','web',NULL,NULL),(207,'delete-payee','web',NULL,NULL),(208,'training_module','web',NULL,NULL),(209,'trainer','web',NULL,NULL),(210,'view-trainer','web',NULL,NULL),(211,'store-trainer','web',NULL,NULL),(212,'edit-trainer','web',NULL,NULL),(213,'delete-trainer','web',NULL,NULL),(214,'training','web',NULL,NULL),(215,'view-training','web',NULL,NULL),(216,'store-training','web',NULL,NULL),(217,'edit-training','web',NULL,NULL),(218,'delete-training','web',NULL,NULL),(219,'access-module','web',NULL,NULL),(220,'access-variable_type','web',NULL,NULL),(221,'access-variable_method','web',NULL,NULL),(222,'access-language','web',NULL,NULL),(223,'announcement','web',NULL,NULL),(224,'store-announcement','web',NULL,NULL),(225,'edit-announcement','web',NULL,NULL),(226,'delete-announcement','web',NULL,NULL),(227,'company','web',NULL,NULL),(228,'view-company','web',NULL,NULL),(229,'store-company','web',NULL,NULL),(230,'edit-company','web',NULL,NULL),(231,'delete-company','web',NULL,NULL),(232,'department','web',NULL,NULL),(233,'view-department','web',NULL,NULL),(234,'store-department','web',NULL,NULL),(235,'edit-department','web',NULL,NULL),(236,'delete-department','web',NULL,NULL),(237,'designation','web',NULL,NULL),(238,'view-designation','web',NULL,NULL),(239,'store-designation','web',NULL,NULL),(240,'edit-designation','web',NULL,NULL),(241,'delete-designation','web',NULL,NULL),(242,'location','web',NULL,NULL),(243,'view-location','web',NULL,NULL),(244,'store-location','web',NULL,NULL),(245,'edit-location','web',NULL,NULL),(246,'delete-location','web',NULL,NULL),(247,'policy','web',NULL,NULL),(248,'store-policy','web',NULL,NULL),(249,'edit-policy','web',NULL,NULL),(250,'delete-policy','web',NULL,NULL),(251,'view-cms','web',NULL,NULL),(252,'store-cms','web',NULL,NULL),(253,'store-user','web',NULL,NULL),(254,'delete-attendance','web',NULL,NULL),(255,'view-leave','web',NULL,NULL),(256,'store-leave','web',NULL,NULL),(257,'edit-leave','web',NULL,NULL),(258,'delete-leave','web',NULL,NULL),(259,'cms','web',NULL,NULL),(260,'performance','web',NULL,NULL),(261,'goal-type','web',NULL,NULL),(262,'view-goal-type','web',NULL,NULL),(263,'store-goal-type','web',NULL,NULL),(264,'edit-goal-type','web',NULL,NULL),(265,'delete-goal-type','web',NULL,NULL),(266,'goal-tracking','web',NULL,NULL),(267,'view-goal-tracking','web',NULL,NULL),(268,'store-goal-tracking','web',NULL,NULL),(269,'edit-goal-tracking','web',NULL,NULL),(270,'delete-goal-tracking','web',NULL,NULL),(271,'indicator','web',NULL,NULL),(272,'view-indicator','web',NULL,NULL),(273,'store-indicator','web',NULL,NULL),(274,'edit-indicator','web',NULL,NULL),(275,'delete-indicator','web',NULL,NULL),(276,'appraisal','web',NULL,NULL),(277,'view-appraisal','web',NULL,NULL),(278,'store-appraisal','web',NULL,NULL),(279,'edit-appraisal','web',NULL,NULL),(280,'delete-appraisal','web',NULL,NULL),(281,'assets-and-category','web',NULL,NULL),(282,'category','web',NULL,NULL),(283,'view-assets-category','web',NULL,NULL),(284,'store-assets-category','web',NULL,NULL),(285,'edit-assets-category','web',NULL,NULL),(286,'delete-assets-category','web',NULL,NULL),(287,'assets','web',NULL,NULL),(288,'view-assets','web',NULL,NULL),(289,'store-assets','web',NULL,NULL),(290,'edit-assets','web',NULL,NULL),(291,'delete-assets','web',NULL,NULL),(292,'daily-attendances','web',NULL,NULL),(293,'date-wise-attendances','web',NULL,NULL),(294,'monthly-attendances','web',NULL,NULL),(295,'set-permission','web',NULL,NULL),(296,'get-leave-notification','web',NULL,NULL),(297,'report-pension','web',NULL,NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `description` longtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `added_by` varchar(40) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `policies_company_id_foreign` (`company_id`),
  KEY `policies_added_by_foreign` (`added_by`),
  CONSTRAINT `policies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_bugs`
--

DROP TABLE IF EXISTS `project_bugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_bugs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` mediumtext NOT NULL,
  `bug_attachment` varchar(191) DEFAULT NULL,
  `status` varchar(191) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_bugs_user_id_foreign` (`user_id`),
  KEY `project_bugs_project_id_foreign` (`project_id`),
  CONSTRAINT `project_bugs_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_bugs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_bugs`
--

LOCK TABLES `project_bugs` WRITE;
/*!40000 ALTER TABLE `project_bugs` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_bugs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_discussions`
--

DROP TABLE IF EXISTS `project_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_discussions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `project_discussion` mediumtext NOT NULL,
  `discussion_attachment` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_discussions_user_id_foreign` (`user_id`),
  KEY `project_discussions_project_id_foreign` (`project_id`),
  CONSTRAINT `project_discussions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_discussions`
--

LOCK TABLES `project_discussions` WRITE;
/*!40000 ALTER TABLE `project_discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_files`
--

DROP TABLE IF EXISTS `project_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `file_title` varchar(191) NOT NULL,
  `file_attachment` varchar(191) NOT NULL,
  `file_description` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_files_user_id_foreign` (`user_id`),
  KEY `project_files_project_id_foreign` (`project_id`),
  CONSTRAINT `project_files_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_files_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_files`
--

LOCK TABLES `project_files` WRITE;
/*!40000 ALTER TABLE `project_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `client_id` bigint(20) unsigned DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `project_priority` varchar(40) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `summary` mediumtext DEFAULT NULL,
  `project_status` varchar(40) NOT NULL DEFAULT 'not started',
  `project_note` longtext DEFAULT NULL,
  `project_progress` varchar(191) DEFAULT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `added_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `projects_client_id_foreign` (`client_id`),
  KEY `projects_company_id_foreign` (`company_id`),
  KEY `projects_added_by_foreign` (`added_by`),
  CONSTRAINT `projects_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `projects_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promotions`
--

DROP TABLE IF EXISTS `promotions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promotions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `promotion_title` varchar(40) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `promotion_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `promotions_company_id_foreign` (`company_id`),
  KEY `promotions_employee_id_foreign` (`employee_id`),
  CONSTRAINT `promotions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `promotions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promotions`
--

LOCK TABLES `promotions` WRITE;
/*!40000 ALTER TABLE `promotions` DISABLE KEYS */;
/*!40000 ALTER TABLE `promotions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualification_education_levels`
--

DROP TABLE IF EXISTS `qualification_education_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualification_education_levels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qualification_education_levels_company_id_foreign` (`company_id`),
  CONSTRAINT `qualification_education_levels_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualification_education_levels`
--

LOCK TABLES `qualification_education_levels` WRITE;
/*!40000 ALTER TABLE `qualification_education_levels` DISABLE KEYS */;
/*!40000 ALTER TABLE `qualification_education_levels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualification_languages`
--

DROP TABLE IF EXISTS `qualification_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualification_languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qualification_languages_company_id_foreign` (`company_id`),
  CONSTRAINT `qualification_languages_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualification_languages`
--

LOCK TABLES `qualification_languages` WRITE;
/*!40000 ALTER TABLE `qualification_languages` DISABLE KEYS */;
/*!40000 ALTER TABLE `qualification_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qualification_skills`
--

DROP TABLE IF EXISTS `qualification_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qualification_skills` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qualification_skills_company_id_foreign` (`company_id`),
  CONSTRAINT `qualification_skills_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qualification_skills`
--

LOCK TABLES `qualification_skills` WRITE;
/*!40000 ALTER TABLE `qualification_skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `qualification_skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `relation_types`
--

DROP TABLE IF EXISTS `relation_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `relation_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `relation_types`
--

LOCK TABLES `relation_types` WRITE;
/*!40000 ALTER TABLE `relation_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `relation_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resignations`
--

DROP TABLE IF EXISTS `resignations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resignations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `notice_date` date DEFAULT NULL,
  `resignation_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resignations_company_id_foreign` (`company_id`),
  KEY `resignations_department_id_foreign` (`department_id`),
  KEY `resignations_employee_id_foreign` (`employee_id`),
  CONSTRAINT `resignations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resignations_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `resignations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resignations`
--

LOCK TABLES `resignations` WRITE;
/*!40000 ALTER TABLE `resignations` DISABLE KEYS */;
/*!40000 ALTER TABLE `resignations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,5),(1,6),(2,5),(2,6),(3,5),(3,6),(4,5),(4,6),(5,5),(5,6),(6,5),(6,6),(7,5),(7,6),(8,5),(8,6),(9,5),(9,6),(10,5),(10,6),(11,5),(11,6),(13,5),(13,6),(14,5),(14,6),(15,5),(15,6),(16,5),(16,6),(17,5),(17,6),(18,5),(18,6),(20,5),(20,6),(21,5),(21,6),(22,5),(22,6),(23,5),(23,6),(24,5),(24,6),(25,5),(25,6),(26,5),(26,6),(27,5),(27,6),(28,5),(28,6),(29,5),(29,6),(30,5),(30,6),(31,5),(31,6),(32,5),(32,6),(33,5),(33,6),(34,5),(34,6),(35,5),(35,6),(36,5),(36,6),(37,5),(37,6),(38,5),(38,6),(39,5),(39,6),(41,5),(41,6),(42,5),(42,6),(43,5),(43,6),(44,5),(44,6),(46,5),(46,6),(47,5),(47,6),(48,5),(48,6),(49,5),(49,6),(50,5),(50,6),(51,5),(51,6),(52,5),(52,6),(53,5),(53,6),(54,5),(54,6),(55,5),(55,6),(56,5),(56,6),(57,5),(57,6),(58,5),(58,6),(59,5),(59,6),(60,5),(60,6),(61,5),(61,6),(62,5),(62,6),(63,5),(63,6),(64,5),(64,6),(65,5),(65,6),(66,5),(66,6),(67,5),(67,6),(68,5),(68,6),(69,5),(69,6),(70,5),(70,6),(71,5),(71,6),(72,5),(72,6),(73,5),(73,6),(74,5),(74,6),(75,5),(75,6),(76,5),(76,6),(81,5),(82,5),(83,5),(84,5),(85,5),(86,5),(87,5),(90,5),(91,5),(92,5),(93,5),(94,5),(95,5),(96,5),(97,5),(98,5),(104,5),(105,5),(106,5),(107,5),(108,5),(109,5),(110,5),(112,5),(113,5),(114,5),(115,5),(116,5),(117,5),(118,5),(119,5),(120,5),(121,5),(122,5),(123,5),(124,5),(125,5),(126,5),(127,5),(128,5),(129,5),(130,5),(131,5),(132,5),(133,5),(134,5),(135,5),(136,5),(137,5),(138,5),(139,5),(140,5),(141,5),(142,5),(144,5),(144,6),(145,5),(145,6),(146,5),(147,5),(148,5),(149,5),(150,5),(151,5),(152,5),(153,5),(154,5),(156,5),(157,5),(158,5),(159,5),(160,5),(161,5),(162,5),(163,5),(164,5),(165,5),(166,5),(167,5),(168,5),(169,5),(169,6),(170,5),(170,6),(171,5),(171,6),(172,5),(172,6),(173,5),(173,6),(176,5),(177,5),(178,5),(179,5),(180,5),(181,5),(182,5),(183,5),(184,5),(185,5),(186,5),(187,5),(188,5),(189,5),(190,5),(191,5),(192,5),(193,5),(194,5),(195,5),(196,5),(197,5),(198,5),(199,5),(200,5),(201,5),(202,5),(203,5),(204,5),(205,5),(206,5),(207,5),(208,5),(209,5),(210,5),(211,5),(212,5),(213,5),(214,5),(215,5),(216,5),(217,5),(218,5),(220,5),(220,6),(221,5),(221,6),(222,5),(222,6),(223,5),(224,5),(225,5),(226,5),(227,5),(228,5),(229,5),(230,5),(231,5),(232,5),(233,5),(234,5),(235,5),(236,5),(237,5),(238,5),(239,5),(240,5),(241,5),(242,5),(243,5),(244,5),(245,5),(246,5),(247,5),(248,5),(249,5),(250,5),(251,5),(252,5),(253,5),(253,6),(254,5),(254,6),(255,5),(255,6),(256,5),(256,6),(257,5),(257,6),(258,5),(258,6),(259,5),(260,5),(261,5),(262,5),(263,5),(264,5),(265,5),(266,5),(267,5),(268,5),(269,5),(270,5),(271,5),(272,5),(273,5),(274,5),(275,5),(276,5),(277,5),(278,5),(279,5),(280,5),(281,5),(282,5),(283,5),(284,5),(285,5),(286,5),(287,5),(288,5),(289,5),(290,5),(291,5),(292,5),(293,5),(294,5),(295,5),(295,6),(296,5),(296,6),(297,5);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','web','Can access and change everything',1,NULL,NULL),(2,'employee','web','Default access',1,'2020-07-26 13:50:45','2020-07-26 13:50:45'),(3,'client','web','When you create a client, this role and associated.',1,'2020-10-08 03:10:23','2020-10-08 03:10:23'),(4,'Manager','web','Can Manage',1,'2021-02-24 10:24:58','2021-02-24 10:24:58'),(5,'Editor','web','Custom access',1,'2021-02-24 10:24:58','2021-02-24 10:24:58'),(6,'HR','web','',1,'2021-09-05 03:12:28','2021-09-05 03:12:28');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_allowances`
--

DROP TABLE IF EXISTS `salary_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_allowances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(191) NOT NULL,
  `first_date` date DEFAULT NULL,
  `allowance_title` varchar(191) NOT NULL,
  `allowance_amount` varchar(191) NOT NULL,
  `is_taxable` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_allowances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_allowances`
--

LOCK TABLES `salary_allowances` WRITE;
/*!40000 ALTER TABLE `salary_allowances` DISABLE KEYS */;
INSERT INTO `salary_allowances` VALUES (1,65,'May-2025','2025-05-01','Fuel','5000',0,'2025-09-27 09:55:47','2025-09-27 09:55:47');
/*!40000 ALTER TABLE `salary_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_basics`
--

DROP TABLE IF EXISTS `salary_basics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_basics` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(191) NOT NULL,
  `first_date` date DEFAULT NULL,
  `payslip_type` varchar(191) NOT NULL,
  `basic_salary` double NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_basics_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_basics_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_basics`
--

LOCK TABLES `salary_basics` WRITE;
/*!40000 ALTER TABLE `salary_basics` DISABLE KEYS */;
INSERT INTO `salary_basics` VALUES (1,61,'May-2025','2025-05-01','Monthly',50000,'2025-05-26 17:57:57','2025-05-26 17:57:57'),(2,62,'May-2025','2025-05-01','Monthly',41440,'2025-05-26 17:58:39','2025-05-26 17:58:39'),(3,63,'May-2025','2025-05-01','Monthly',40700,'2025-05-26 17:58:58','2025-05-26 17:58:58'),(4,64,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 17:59:19','2025-05-26 17:59:19'),(5,65,'May-2025','2025-05-01','Monthly',42750,'2025-05-26 17:59:38','2025-05-26 17:59:38'),(6,66,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 17:59:56','2025-05-26 17:59:56'),(7,67,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:00:21','2025-05-26 18:00:21'),(8,68,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:00:36','2025-05-26 18:00:36'),(9,69,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:00:50','2025-05-26 18:00:50'),(10,70,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:01:09','2025-05-26 18:01:09'),(11,71,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:01:34','2025-05-26 18:01:34'),(12,72,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:01:49','2025-05-26 18:01:49'),(13,73,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:01:57','2025-05-26 18:01:57'),(14,74,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:02:21','2025-05-26 18:02:21'),(15,75,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:02:29','2025-05-26 18:02:29'),(16,76,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:02:37','2025-05-26 18:02:37'),(17,77,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:03:02','2025-05-26 18:03:02'),(18,78,'May-2025','2025-05-01','Monthly',38500,'2025-05-26 18:03:14','2025-05-26 18:03:14'),(19,79,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:03:26','2025-05-26 18:03:26'),(20,80,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:03:37','2025-05-26 18:03:37'),(21,81,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:04:04','2025-05-26 18:04:04'),(22,82,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:04:11','2025-05-26 18:04:11'),(23,83,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:04:17','2025-05-26 18:04:17'),(24,84,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:04:25','2025-05-26 18:04:25'),(25,85,'May-2025','2025-05-01','Monthly',42000,'2025-05-26 18:04:45','2025-05-26 18:04:45'),(26,86,'May-2025','2025-05-01','Monthly',40000,'2025-05-26 18:04:56','2025-05-26 18:04:56'),(27,87,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:05:06','2025-05-26 18:05:06'),(28,88,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:05:29','2025-05-26 18:05:29'),(29,89,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:05:43','2025-05-26 18:05:43'),(30,90,'May-2025','2025-05-01','Monthly',45910,'2025-05-26 18:05:53','2025-05-26 18:05:53'),(31,91,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:06:12','2025-05-26 18:06:12'),(32,92,'May-2025','2025-05-01','Monthly',40000,'2025-05-26 18:06:22','2025-05-26 18:06:22'),(33,93,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:06:30','2025-05-26 18:06:30'),(34,94,'May-2025','2025-05-01','Monthly',39000,'2025-05-26 18:06:38','2025-05-26 18:06:38'),(35,95,'May-2025','2025-05-01','Monthly',56000,'2025-05-26 18:07:08','2025-05-26 18:07:08'),(36,96,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:07:21','2025-05-26 18:07:21'),(37,97,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:07:28','2025-05-26 18:07:28'),(38,98,'May-2025','2025-05-01','Monthly',92160,'2025-05-26 18:07:39','2025-05-26 18:07:39'),(39,99,'May-2025','2025-05-01','Monthly',45910,'2025-05-26 18:08:01','2025-05-26 18:08:01'),(40,100,'May-2025','2025-05-01','Monthly',40000,'2025-05-26 18:08:10','2025-05-26 18:08:10'),(41,101,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:08:18','2025-05-26 18:08:18'),(42,102,'May-2025','2025-05-01','Monthly',45910,'2025-05-26 18:08:26','2025-05-26 18:08:26'),(43,103,'May-2025','2025-05-01','Monthly',45910,'2025-05-26 18:08:49','2025-05-26 18:08:49'),(44,104,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:08:57','2025-05-26 18:08:57'),(45,105,'May-2025','2025-05-01','Monthly',41440,'2025-05-26 18:09:05','2025-05-26 18:09:05'),(46,106,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:09:12','2025-05-26 18:09:12'),(47,107,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:09:29','2025-05-26 18:09:29'),(48,108,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:09:39','2025-05-26 18:09:39'),(49,109,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:09:45','2025-05-26 18:09:45'),(50,110,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:09:58','2025-05-26 18:09:58'),(51,111,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:10:18','2025-05-26 18:10:18'),(52,112,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:10:26','2025-05-26 18:10:26'),(53,113,'May-2025','2025-05-01','Monthly',55000,'2025-05-26 18:10:35','2025-05-26 18:10:35'),(54,114,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:10:53','2025-05-26 18:10:53'),(55,115,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:11:00','2025-05-26 18:11:00'),(56,116,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:11:09','2025-05-26 18:11:09'),(57,117,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:11:17','2025-05-26 18:11:17'),(58,118,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:11:34','2025-05-26 18:11:34'),(59,119,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:14:04','2025-05-26 18:14:04'),(60,120,'May-2025','2025-05-01','Monthly',37000,'2025-05-26 18:14:21','2025-05-26 18:14:21');
/*!40000 ALTER TABLE `salary_basics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_commissions`
--

DROP TABLE IF EXISTS `salary_commissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_commissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(191) NOT NULL,
  `commission_title` varchar(191) NOT NULL,
  `first_date` date DEFAULT NULL,
  `commission_amount` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_commissions_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_commissions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_commissions`
--

LOCK TABLES `salary_commissions` WRITE;
/*!40000 ALTER TABLE `salary_commissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `salary_commissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_deductions`
--

DROP TABLE IF EXISTS `salary_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_deductions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(50) NOT NULL,
  `first_date` date DEFAULT NULL,
  `deduction_title` varchar(191) NOT NULL,
  `deduction_amount` varchar(191) NOT NULL,
  `deduction_type_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_deductions_employee_id_foreign` (`employee_id`),
  KEY `salary_deductions_deduction_type_id_foreign` (`deduction_type_id`),
  CONSTRAINT `salary_deductions_deduction_type_id_foreign` FOREIGN KEY (`deduction_type_id`) REFERENCES `deduction_types` (`id`),
  CONSTRAINT `salary_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_deductions`
--

LOCK TABLES `salary_deductions` WRITE;
/*!40000 ALTER TABLE `salary_deductions` DISABLE KEYS */;
INSERT INTO `salary_deductions` VALUES (1,65,'May-2025','2025-05-01','EOBI','500',1,'2025-09-27 10:01:21','2025-09-27 10:01:21'),(2,64,'May-2025','2025-05-01','EOBI','500',1,'2025-09-27 11:05:59','2025-09-27 11:05:59');
/*!40000 ALTER TABLE `salary_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_loans`
--

DROP TABLE IF EXISTS `salary_loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_loans` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(50) NOT NULL,
  `first_date` date DEFAULT NULL,
  `loan_title` varchar(191) NOT NULL,
  `loan_amount` varchar(191) NOT NULL,
  `loan_type_id` bigint(20) unsigned NOT NULL DEFAULT 1,
  `loan_time` varchar(191) NOT NULL,
  `amount_remaining` varchar(191) NOT NULL,
  `time_remaining` varchar(191) NOT NULL,
  `monthly_payable` varchar(50) NOT NULL,
  `reason` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deduction_type_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_loans_employee_id_foreign` (`employee_id`),
  KEY `salary_loans_loan_type_id_foreign` (`loan_type_id`),
  CONSTRAINT `salary_loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_loans_loan_type_id_foreign` FOREIGN KEY (`loan_type_id`) REFERENCES `loan_types` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_loans`
--

LOCK TABLES `salary_loans` WRITE;
/*!40000 ALTER TABLE `salary_loans` DISABLE KEYS */;
INSERT INTO `salary_loans` VALUES (1,65,'May-2025','2025-05-01','Health','10000',2,'4','7500','3','2500','Accident','2025-09-27 10:01:09','2025-09-27 10:16:20',NULL),(2,64,'May-2025','2025-05-01','Car','10000',1,'5','8000','4','2000','Car Purchase','2025-09-27 11:05:46','2025-09-27 11:46:48',NULL);
/*!40000 ALTER TABLE `salary_loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_other_payments`
--

DROP TABLE IF EXISTS `salary_other_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_other_payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(50) NOT NULL DEFAULT '',
  `first_date` date DEFAULT NULL,
  `other_payment_title` varchar(191) NOT NULL,
  `other_payment_amount` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_other_payments_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_other_payments_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_other_payments`
--

LOCK TABLES `salary_other_payments` WRITE;
/*!40000 ALTER TABLE `salary_other_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `salary_other_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_overtimes`
--

DROP TABLE IF EXISTS `salary_overtimes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salary_overtimes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `month_year` varchar(50) NOT NULL,
  `first_date` date DEFAULT NULL,
  `overtime_title` varchar(191) NOT NULL,
  `no_of_days` varchar(191) NOT NULL,
  `overtime_hours` varchar(191) NOT NULL,
  `overtime_rate` varchar(191) NOT NULL,
  `overtime_amount` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_overtimes_employee_id_foreign` (`employee_id`),
  CONSTRAINT `salary_overtimes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_overtimes`
--

LOCK TABLES `salary_overtimes` WRITE;
/*!40000 ALTER TABLE `salary_overtimes` DISABLE KEYS */;
/*!40000 ALTER TABLE `salary_overtimes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status_title` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

LOCK TABLES `statuses` WRITE;
/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `support_tickets` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `ticket_code` varchar(15) NOT NULL,
  `subject` varchar(191) NOT NULL,
  `ticket_priority` varchar(40) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `ticket_remarks` mediumtext DEFAULT NULL,
  `ticket_status` varchar(40) NOT NULL,
  `ticket_note` varchar(191) DEFAULT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `ticket_attachment` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_code_unique` (`ticket_code`),
  KEY `support_tickets_company_id_foreign` (`company_id`),
  KEY `support_tickets_department_id_foreign` (`department_id`),
  KEY `support_tickets_employee_id_foreign` (`employee_id`),
  CONSTRAINT `support_tickets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `support_tickets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_discussions`
--

DROP TABLE IF EXISTS `task_discussions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_discussions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `task_discussion` mediumtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_discussions_user_id_foreign` (`user_id`),
  KEY `task_discussions_task_id_foreign` (`task_id`),
  CONSTRAINT `task_discussions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_discussions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_discussions`
--

LOCK TABLES `task_discussions` WRITE;
/*!40000 ALTER TABLE `task_discussions` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_discussions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_files`
--

DROP TABLE IF EXISTS `task_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_files` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `file_title` varchar(191) NOT NULL,
  `file_attachment` varchar(191) NOT NULL,
  `file_description` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_files_user_id_foreign` (`user_id`),
  KEY `task_files_task_id_foreign` (`task_id`),
  CONSTRAINT `task_files_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_files_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_files`
--

LOCK TABLES `task_files` WRITE;
/*!40000 ALTER TABLE `task_files` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_files` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_name` varchar(191) NOT NULL,
  `project_id` bigint(20) unsigned NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `task_hour` varchar(40) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `task_status` varchar(40) NOT NULL DEFAULT 'not started',
  `task_note` mediumtext DEFAULT NULL,
  `task_progress` varchar(191) DEFAULT NULL,
  `is_notify` tinyint(4) DEFAULT NULL,
  `added_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_project_id_foreign` (`project_id`),
  KEY `tasks_company_id_foreign` (`company_id`),
  KEY `tasks_added_by_foreign` (`added_by`),
  CONSTRAINT `tasks_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_types`
--

DROP TABLE IF EXISTS `tax_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tax_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `rate` varchar(191) NOT NULL,
  `type` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_types`
--

LOCK TABLES `tax_types` WRITE;
/*!40000 ALTER TABLE `tax_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `termination_types`
--

DROP TABLE IF EXISTS `termination_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `termination_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `termination_title` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `termination_types`
--

LOCK TABLES `termination_types` WRITE;
/*!40000 ALTER TABLE `termination_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `termination_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `terminations`
--

DROP TABLE IF EXISTS `terminations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `terminations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `terminated_employee` bigint(20) unsigned NOT NULL,
  `termination_type` bigint(20) unsigned DEFAULT NULL,
  `termination_date` date NOT NULL,
  `notice_date` date NOT NULL,
  `status` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `terminations_company_id_foreign` (`company_id`),
  KEY `terminations_terminated_employee_foreign` (`terminated_employee`),
  KEY `terminations_termination_type_foreign` (`termination_type`),
  CONSTRAINT `terminations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `terminations_terminated_employee_foreign` FOREIGN KEY (`terminated_employee`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `terminations_termination_type_foreign` FOREIGN KEY (`termination_type`) REFERENCES `termination_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `terminations`
--

LOCK TABLES `terminations` WRITE;
/*!40000 ALTER TABLE `terminations` DISABLE KEYS */;
/*!40000 ALTER TABLE `terminations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_comments`
--

DROP TABLE IF EXISTS `ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ticket_comments` mediumtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_comments_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_comments`
--

LOCK TABLES `ticket_comments` WRITE;
/*!40000 ALTER TABLE `ticket_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainers`
--

DROP TABLE IF EXISTS `trainers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trainers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) NOT NULL,
  `last_name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `address` mediumtext DEFAULT NULL,
  `expertise` mediumtext NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trainers_company_id_foreign` (`company_id`),
  CONSTRAINT `trainers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainers`
--

LOCK TABLES `trainers` WRITE;
/*!40000 ALTER TABLE `trainers` DISABLE KEYS */;
/*!40000 ALTER TABLE `trainers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_lists`
--

DROP TABLE IF EXISTS `training_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_lists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `training_cost` varchar(191) NOT NULL,
  `status` varchar(30) NOT NULL,
  `remarks` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `trainer_id` bigint(20) unsigned DEFAULT NULL,
  `training_type_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_lists_company_id_foreign` (`company_id`),
  KEY `training_lists_trainer_id_foreign` (`trainer_id`),
  KEY `training_lists_training_type_id_foreign` (`training_type_id`),
  CONSTRAINT `training_lists_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_lists_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `trainers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `training_lists_training_type_id_foreign` FOREIGN KEY (`training_type_id`) REFERENCES `training_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_lists`
--

LOCK TABLES `training_lists` WRITE;
/*!40000 ALTER TABLE `training_lists` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_lists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_types`
--

DROP TABLE IF EXISTS `training_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `training_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_types`
--

LOCK TABLES `training_types` WRITE;
/*!40000 ALTER TABLE `training_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `training_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfers`
--

DROP TABLE IF EXISTS `transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `from_department_id` bigint(20) unsigned DEFAULT NULL,
  `to_department_id` bigint(20) unsigned DEFAULT NULL,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `transfer_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transfers_company_id_foreign` (`company_id`),
  KEY `transfers_from_department_id_foreign` (`from_department_id`),
  KEY `transfers_to_department_id_foreign` (`to_department_id`),
  KEY `transfers_employee_id_foreign` (`employee_id`),
  CONSTRAINT `transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_from_department_id_foreign` FOREIGN KEY (`from_department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transfers_to_department_id_foreign` FOREIGN KEY (`to_department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfers`
--

LOCK TABLES `transfers` WRITE;
/*!40000 ALTER TABLE `transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(10) unsigned NOT NULL,
  `group` varchar(191) DEFAULT NULL,
  `key` text NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `translations_language_id_foreign` (`language_id`),
  CONSTRAINT `translations_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `travel_types`
--

DROP TABLE IF EXISTS `travel_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travel_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `arrangement_type` varchar(191) NOT NULL,
  `company_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `travel_types_company_id_foreign` (`company_id`),
  CONSTRAINT `travel_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_types`
--

LOCK TABLES `travel_types` WRITE;
/*!40000 ALTER TABLE `travel_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `travel_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `travels`
--

DROP TABLE IF EXISTS `travels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `travels` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `employee_id` bigint(20) unsigned NOT NULL,
  `travel_type` bigint(20) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `purpose_of_visit` varchar(191) DEFAULT NULL,
  `place_of_visit` varchar(191) DEFAULT NULL,
  `expected_budget` varchar(20) DEFAULT NULL,
  `actual_budget` varchar(20) DEFAULT NULL,
  `travel_mode` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `travels_company_id_foreign` (`company_id`),
  KEY `travels_employee_id_foreign` (`employee_id`),
  KEY `travels_travel_type_foreign` (`travel_type`),
  CONSTRAINT `travels_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travels_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travels_travel_type_foreign` FOREIGN KEY (`travel_type`) REFERENCES `travel_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travels`
--

LOCK TABLES `travels` WRITE;
/*!40000 ALTER TABLE `travels` DISABLE KEYS */;
/*!40000 ALTER TABLE `travels` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) DEFAULT NULL,
  `last_name` varchar(191) DEFAULT NULL,
  `username` varchar(64) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) DEFAULT NULL,
  `profile_photo` varchar(191) DEFAULT NULL,
  `profile_bg` varchar(191) DEFAULT NULL,
  `role_users_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `last_login_ip` varchar(32) DEFAULT NULL,
  `last_login_date` timestamp(2) NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_role_users_id_foreign` (`role_users_id`),
  CONSTRAINT `users_role_users_id_foreign` FOREIGN KEY (`role_users_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (59,'Bilal','Hasan','bilal','bilal.ata45@gmail.com',NULL,'$2y$10$1CuDr/maiKDCynpnO9AzkOHgeLFK56LU2usAjuX8PJ3BeSnN9rO2i','bilal_1748261538.JPG',NULL,1,1,'03363219492','125.209.82.213','2025-05-26 17:14:26.00',NULL,NULL,'2025-05-26 17:12:18','2025-05-26 17:12:18',NULL),(60,'Trims Tech','Packaging','trimstech','info@cubetechwiz.com',NULL,'$2y$10$hoJJUt1CgPomD6M90TDQ3ef7R8ankOu1EiWIrCOx/uHznL2yfW4AS','trimstech_1748268166.jpg',NULL,1,1,'1234567','125.209.82.213','2025-05-26 19:02:23.00',NULL,NULL,'2025-05-26 17:18:00','2025-05-26 19:02:46',NULL),(61,'MUHAMMAD UZAIR SIDDIQUI','MUHAMMAD NAFEES SIDDIQUI',NULL,NULL,NULL,'$2y$10$lNw9UTxt0w4CpK9tDDLqb.PY30AJGleK7iUQ0hsE3p1tgPzUO8eIG',NULL,NULL,2,1,'0331-2700598',NULL,NULL,NULL,NULL,'2025-05-26 17:53:52','2025-05-26 17:53:52',NULL),(62,'M.SAEED KHAN','M.IDREES KHAN',NULL,NULL,NULL,'$2y$10$/R5YOZK7k70eGa1x7yN6cuuFa9VyiSNDHhNP2bcogaSxYtoDh9GPS',NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'2025-05-26 17:53:52','2025-05-26 17:53:52',NULL),(63,'M.ASIF','LIAQUAT ULLAH',NULL,NULL,NULL,'$2y$10$TmgYhf/H95dIXKCPTQQKYuywWIVDYzvbSUMi07ZZYyY97lvzDUdr2',NULL,NULL,2,1,'0313-8169680',NULL,NULL,NULL,NULL,'2025-05-26 17:53:52','2025-05-26 17:53:52',NULL),(64,'M.TASLEEM','M.SALEEM QURESHI',NULL,NULL,NULL,'$2y$10$kNCvss1Kd.FoYMU4c81dueckg8TRnonMVWrwzHHTwGIXD6gw2jzgm',NULL,NULL,2,1,'0317-2275112',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(65,'HASEEB AHMED','SAEED AHMED',NULL,NULL,NULL,'$2y$10$GLfuYy5d1ZkF8GgHs45Qie6b20bIICJuULgvEg/Ujd737qQjldNxK',NULL,NULL,2,1,'0304-9771988',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(66,'M HUZAIFA','NOOR MUHAMMAD',NULL,NULL,NULL,'$2y$10$PhYEyAlJAdf4iC5Ce6H/nOo2sl41kwasYuIGdDR0b6FNCx44SY9Z.',NULL,NULL,2,1,'0311-1207843',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(67,'M.ZAYAN','M.IMRAN KHAN',NULL,NULL,NULL,'$2y$10$aKp.JcS5n8gvu.kArwXnl.uhEeiyqso6LXzpl4.66hRVtPuPuOSgS',NULL,NULL,2,1,'0316-2412400',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(68,'ADAN ISHAAQ','ISHAAQ MASEEH',NULL,NULL,NULL,'$2y$10$DTUo8ZRb1H7khphruDuTW.bsnZmxC7yYUHqD400yD0/VRVQVwSwWa',NULL,NULL,2,1,'0341-2256280',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(69,'AHMED','M.ISMAIL',NULL,NULL,NULL,'$2y$10$cDF5GCKKw0UayqAjIIDt9OiME1XaPgPMwaaHMxwspskWxnYPnx1oq',NULL,NULL,2,1,'0311-2188976',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(70,'M.ALI','M.IQBAL',NULL,NULL,NULL,'$2y$10$abc7F.lZlU212IQE5d/UzO1LhQT6Td6xFlUTaV0yFnXWqoXpRapz2',NULL,NULL,2,1,'0310-2065288',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(71,'SAQIB ALI','M YOUSUF',NULL,NULL,NULL,'$2y$10$bKOVAjoDnOfbHEVWScwzWONpAiJDNDKr3Kv5vk.RaWpB1HKQ9UF6y',NULL,NULL,2,1,'0314-2092182',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(72,'M.RASHID','M.HANIF',NULL,NULL,NULL,'$2y$10$EGgcVu9I.RZh.gVxIYhAZOlm1UGpUzPWRkJtSiHAwrxvqAZEKUxTG',NULL,NULL,2,1,'0318-2068041',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(73,'M.AZEEM','M.ASLAM',NULL,NULL,NULL,'$2y$10$kIv3e5Iwj93xc5OdWP88Y.ZD46sRuxLNHDaeOdHbDKwDXCSubFGm.',NULL,NULL,2,1,'0319-4626158',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(74,'SHERYAR NOOR','M.NOOR',NULL,NULL,NULL,'$2y$10$a9aMsG2GI92l/tdfKC/Ow.jWzvFUMvN31CJQQfp5A5fjXjW3HQnXa',NULL,NULL,2,1,'0313-2146283',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(75,'SYED JUNAID ALI','SYED RAHAT ALI',NULL,NULL,NULL,'$2y$10$c86NmRbfxPgmsS6u9g224.epTv1psAP6hWx3Jmim8tltPhZda2CbW',NULL,NULL,2,1,'0315-3752590',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(76,'ASAD','AZHAR HUSSAIN',NULL,NULL,NULL,'$2y$10$gtAppepOjhjP3SGE09cMQO5X4.5y/XKvEiIZqv9HvdFsXzlgAdBtS',NULL,NULL,2,1,'0310-0307673',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(77,'ASIF NOMAN','ABDUL SAMAD',NULL,NULL,NULL,'$2y$10$13a4Bbvs38kA9sHCDuxHOOBbY8xC3jXFecag.l3MTXAhASiaYQ3gy',NULL,NULL,2,1,'0312-8135715',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(78,'KASHAN HUSSAIN','NAVEED HUSSAIN',NULL,NULL,NULL,'$2y$10$cO9Jty5qNxBAl9FtgrBe9u7jLpeRSXWLnyH4pwNYcngq.6vLbaiW2',NULL,NULL,2,1,'0312-3931273',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(79,'S.ASGHAR HUSSAIN JAFFRI','S.ARSHAD HUSSAIN',NULL,NULL,NULL,'$2y$10$lBm6ORdYACdgeWgpzYzt7OvlukAfaarAARfIXw7du2od1BU46qWrO',NULL,NULL,2,1,'0335-0310659',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(80,'M.AHSAN','M.SALEEM KHAN',NULL,NULL,NULL,'$2y$10$bC5r2MLom31AJ/VmNk66we4xODYk7EGSMidrWkwJLopAFEMZBZL4a',NULL,NULL,2,1,'0311-3128071',NULL,NULL,NULL,NULL,'2025-05-26 17:53:53','2025-05-26 17:53:53',NULL),(81,'M.WAQAR','FAHEEM UDDIN',NULL,NULL,NULL,'$2y$10$V5mOzj7fo2ZzD6Nwuv7YYeO3G12afwHOENzUvqlycs3Y4P/GDAs4O',NULL,NULL,2,1,'0310-2618101',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(82,'ABDULLAH','MUHAMMAD SALEEM',NULL,NULL,NULL,'$2y$10$I5oxVhtHrfaPF6A4MR6uxehvBNZNsAdQvw6tfKsjokWsEslM1J4cO',NULL,NULL,2,1,'0300-9208815',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(83,'MUHIB HUSSAIN','NAVEED HUSSAIN',NULL,NULL,NULL,'$2y$10$EWjMPrnD8/6I.5NB1IVucuHUQ4lEZ/.0v9LUyO4AKM/DcUWiEDVFe',NULL,NULL,2,1,'0312-3605883',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(84,'MUHAMMAD JAWWAD AHMED','MUHAMMAD IRFAN',NULL,NULL,NULL,'$2y$10$BtlyCf0SEsoNBEO8k4leUe60RMkUrUYmbjiDSI5.2dUIOkwm9c6Dq',NULL,NULL,2,1,'0311-2353777',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(85,'FARHAN ','MUHAMMAD SAMI',NULL,NULL,NULL,'$2y$10$8I3TtGXssiR1wyUjLLNYIe.FDHBRvmcsYeFsJl.f0wuoibSz9Pg1K',NULL,NULL,2,1,'0311-7765671',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(86,'MUHAMMAD SAQIB','MUHAMMAD RAFIQ',NULL,NULL,NULL,'$2y$10$rXbpHLlHvOLWUp5DUbRjm.cQfYbT9CX90r6b745PMxYGwo8Hqejqe',NULL,NULL,2,1,'0311-3243045',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(87,'DANISH','M.SAGHEER',NULL,NULL,NULL,'$2y$10$8fzq0JMejlV2oN57/WkwB.qRZD.I8QU/all4bkStoiSypCH0HHJMS',NULL,NULL,2,1,'0343-1854910',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(88,'M.ARSHAD HUSSAIN','KHADIM HUSSAIN',NULL,NULL,NULL,'$2y$10$JTpBrk/aWA4dZ7Rd4VdmLOSg.zN2Xoan5Haxhfra/UX..71sBKeBq',NULL,NULL,2,1,'0318-3755795',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(89,'USAMA SIDDIQUI','SAMI ULLAH',NULL,NULL,NULL,'$2y$10$yZN6KKq9FqRCdZ35ozAhi.d0kDzhisMO.o6RNoToL/lvSCykNN4q6',NULL,NULL,2,1,'0304-2109579',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(90,'TOUSEEF AHMED','FAREED AHMED',NULL,NULL,NULL,'$2y$10$G9mMqlU.z1gie9grzmVGJuQPNFIfCClrfTPseTcBWTpjU7rPK0Qba',NULL,NULL,2,1,'0318-2018162',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(91,'M.AYAZ','ABDUL REHMAN',NULL,NULL,NULL,'$2y$10$fcEXOrKd3DChOBe0.yr7nOwrCduqoA4LcLvFPjQ62EHsiMMm1tdeq',NULL,NULL,2,1,'0317-2301015',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(92,'HASSAN HUSSAIN','AZHAR HUSSAIN',NULL,NULL,NULL,'$2y$10$3YNW.SsI1WVTPKQeKJGbfevn9lJEJjYrAwTmegL/szWKuMtR/1vwi',NULL,NULL,2,1,'0317-1837975',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(93,'FAHAD KHAN','ABDUL HAFEEZ KHAN',NULL,NULL,NULL,'$2y$10$A7e2HpEjmQr3HPE6tVw73eOk6l3ZKmgb6bKRbNjeiLRmfiUq.iace',NULL,NULL,2,1,'0310-2639542',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(94,'M.USAMA','M.RASHEED',NULL,NULL,NULL,'$2y$10$w6jeD8TWhXgrtPoZ4Xpu1en1JVDnp8z/IFqKybPia0BwehKOoP0Sq',NULL,NULL,2,1,'0316-2297511',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(95,'M.ABDUL WASEEM','M.ABDUL AZEEM',NULL,NULL,NULL,'$2y$10$MD0TddYvyFMIFsDxq3oRYO7JgVKeBjhXftqq9fVOIMzJlR4KVURj.',NULL,NULL,2,1,'0315-2140152',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(96,'M SAJID','M SADIQ',NULL,NULL,NULL,'$2y$10$KDgC074HhkfFqcYG6ctVaOcSZxoBGgS6NRTDMDPGNe2FUn3bPRyze',NULL,NULL,2,1,'0312-2303960',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(97,'SHEMUS','ZAFAR MASIH',NULL,NULL,NULL,'$2y$10$vitx7sbZfti16ws.C0/qmu9TIPiRPqyd9mA0mUzedk0TEWGael626',NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(98,'MIRZA YASIR BAIG','MIRZA ANEES BAIG',NULL,NULL,NULL,'$2y$10$a/.bs.w2b7usFh4DIERxqephzEyVDq2P3mi2volFjXVE6kDaoo7Eu',NULL,NULL,2,1,'0345-6074340',NULL,NULL,NULL,NULL,'2025-05-26 17:53:54','2025-05-26 17:53:54',NULL),(99,'M HAMMAD','M EJAZ',NULL,NULL,NULL,'$2y$10$ZlTC68pZj1TwoUueab3Uz.RVYRKUG1V.L0w0hnhF5xXwoZG8Ny5KO',NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(100,'M MUSTAFA','ALTAF HUSSAIN',NULL,NULL,NULL,'$2y$10$aPL36W9lqKgiicmlgc0hRuDojjDEdT5uDwfluVWOKlHtCAznEYmrS',NULL,NULL,2,1,'0336-1843366',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(101,'M.NAVEED','NOOR M.ABBASI',NULL,NULL,NULL,'$2y$10$nH0wTGf3nSlVImBGKM1Hg.cvZE8wAomG7/kxfQI831N7dI.Qi92jO',NULL,NULL,2,1,'0310-0273594',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(102,'M. AHTISHAM','M. SALEEM',NULL,NULL,NULL,'$2y$10$QYWP7huifWYHPRwmKyTUeexM0F7ohE0IOQ87IJaghLX1K1EpGn0Jy',NULL,NULL,2,1,'0317-8564846',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(103,'USAMA SIDDIQUI','MUHAMMAD ASLAM SIDDIQUI',NULL,NULL,NULL,'$2y$10$oRIqr2nBG2XaXL6Suhggn.ZpnU97DUHDeR9POReDgdOO4XPqtEWR2',NULL,NULL,2,1,'0316-2406469',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(104,'HASNAIN ALI','REHMAT ALI',NULL,NULL,NULL,'$2y$10$MWoRemiZMpKxkB25/1YU.eBJE3m8YHZKiXFJLwh5TEy0bZOI.M.wy',NULL,NULL,2,1,'0318-2149535',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(105,'M.FAZEEL','M.SABIR',NULL,NULL,NULL,'$2y$10$O8svqIw2E3FHHtDZdK11fuxyXKCV4eB08sAu9qZ58U5y2HEmwhIYy',NULL,NULL,2,1,'0310-1188282',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(106,'M.KAMRAN','M.RAMZAN',NULL,NULL,NULL,'$2y$10$PcARJj/LmzboHzabXRwVK.834KWL7awJXL4Iz88jmlLmMc.mTqp/S',NULL,NULL,2,1,'0315-8391635',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(107,'M.IMRAN','BABU KHAN',NULL,NULL,NULL,'$2y$10$rFZSF5VtjAqgyQKXkjhNdugjjWgKervNXEgUi3Cm8T1Qls/UgW7pi',NULL,NULL,2,1,'0310-2388576',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(108,'M.BASHEER','AZMAT',NULL,NULL,NULL,'$2y$10$8vkLmJ5XRuI3JDKjsHKJuOPmbmptYwHYljnzWLq.t8FA5O6nvwWtW',NULL,NULL,2,1,'0335-0214680',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(109,'M.ASLAM','AZAR KHAN',NULL,NULL,NULL,'$2y$10$Y8rzWMW36PNDO3iIxbKmtuf0zO/KA0oeqtP/JQFRXF.aSXBQgo.da',NULL,NULL,2,1,'0311-3823918',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(110,'S.GHULAM ABBAS','S.RAHBER HUSSAIN',NULL,NULL,NULL,'$2y$10$rnLvd12n7qT8QUzixNSV2.ylxWBeOnTSrui/rVcZj9/GA5RztI3qy',NULL,NULL,2,1,'0348-2314565',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(111,'ASIM KHAN','M.MAQSOOD KHAN',NULL,NULL,NULL,'$2y$10$2OVYAAk9kcKfaORlN2z4ou6Q/S8RcfTju.LkuB70j4A9OXbPEtGAO',NULL,NULL,2,1,'0310-2384677',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(112,'ATIF MEHMOOD','PARVAIZ MAHMOOD',NULL,NULL,NULL,'$2y$10$5IhQgfHlfkz4X9S4asO65e752r3arrlzHqObjAZcD8DkMjD1/X/Uy',NULL,NULL,2,1,'0314-2798256',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(113,'ASAD HASNAIN','M.ARSHAD',NULL,NULL,NULL,'$2y$10$snoWVYkO8RPMLHeCgxM7cOlouCoRB1jLZrES7qNSPWA2bbNAwV.92',NULL,NULL,2,1,'0370-1001653',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(114,'M.ARHAM','M.ASIF',NULL,NULL,NULL,'$2y$10$j6wfPLLFQjKyYS1P0vNNWeTqodzdcdVfS1CFdc54f/APBm5FLXCpW',NULL,NULL,2,1,'0312-8972200',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(115,'MUHAMMAD AHMED RAZA','ZAKIR HUSSAIN',NULL,NULL,NULL,'$2y$10$jrXA6yRRNy.Zaq4G6pO43.XiabmmFH3v9hSYPN8R.1hvqC/bhIdAO',NULL,NULL,2,1,'0312-2798674',NULL,NULL,NULL,NULL,'2025-05-26 17:53:55','2025-05-26 17:53:55',NULL),(116,'HASHAM RAZA','NAFEES AHMED',NULL,NULL,NULL,'$2y$10$dL1WPPCp2CONRtypBErXZ.cDKOxkG9FBZZnyRYTbCNZo4tCgOZOvi',NULL,NULL,2,1,'0311-1069305',NULL,NULL,NULL,NULL,'2025-05-26 17:53:56','2025-05-26 17:53:56',NULL),(117,'WAJAHAT ALI','LIAQUAT ALI',NULL,NULL,NULL,'$2y$10$ZsXNa.aCllOs6hxSdH7dBe0yy8qJoINOPnKwpoG.k6X4MgJ97MrVC',NULL,NULL,2,1,'0315-8489339',NULL,NULL,NULL,NULL,'2025-05-26 17:53:56','2025-05-26 17:53:56',NULL),(118,'ABDUL RAHEEM ','A',NULL,NULL,NULL,'$2y$10$RDpzYdumcEKEPWz.u4QAuOgM2N8f0AFmE9zlIoMKmWXZK6OA37aBC',NULL,NULL,2,1,NULL,NULL,NULL,NULL,NULL,'2025-05-26 17:53:56','2025-05-26 17:53:56',NULL),(119,'NASEER ','GULAM RASOOL',NULL,NULL,NULL,'$2y$10$vpcrvUgNgN6Wr/.djtLu2esrre6DJeBNLHV2oMFLLpuG7WzRYtcN.',NULL,NULL,2,1,'0316-8531748',NULL,NULL,NULL,NULL,'2025-05-26 17:53:56','2025-05-26 17:53:56',NULL),(120,'MOHAMMAD MOHEUDDIN QADRI','Muhammad Shakeel',NULL,NULL,NULL,'$2y$10$u0.2TtzJUeyQHtvMIqZyZuKdizjFRdMbdY6z014RrqTFEsQHE09Uu',NULL,NULL,2,1,'0333-3304842',NULL,NULL,NULL,NULL,'2025-05-26 17:53:56','2025-05-26 17:53:56',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warnings`
--

DROP TABLE IF EXISTS `warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warnings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `company_id` bigint(20) unsigned NOT NULL,
  `warning_to` bigint(20) unsigned NOT NULL,
  `warning_type` bigint(20) unsigned DEFAULT NULL,
  `warning_date` date NOT NULL,
  `status` varchar(40) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warnings_company_id_foreign` (`company_id`),
  KEY `warnings_warning_to_foreign` (`warning_to`),
  KEY `warnings_warning_type_foreign` (`warning_type`),
  CONSTRAINT `warnings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warnings_warning_to_foreign` FOREIGN KEY (`warning_to`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warnings_warning_type_foreign` FOREIGN KEY (`warning_type`) REFERENCES `warnings_type` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warnings`
--

LOCK TABLES `warnings` WRITE;
/*!40000 ALTER TABLE `warnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `warnings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warnings_type`
--

DROP TABLE IF EXISTS `warnings_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `warnings_type` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `warning_title` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warnings_type`
--

LOCK TABLES `warnings_type` WRITE;
/*!40000 ALTER TABLE `warnings_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `warnings_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'u902429527_ttphrm'
--

--
-- Dumping routines for database 'u902429527_ttphrm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-30  9:26:43
