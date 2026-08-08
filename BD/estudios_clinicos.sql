-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: estudios_clinicos
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
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
  `contenido` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adendas_estudio_id_index` (`estudio_id`),
  KEY `adendas_medico_id_index` (`medico_id`),
  CONSTRAINT `adendas_estudio_id_foreign` FOREIGN KEY (`estudio_id`) REFERENCES `estudios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `adendas_medico_id_foreign` FOREIGN KEY (`medico_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adendas`
--

LOCK TABLES `adendas` WRITE;
/*!40000 ALTER TABLE `adendas` DISABLE KEYS */;
INSERT INTO `adendas` VALUES (1,28,16,'Esta bien','2026-08-08 18:11:10','2026-08-08 18:11:10');
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
  `disco` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_original` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tamano_bytes` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `archivos_estudio_estudio_id_index` (`estudio_id`),
  CONSTRAINT `archivos_estudio_estudio_id_foreign` FOREIGN KEY (`estudio_id`) REFERENCES `estudios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archivos_estudio`
--

LOCK TABLES `archivos_estudio` WRITE;
/*!40000 ALTER TABLE `archivos_estudio` DISABLE KEYS */;
INSERT INTO `archivos_estudio` VALUES (1,12,'estudios','estudios/12/WVEGMqNjr6yU1XVozMJitNDk70jZ5qNk05HEhp2U.pdf','tp-1.pdf','application/pdf',427203,'2026-08-05 16:57:15','2026-08-05 16:57:15'),(2,36,'estudios','estudios/36/zTM7SRT6Ip5BJZpeERVFAourMlbdG4yaSCGoC4W8.png','imagen_2026-08-07_124049395.png','image/png',18413,'2026-08-07 18:40:52','2026-08-07 18:40:52'),(3,105,'estudios','estudios/105/ykwVlEroKkeZqqe0awJ26ERO6DEhyWmqPHmrkrFs.png','icon.png','image/png',1338300,'2026-08-07 22:02:40','2026-08-07 22:02:40'),(4,106,'estudios','estudios/106/jyhuJdYuoGiXeZKSI8INPrUxa1ha6yBlecaJkeAy.png','icon.png','image/png',1338300,'2026-08-07 22:05:02','2026-08-07 22:05:02'),(5,107,'estudios','estudios/107/AabjFiFK1gBv2B38Kx9qF5EvmzzjlUs6a3PyCczn.png','icon.png','image/png',1338300,'2026-08-08 00:25:21','2026-08-08 00:25:21');
/*!40000 ALTER TABLE `archivos_estudio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `paciente_nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_dni` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `paciente_edad` tinyint unsigned NOT NULL,
  `tipo_estudio_id` bigint unsigned NOT NULL,
  `tecnico_id` bigint unsigned NOT NULL,
  `medico_id` bigint unsigned DEFAULT NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nuevo',
  `fecha_estudio` datetime NOT NULL,
  `informe` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `motivo_rechazo` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudios`
--

LOCK TABLES `estudios` WRITE;
/*!40000 ALTER TABLE `estudios` DISABLE KEYS */;
INSERT INTO `estudios` VALUES (12,'seba','30343176',43,96,12,NULL,'nuevo','2026-08-22 14:00:00',NULL,NULL,NULL,'2026-08-05 16:57:15','2026-08-05 16:57:15'),(26,'Ana Rodríguez','12345678',45,81,12,16,'informado','2026-08-03 21:34:01','Electrocardiograma - Ritmo sinusal normal, sin alteraciones',NULL,'2026-08-04 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(27,'Carlos Gómez','87654321',52,82,12,16,'informado','2026-08-02 21:34:01','Holter 24hs - Sin alteraciones significativas',NULL,'2026-08-03 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(28,'María López','11223344',38,83,12,16,'informado','2026-08-04 21:34:01','Ecocardiograma - Función ventricular preservada',NULL,'2026-08-05 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(29,'José Fernández','99887766',61,84,12,16,'informado','2026-08-01 21:34:01','Test de Esfuerzo - Respuesta cardíaca adecuada',NULL,'2026-08-02 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(30,'Laura Martínez','55443322',29,85,12,16,'informado','2026-07-31 21:34:01','Monitoreo Ambulatorio - Valores dentro de rango normal',NULL,'2026-08-01 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(31,'Pedro Sánchez','66778899',73,81,12,16,'informado','2026-07-30 21:34:01','Electrocardiograma - Fibrilación auricular controlada',NULL,'2026-07-31 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(32,'Sofía Ramírez','33445566',34,82,12,16,'informado','2026-07-29 21:34:01','Holter 24hs - Bradicardia sinusal',NULL,'2026-07-30 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(33,'Miguel Torres','22334455',47,83,12,16,'informado','2026-07-28 21:34:01','Ecocardiograma - Hipertrofia ventricular leve',NULL,'2026-07-29 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(34,'Elena Díaz','77889900',55,84,12,16,'informado','2026-07-27 21:34:01','Test de Esfuerzo - Isquemia inducible',NULL,'2026-07-28 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(35,'Roberto Castro','44556677',42,85,12,16,'informado','2026-07-26 21:34:01','Monitoreo Ambulatorio - Hipertensión sistólica aislada',NULL,'2026-07-27 21:34:01','2026-08-06 00:34:01','2026-08-06 00:34:01'),(36,'Lucas','26265648',20,88,13,NULL,'nuevo','2026-08-07 12:39:00',NULL,NULL,NULL,'2026-08-07 18:40:51','2026-08-07 18:40:51'),(37,'Tomás Álvarez','40111222',28,90,12,17,'informado','2026-08-01 10:00:00','Electroencefalograma de vigilia normal. Sin actividad epileptiforme.',NULL,'2026-08-02 10:00:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(38,'Julia Blanco','29333444',45,91,12,17,'informado','2026-08-02 11:30:00','Electromiografía: Signos compatibles con síndrome del túnel carpiano bilateral leve.',NULL,'2026-08-03 11:30:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(39,'Andrés Costa','35666777',39,90,12,NULL,'nuevo','2026-08-06 09:00:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(40,'Marta Domínguez','18999888',62,94,12,18,'informado','2026-08-03 14:00:00','Radiografía de rodilla derecha: Signos de gonartrosis grado II. Pinzamiento articular.',NULL,'2026-08-04 14:00:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(41,'Lucas Espinosa','42555666',22,96,12,18,'informado','2026-08-04 15:20:00','Resonancia Magnética de hombro: Ruptura parcial del tendón supraespinoso.',NULL,'2026-08-05 15:20:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(42,'Clara Fuentes','38444333',35,94,12,NULL,'nuevo','2026-08-06 10:15:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(43,'Héctor Giménez','14222111',71,98,12,19,'informado','2026-08-01 09:30:00','Fondo de ojo: Retinopatía diabética no proliferativa leve en ambos ojos.',NULL,'2026-08-02 09:30:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(44,'Silvana Herrera','33111000',41,100,12,19,'informado','2026-08-02 10:45:00','OCT Macular: Perfil foveal conservado, sin alteraciones retinianas.',NULL,'2026-08-03 10:45:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(45,'Diego Ibarra','39888777',31,98,12,NULL,'nuevo','2026-08-06 11:00:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(46,'Lorena Juárez','27666555',49,102,12,20,'informado','2026-08-03 08:00:00','Endoscopía Alta: Gastritis eritematosa antral. Se toman biopsias para HP.',NULL,'2026-08-04 08:00:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(47,'Fabián Krause','21444555',56,103,12,20,'informado','2026-08-04 09:30:00','Colonoscopía: Pólipo sésil en colon sigmoides, resecado con asa. Resto sin particularidades.',NULL,'2026-08-05 09:30:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(48,'Natalia Luna','31222999',42,102,12,NULL,'nuevo','2026-08-06 12:30:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(49,'Oscar Morales','25888999',51,104,12,21,'informado','2026-08-01 16:00:00','Dermatoscopía: Nevus atípico en región dorsal. Se sugiere excisión quirúrgica.',NULL,'2026-08-02 16:00:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(50,'Paula Núñez','36777111',37,105,12,21,'informado','2026-08-02 17:15:00','Biopsia Cutánea: Fragmento compatible con carcinoma basocelular nodular. Márgenes libres.',NULL,'2026-08-03 17:15:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(51,'Ramón Ortega','17444222',65,104,12,NULL,'nuevo','2026-08-06 14:00:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(52,'Camila Páez','41555333',25,106,12,22,'informado','2026-08-03 18:00:00','Evaluación Neurocognitiva: Atención y memoria de trabajo conservadas. Ansiedad generalizada.',NULL,'2026-08-04 18:00:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(53,'Esteban Quiroga','28999444',48,107,12,22,'informado','2026-08-04 19:30:00','Psicodiagnóstico: Indicadores de episodio depresivo moderado. Inicia esquema terapéutico.',NULL,'2026-08-05 19:30:00','2026-08-07 16:26:07','2026-08-07 16:26:07'),(54,'Verónica Ríos','34111888',39,106,12,NULL,'nuevo','2026-08-06 16:45:00',NULL,NULL,NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(55,'Luis Gómez','22111222',45,86,12,9,'informado','2026-08-01 10:00:00','Espirometría: Obstrucción leve.',NULL,'2026-08-01 12:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(56,'Ana Ruiz','33222333',50,87,12,9,'informado','2026-08-02 11:00:00','Gasometría: Parámetros normales.',NULL,'2026-08-02 13:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(57,'Carlos Paz','44333444',60,88,12,9,'informado','2026-08-03 09:00:00','Difusión pulmonar conservada.',NULL,'2026-08-03 10:30:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(58,'Marta Sosa','55444555',38,89,12,9,'informado','2026-08-04 08:30:00','Polisomnografía: SAOS moderado.',NULL,'2026-08-04 11:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(59,'Hugo Díaz','66555666',70,86,12,9,'informado','2026-08-05 14:00:00','Espirometría: Restricción severa.',NULL,'2026-08-05 16:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(60,'Elena Ríos','77666777',41,87,12,9,'informado','2026-08-06 15:00:00','Gasometría: Hipoxemia leve.',NULL,'2026-08-06 17:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(61,'Raúl Silva','88777888',55,86,12,9,'informado','2026-08-06 16:00:00','Espirometría normal.',NULL,'2026-08-06 18:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(62,'Sofía M','11222333',29,90,12,17,'informado','2026-08-01 00:00:00','EEG Normal',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(63,'Juan M','11333444',33,91,12,17,'informado','2026-08-02 00:00:00','EMG Sin alteraciones',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(64,'Pedro M','11444555',40,92,12,17,'informado','2026-08-03 00:00:00','Potenciales conservados',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(65,'Lucía M','11555666',55,93,12,17,'informado','2026-08-04 00:00:00','Conducción normal',NULL,'2026-08-05 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(66,'Diego M','11666777',60,90,12,17,'informado','2026-08-05 00:00:00','EEG con lentificación',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(67,'A1','211',20,94,12,18,'informado','2026-08-01 00:00:00','Fractura limpia',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(68,'A2','212',21,95,12,18,'informado','2026-08-02 00:00:00','Sin lesiones óseas',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(69,'A3','213',22,96,12,18,'informado','2026-08-03 00:00:00','Edema óseo',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(70,'A4','214',23,97,12,18,'informado','2026-08-04 00:00:00','Osteopenia',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(71,'A5','215',24,94,12,18,'informado','2026-08-05 00:00:00','Fisura menor',NULL,'2026-08-05 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(72,'A6','216',25,95,12,18,'informado','2026-08-06 00:00:00','Columna estable',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(73,'A7','217',26,96,12,18,'informado','2026-08-06 00:00:00','Desgarro muscular',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(74,'A8','218',27,94,12,18,'informado','2026-08-06 00:00:00','Artrosis leve',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(75,'B1','311',30,98,12,19,'informado','2026-08-01 00:00:00','Retina normal',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(76,'B2','312',31,99,12,19,'informado','2026-08-01 00:00:00','Campo visual completo',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(77,'B3','313',32,100,12,19,'informado','2026-08-02 00:00:00','Mácula sin edema',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(78,'B4','314',33,101,12,19,'informado','2026-08-02 00:00:00','Globo ocular sano',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(79,'B5','315',34,98,12,19,'informado','2026-08-03 00:00:00','Hemorragia leve',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(80,'B6','316',35,99,12,19,'informado','2026-08-04 00:00:00','Escotoma periférico',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(81,'B7','317',36,100,12,19,'informado','2026-08-04 00:00:00','Nervio óptico normal',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(82,'B8','318',37,101,12,19,'informado','2026-08-05 00:00:00','Desprendimiento vítreo',NULL,'2026-08-05 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(83,'B9','319',38,98,12,19,'informado','2026-08-06 00:00:00','Retinopatía grado 1',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(84,'B10','320',39,100,12,19,'informado','2026-08-06 00:00:00','OCT sin hallazgos',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(85,'C1','411',40,102,12,20,'informado','2026-08-01 00:00:00','Gastritis crónica',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(86,'C2','412',41,103,12,20,'informado','2026-08-02 00:00:00','Pólipo benigno',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(87,'C3','413',42,102,12,20,'informado','2026-08-03 00:00:00','Esófago normal',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(88,'C4','414',43,103,12,20,'informado','2026-08-04 00:00:00','Diverticulosis colónica',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(89,'C5','415',44,102,12,20,'informado','2026-08-05 00:00:00','Hernia hiatal',NULL,'2026-08-05 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(90,'C6','416',45,103,12,20,'informado','2026-08-06 00:00:00','Mucosa colónica sana',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(91,'D1','511',50,104,12,21,'informado','2026-08-01 00:00:00','Nevus atípico',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(92,'D2','512',51,105,12,21,'informado','2026-08-01 00:00:00','Dermatitis por contacto',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(93,'D3','513',52,104,12,21,'informado','2026-08-02 00:00:00','Queratosis seborreica',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(94,'D4','514',53,105,12,21,'informado','2026-08-03 00:00:00','Melanoma in situ',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(95,'D5','515',54,104,12,21,'informado','2026-08-04 00:00:00','Lentigo solar',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(96,'D6','516',55,105,12,21,'informado','2026-08-04 00:00:00','Psoriasis vulgar',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(97,'D7','517',56,104,12,21,'informado','2026-08-05 00:00:00','Rosácea',NULL,'2026-08-05 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(98,'D8','518',57,105,12,21,'informado','2026-08-06 00:00:00','Eccema atópico',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(99,'D9','519',58,104,12,21,'informado','2026-08-06 00:00:00','Cicatriz queloide',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(100,'E1','611',60,106,12,22,'informado','2026-08-01 00:00:00','Funciones cognitivas ok',NULL,'2026-08-01 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(101,'E2','612',61,107,12,22,'informado','2026-08-02 00:00:00','Ansiedad leve',NULL,'2026-08-02 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(102,'E3','613',62,106,12,22,'informado','2026-08-03 00:00:00','Atención dispersa',NULL,'2026-08-03 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(103,'E4','614',63,107,12,22,'informado','2026-08-04 00:00:00','TOC diagnosticado',NULL,'2026-08-04 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(104,'E5','615',64,106,12,22,'informado','2026-08-06 00:00:00','Test de memoria normal',NULL,'2026-08-06 00:00:00','2026-08-07 16:29:08','2026-08-07 16:29:08'),(105,'Luka Garro','475559656',10,92,12,NULL,'nuevo','2026-08-03 16:02:00',NULL,NULL,NULL,'2026-08-07 22:02:38','2026-08-07 22:02:38'),(106,'Hugo Rodriguez','35669875',40,82,12,NULL,'nuevo','2026-08-07 16:04:00',NULL,NULL,NULL,'2026-08-07 22:05:02','2026-08-07 22:05:02'),(107,'Florencia Lopez','22256639',38,107,12,16,'informado','2026-08-28 18:25:00','Fppep',NULL,'2026-08-07 21:26:08','2026-08-08 00:25:21','2026-08-08 18:10:59');
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
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('FVgllDxD8dzkz57khaU2ipQ0VgKhX7Uak5GaTkRD',13,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0','eyJfdG9rZW4iOiI1WDhoZ3lGTDNBMDA1YUxlQzd0ZUpiS2VJVHM4NU40dXFsMVp2bnZDIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3JyaGhcL2Rhc2hib2FyZCIsInJvdXRlIjoicnJoaC5kYXNoYm9hcmQifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjEzfQ==',1786139230),('Ij0HDENKHoYziNrF9wTTFSGTBsrLwuOpCARXrFuN',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0','eyJfdG9rZW4iOiJ3VzNxQ3N2dXBCcUhlRWxUeDl2ZmtER05PbVR0RTQ0Q3J4ZVgybUw0IiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3JyaGhcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1786201173),('lwaNL0hGvt5fMeci0whCTQgIuymnr2Uzuaiv8ucj',16,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0','eyJfdG9rZW4iOiJUYUUyWlNQYU1hY0J2NHNqU1RuWXk2R3VvRVcxSFh4WWM4eTFveEEzIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL21lZGljb1wvZXN0dWRpb3MiLCJyb3V0ZSI6Im1lZGljby5lc3R1ZGlvcy5pbmRleCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MTZ9',1786201184),('pjKMmL4In9Nr9ZkUSSCXIVkxtx37EvMnRkcfCUeA',13,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0','eyJfdG9rZW4iOiJrMkhDaHA0cEFmWHFpSDhGRjFJU1hHbkxzT3hjTGdTaElSSFlzelpKIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3JyaGhcL2xpcXVpZGFjaW9uXC8xNiIsInJvdXRlIjoicnJoaC5saXF1aWRhY2lvbi5kZXRhbGxlIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoxM30=',1786201993),('VPF6oF6rJkGPcCXTto97LgiV4JzcY4p6gC3LQ7Tc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 OPR/133.0.0.0','eyJfdG9rZW4iOiJpV3VkUjNwTHR6dUdRdDFjbUlKMlNXZmpxUWRoa1Bvc1F3eDNVZ0FPIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL3JyaGhcL2Rhc2hib2FyZCJ9LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1786201173);
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
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tipos_estudio_especialidad_id_index` (`especialidad_id`),
  CONSTRAINT `tipos_estudio_especialidad_id_foreign` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_estudio`
--

LOCK TABLES `tipos_estudio` WRITE;
/*!40000 ALTER TABLE `tipos_estudio` DISABLE KEYS */;
INSERT INTO `tipos_estudio` VALUES (81,1,'Electrocardiograma','2026-08-05 12:24:39','2026-08-05 12:24:39'),(82,1,'Holter 24hs','2026-08-05 12:24:39','2026-08-05 12:24:39'),(83,1,'Ecocardiograma','2026-08-05 12:24:39','2026-08-05 12:24:39'),(84,1,'Test de Esfuerzo','2026-08-05 12:24:39','2026-08-05 12:24:39'),(85,1,'Monitoreo Ambulatorio de Presión','2026-08-05 12:24:39','2026-08-05 12:24:39'),(86,2,'Espirometría','2026-08-05 12:24:44','2026-08-05 12:24:44'),(87,2,'Gasometría Arterial','2026-08-05 12:24:44','2026-08-05 12:24:44'),(88,2,'Prueba de Difusión Pulmonar','2026-08-05 12:24:44','2026-08-05 12:24:44'),(89,2,'Polisomnografía','2026-08-05 12:24:44','2026-08-05 12:24:44'),(90,3,'Electroencefalograma','2026-08-05 12:24:56','2026-08-05 12:24:56'),(91,3,'Electromiografía','2026-08-05 12:24:56','2026-08-05 12:24:56'),(92,3,'Potenciales Evocados','2026-08-05 12:24:56','2026-08-05 12:24:56'),(93,3,'Estudio de Conducción Nerviosa','2026-08-05 12:24:56','2026-08-05 12:24:56'),(94,4,'Radiografía','2026-08-05 12:25:02','2026-08-05 12:25:02'),(95,4,'Tomografía Computarizada','2026-08-05 12:25:02','2026-08-05 12:25:02'),(96,4,'Resonancia Magnética','2026-08-05 12:25:02','2026-08-05 12:25:02'),(97,4,'Densitometría Ósea','2026-08-05 12:25:02','2026-08-05 12:25:02'),(98,5,'Fondo de Ojo','2026-08-05 12:25:08','2026-08-05 12:25:08'),(99,5,'Campimetría','2026-08-05 12:25:08','2026-08-05 12:25:08'),(100,5,'Tomografía de Coherencia Óptica','2026-08-05 12:25:08','2026-08-05 12:25:08'),(101,5,'Ecografía Ocular','2026-08-05 12:25:08','2026-08-05 12:25:08'),(102,6,'Endoscopía Digestiva Alta','2026-08-07 16:26:07','2026-08-07 16:26:07'),(103,6,'Colonoscopía','2026-08-07 16:26:07','2026-08-07 16:26:07'),(104,7,'Dermatoscopía Digital','2026-08-07 16:26:07','2026-08-07 16:26:07'),(105,7,'Biopsia Cutánea','2026-08-07 16:26:07','2026-08-07 16:26:07'),(106,8,'Evaluación Neurocognitiva','2026-08-07 16:26:07','2026-08-07 16:26:07'),(107,8,'Psicodiagnóstico','2026-08-07 16:26:07','2026-08-07 16:26:07');
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
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tecnico',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Sistema','admin@hospital.com','admin',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(2,'Dr. Juan Pérez','juan.perez@hospital.com','medico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(3,'Dr. María García','maria.garcia@hospital.com','medico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(4,'Téc. Carlos López','carlos.lopez@hospital.com','tecnico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(5,'Téc. Ana Martínez','ana.martinez@hospital.com','tecnico',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(6,'RRHH Laura Díaz','laura.diaz@hospital.com','rrhh',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(7,'Call Center Pedro Ruiz','pedro.ruiz@hospital.com','callcenter',NULL,'$2y$12$QeF7hT6hQeF7hT6hQeF7hO',NULL,'2026-08-05 11:40:38','2026-08-05 11:40:38'),(8,'Dr. Juan Pérez','juan.perez@reportflow.local','medico',NULL,'$2y$12$T2/T12sF9sP9sU9sU9sU9eU9sU9sU9sU9sU9u',NULL,'2026-08-05 12:05:25','2026-08-05 12:05:25'),(9,'Dra. María García','maria.garcia@reportflow.local','medico',NULL,'$2y$12$T2/T12sF9sP9sU9sU9sU9eU9sU9sU9sU9sU9u',NULL,'2026-08-05 12:05:25','2026-08-05 12:05:25'),(12,'Técnico Prueba','tecnico.prueba@reportflow.local','tecnico',NULL,'$2y$12$v.mPEwmbabtpmR.zObsDV.dJLoUudyBM7Z6oD7fb1EKS25oDHvY6.',NULL,'2026-08-05 12:20:51','2026-08-06 02:46:38'),(13,'RRHH - Administración','rrhh@reportflow.local','rrhh',NULL,'$2y$12$GW6rNKJV1SMubcjU9.HID.sLyhTjRAsLY4Oisesb14K7J4rYLsYMu',NULL,'2026-08-05 19:35:59','2026-08-06 02:46:39'),(16,'Dr. Juan Pérez','medico@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-05 23:36:11','2026-08-06 02:46:39'),(17,'Dra. Lucía Fernandez','neuro@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(18,'Dr. Martín Gómez','trauma@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(19,'Dra. Valeria Torres','oftalmo@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(20,'Dr. Jorge Medina','gastro@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(21,'Dra. Silvia Paz','dermato@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07'),(22,'Dr. Ricardo Silva','psiquiatria@reportflow.local','medico',NULL,'$2y$12$UkWCTR922Z6DzGPso/juQui3mUkAXOGJRXwRYQSFgrQugnM60gbP.',NULL,'2026-08-07 16:26:07','2026-08-07 16:26:07');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'estudios_clinicos'
--

--
-- Dumping routines for database 'estudios_clinicos'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-08 12:14:05
