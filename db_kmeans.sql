-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: db_kmeans_clustering
-- ------------------------------------------------------
-- Server version	8.0.35

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('admin@coder.com|127.0.0.1','i:1;',1774237210),('admin@coder.com|127.0.0.1:timer','i:1774237210;',1774237210),('dani@coder.com|127.0.0.1','i:1;',1774153480),('dani@coder.com|127.0.0.1:timer','i:1774153480;',1774153480);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calculation_logs`
--

DROP TABLE IF EXISTS `calculation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `calculation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `k_value` int NOT NULL,
  `dbi_score` double DEFAULT NULL,
  `total_iterations` int DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calculation_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `calculation_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calculation_logs`
--

LOCK TABLES `calculation_logs` WRITE;
/*!40000 ALTER TABLE `calculation_logs` DISABLE KEYS */;
INSERT INTO `calculation_logs` VALUES (2,NULL,3,0.39364964213812,NULL,NULL,'2026-03-23 15:06:20','2026-03-23 15:06:20'),(3,3,4,0.1908603403792,100,'Perhitungan K-Means dengan K=4','2026-03-23 15:18:17','2026-03-23 15:18:17'),(4,3,3,0.39364964213812,4,'Perhitungan K-Means dengan K=3','2026-03-23 15:18:58','2026-03-23 15:18:58'),(5,3,3,0.39364964213812,3,'Perhitungan K-Means dengan K=3','2026-03-23 15:19:38','2026-03-23 15:19:38'),(6,3,3,0.39364964213812,2,'Perhitungan K-Means dengan K=3','2026-03-23 15:19:39','2026-03-23 15:19:39'),(7,3,3,0.39364964213812,2,'Perhitungan K-Means dengan K=3','2026-03-23 15:20:02','2026-03-23 15:20:02'),(8,3,3,0.39364964213812,2,'Perhitungan K-Means dengan K=3','2026-03-23 15:20:03','2026-03-23 15:20:03'),(9,3,3,0.68149216493532,2,'Perhitungan K-Means dengan K=3','2026-03-24 19:29:39','2026-03-24 19:29:39');
/*!40000 ALTER TABLE `calculation_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calculation_result`
--

DROP TABLE IF EXISTS `calculation_result`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `calculation_result` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `calculation_log_id` bigint unsigned NOT NULL,
  `cluster_number` int NOT NULL,
  `snapshot_data` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calculation_result_calculation_log_id_foreign` (`calculation_log_id`),
  CONSTRAINT `calculation_result_calculation_log_id_foreign` FOREIGN KEY (`calculation_log_id`) REFERENCES `calculation_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calculation_result`
--

LOCK TABLES `calculation_result` WRITE;
/*!40000 ALTER TABLE `calculation_result` DISABLE KEYS */;
INSERT INTO `calculation_result` VALUES (1,2,1,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:06:20','2026-03-23 15:06:20'),(2,2,2,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:06:20','2026-03-23 15:06:20'),(3,2,3,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:06:20','2026-03-23 15:06:20'),(4,2,3,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:06:20','2026-03-23 15:06:20'),(5,2,3,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:06:20','2026-03-23 15:06:20'),(6,3,1,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:18:17','2026-03-23 15:18:17'),(7,3,2,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:18:17','2026-03-23 15:18:17'),(8,3,2,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:18:17','2026-03-23 15:18:17'),(9,3,3,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:18:17','2026-03-23 15:18:17'),(10,3,4,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:18:17','2026-03-23 15:18:17'),(11,4,1,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:18:58','2026-03-23 15:18:58'),(12,4,2,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:18:58','2026-03-23 15:18:58'),(13,4,2,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:18:58','2026-03-23 15:18:58'),(14,4,2,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:18:58','2026-03-23 15:18:58'),(15,4,3,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:18:58','2026-03-23 15:18:58'),(16,5,1,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:19:38','2026-03-23 15:19:38'),(17,5,2,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:19:38','2026-03-23 15:19:38'),(18,5,3,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:19:38','2026-03-23 15:19:38'),(19,5,3,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:19:38','2026-03-23 15:19:38'),(20,5,3,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:19:38','2026-03-23 15:19:38'),(21,6,1,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:19:39','2026-03-23 15:19:39'),(22,6,1,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:19:39','2026-03-23 15:19:39'),(23,6,1,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:19:39','2026-03-23 15:19:39'),(24,6,2,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:19:39','2026-03-23 15:19:39'),(25,6,3,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:19:39','2026-03-23 15:19:39'),(26,7,1,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:20:02','2026-03-23 15:20:02'),(27,7,2,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:20:02','2026-03-23 15:20:02'),(28,7,2,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:20:02','2026-03-23 15:20:02'),(29,7,2,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:20:02','2026-03-23 15:20:02'),(30,7,3,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:20:02','2026-03-23 15:20:02'),(31,8,1,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-23 15:20:03','2026-03-23 15:20:03'),(32,8,2,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 3, \"v2\": 2}, \"student_id\": 6}','2026-03-23 15:20:03','2026-03-23 15:20:03'),(33,8,2,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 2, \"v3\": 4}, \"student_id\": 14}','2026-03-23 15:20:03','2026-03-23 15:20:03'),(34,8,2,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-23 15:20:03','2026-03-23 15:20:03'),(35,8,3,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-23 15:20:03','2026-03-23 15:20:03'),(36,9,1,'{\"nis\": \"c2\", \"name\": \"sad\", \"scores\": {\"v1\": 1.5, \"v2\": 2, \"v3\": 3}, \"student_id\": 13}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(37,9,2,'{\"nis\": \"q3tr\", \"name\": \"fdsf\", \"scores\": {\"v1\": 2.5, \"v2\": 3.5, \"v3\": 3.5}, \"student_id\": 3}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(38,9,2,'{\"nis\": \"betb\", \"name\": \"fsbsdvs\", \"scores\": {\"v1\": 3, \"v2\": 3, \"v3\": 5}, \"student_id\": 8}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(39,9,2,'{\"nis\": \"c1\", \"name\": \"udin\", \"scores\": {\"v1\": 4, \"v2\": 5, \"v3\": 5}, \"student_id\": 12}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(40,9,2,'{\"nis\": \"c4\", \"name\": \"rgw\", \"scores\": {\"v1\": 4, \"v2\": 2, \"v3\": 4}, \"student_id\": 15}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(41,9,3,'{\"nis\": \"qwdasd\", \"name\": \"ascac\", \"scores\": {\"v1\": 1, \"v2\": 4.333333333333333, \"v3\": 2}, \"student_id\": 6}','2026-03-24 19:29:39','2026-03-24 19:29:39'),(42,9,3,'{\"nis\": \"c3\", \"name\": \"asd\", \"scores\": {\"v1\": 3, \"v2\": 5, \"v3\": 1.6666666666666667}, \"student_id\": 14}','2026-03-24 19:29:39','2026-03-24 19:29:39');
/*!40000 ALTER TABLE `calculation_result` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criteria`
--

DROP TABLE IF EXISTS `criteria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `criteria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `weight` double NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `criteria_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criteria`
--

LOCK TABLES `criteria` WRITE;
/*!40000 ALTER TABLE `criteria` DISABLE KEYS */;
INSERT INTO `criteria` VALUES (2,'v1','fokus',1,'2026-03-22 18:44:09','2026-03-22 18:44:09'),(5,'v2','caca',1,'2026-03-22 19:33:07','2026-03-22 19:33:07'),(6,'v3','casc',1,'2026-03-22 19:39:40','2026-03-22 19:39:40');
/*!40000 ALTER TABLE `criteria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criterion_scales`
--

DROP TABLE IF EXISTS `criterion_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `criterion_scales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `criterion_id` bigint unsigned NOT NULL,
  `scale_value` int NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `criterion_scales_criterion_id_foreign` (`criterion_id`),
  CONSTRAINT `criterion_scales_criterion_id_foreign` FOREIGN KEY (`criterion_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criterion_scales`
--

LOCK TABLES `criterion_scales` WRITE;
/*!40000 ALTER TABLE `criterion_scales` DISABLE KEYS */;
INSERT INTO `criterion_scales` VALUES (1,2,1,'zzzz','2026-03-22 19:24:34','2026-03-22 19:24:34'),(2,2,2,'wrwet','2026-03-22 19:24:34','2026-03-22 19:24:34'),(3,2,3,'wfrwbet','2026-03-22 19:24:34','2026-03-22 19:24:34'),(4,2,4,'bten','2026-03-22 19:24:34','2026-03-22 19:24:34'),(5,2,5,'ryntynty','2026-03-22 19:24:34','2026-03-22 19:24:34'),(11,5,1,'c d','2026-03-22 19:33:07','2026-03-22 19:33:07'),(12,5,2,'fxc sd','2026-03-22 19:33:07','2026-03-22 19:33:07'),(13,5,3,'sd sd','2026-03-22 19:33:07','2026-03-22 19:33:07'),(14,5,4,'s zx z','2026-03-22 19:33:07','2026-03-22 19:33:07'),(15,5,5,'x zx zx','2026-03-22 19:33:07','2026-03-22 19:33:07'),(16,6,1,'v ds','2026-03-22 19:39:40','2026-03-22 19:39:40'),(17,6,2,'s sc','2026-03-22 19:39:40','2026-03-22 19:39:40'),(18,6,3,'sdv','2026-03-22 19:39:40','2026-03-22 19:39:40'),(19,6,4,'sc s','2026-03-22 19:39:40','2026-03-22 19:39:40'),(20,6,5,'sf sf','2026-03-22 19:39:40','2026-03-22 19:39:40');
/*!40000 ALTER TABLE `criterion_scales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_11_050647_create_student_table',1),(5,'2026_03_11_050707_create_criteria_table',1),(6,'2026_03_11_050727_create_student_score_table',1),(7,'2026_03_11_050759_create_calculation_logs_table',1),(8,'2026_03_11_050835_create_calculation_result_table',1),(9,'2026_03_23_031058_create_criterion_scales_table',2),(10,'2026_03_24_121825_add_teacher_id_to_student_score_table',3),(11,'2026_03_25_025204_add_is_active_to_users_table',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('rnjGPfhFCBpvTqBrvHzxtl5NJdXYKo7CuPI0VTIZ',6,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMkJGZ1BJQjc4Z3JTUHM2SUl1SXQwZFJDY1FUMklkN3dXRUxlOHVGcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzA6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9yZWdpc3RlciI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjY7fQ==',1774358913),('WjcBzonRG2f96l8nBZmMOjYjI8R3n3PKsxOr0HCI',3,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiY2M0Z3RCWjIyaFRIdmg3anBDVTRlbEdtSmZ2OUh4dzVyRVZldVFjbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9rbWVhbnMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc3NDQwODA5OTt9fQ==',1774415219);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('L','P') DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_student_id_unique` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES (1,'123','yono','P',1,'2026-03-22 15:18:07','2026-03-24 04:56:15'),(2,'651','gim','L',1,'2026-03-22 15:18:27','2026-03-22 15:18:45'),(3,'q3tr','fdsf','P',1,'2026-03-22 15:32:20','2026-03-22 15:32:20'),(4,'fsdvs','vsvsd','L',1,'2026-03-22 15:32:26','2026-03-22 15:32:26'),(5,'vsdvs','sdvsd','L',1,'2026-03-22 15:32:36','2026-03-22 15:32:36'),(6,'qwdasd','ascac','L',1,'2026-03-22 15:32:54','2026-03-22 15:32:54'),(7,'caxca','xcasc','P',1,'2026-03-22 15:33:04','2026-03-22 15:33:04'),(8,'betb','fsbsdvs','L',1,'2026-03-22 15:33:12','2026-03-22 15:33:12'),(9,'scx','csdcsd','L',0,'2026-03-22 15:33:20','2026-03-22 15:41:50'),(10,'32r2','fdscsdc','L',0,'2026-03-22 15:33:27','2026-03-22 15:41:48'),(11,'ceg5r','bebsdc','L',0,'2026-03-22 15:33:36','2026-03-22 15:41:45'),(12,'c1','udin','L',1,'2026-03-22 18:25:53','2026-03-22 18:25:53'),(13,'c2','sad','P',1,'2026-03-22 18:25:53','2026-03-22 18:25:53'),(14,'c3','asd','L',1,'2026-03-22 18:25:53','2026-03-22 18:25:53'),(15,'c4','rgw','P',1,'2026-03-22 18:25:53','2026-03-22 18:25:53');
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_score`
--

DROP TABLE IF EXISTS `student_score`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `student_score` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint unsigned NOT NULL,
  `criterion_id` bigint unsigned NOT NULL,
  `score` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `teacher_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_score_student_id_foreign` (`student_id`),
  KEY `student_score_criterion_id_foreign` (`criterion_id`),
  KEY `student_score_teacher_id_foreign` (`teacher_id`),
  CONSTRAINT `student_score_criterion_id_foreign` FOREIGN KEY (`criterion_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_score_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_score_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_score`
--

LOCK TABLES `student_score` WRITE;
/*!40000 ALTER TABLE `student_score` DISABLE KEYS */;
INSERT INTO `student_score` VALUES (1,6,2,1,'2026-03-22 18:44:42','2026-03-24 04:46:30',5),(4,14,2,3,'2026-03-22 19:36:57','2026-03-24 04:46:38',5),(6,14,5,5,'2026-03-22 19:36:57','2026-03-24 04:46:38',5),(7,6,5,5,'2026-03-22 19:37:06','2026-03-24 04:46:30',NULL),(8,14,6,1,'2026-03-22 19:40:17','2026-03-24 04:46:38',NULL),(9,15,2,4,'2026-03-23 07:40:33','2026-03-23 07:51:13',NULL),(10,15,5,2,'2026-03-23 07:40:33','2026-03-23 07:51:13',NULL),(11,15,6,4,'2026-03-23 07:40:33','2026-03-23 07:51:13',NULL),(12,13,2,1,'2026-03-23 07:51:13','2026-03-23 07:51:13',NULL),(13,13,5,2,'2026-03-23 07:51:13','2026-03-23 07:51:13',NULL),(14,13,6,3,'2026-03-23 07:51:13','2026-03-23 07:51:13',NULL),(15,12,2,4,'2026-03-23 07:51:49','2026-03-23 07:51:49',NULL),(16,12,5,5,'2026-03-23 07:51:49','2026-03-23 07:51:49',NULL),(17,12,6,5,'2026-03-23 07:51:49','2026-03-23 07:51:49',NULL),(18,6,2,1,'2026-03-24 04:44:00','2026-03-24 04:44:00',4),(19,6,5,3,'2026-03-24 04:44:00','2026-03-24 04:44:00',4),(20,6,6,2,'2026-03-24 04:44:00','2026-03-24 04:44:00',4),(21,14,2,3,'2026-03-24 04:44:10','2026-03-24 04:44:10',4),(22,14,5,5,'2026-03-24 04:44:10','2026-03-24 04:44:10',4),(23,14,6,3,'2026-03-24 04:44:10','2026-03-24 04:44:10',4),(24,6,5,5,'2026-03-24 04:44:40','2026-03-24 04:44:40',5),(25,6,6,2,'2026-03-24 04:44:40','2026-03-24 04:44:40',5),(26,14,6,1,'2026-03-24 04:44:50','2026-03-24 04:44:50',5),(27,3,2,3,'2026-03-24 05:18:13','2026-03-24 05:18:13',5),(28,3,5,4,'2026-03-24 05:18:13','2026-03-24 05:18:13',5),(29,3,6,3,'2026-03-24 05:18:13','2026-03-24 05:18:13',5),(30,3,2,2,'2026-03-24 05:18:40','2026-03-24 05:18:40',4),(31,3,5,3,'2026-03-24 05:18:40','2026-03-24 05:18:40',4),(32,3,6,4,'2026-03-24 05:18:40','2026-03-24 05:18:40',4),(33,13,2,2,'2026-03-24 05:27:11','2026-03-24 05:27:11',5),(34,13,5,2,'2026-03-24 05:27:11','2026-03-24 05:27:11',5),(35,13,6,3,'2026-03-24 05:27:11','2026-03-24 05:27:11',5),(36,8,2,3,'2026-03-24 19:28:24','2026-03-24 19:28:24',4),(37,8,5,3,'2026-03-24 19:28:24','2026-03-24 19:28:24',4),(38,8,6,5,'2026-03-24 19:28:24','2026-03-24 19:28:24',4);
/*!40000 ALTER TABLE `student_score` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'dheny','denicahyono13@gmail.com',NULL,'$2y$12$RxVW2AtuNVj9n7cDBEejROdRUytMqOp93Ipd6Fyb9Zug0rADyoley','admin',0,NULL,'2026-03-21 19:35:51','2026-03-24 19:16:33'),(2,'Test User','test@example.com','2026-03-21 20:20:49','$2y$12$ktTJzn48glMBZoxEojKoDOZAuKnD857Wn93H.otr3LU8JB18lH85i','teacher',1,'QGaU1cKmCf','2026-03-21 20:20:49','2026-03-24 19:19:00'),(3,'Admin Codero','admin@codero.com',NULL,'$2y$12$UzZz3o2Zh8POLJAkkFYQ8ODSZo7kYvY1sOBv91av.vFlGnzLgkE0u','admin',1,NULL,'2026-03-21 20:20:50','2026-03-21 20:20:50'),(4,'Dani Cahyono','dani@codero.com',NULL,'$2y$12$2Z6EexKb7eRRzcDt40MIZOMJ1saHDSP75wA9flPwHRCj21pklEeO.','teacher',1,NULL,'2026-03-21 20:20:50','2026-03-24 19:16:18'),(5,'yono','yono@codero.com',NULL,'$2y$12$mR7SSWo7.MOMYogz6M7Be.BD93MhCLsHkcShO8910lUIuGrcUMdBy','teacher',1,NULL,'2026-03-24 04:05:26','2026-03-24 04:05:26'),(6,'dhino','dhino@codero.com',NULL,'$2y$12$xPFuyOYJS850D9L7yuqgMOJOg8nCsY//jVKnVi/fyvfUfIr1ZdT1a','teacher',1,NULL,'2026-03-24 05:28:33','2026-03-24 19:18:43');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-26 17:44:42
