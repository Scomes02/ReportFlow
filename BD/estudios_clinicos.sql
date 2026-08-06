CREATE DATABASE  IF NOT EXISTS `estudios_clinicos` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `estudios_clinicos`;
-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: estudios_clinicos
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `adendas`
--

DROP TABLE IF EXISTS `adendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adendas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `estudio_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adendas_estudio_id_index` (`estudio_id`),
  KEY `adendas_medico_id_index` (`medico_id`),
  CONSTRAINT `adendas_estudio_id_foreign` FOREIGN KEY (`estudio_id`) REFERENCES `estudios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `adendas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adendas`
--

LOCK TABLES `adendas` WRITE;
/*!40000 ALTER TABLE `adendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `adendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `archivos_estudio`
--

DROP TABLE IF EXISTS `archivos_estudio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `archivos_estudio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `estudio_id` bigint unsigned NOT NULL,
  `disco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamano_bytes` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `archivos_estudio_estudio_id_index` (`estudio_id`),
  CONSTRAINT `archivos_estudio_estudio_id_foreign` FOREIGN KEY (`estudio_id`) REFERENCES `estudios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archivos_estudio`
--

LOCK TABLES `archivos_estudio` WRITE;
/*!40000 ALTER TABLE `archivos_estudio` DISABLE KEYS */;
INSERT INTO `archivos_estudio` VALUES (1,12,'estudios','estudios/12/WVEGMqNjr6yU1XVozMJitNDk70jZ5qNk05HEhp2U.pdf','tp-1.pdf','application/pdf',427203,'2026-08-05 16:57:15','2026-08-05 16:57:15');
/*!40000 ALTER TABLE `archivos_estudio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `especialidades` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidades`
--

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
INSERT INTO `especialidades` VALUES (1,'Cardiología','cardiologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(2,'Neumonología','neumonologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(3,'Neurología','neurologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(4,'Traumatología','traumatologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(5,'Oftalmología','oftalmologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(6,'Gastroenterología','gastroenterologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(7,'Dermatología','dermatologia','2026-08-05 12:24:33','2026-08-05 12:24:33'),(8,'Psiquiatría','psiquiatria','2026-08-05 12:24:33','2026-08-05 12:24:33');
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudios`
--

DROP TABLE IF EXISTS `estudios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estudios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `paciente_nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_edad` tinyint unsigned NOT NULL,
  `tipo_estudio_id` bigint unsigned NOT NULL,
  `tecnico_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nuevo',
  `fecha_estudio` datetime NOT NULL,
  `informe` text COLLATE utf8mb4_unicode_ci,
  `motivo_rechazo` text COLLATE utf8mb4_unicode_ci,
  `firmado_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudios_tipo_estudio_id_index` (`tipo_estudio_id`),
  KEY `estudios_tecnico_id_index` (`tecnico_id`),
  KEY `estudios_medico_id_index` (`medico_id`),
  KEY `estudios_estado_index` (`estado`),
  KEY `estudios_paciente_dni_index` (`paciente_dni`),
  CONSTRAINT `estudios_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estudios_tecnico_id_foreign` FOREIGN KEY (`tecnico_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `estudios_tipo_estudio_id_foreign` FOREIGN KEY (`tipo_estudio_id`) REFERENCES `tipos_estudio` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudios`
--

LOCK TABLES `estudios` WRITE;
/*!40000 ALTER TABLE `estudios` DISABLE KEYS */;
INSERT INTO `estudios` VALUES (12,'seba','30343176',43,96,12,NULL,'nuevo','2026-08-22 14:00:00',NULL,NULL,NULL,'2026-08-05 16:57:15','2026-08-05 16:57:15'),(26,'Ana Rodríguez','12345678',45,81,12,16,'informado','2026-08-03 21:34:01','Electrocardiograma - Ritmo sinusal normal, sin alteraciones',NULL,'2026-08-04 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(27,'Carlos Gómez','87654321',52,82,12,16,'informado','2026-08-02 21:34:01','Holter 24hs - Sin alteraciones significativas',NULL,'2026-08-03 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(28,'María López','11223344',38,83,12,16,'informado','2026-08-04 21:34:01','Ecocardiograma - Función ventricular preservada',NULL,'2026-08-05 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(29,'José Fernández','99887766',61,84,12,16,'informado','2026-08-01 21:34:01','Test de Esfuerzo - Respuesta cardíaca adecuada',NULL,'2026-08-02 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(30,'Laura Martínez','55443322',29,85,12,16,'informado','2026-07-31 21:34:01','Monitoreo Ambulatorio - Valores dentro de rango normal',NULL,'2026-08-01 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(31,'Pedro Sánchez','66778899',73,81,12,16,'informado','2026-07-30 21:34:01','Electrocardiograma - Fibrilación auricular controlada',NULL,'2026-07-31 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(32,'Sofía Ramírez','33445566',34,82,12,16,'informado','2026-07-29 21:34:01','Holter 24hs - Bradicardia sinusal',NULL,'2026-07-30 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(33,'Miguel Torres','22334455',47,83,12,16,'informado','2026-07-28 21:34:01','Ecocardiograma - Hipertrofia ventricular leve',NULL,'2026-07-29 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(34,'Elena Díaz','77889900',55,84,12,16,'informado','2026-07-27 21:34:01','Test de Esfuerzo - Isquemia inducible',NULL,'2026-07-28 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(35,'Roberto Castro','44556677',42,85,12,16,'informado','2026-07-26 21:34:01','Monitoreo Ambulatorio - Hipertensión sistólica aislada',NULL,'2026-07-27 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01');
/*!40000 ALTER TABLE `estudios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('7W7PRTNyt0ptnGwcceoPJbM1j4NArm7LHzKYH0eu',13,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.131.0 Chrome/148.0.7778.280 Electron/42.7.0 Safari/537.36','eyJfdG9rZW4iOiJyZjFjSkxCU1JFM1BRNjRRcGhNMTFqNFFEWFpyZm1iZnRMT1U5TWY1IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDAwXC9ycmhoXC9hcmNoaXZvIiwicm91dGUiOiJycmhoLmFyY2hpdm8ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTN9',1786011960),('HyMWc2KRyvI2cscRDn9X1XDV9v6R8zDkfS2miDDa',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiI0ZkVuNDJyVkRVUlJvb0ZBdWRNY3RycXZlQzYzTFJHeVh3T25hNzFxIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1786018354),('pWUlTJO3MI1TDFvvDvZtCxVILPFkOFHDwq7m187I',13,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJEaktlWUlSaUt1aDAzZWhhSERYMXBad1ozVUlZeVNKMjB1SU9ENGJEIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL3JyaGhcL2xpcXVpZGFjaW9uXC8xNiIsInJvdXRlIjoicnJoaC5saXF1aWRhY2lvbi5kZXRhbGxlIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxM30=',1785981790);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_estudio`
--

DROP TABLE IF EXISTS `tipos_estudio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_estudio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `especialidad_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipos_estudio_especialidad_id_index` (`especialidad_id`),
  CONSTRAINT `tipos_estudio_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_estudio`
--

LOCK TABLES `tipos_estudio` WRITE;
/*!40000 ALTER TABLE `tipos_estudio` DISABLE KEYS */;
INSERT INTO `tipos_estudio` VALUES (81,1,'Electrocardiograma','2026-08-05 12:24:39','2026-08-05 12:24:39'),(82,1,'Holter 24hs','2026-08-05 12:24:39','2026-08-05 12:24:39'),(83,1,'Ecocardiograma','2026-08-05 12:24:39','2026-08-05 12:24:39'),(84,1,'Test de Esfuerzo','2026-08-05 12:24:39','2026-08-05 12:24:39'),(85,1,'Monitoreo Ambulatorio de Presión','2026-08-05 12:24:39','2026-08-05 12:24:39'),(86,2,'Espirometría','2026-08-05 12:24:44','2026-08-05 12:24:44'),(87,2,'Gasometría Arterial','2026-08-05 12:24:44','2026-08-05 12:24:44'),(88,2,'Prueba de Difusión Pulmonar','2026-08-05 12:24:44','2026-08-05 12:24:44'),(89,2,'Polisomnografía','2026-08-05 12:24:44','2026-08-05 12:24:44'),(90,3,'Electroencefalograma','2026-08-05 12:24:56','2026-08-05 12:24:56'),(91,3,'Electromiografía','2026-08-05 12:24:56','2026-08-05 12:24:56'),(92,3,'Potenciales Evocados','2026-08-05 12:24:56','2026-08-05 12:24:56'),(93,3,'Estudio de Conducción Nerviosa','2026-08-05 12:24:56','2026-08-05 12:24:56'),(94,4,'Radiografía','2026-08-05 12:25:02','2026-08-05 12:25:02'),(95,4,'Tomografía Computarizada','2026-08-05 12:25:02','2026-08-05 12:25:02'),(96,4,'Resonancia Magnética','2026-08-05 12:25:02','2026-08-05 12:25:02'),(97,4,'Densitometría Ósea','2026-08-05 12:25:02','2026-08-05 12:25:02'),(98,5,'Fondo de Ojo','2026-08-05 12:25:08','2026-08-05 12:25:08'),(99,5,'Campimetría','2026-08-05 12:25:08','2026-08-05 12:25:08'),(100,5,'Tomografía de Coherencia Óptica','2026-08-05 12:25:08','2026-08-05 12:25:08'),(101,5,'Ecografía Ocular','2026-08-05 12:25:08','2026-08-05 12:25:08');
/*!40000 ALTER TABLE `tipos_estudio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tecnico',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Sistema','admin@hospital.com','admin',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(2,'Dr. Juan Pérez','juan.perez@hospital.com','medico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(3,'Dr. María García','maria.garcia@hospital.com','medico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(4,'Téc. Carlos López','carlos.lopez@hospital.com','tecnico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(5,'Téc. Ana Martínez','ana.martinez@hospital.com','tecnico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(6,'RRHH Laura Díaz','laura.diaz@hospital.com','rrhh',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(7,'Call Center Pedro Ruiz','pedro.ruiz@hospital.com','callcenter',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(8,'Dr. Juan Pérez','juan.perez@reportflow.local','medico',NULL,'$2y$12$T2/T12sF9sP9sU9sU9sU9eU9sU9sU9sU9sU9u',NULL,'2026-08-05 12:05:25','2026-08-05 12:05:25'),(9,'Dra. María García','maria.garcia@reportflow.local','medico',NULL,'$2y$12$T2/T12sF9sP9sU9sU9sU9eU9sU9sU9sU9sU9u',NULL,'2026-08-05 12:05:25','2026-08-05 12:05:25'),(12,'Técnico Prueba','tecnico.prueba@reportflow.local','tecnico',NULL,'$2y$12$v.mPEwmbabtpmR.zObsDV.dJLoUudyBM7Z6oD7fb1EKS25oDHvY6.',NULL,'2026-08-05 12:20:51','2026-08-06 02:46:38'),(13,'RRHH - Administración','rrhh@reportflow.local','rrhh',NULL,'$2y$12$GW6rNKJV1SMubcjU9.HID.sLyhTjRAsLY4Oisesb14K7J4rYLsYMu',NULL,'2026-08-05 19:35:59','2026-08-06 02:46:39'),(16,'Dr. Juan Pérez','medico@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-05 23:36:11','2026-08-06 02:46:39');
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

-- Dump completed on 2026-08-06  9:48:58
