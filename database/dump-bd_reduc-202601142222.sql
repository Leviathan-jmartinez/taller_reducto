-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: bd_reduc
-- ------------------------------------------------------
-- Server version	12.1.2-MariaDB

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
-- Table structure for table `ajuste_inventario`
--

DROP TABLE IF EXISTS `ajuste_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ajuste_inventario` (
  `idajuste_inventario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `fecha` datetime NOT NULL,
  `tipo_inv` varchar(30) DEFAULT NULL,
  `descripcion` varchar(30) DEFAULT NULL,
  `fecha_ajuste` datetime DEFAULT NULL,
  `sucursal_id` int(10) unsigned DEFAULT NULL,
  `ajustadoPor` int(10) DEFAULT NULL,
  PRIMARY KEY (`idajuste_inventario`),
  KEY `ajuste_inventario_FKIndex2` (`id_usuario`),
  KEY `fk_ajuste_sucursal` (`sucursal_id`),
  CONSTRAINT `ajuste_inventario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_ajuste_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
INSERT INTO `ajuste_inventario` VALUES (1,1,1,'2023-09-17 00:00:00','General','fdgdg',NULL,2,NULL),(4,1,1,'2025-12-13 00:00:00','Categoria','test2',NULL,2,NULL),(5,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(6,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(7,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(8,1,1,'2025-12-13 00:00:00','Producto','test',NULL,2,NULL),(9,1,1,'2025-12-13 00:00:00','Producto','test24',NULL,2,NULL),(10,1,1,'2025-12-13 00:00:00','Producto','test4',NULL,2,NULL),(11,1,1,'2025-12-13 00:00:00','Producto','test6',NULL,2,NULL),(12,1,1,'2025-12-13 00:00:00','Producto','test8',NULL,2,NULL),(13,1,1,'2025-12-13 00:00:00','Categoria','test9',NULL,2,NULL),(14,1,1,'2025-12-13 00:00:00','Proveedor','test9',NULL,2,NULL),(15,1,1,'2025-12-13 00:00:00','General','test',NULL,2,NULL),(16,1,1,'2025-12-13 00:00:00','General','test10',NULL,2,NULL),(17,1,1,'2025-12-13 00:00:00','General','test11',NULL,2,NULL),(18,1,1,'2025-12-13 00:00:00','General','trest22',NULL,2,NULL),(19,1,1,'2025-12-13 00:00:00','General','test33',NULL,2,NULL),(20,1,1,'2025-12-13 00:00:00','General','trs23',NULL,2,NULL),(21,1,1,'2025-12-13 00:00:00','General','tes23',NULL,2,NULL),(22,1,1,'2025-12-13 00:00:00','General','test3',NULL,2,NULL),(23,1,1,'2025-12-13 00:00:00','General','test434',NULL,2,NULL),(24,1,0,'2025-12-14 00:00:00','General','rwa3453',NULL,2,NULL),(25,1,0,'2025-12-14 00:00:00','General','test12',NULL,2,NULL),(26,1,0,'2025-12-14 00:00:00','General','test3',NULL,2,NULL),(27,1,0,'2025-12-14 13:54:44','General','test1',NULL,2,NULL),(28,1,0,'2025-12-14 13:55:26','Categoria','teste122',NULL,2,NULL),(29,1,0,'2025-12-14 13:56:18','Proveedor','test344',NULL,2,NULL),(30,1,0,'2025-12-14 13:56:58','Producto','teste56',NULL,2,NULL),(31,1,1,'2025-12-14 15:36:07','General','test sin estado',NULL,2,NULL),(32,1,1,'2025-12-14 15:42:31','General','etes susucrsal',NULL,2,NULL),(33,1,1,'2025-12-14 16:24:44','General','test final',NULL,2,NULL),(34,1,1,'2025-12-14 16:26:44','General','test fina 2',NULL,2,NULL),(35,1,1,'2025-12-14 16:35:08','General','ajuste por error ',NULL,2,NULL),(36,1,1,'2025-12-14 16:38:00','General','test',NULL,2,NULL),(37,1,1,'2025-12-14 16:38:43','Producto','test',NULL,2,NULL),(38,1,1,'2025-12-14 16:56:22','General','final',NULL,2,NULL),(39,1,1,'2025-12-14 16:56:49','General','final final',NULL,2,NULL),(40,1,1,'2025-12-14 17:16:50','General','este',NULL,2,NULL),(41,1,0,'2025-12-14 17:21:39','General','test final','2025-12-14 17:22:03',2,1),(42,1,1,'2025-12-14 17:26:45','General','general final',NULL,2,NULL),(43,1,1,'2025-12-14 17:27:13','Categoria','categoria final',NULL,2,NULL),(44,1,1,'2025-12-14 17:27:53','Proveedor','proveedor final',NULL,2,NULL),(45,1,1,'2025-12-14 17:28:25','Producto','producto final',NULL,2,NULL),(46,1,2,'2025-12-14 22:59:50','Producto','test anular','2025-12-14 23:00:11',2,1),(47,1,0,'2025-12-15 20:09:17','General','test final 210','2025-12-15 20:10:06',2,1),(48,1,2,'2025-12-15 20:22:02','General','test final','2025-12-15 20:22:27',2,1),(49,1,0,'2025-12-15 20:22:50','General','test final recorrido','2025-12-15 20:23:13',2,1),(50,1,2,'2025-12-28 20:41:36','Categoria','test1','2025-12-28 20:42:35',2,1),(51,1,2,'2026-01-03 22:11:32','General','PRUEBA MULTI','2026-01-03 22:22:42',2,1),(52,1,0,'2026-01-03 22:25:45','General','tests diferencia','2026-01-03 22:26:44',2,1),(53,7,1,'2026-01-08 22:47:02','General','Inventario anual',NULL,3,NULL),(54,1,0,'2026-01-09 19:11:59','General','asdad',NULL,2,NULL),(55,1,1,'2026-01-11 21:27:46','General','general div',NULL,2,NULL),(56,1,2,'2026-01-12 15:35:40','General','ucompra','2026-01-12 15:37:32',2,7),(57,7,2,'2026-01-14 20:28:43','Producto','inv puntual','2026-01-14 20:29:04',2,7);
/*!40000 ALTER TABLE `ajuste_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ajuste_inventario_detalle`
--

DROP TABLE IF EXISTS `ajuste_inventario_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ajuste_inventario_detalle` (
  `idajuste_inventario` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad_teorica` double NOT NULL,
  `cantidad_fisica` double NOT NULL,
  `costo` double NOT NULL,
  `diferencia` double DEFAULT NULL,
  PRIMARY KEY (`idajuste_inventario`,`id_articulo`),
  KEY `ajuste_inventario_detalle_FKIndex1` (`idajuste_inventario`),
  KEY `ajuste_inventario_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `ajuste_inventario_detalle_ibfk_1` FOREIGN KEY (`idajuste_inventario`) REFERENCES `ajuste_inventario` (`idajuste_inventario`),
  CONSTRAINT `ajuste_inventario_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario_detalle`
--

LOCK TABLES `ajuste_inventario_detalle` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario_detalle` DISABLE KEYS */;
INSERT INTO `ajuste_inventario_detalle` VALUES (10,9,6,6,2500,1),(11,9,6,6,2500,1),(12,9,6,6,2500,1),(12,10,69,69,1555,1),(12,11,45,45,1555,1),(13,9,6,6,2500,1),(13,11,45,45,1555,1),(14,10,69,69,1555,1),(14,11,45,45,1555,1),(15,9,6,6,2500,1),(15,10,69,69,1555,1),(15,11,45,45,1555,1),(16,9,6,6,2500,1),(16,10,69,69,1555,1),(16,11,45,45,1555,1),(17,9,6,6,2500,1),(17,10,69,69,1555,1),(17,11,45,45,1555,1),(18,9,6,6,2500,1),(18,10,69,69,1555,1),(18,11,45,45,1555,1),(19,9,6,6,2500,1),(19,10,69,69,1555,1),(19,11,45,45,1555,1),(20,9,6,6,2500,1),(20,10,69,69,1555,1),(20,11,45,45,1555,1),(21,9,6,6,2500,1),(21,10,69,69,1555,1),(21,11,45,45,1555,1),(22,9,6,6,2500,1),(22,10,69,69,1555,1),(22,11,45,45,1555,1),(23,9,6,6,2500,1),(23,10,69,69,1555,1),(23,11,45,45,1555,1),(24,9,6,6,2500,1),(24,10,69,69,1555,1),(24,11,45,45,1555,1),(25,9,6,90,2500,84),(25,10,69,45,1555,-24),(25,11,45,180,1555,135),(26,9,6,6,2500,1),(26,10,69,69,1555,1),(26,11,45,45,1555,1),(27,9,6,6,2500,1),(27,10,69,69,1555,1),(27,11,45,45,1555,1),(28,9,6,6,2500,1),(28,11,45,45,1555,1),(29,10,69,69,1555,1),(29,11,45,45,1555,1),(30,9,6,6,2500,1),(31,9,6,6,2500,NULL),(31,10,69,69,1555,NULL),(31,11,45,45,1555,NULL),(32,9,6,6,2500,NULL),(32,10,69,69,1555,NULL),(32,11,45,45,1555,NULL),(33,9,174,100,2500,-74),(33,10,21,100,1555,79),(33,11,315,100,1555,-215),(34,9,26,100,2500,74),(34,10,179,100,1555,-79),(34,11,-115,100,1555,215),(35,9,248,100,2500,-148),(35,10,-58,100,1555,158),(35,11,530,100,1555,-430),(36,9,100,90,2500,-10),(36,10,100,90,1555,-10),(36,11,100,90,1555,-10),(37,9,90,10,2500,-80),(38,9,5,5,2500,NULL),(38,10,90,90,1555,NULL),(38,11,90,90,1555,NULL),(39,9,5,10,2500,5),(39,10,90,10,1555,-80),(39,11,90,10,1555,-80),(40,9,-70,10,2500,80),(40,10,10,10,1555,0),(40,11,10,10,1555,0),(41,9,10,20,2500,10),(41,10,10,20,1555,10),(41,11,10,20,1555,10),(42,1,0,0,6000,NULL),(42,6,0,0,5000,NULL),(42,7,0,0,4500,NULL),(42,8,0,0,7500,NULL),(42,9,20,20,2500,NULL),(42,10,20,20,1555,NULL),(42,11,20,20,1555,NULL),(42,13,0,0,5000,NULL),(43,1,0,0,6000,NULL),(43,9,20,20,2500,NULL),(43,11,20,20,1555,NULL),(43,13,0,0,5000,NULL),(44,1,0,0,6000,NULL),(44,6,0,0,5000,NULL),(44,7,0,0,4500,NULL),(44,8,0,0,7500,NULL),(44,10,20,20,1555,NULL),(44,11,20,20,1555,NULL),(44,13,0,0,5000,NULL),(45,9,20,20,2500,NULL),(46,9,20,50,2500,30),(47,1,0,10,6000,10),(47,6,0,10,5000,10),(47,7,0,10,4500,10),(47,8,0,10,7500,10),(47,9,40,10,2500,-30),(47,10,10,10,1555,0),(47,11,10,10,1555,0),(47,13,0,10,5000,10),(48,1,0,10,6000,10),(48,6,0,10,5000,10),(48,7,0,10,4500,10),(48,8,0,10,7500,10),(48,9,40,40,2500,0),(48,10,10,10,1555,0),(48,11,10,10,1555,0),(48,13,0,10,5000,10),(49,1,10,100,6000,90),(49,6,10,100,5000,90),(49,7,10,100,4500,90),(49,8,10,100,7500,90),(49,9,40,400,2500,360),(49,10,10,100,1555,90),(49,11,10,100,1555,90),(49,13,10,100,5000,90),(50,6,25,21,5000,-4),(50,7,10,11,4500,1),(50,8,10,11,7500,1),(50,10,29,22,1555,-7),(51,1,10,10,6000,0),(51,6,26,26,5000,0),(51,7,28,28,4500,0),(51,8,99,99,7500,0),(51,9,14,14,2500,0),(51,10,37,37,1555,0),(51,11,50,50,1555,0),(51,13,0,10,5000,10),(52,1,10,10,6000,0),(52,6,26,26,5000,0),(52,7,28,28,4500,0),(52,8,99,100,7500,1),(52,9,14,15,2500,1),(52,10,37,36,1555,-1),(52,11,50,50,1555,0),(52,13,10,10,5000,0),(53,1,0,0,7000,NULL),(53,6,0,0,5000,NULL),(53,7,0,0,4500,NULL),(53,8,6,6,7500,NULL),(53,9,24,24,2500,NULL),(53,10,22,22,1555,NULL),(53,11,0,0,1555,NULL),(53,13,10,10,5000,NULL),(54,1,10,10,7000,NULL),(54,6,26,26,5000,NULL),(54,7,28,28,4500,NULL),(54,8,95,95,7500,NULL),(54,9,14,14,2500,NULL),(54,10,33,33,1555,NULL),(54,11,50,50,1555,NULL),(54,13,10,10,5000,NULL),(55,1,10,10,7000,NULL),(55,6,26,26,5000,NULL),(55,7,28,28,4500,NULL),(55,8,94,94,7500,NULL),(55,9,14,14,2500,NULL),(55,10,33,33,1555,NULL),(55,11,50,50,1555,NULL),(55,13,9,9,5000,NULL),(56,1,10,10,7000,0),(56,6,26,26,5000,0),(56,7,28,28,4500,0),(56,8,94,94,7500,0),(56,9,14,14,2500,0),(56,10,33,33,1555,0),(56,11,50,50,1555,0),(56,13,9,10,5000,1),(57,17,0,3,900000,3);
/*!40000 ALTER TABLE `ajuste_inventario_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apercier_cajas`
--

DROP TABLE IF EXISTS `apercier_cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apercier_cajas` (
  `nroapercier_cajas` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `idcajas` int(10) unsigned NOT NULL,
  `fecha_aper` datetime NOT NULL,
  `monto_aper` int(10) unsigned NOT NULL,
  `fecha_cierre` datetime NOT NULL,
  `monto_cierre` int(10) unsigned NOT NULL,
  `monto_efectivo` int(10) unsigned NOT NULL,
  `monto_cheque` int(10) unsigned NOT NULL,
  `monto_tarjeta` int(10) unsigned NOT NULL,
  `nrofac_ini` varchar(60) NOT NULL,
  `nrofac_fin` varchar(60) NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  PRIMARY KEY (`nroapercier_cajas`),
  KEY `apercier_cajas_FKIndex1` (`idcajas`),
  KEY `apercier_cajas_FKIndex2` (`id_usuario`),
  CONSTRAINT `apercier_cajas_ibfk_1` FOREIGN KEY (`idcajas`) REFERENCES `cajas` (`idcajas`),
  CONSTRAINT `apercier_cajas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apercier_cajas`
--

LOCK TABLES `apercier_cajas` WRITE;
/*!40000 ALTER TABLE `apercier_cajas` DISABLE KEYS */;
/*!40000 ALTER TABLE `apercier_cajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articulos`
--

DROP TABLE IF EXISTS `articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulos` (
  `id_articulo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int(10) unsigned NOT NULL,
  `idproveedores` int(10) unsigned NOT NULL,
  `idunidad_medida` int(10) unsigned NOT NULL,
  `idiva` int(10) unsigned NOT NULL,
  `id_marcas` int(10) unsigned NOT NULL,
  `desc_articulo` varchar(70) DEFAULT NULL,
  `precio_venta` double DEFAULT NULL,
  `precio_compra` double DEFAULT NULL,
  `codigo` varchar(16) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `date_updated` datetime NOT NULL,
  `date_created` datetime NOT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_articulo`),
  KEY `articulos_FKIndex1` (`id_marcas`),
  KEY `articulos_FKIndex2` (`idiva`),
  KEY `articulos_FKIndex3` (`idunidad_medida`),
  KEY `articulos_FKIndex4` (`idproveedores`),
  KEY `articulos_FKIndex5` (`id_categoria`),
  CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`id_marcas`) REFERENCES `marcas` (`id_marcas`),
  CONSTRAINT `articulos_ibfk_2` FOREIGN KEY (`idiva`) REFERENCES `tipo_impuesto` (`idiva`),
  CONSTRAINT `articulos_ibfk_3` FOREIGN KEY (`idunidad_medida`) REFERENCES `unidad_medida` (`idunidad_medida`),
  CONSTRAINT `articulos_ibfk_4` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `articulos_ibfk_5` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (1,1,1,1,1,1,'Cubiertas Aro14',250000,200000,'123456687',1,'2026-01-14 13:36:05','2025-07-23 21:17:00','producto'),(6,2,1,1,1,1,'fdsf',8000,5000,'12313213',1,'2025-07-23 21:38:44','2025-07-23 21:38:44','producto'),(7,2,1,1,3,1,'Aceite  20W50',6890,4500,'8888888',1,'2025-07-23 21:40:31','2025-07-23 21:40:31','producto'),(8,2,1,1,2,1,'Amortiguador',10000,7500,'7840058002105',1,'2026-01-13 12:19:06','2025-07-24 14:00:19','producto'),(9,1,2,2,2,2,'Luces',3000,2500,'1234567',1,'2025-07-27 18:10:27','2025-07-24 14:03:25','producto'),(10,2,1,1,2,1,'Liquido refrigerante',6000,1555,'123456',1,'2025-07-27 18:08:10','2025-07-24 14:16:35','producto'),(11,2,1,1,1,1,'Llantas aro 14',350000,300000,'13456',1,'2026-01-14 13:37:06','2025-07-24 14:19:36','producto'),(13,1,1,1,1,1,'test',6000,5000,'16667',1,'2025-07-27 16:37:26','2025-07-27 16:37:26','producto'),(14,1,1,1,2,27,'Servicio de mantenimiento vehiculos pequeños',150000,0,'30',1,'2026-01-14 13:38:46','2026-01-14 13:38:46','servicio'),(15,2,4,1,2,30,'Bujias',50000,35000,'7894321458',1,'2026-01-14 13:41:52','2026-01-14 13:41:52','producto'),(16,2,1,1,2,27,'Servicio de Mecanica a vehiculos medianos',350000,0,'31',1,'2026-01-14 20:26:55','2026-01-14 20:26:55','servicio'),(17,2,1,2,2,5,'Cremallera T cross',1200000,900000,'45',1,'2026-01-14 20:28:01','2026-01-14 20:28:01','producto');
/*!40000 ALTER TABLE `articulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bancos` (
  `idbancos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `banc_nomb` varchar(60) DEFAULT NULL,
  `banc_direcc` varchar(120) DEFAULT NULL,
  `banc_tele` varchar(60) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idbancos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

LOCK TABLES `bancos` WRITE;
/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cajas`
--

DROP TABLE IF EXISTS `cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cajas` (
  `idcajas` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idtimbrado` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  `caj_descri` varchar(100) NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `punto_expedicion` int(10) unsigned DEFAULT NULL,
  `first_number` int(10) unsigned DEFAULT NULL,
  `last_number` int(10) unsigned DEFAULT NULL,
  `used_number` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcajas`),
  KEY `cajas_FKIndex1` (`id_sucursal`),
  KEY `cajas_FKIndex2` (`idtimbrado`),
  CONSTRAINT `cajas_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `cajas_ibfk_2` FOREIGN KEY (`idtimbrado`) REFERENCES `timbrado` (`idtimbrado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cajas`
--

LOCK TABLES `cajas` WRITE;
/*!40000 ALTER TABLE `cajas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargos`
--

DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargos` (
  `idcargos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcargos`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargos`
--

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` VALUES (1,'Adminstador del Sistema',1),(2,'Auxiliar',1);
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_descri` varchar(20) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Aceites',1),(2,'Electricidad',1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciudades` (
  `id_ciudad` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ciu_descri` varchar(50) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudades`
--

LOCK TABLES `ciudades` WRITE;
/*!40000 ALTER TABLE `ciudades` DISABLE KEYS */;
INSERT INTO `ciudades` VALUES (1,'Itaugua',1),(2,'Capiata',1),(3,'San Lorenzo',1);
/*!40000 ALTER TABLE `ciudades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ciudad` int(10) unsigned DEFAULT NULL,
  `doc_number` varchar(20) DEFAULT NULL,
  `nombre_cliente` varchar(50) DEFAULT NULL,
  `apellido_cliente` varchar(50) DEFAULT NULL,
  `direccion_cliente` varchar(50) DEFAULT NULL,
  `celular_cliente` varchar(15) DEFAULT NULL,
  `estado_civil` varchar(30) DEFAULT NULL,
  `estado_cliente` tinyint(4) DEFAULT NULL,
  `digito_v` varchar(1) DEFAULT NULL,
  `doc_type` varchar(15) DEFAULT NULL,
  `email_cliente` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `clientes_FKIndex1` (`id_ciudad`),
  CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,1,'4964127','Juan','Martinez','ruta 2','0986203431','Soltero/a',1,'1','CI','juanmartinez076@gmail.com'),(2,1,'80016096','Retail S.A.','','ruta 2','0986203431','',1,'7','RUC',NULL),(3,1,'1299450','Gricelda','Martinez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0985518660','Soltero/a',1,'1','CI','gmartinez076@gmail.com'),(4,1,'3216547','Jose','Perez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0986203431','Soltero/a',1,'','CI','jperez076@gmail.com'),(6,1,'80002004','trebolin','','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0986203431','',1,'5','RUC','trebolin076@gmail.com'),(8,1,'80019656','TEST','','dsfsdfsdfsdf','',NULL,1,'3','RUC',''),(10,1,'1456789','joselito','test','dsajdlasdjlsajdl','098456123',NULL,1,'','CI','dasdakljsdq@asdas.com'),(11,3,'1234567','asdasdsd','','asdasdasd','','Soltero/a',1,'8','RUC',''),(12,3,'1236547','pedro','perez','asdasd','0985123456','Soltero/a',1,'','CI','pedro@gmail.com'),(14,2,'321654712','cambio','cambio','cambio','0983123456','Soltero/a',1,'1','CC','cambio076@gmail.com'),(15,1,'1299451','Gricelda','Martinez','ruta py 02 km 28','0985518660','Soltero/a',1,'','CI','gmartinez@gmail.com');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro_cheque`
--

DROP TABLE IF EXISTS `cobro_cheque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro_cheque` (
  `idcobro_cheque` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int(10) unsigned NOT NULL,
  `idforma_cobro` int(10) unsigned NOT NULL,
  `idbancos` int(10) unsigned NOT NULL,
  `total_cheq` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobro_cheque`),
  KEY `cobro_cheque_FKIndex1` (`idbancos`),
  KEY `cobro_cheque_FKIndex2` (`idforma_cobro`,`idcobros`),
  CONSTRAINT `cobro_cheque_ibfk_1` FOREIGN KEY (`idbancos`) REFERENCES `bancos` (`idbancos`),
  CONSTRAINT `cobro_cheque_ibfk_2` FOREIGN KEY (`idforma_cobro`, `idcobros`) REFERENCES `cobro_detalle` (`idforma_cobro`, `idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro_cheque`
--

LOCK TABLES `cobro_cheque` WRITE;
/*!40000 ALTER TABLE `cobro_cheque` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro_cheque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro_detalle`
--

DROP TABLE IF EXISTS `cobro_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro_detalle` (
  `idforma_cobro` int(10) unsigned NOT NULL,
  `idcobros` int(10) unsigned NOT NULL,
  `monto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idforma_cobro`,`idcobros`),
  KEY `cobro_detalle_FKIndex1` (`idforma_cobro`),
  KEY `cobro_detalle_FKIndex2` (`idcobros`),
  CONSTRAINT `cobro_detalle_ibfk_1` FOREIGN KEY (`idforma_cobro`) REFERENCES `forma_cobro` (`idforma_cobro`),
  CONSTRAINT `cobro_detalle_ibfk_2` FOREIGN KEY (`idcobros`) REFERENCES `cobros` (`idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro_detalle`
--

LOCK TABLES `cobro_detalle` WRITE;
/*!40000 ALTER TABLE `cobro_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro_efectivo`
--

DROP TABLE IF EXISTS `cobro_efectivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro_efectivo` (
  `idcobro_efectivo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int(10) unsigned NOT NULL,
  `idforma_cobro` int(10) unsigned NOT NULL,
  `total_efe` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobro_efectivo`),
  KEY `cobro_efectivo_FKIndex1` (`idforma_cobro`,`idcobros`),
  CONSTRAINT `cobro_efectivo_ibfk_1` FOREIGN KEY (`idforma_cobro`, `idcobros`) REFERENCES `cobro_detalle` (`idforma_cobro`, `idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro_efectivo`
--

LOCK TABLES `cobro_efectivo` WRITE;
/*!40000 ALTER TABLE `cobro_efectivo` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro_efectivo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro_tarjeta`
--

DROP TABLE IF EXISTS `cobro_tarjeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro_tarjeta` (
  `idcobro_tarj` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int(10) unsigned NOT NULL,
  `idforma_cobro` int(10) unsigned NOT NULL,
  `identidad_emisora` int(10) unsigned NOT NULL,
  `total_tarj` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobro_tarj`),
  KEY `cobro_tarjeta_FKIndex1` (`identidad_emisora`),
  KEY `cobro_tarjeta_FKIndex2` (`idforma_cobro`,`idcobros`),
  CONSTRAINT `cobro_tarjeta_ibfk_1` FOREIGN KEY (`identidad_emisora`) REFERENCES `entidad_emisora` (`identidad_emisora`),
  CONSTRAINT `cobro_tarjeta_ibfk_2` FOREIGN KEY (`idforma_cobro`, `idcobros`) REFERENCES `cobro_detalle` (`idforma_cobro`, `idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro_tarjeta`
--

LOCK TABLES `cobro_tarjeta` WRITE;
/*!40000 ALTER TABLE `cobro_tarjeta` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro_tarjeta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobros`
--

DROP TABLE IF EXISTS `cobros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobros` (
  `idcobros` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idfactura` int(10) unsigned NOT NULL,
  `nroapercier_cajas` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `cobro_fecha` date NOT NULL,
  `cobro_estado` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idcobros`),
  KEY `cobros_FKIndex1` (`id_usuario`),
  KEY `cobros_FKIndex2` (`nroapercier_cajas`),
  KEY `cobros_FKIndex3` (`idfactura`),
  CONSTRAINT `cobros_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `cobros_ibfk_2` FOREIGN KEY (`nroapercier_cajas`) REFERENCES `apercier_cajas` (`nroapercier_cajas`),
  CONSTRAINT `cobros_ibfk_3` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobros`
--

LOCK TABLES `cobros` WRITE;
/*!40000 ALTER TABLE `cobros` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `colores`
--

DROP TABLE IF EXISTS `colores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colores` (
  `id_color` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `col_descripcion` varchar(20) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colores`
--

LOCK TABLES `colores` WRITE;
/*!40000 ALTER TABLE `colores` DISABLE KEYS */;
INSERT INTO `colores` VALUES (1,'AZUL',1),(2,'BLANCO',1),(3,'NEGRO',1),(4,'GRIS',1),(5,'PLATEADO',1),(7,'ROJO',1),(8,'VERDE',1),(9,'AMARILLO',1),(10,'MARRÓN',1),(11,'BEIGE',1),(12,'NARANJA',1),(13,'CELESTE',1),(14,'BORDÓ',1),(15,'VIOLETA',1),(16,'CHAMPAGNE',1),(17,'DORADO',1),(18,'TURQUESA',1),(19,'CREMA',1),(20,'GRIS OSCURO',1),(21,'GRIS CLARO',1),(22,'AZUL OSCURO',1),(23,'AZUL MARINO',1),(24,'ROJO OSCURO',1),(25,'VERDE OSCURO',1),(26,'NEGRO METALIZADO',1),(27,'BLANCO PERLADO',1),(28,'PLATEADO METALIZADO',1);
/*!40000 ALTER TABLE `colores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra_cabecera`
--

DROP TABLE IF EXISTS `compra_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra_cabecera` (
  `idcompra_cabecera` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedores` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `nro_factura` varchar(30) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `nro_timbrado` int(10) unsigned DEFAULT NULL,
  `vencimiento_timbrado` date DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `total_compra` decimal(12,2) DEFAULT NULL,
  `condicion` varchar(20) DEFAULT NULL,
  `compra_intervalo` varchar(20) DEFAULT NULL,
  `idOcompra` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  PRIMARY KEY (`idcompra_cabecera`),
  KEY `compra_cabecera_FKIndex1` (`id_usuario`),
  KEY `compra_cabecera_FKIndex2` (`idproveedores`),
  KEY `idx_compra_sucursal` (`id_sucursal`),
  KEY `idx_compra_fecha_estado_sucursal` (`fecha_factura`,`estado`,`id_sucursal`),
  CONSTRAINT `compra_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `compra_cabecera_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `fk_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_cabecera`
--

LOCK TABLES `compra_cabecera` WRITE;
/*!40000 ALTER TABLE `compra_cabecera` DISABLE KEYS */;
INSERT INTO `compra_cabecera` VALUES (30,1,1,2,'2025-12-12 21:13:11','001-001-0000001','2025-12-12',12345678,'2025-12-31',0,1963000.00,'credito','15',12,'2025-12-12 21:25:53',1),(31,1,1,2,'2025-12-12 21:36:22','001-001-0000002','2025-12-12',12345678,'2025-12-31',1,1963000.00,'credito','15',12,NULL,NULL),(32,1,1,2,'2025-12-12 21:40:08','001-001-0000003','2025-12-12',12345678,'2025-12-31',1,1963000.00,'credito','10',12,NULL,NULL),(33,1,1,2,'2025-12-12 21:43:34','001-001-0000004','2025-12-12',12345678,'2025-12-31',1,1963000.00,'credito','5',12,NULL,NULL),(34,2,1,2,'2025-12-12 21:47:32','010-001-0000001','2025-12-12',12345687,'2025-12-31',1,27000.00,'credito','23',10,NULL,NULL),(35,1,1,2,'2025-12-28 20:15:16','001-001-0000056','2025-12-28',12345678,'2025-12-31',1,39592.00,'contado','1',29,NULL,NULL),(36,1,1,2,'2025-12-28 20:20:52','001-001-0000058','2025-12-28',12345678,'2025-12-30',1,1062996.00,'credito','30',NULL,NULL,NULL),(37,1,1,2,'2026-01-02 21:37:38','001-001-0000049','2026-01-02',12345678,'2026-01-31',1,27775.00,'credito','3',28,NULL,NULL),(38,1,1,2,'2026-01-02 21:39:46','001-001-0000049','2026-01-02',12345678,'2026-01-31',1,27775.00,'credito','3',28,NULL,NULL),(39,1,1,2,'2026-01-02 21:48:29','123123123123','2026-01-02',91823981,'2026-01-31',1,2741870.00,'credito','2',27,NULL,NULL),(40,1,1,2,'2026-01-02 21:48:35','123123123123','2026-01-02',91823981,'2026-01-31',1,2741870.00,'credito','2',27,NULL,NULL),(41,1,1,2,'2026-01-02 21:55:18','001-001-0000055','2026-01-02',12345678,'2026-01-31',1,2741870.00,'credito','3',27,NULL,NULL),(42,1,1,2,'2026-01-02 21:56:43','001-001-0000055','2026-01-02',12345678,'2026-01-31',1,2741870.00,'credito','3',27,NULL,NULL),(43,1,1,2,'2026-01-02 22:03:07','001-001-0000050','2026-01-02',12345678,'2026-01-15',1,540589.00,'credito','2',NULL,NULL,NULL),(44,1,1,2,'2026-01-02 22:03:55','001-001-0000050','2026-01-02',12345678,'2026-01-15',1,540589.00,'credito','2',NULL,NULL,NULL),(45,1,1,2,'2026-01-02 22:12:56','001-002-0000052','2026-01-02',12345678,'2026-01-31',1,114438.00,'credito','2',NULL,NULL,NULL),(46,1,1,2,'2026-01-02 22:14:19','001-002-0000052','2026-01-02',12345678,'2026-01-31',1,114438.00,'credito','2',NULL,NULL,NULL),(47,1,1,2,'2026-01-03 15:16:46','001-001-0000069','2026-01-03',12345678,'2026-01-31',0,95000.00,'credito','30',30,NULL,1),(48,1,1,2,'2026-01-03 15:25:25','001-001-0000070','2026-01-03',12345678,'2026-01-31',1,118500.00,'credito','25',NULL,NULL,NULL),(49,1,1,2,'2026-01-12 17:48:25','001-001-0000007','2026-01-12',12345678,'2026-01-31',1,50000.00,'contado','1',32,NULL,NULL),(50,1,1,2,'2026-01-12 18:11:37','001-001-0000045','2026-01-12',12345678,'2026-01-31',0,50000.00,'credito','15',32,'2026-01-12 20:56:35',1),(51,4,7,2,'2026-01-14 13:45:49','001-002-0000001','2026-01-14',32165498,'2026-01-31',1,960000.00,'credito','15',34,NULL,NULL);
/*!40000 ALTER TABLE `compra_cabecera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra_detalle`
--

DROP TABLE IF EXISTS `compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra_detalle` (
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `cantidad_recibida` bigint(20) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `ivaPro` decimal(12,2) DEFAULT NULL,
  `tipo_iva` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idcompra_cabecera`,`id_articulo`),
  KEY `compra_cabecera_has_orden_compra_detalle_FKIndex1` (`idcompra_cabecera`),
  KEY `compra_detalle_FKIndex2` (`id_articulo`),
  KEY `idx_compra_detalle_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `compra_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_detalle`
--

LOCK TABLES `compra_detalle` WRITE;
/*!40000 ALTER TABLE `compra_detalle` DISABLE KEYS */;
INSERT INTO `compra_detalle` VALUES (30,10,56000.00,23,1288000.00,117090.91,'0'),(30,11,45000.00,15,675000.00,32142.86,'0'),(31,10,56000.00,23,1288000.00,117090.91,'0'),(31,11,45000.00,15,675000.00,32142.86,'0'),(32,10,56000.00,23,1288000.00,117090.91,'0'),(32,11,45000.00,15,675000.00,32142.86,'0'),(33,10,56000.00,23,1288000.00,117090.91,'0'),(33,11,45000.00,15,675000.00,32142.86,'0'),(34,9,4500.00,6,27000.00,2454.55,'0'),(35,10,5656.00,7,39592.00,3599.27,'0'),(36,6,45000.00,15,675000.00,32142.86,'0'),(36,10,32333.00,12,387996.00,35272.36,'0'),(38,8,5555.00,5,27775.00,2525.00,'0'),(42,8,5555.00,15,83325.00,7575.00,'2'),(42,10,5656.00,20,113120.00,10283.64,'2'),(42,11,56565.00,45,2545425.00,121210.71,'1'),(44,6,45666.00,5,228330.00,10872.86,'1'),(44,8,13333.00,23,306659.00,27878.09,'2'),(44,10,5600.00,1,5600.00,509.09,'2'),(45,7,5000.00,10,50000.00,0.00,'3'),(45,8,2222.00,23,51106.00,4646.00,'2'),(45,10,4444.00,3,13332.00,1212.00,'2'),(46,7,5000.00,10,50000.00,0.00,'3'),(46,8,2222.00,23,51106.00,4646.00,'2'),(46,10,4444.00,3,13332.00,1212.00,'2'),(47,10,5000.00,10,50000.00,4545.45,'2'),(47,13,4500.00,10,45000.00,2142.86,'1'),(48,8,5600.00,10,56000.00,5090.91,'2'),(48,10,5000.00,10,50000.00,4545.45,'2'),(48,11,2500.00,5,12500.00,595.24,'1'),(49,10,5000.00,10,50000.00,4545.45,'2'),(50,10,5000.00,10,50000.00,4545.45,'2'),(51,15,32000.00,30,960000.00,87272.73,'2');
/*!40000 ALTER TABLE `compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_a_pagar`
--

DROP TABLE IF EXISTS `cuentas_a_pagar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_a_pagar` (
  `idcuentas_a_pagar` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `tipo_movimiento` varchar(20) DEFAULT NULL,
  `referencia_tipo` varchar(30) DEFAULT NULL,
  `referencia_id` int(10) unsigned DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `saldo` decimal(12,2) NOT NULL,
  `nro_cuotas` int(10) unsigned DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  `observacion` varchar(255) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_a_pagar`,`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex1` (`idcompra_cabecera`),
  KEY `idx_cxp_sucursal` (`id_sucursal`),
  CONSTRAINT `cuentas_a_pagar_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `fk_cxp_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_a_pagar`
--

LOCK TABLES `cuentas_a_pagar` WRITE;
/*!40000 ALTER TABLE `cuentas_a_pagar` DISABLE KEYS */;
INSERT INTO `cuentas_a_pagar` VALUES (15,30,NULL,NULL,NULL,NULL,654333.00,654333.00,1,'2025-12-27',NULL,NULL,0),(16,30,NULL,NULL,NULL,NULL,654333.00,654333.00,2,'2026-01-11',NULL,NULL,0),(17,30,NULL,NULL,NULL,NULL,654333.00,654333.00,3,'2026-01-26',NULL,NULL,0),(18,31,NULL,NULL,NULL,NULL,654333.00,654333.00,1,'2025-12-27',NULL,NULL,1),(19,31,NULL,NULL,NULL,NULL,654333.00,654333.00,2,'2026-01-11',NULL,NULL,1),(20,31,NULL,NULL,NULL,NULL,654333.00,654333.00,3,'2026-01-26',NULL,NULL,1),(21,32,NULL,NULL,NULL,NULL,490750.00,490750.00,1,'2025-12-22',NULL,NULL,1),(22,32,NULL,NULL,NULL,NULL,490750.00,490750.00,2,'2026-01-01',NULL,NULL,1),(23,32,NULL,NULL,NULL,NULL,490750.00,490750.00,3,'2026-01-11',NULL,NULL,1),(24,32,NULL,NULL,NULL,NULL,490750.00,490750.00,4,'2026-01-21',NULL,NULL,1),(25,33,NULL,NULL,NULL,NULL,981500.00,981500.00,1,'2025-12-17',NULL,NULL,1),(26,33,NULL,NULL,NULL,NULL,981500.00,981500.00,2,'2025-12-22',NULL,NULL,1),(27,34,NULL,NULL,NULL,NULL,6750.00,6750.00,1,'2026-01-04',NULL,NULL,1),(28,34,NULL,NULL,NULL,NULL,6750.00,6750.00,2,'2026-01-27',NULL,NULL,1),(29,34,NULL,NULL,NULL,NULL,6750.00,6750.00,3,'2026-02-19',NULL,NULL,1),(30,34,NULL,NULL,NULL,NULL,6750.00,6750.00,4,'2026-03-14',NULL,NULL,1),(31,33,NULL,'credito','nota_compra',3,-1010000.00,-1010000.00,NULL,NULL,'2025-12-20 13:59:53','Nota credito',1),(32,32,NULL,'credito','nota_compra',4,-1963000.00,-1963000.00,NULL,NULL,'2025-12-20 14:03:51','Nota credito',1),(33,30,NULL,'credito','nota_compra',5,-560000.00,-560000.00,NULL,NULL,'2025-12-20 14:10:30','Nota credito',1),(34,31,NULL,'debito','nota_compra',6,280000.00,280000.00,NULL,NULL,'2025-12-20 14:12:02','Nota debito',1),(35,33,NULL,'anulacion','nota_compra',1,-1010000.00,-1010000.00,NULL,NULL,'2025-12-20 15:28:09','Anulación nota',1),(36,33,NULL,'anulacion','nota_compra',3,1010000.00,1010000.00,NULL,NULL,'2025-12-20 15:30:42','Anulación nota credito',1),(37,32,NULL,'anulacion','nota_compra',4,1963000.00,1963000.00,NULL,NULL,'2025-12-20 15:32:14','Anulación nota credito',1),(38,31,NULL,'anulacion','nota_compra',6,-280000.00,-280000.00,NULL,NULL,'2025-12-20 15:32:19','Anulación nota debito',1),(39,35,NULL,NULL,NULL,NULL,39592.00,39592.00,1,'2025-12-29',NULL,NULL,1),(40,36,NULL,NULL,NULL,NULL,354332.00,354332.00,1,'2026-01-27',NULL,NULL,1),(41,36,NULL,NULL,NULL,NULL,354332.00,354332.00,2,'2026-02-26',NULL,NULL,1),(42,36,NULL,NULL,NULL,NULL,354332.00,354332.00,3,'2026-03-28',NULL,NULL,1),(43,35,NULL,'debito','nota_compra',7,10500.00,10500.00,NULL,NULL,'2025-12-28 20:26:19','Nota debito',1),(44,35,NULL,'debito','nota_compra',8,7000.00,7000.00,NULL,NULL,'2025-12-28 20:35:12','Nota debito',1),(45,35,NULL,'credito','nota_compra',9,-7000.00,-7000.00,NULL,NULL,'2025-12-28 20:37:05','Nota credito',1),(46,36,NULL,'debito','nota_compra',10,246000.00,246000.00,NULL,NULL,'2025-12-28 20:40:42','Nota debito',1),(47,38,NULL,NULL,NULL,NULL,9258.33,9258.33,1,'2026-01-05',NULL,NULL,1),(48,38,NULL,NULL,NULL,NULL,9258.33,9258.33,2,'2026-01-08',NULL,NULL,1),(49,38,NULL,NULL,NULL,NULL,9258.33,9258.33,3,'2026-01-11',NULL,NULL,1),(50,42,NULL,NULL,NULL,NULL,913956.67,913956.67,1,'2026-01-05',NULL,NULL,1),(51,42,NULL,NULL,NULL,NULL,913956.67,913956.67,2,'2026-01-08',NULL,NULL,1),(52,42,NULL,NULL,NULL,NULL,913956.67,913956.67,3,'2026-01-11',NULL,NULL,1),(53,44,NULL,NULL,NULL,NULL,270294.50,270294.50,1,'2026-01-04',NULL,NULL,1),(54,44,NULL,NULL,NULL,NULL,270294.50,270294.50,2,'2026-01-06',NULL,NULL,1),(55,45,NULL,NULL,NULL,NULL,57219.00,57219.00,1,'2026-01-04',NULL,NULL,1),(56,45,NULL,NULL,NULL,NULL,57219.00,57219.00,2,'2026-01-06',NULL,NULL,1),(57,46,NULL,NULL,NULL,NULL,57219.00,57219.00,1,'2026-01-04',NULL,NULL,1),(58,46,NULL,NULL,NULL,NULL,57219.00,57219.00,2,'2026-01-06',NULL,NULL,1),(59,47,2,'COMPRA','INGRESO_COMPRA',NULL,31666.67,31666.67,1,'2026-02-02','2026-01-03 15:16:46','Factura 001-001-0000069',0),(60,47,2,'COMPRA','INGRESO_COMPRA',NULL,31666.67,31666.67,2,'2026-03-04','2026-01-03 15:16:46','Factura 001-001-0000069',0),(61,47,2,'COMPRA','INGRESO_COMPRA',NULL,31666.67,31666.67,3,'2026-04-03','2026-01-03 15:16:46','Factura 001-001-0000069',0),(62,48,2,'COMPRA','INGRESO_COMPRA',NULL,59250.00,59250.00,1,'2026-01-28','2026-01-03 15:25:25','Factura 001-001-0000070',1),(63,48,2,'COMPRA','INGRESO_COMPRA',NULL,59250.00,59250.00,2,'2026-02-22','2026-01-03 15:25:25','Factura 001-001-0000070',1),(64,47,2,'credito','nota_compra',11,-95000.00,-95000.00,NULL,NULL,'2026-01-03 21:35:51','Nota credito',1),(65,38,2,'credito','nota_compra',12,-27775.00,-27775.00,NULL,NULL,'2026-01-03 21:39:37','Nota credito',1),(66,44,2,'credito','nota_compra',13,-540589.00,-540589.00,NULL,NULL,'2026-01-03 21:45:30','Nota credito',1),(67,35,2,'credito','nota_compra',14,-39592.00,-39592.00,NULL,NULL,'2026-01-03 21:46:58','Nota credito',1),(68,36,2,'credito','nota_compra',15,-1062996.00,-1062996.00,NULL,NULL,'2026-01-03 21:50:19','Nota credito',1),(69,42,2,'credito','nota_compra',16,-2741870.00,-2741870.00,NULL,NULL,'2026-01-03 21:52:36','Nota credito',1),(70,48,2,'credito','nota_compra',17,-118500.00,-118500.00,NULL,NULL,'2026-01-03 21:54:01','Nota credito',1),(71,48,2,'credito','nota_compra',18,-118500.00,-118500.00,NULL,NULL,'2026-01-03 21:56:16','Nota credito',1),(72,48,2,'debito','nota_compra',19,25000.00,25000.00,NULL,NULL,'2026-01-03 21:59:29','Nota debito',1),(73,48,2,'credito','nota_compra',20,-25000.00,-25000.00,NULL,NULL,'2026-01-03 22:03:12','Nota credito',1),(74,44,2,'anulacion','nota_compra',13,540589.00,540589.00,NULL,NULL,'2026-01-03 22:05:33','Anulación nota credito',1),(75,35,2,'anulacion','nota_compra',14,39592.00,39592.00,NULL,NULL,'2026-01-03 22:07:01','Anulación nota credito',1),(76,48,2,'anulacion','nota_compra',19,-25000.00,-25000.00,NULL,NULL,'2026-01-03 22:07:23','Anulación nota debito',1),(77,33,2,'credito','nota_compra',21,-1963000.00,-1963000.00,NULL,NULL,'2026-01-04 20:19:27','Nota credito',1),(78,46,2,'credito','nota_compra',22,-114438.00,-114438.00,NULL,NULL,'2026-01-04 20:32:31','Nota credito',1),(79,45,2,'credito','nota_compra',23,-62000.00,-62000.00,NULL,NULL,'2026-01-04 20:33:40','Nota credito',1),(80,49,2,'COMPRA','INGRESO_COMPRA',NULL,50000.00,50000.00,1,'2026-01-13','2026-01-12 17:48:25','Factura 001-001-0000007',1),(81,50,2,'COMPRA','INGRESO_COMPRA',NULL,25000.00,25000.00,1,'2026-01-27','2026-01-12 18:11:37','Factura 001-001-0000045',0),(82,50,2,'COMPRA','INGRESO_COMPRA',NULL,25000.00,25000.00,2,'2026-02-11','2026-01-12 18:11:37','Factura 001-001-0000045',0),(83,51,2,'COMPRA','INGRESO_COMPRA',NULL,320000.00,320000.00,1,'2026-01-29','2026-01-14 13:45:49','Factura 001-002-0000001',1),(84,51,2,'COMPRA','INGRESO_COMPRA',NULL,320000.00,320000.00,2,'2026-02-13','2026-01-14 13:45:49','Factura 001-002-0000001',1),(85,51,2,'COMPRA','INGRESO_COMPRA',NULL,320000.00,320000.00,3,'2026-02-28','2026-01-14 13:45:49','Factura 001-002-0000001',1),(86,51,2,'credito','nota_compra',24,-32000.00,-32000.00,NULL,NULL,'2026-01-14 14:20:57','Nota credito',1),(87,51,2,'anulacion','nota_compra',24,32000.00,32000.00,NULL,NULL,'2026-01-14 14:32:03','Anulación Nota credito',1),(88,51,2,'debito','nota_compra',25,45000.00,45000.00,NULL,NULL,'2026-01-14 14:46:54','Nota debito 001-002-0000002',1),(89,51,2,'debito','nota_compra',26,30000.00,30000.00,NULL,NULL,'2026-01-14 14:48:58','Nota debito 001-002-0000002',1);
/*!40000 ALTER TABLE `cuentas_a_pagar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_cobrar`
--

DROP TABLE IF EXISTS `cuentas_cobrar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_cobrar` (
  `idcuentas_cobrar` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int(10) unsigned NOT NULL,
  `fecha_reg` date DEFAULT NULL,
  `fecha_venc` int(10) unsigned DEFAULT NULL,
  `cuotas_acobrar` int(10) unsigned DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_cobrar`),
  KEY `cuentas_cobrar_FKIndex1` (`idcobros`),
  CONSTRAINT `cuentas_cobrar_ibfk_1` FOREIGN KEY (`idcobros`) REFERENCES `cobros` (`idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_cobrar`
--

LOCK TABLES `cuentas_cobrar` WRITE;
/*!40000 ALTER TABLE `cuentas_cobrar` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuentas_cobrar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depositos`
--

DROP TABLE IF EXISTS `depositos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depositos` (
  `iddeposito` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(10) unsigned NOT NULL,
  `depos_descri` varchar(120) NOT NULL,
  `abreviatura` varchar(20) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`iddeposito`),
  KEY `deposito_FKIndex1` (`id_sucursal`),
  CONSTRAINT `depositos_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depositos`
--

LOCK TABLES `depositos` WRITE;
/*!40000 ALTER TABLE `depositos` DISABLE KEYS */;
INSERT INTO `depositos` VALUES (1,2,'Deposito 1','Dep1',1);
/*!40000 ALTER TABLE `depositos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuento_cliente`
--

DROP TABLE IF EXISTS `descuento_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descuento_cliente` (
  `id_descuento` int(10) unsigned NOT NULL,
  `id_cliente` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_descuento`,`id_cliente`),
  KEY `fk_desc_cliente_cli` (`id_cliente`),
  CONSTRAINT `fk_desc_cliente_cli` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_desc_cliente_desc` FOREIGN KEY (`id_descuento`) REFERENCES `descuentos` (`id_descuento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuento_cliente`
--

LOCK TABLES `descuento_cliente` WRITE;
/*!40000 ALTER TABLE `descuento_cliente` DISABLE KEYS */;
INSERT INTO `descuento_cliente` VALUES (3,1),(4,1),(3,4),(3,10);
/*!40000 ALTER TABLE `descuento_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuentos`
--

DROP TABLE IF EXISTS `descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descuentos` (
  `id_descuento` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('PORCENTAJE','MONTO_FIJO') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `es_reutilizable` tinyint(1) NOT NULL DEFAULT 0,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `id_usuario_crea` int(10) unsigned NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `id_usuario_modifica` int(10) unsigned DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_descuento`),
  KEY `fk_desc_usuario_crea` (`id_usuario_crea`),
  KEY `fk_desc_usuario_modifica` (`id_usuario_modifica`),
  CONSTRAINT `fk_desc_usuario_crea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_desc_usuario_modifica` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos`
--

LOCK TABLES `descuentos` WRITE;
/*!40000 ALTER TABLE `descuentos` DISABLE KEYS */;
INSERT INTO `descuentos` VALUES (3,'vip','test','PORCENTAJE',10.00,1,1,1,'2025-12-21 19:29:45',1,'2025-12-21 20:01:42'),(4,'Cliente Fidelizacion','Fidelizacion de Cliente','PORCENTAJE',5.00,1,1,8,'2026-01-14 22:06:47',NULL,NULL);
/*!40000 ALTER TABLE `descuentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_fac`
--

DROP TABLE IF EXISTS `detalle_fac`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_fac` (
  `id_articulo` int(10) unsigned NOT NULL,
  `idfactura` int(10) unsigned NOT NULL,
  `det_cant` int(10) unsigned NOT NULL,
  `det_prec` int(10) unsigned NOT NULL,
  `det_total` int(10) unsigned NOT NULL,
  KEY `detalle_fac_FKIndex1` (`id_articulo`),
  KEY `detalle_fac_FKIndex2` (`idfactura`),
  CONSTRAINT `detalle_fac_ibfk_2` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_fac`
--

LOCK TABLES `detalle_fac` WRITE;
/*!40000 ALTER TABLE `detalle_fac` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_fac` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `idempleados` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcargos` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  `empleado_estado` int(10) unsigned DEFAULT NULL,
  `nombre` varchar(70) DEFAULT NULL,
  `apellido` varchar(70) DEFAULT NULL,
  `direccion` varchar(120) DEFAULT NULL,
  `celular` varchar(30) DEFAULT NULL,
  `nro_cedula` varchar(10) DEFAULT NULL,
  `estado_civil` varchar(20) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idempleados`),
  KEY `personas_FKIndex2` (`id_sucursal`),
  KEY `personas_FKIndex3` (`idcargos`),
  CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`idcargos`) REFERENCES `cargos` (`idcargos`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (3,2,2,1,'jose','perez','itg','0985234832|','1134452','SOLTERO/A',1),(5,2,2,1,'jaun','martinez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','098203431','4964127','SOLTERO',1),(6,2,2,1,'Jorge','Dure','san lorenzo','0981456978','1322456','Soltero/a',1),(7,2,3,1,'Juan Angel','Figueredo Martinez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0986203456','1234567','Soltero/a',1);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id_empresa` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `ruc` varchar(20) DEFAULT NULL,
  `email_empresa` varchar(50) DEFAULT NULL,
  `telefono_empresa` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (2,'LubriReducto','San Lorenzo - Avda de la Victoria','800160967','lubrireducto@gmail.com','0986123456');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entidad_emisora`
--

DROP TABLE IF EXISTS `entidad_emisora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entidad_emisora` (
  `identidad_emisora` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`identidad_emisora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entidad_emisora`
--

LOCK TABLES `entidad_emisora` WRITE;
/*!40000 ALTER TABLE `entidad_emisora` DISABLE KEYS */;
/*!40000 ALTER TABLE `entidad_emisora` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipo_empleado`
--

DROP TABLE IF EXISTS `equipo_empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_empleado` (
  `id_equipo` int(10) unsigned NOT NULL,
  `idempleados` int(10) unsigned NOT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id_equipo`,`idempleados`),
  KEY `fk_equipo_empleado_empleado` (`idempleados`),
  CONSTRAINT `fk_equipo_empleado_empleado` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`) ON DELETE CASCADE,
  CONSTRAINT `fk_equipo_empleado_equipo` FOREIGN KEY (`id_equipo`) REFERENCES `equipo_trabajo` (`id_equipo`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_empleado`
--

LOCK TABLES `equipo_empleado` WRITE;
/*!40000 ALTER TABLE `equipo_empleado` DISABLE KEYS */;
INSERT INTO `equipo_empleado` VALUES (1,3,'Miembro',1),(1,6,'Miembro',1),(2,5,'Miembro',1),(2,6,'Miembro',1),(4,6,'Miembro',0);
/*!40000 ALTER TABLE `equipo_empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipo_trabajo`
--

DROP TABLE IF EXISTS `equipo_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_trabajo` (
  `id_equipo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(10) unsigned NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT 1,
  PRIMARY KEY (`id_equipo`),
  KEY `fk_equipo_sucursal` (`id_sucursal`),
  CONSTRAINT `fk_equipo_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_trabajo`
--

LOCK TABLES `equipo_trabajo` WRITE;
/*!40000 ALTER TABLE `equipo_trabajo` DISABLE KEYS */;
INSERT INTO `equipo_trabajo` VALUES (1,2,'Equipo 1','Mantenimiento Vehiculos Pequeños',1),(2,2,'Equipo 2','Mantenimiento Vehiculos Pesados',1),(3,2,'Equipo 3','Diagnostico',1),(4,2,'test','test',0);
/*!40000 ALTER TABLE `equipo_trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipo_trabajo_old`
--

DROP TABLE IF EXISTS `equipo_trabajo_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_trabajo_old` (
  `idtrabajos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idempleados` int(10) unsigned NOT NULL,
  `descripciontraba` varchar(100) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idtrabajos`),
  KEY `equipo_trabajo_FKIndex1` (`idempleados`),
  CONSTRAINT `equipo_trabajo_ibfk_1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_trabajo_old`
--

LOCK TABLES `equipo_trabajo_old` WRITE;
/*!40000 ALTER TABLE `equipo_trabajo_old` DISABLE KEYS */;
INSERT INTO `equipo_trabajo_old` VALUES (2,3,'Mantenimiento Vehiculos Pequeños',1);
/*!40000 ALTER TABLE `equipo_trabajo_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura`
--

DROP TABLE IF EXISTS `factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura` (
  `idfactura` int(10) unsigned NOT NULL,
  `idcajas` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_cliente` int(10) unsigned NOT NULL,
  `fac_fecha` date NOT NULL,
  `fac_hora` time NOT NULL,
  `fac_tipo` varchar(100) NOT NULL,
  `fac_total` int(10) unsigned DEFAULT NULL,
  `fac_estado` int(10) unsigned NOT NULL,
  `fac_interv` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idfactura`),
  KEY `factura_FKIndex2` (`id_cliente`),
  KEY `factura_FKIndex3` (`id_usuario`),
  KEY `factura_FKIndex4` (`idcajas`),
  CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `factura_ibfk_3` FOREIGN KEY (`idcajas`) REFERENCES `cajas` (`idcajas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura`
--

LOCK TABLES `factura` WRITE;
/*!40000 ALTER TABLE `factura` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_servicio`
--

DROP TABLE IF EXISTS `factura_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_servicio` (
  `idfactura` int(10) unsigned NOT NULL,
  KEY `registro_servicio_has_factura_FKIndex2` (`idfactura`),
  CONSTRAINT `factura_servicio_ibfk_2` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_servicio`
--

LOCK TABLES `factura_servicio` WRITE;
/*!40000 ALTER TABLE `factura_servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forma_cobro`
--

DROP TABLE IF EXISTS `forma_cobro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forma_cobro` (
  `idforma_cobro` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `formaco_descr` varchar(60) NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idforma_cobro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forma_cobro`
--

LOCK TABLES `forma_cobro` WRITE;
/*!40000 ALTER TABLE `forma_cobro` DISABLE KEYS */;
/*!40000 ALTER TABLE `forma_cobro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libro_compra`
--

DROP TABLE IF EXISTS `libro_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libro_compra` (
  `idlibro_compra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo_comprobante` varchar(20) NOT NULL,
  `serie` varchar(10) NOT NULL,
  `nro_comprobante` varchar(30) NOT NULL,
  `idproveedores` int(10) unsigned NOT NULL,
  `proveedor_nombre` varchar(150) NOT NULL,
  `proveedor_ruc` varchar(30) NOT NULL,
  `exenta` decimal(14,2) DEFAULT 0.00,
  `gravada_5` decimal(14,2) DEFAULT 0.00,
  `iva_5` decimal(14,2) DEFAULT 0.00,
  `gravada_10` decimal(14,2) DEFAULT 0.00,
  `iva_10` decimal(14,2) DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idlibro_compra`),
  KEY `fk_libro_compra_cabecera` (`idcompra_cabecera`),
  KEY `idx_libro_compra_sucursal` (`id_sucursal`,`fecha`),
  CONSTRAINT `fk_libro_compra_cabecera` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`) ON UPDATE CASCADE,
  CONSTRAINT `fk_libro_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_compra`
--

LOCK TABLES `libro_compra` WRITE;
/*!40000 ALTER TABLE `libro_compra` DISABLE KEYS */;
INSERT INTO `libro_compra` VALUES (9,47,2,'2026-01-03','factura','001-001','001-001-0000069',1,'CHACOMER S.A.E.C.A.','80015635-7',0.00,42857.14,2142.86,45454.55,4545.45,95000.00,1,'2026-01-03 15:16:46'),(10,48,2,'2026-01-03','factura','001-001','001-001-0000070',1,'CHACOMER S.A.E.C.A.','80015635-7',0.00,11904.76,595.24,96363.64,9636.36,118500.00,1,'2026-01-03 15:25:25'),(11,49,2,'2026-01-12','factura','001-001','001-001-0000007',1,'CHACOMER S.A.E.C.A.','80015635-7',0.00,0.00,0.00,45454.55,4545.45,50000.00,1,'2026-01-12 17:48:25'),(12,50,2,'2026-01-12','factura','001-001','001-001-0000045',1,'CHACOMER S.A.E.C.A.','80015635-7',0.00,0.00,0.00,45454.55,4545.45,50000.00,0,'2026-01-12 18:11:37'),(13,51,2,'2026-01-14','factura','001-002','001-002-0000001',4,'Mercotec','80012345-7',0.00,0.00,0.00,872727.27,87272.73,960000.00,1,'2026-01-14 13:45:49'),(14,51,2,'2026-01-14','NC','001-002','001-002-0000001',4,'Mercotec','80012345-7',0.00,0.00,0.00,-29090.91,-2909.09,-32000.00,0,'2026-01-14 14:20:57'),(15,51,2,'2026-01-14','ND','001-002','001-002-0000002',4,'Mercotec','80012345-7',0.00,0.00,0.00,40909.09,4090.91,45000.00,1,'2026-01-14 14:46:54'),(16,51,2,'2026-01-14','ND','001-002','001-002-0000002',4,'Mercotec','80012345-7',0.00,0.00,0.00,27272.73,2727.27,30000.00,1,'2026-01-14 14:48:58');
/*!40000 ALTER TABLE `libro_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libro_venta`
--

DROP TABLE IF EXISTS `libro_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libro_venta` (
  `idlibro_venta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idfactura` int(10) unsigned NOT NULL,
  `iva5venta` int(10) unsigned NOT NULL,
  `iva10venta` int(10) unsigned NOT NULL,
  `exentaventa` int(10) unsigned NOT NULL,
  `montoventa` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idlibro_venta`),
  KEY `libro_venta_FKIndex1` (`idfactura`),
  CONSTRAINT `libro_venta_ibfk_1` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_venta`
--

LOCK TABLES `libro_venta` WRITE;
/*!40000 ALTER TABLE `libro_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `libro_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id_marcas` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mar_descri` varchar(40) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_marcas`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Castrol',1),(2,'Shell',1),(3,'Toyota',1),(5,'VOLKSWAGEN',1),(6,'CHEVROLET',1),(7,'FORD',1),(8,'NISSAN',1),(9,'HYUNDAI',1),(10,'KIA',1),(11,'HONDA',1),(12,'MAZDA',1),(13,'MITSUBISHI',1),(14,'SUZUKI',1),(15,'PEUGEOT',1),(16,'RENAULT',1),(17,'FIAT',1),(18,'CITROËN',1),(19,'JEEP',1),(20,'DODGE',1),(21,'CHERY',1),(22,'GEELY',1),(23,'BYD',1),(24,'BMW',1),(25,'MERCEDES-BENZ',1),(26,'AUDI',1),(27,'VOLVO',1),(28,'LAND ROVER',1),(29,'PORSCHE',1),(30,'LEXUS',1),(31,'SUBARU',1),(32,'ISUZU',1),(33,'GREAT WALL',1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modelo_auto`
--

DROP TABLE IF EXISTS `modelo_auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modelo_auto` (
  `id_modeloauto` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_marcas` int(10) unsigned NOT NULL,
  `mod_descri` varchar(50) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_modeloauto`),
  KEY `modelo_auto_FKIndex1` (`id_marcas`),
  CONSTRAINT `modelo_auto_ibfk_1` FOREIGN KEY (`id_marcas`) REFERENCES `marcas` (`id_marcas`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelo_auto`
--

LOCK TABLES `modelo_auto` WRITE;
/*!40000 ALTER TABLE `modelo_auto` DISABLE KEYS */;
INSERT INTO `modelo_auto` VALUES (1,3,'Automovil',1),(2,5,'Gol',1),(3,5,'Voyage',1),(4,5,'Polo',1),(5,5,'Virtus',1),(6,5,'Saveiro',1),(7,5,'Amarok',1),(8,5,'T-Cross',1),(9,5,'Tiguan',1),(10,6,'Onix',1),(11,6,'Prisma',1),(12,6,'Corsa',1),(13,6,'Cruze',1),(14,6,'Tracker',1),(15,6,'S10',1),(16,6,'Spin',1),(17,7,'Fiesta',1),(18,7,'Focus',1),(19,7,'EcoSport',1),(20,7,'Ranger',1),(21,7,'Ka',1),(22,7,'Territory',1),(23,8,'Versa',1),(24,8,'March',1),(25,8,'Sentra',1),(26,8,'Frontier',1),(27,8,'X-Trail',1),(28,8,'Kicks',1),(29,9,'HB20',1),(30,9,'Accent',1),(31,9,'Elantra',1),(32,9,'Tucson',1),(33,9,'Santa Fe',1),(34,10,'Rio',1),(35,10,'Cerato',1),(36,10,'Sportage',1),(37,10,'Sorento',1),(38,10,'Picanto',1),(39,3,'Corolla',1),(40,3,'Yaris',1),(41,3,'Hilux',1),(42,3,'RAV4',1),(43,3,'Land Cruiser',1),(44,3,'Camry',1),(45,3,'Etios',1),(46,3,'Fortuner',1);
/*!40000 ALTER TABLE `modelo_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_compra`
--

DROP TABLE IF EXISTS `nota_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_compra` (
  `idnota_compra` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedor` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(20) NOT NULL,
  `movimiento_stock` varchar(20) NOT NULL DEFAULT 'NINGUNO',
  `nro_documento` varchar(20) NOT NULL,
  `fecha` date NOT NULL,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `total` decimal(12,2) DEFAULT 0.00,
  `descripcion` text DEFAULT NULL,
  `estado` int(10) DEFAULT NULL,
  `idusuario` bigint(20) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `timbrado` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idnota_compra`),
  KEY `idcompra_cabecera` (`idcompra_cabecera`),
  KEY `idx_nota_compra_sucursal` (`id_sucursal`),
  CONSTRAINT `1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `fk_nota_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra`
--

LOCK TABLES `nota_compra` WRITE;
/*!40000 ALTER TABLE `nota_compra` DISABLE KEYS */;
INSERT INTO `nota_compra` VALUES (1,1,'credito','NINGUNO','001-001-0000123','2025-12-20',33,1010000.00,'snotesofs',0,1,2,'2025-12-20 13:53:41','2025-12-20 15:28:09','12345678'),(3,1,'credito','NINGUNO','001-001-0000123','2025-12-20',33,1010000.00,'snotesofs',0,1,2,'2025-12-20 13:59:53','2025-12-20 15:30:42','12345678'),(4,1,'credito','NINGUNO','001-003-0000055','2025-12-20',32,1963000.00,'anulacion',0,1,2,'2025-12-20 14:03:51','2025-12-20 15:32:14','87654321'),(5,1,'credito','NINGUNO','2111111111113','2025-12-20',30,560000.00,'wqeqe',1,1,2,'2025-12-20 14:10:30',NULL,'123455678'),(6,1,'debito','NINGUNO','21321312312','2025-12-20',31,280000.00,'dasdasds',0,1,2,'2025-12-20 14:12:02','2025-12-20 15:32:19','123456577'),(7,1,'debito','NINGUNO','001-001-0000012','2025-12-28',35,10500.00,'aumento de precio',1,1,2,'2025-12-28 20:26:19',NULL,'12345678'),(8,1,'debito','NINGUNO','001-001-0000045','2025-12-28',35,7000.00,'test',1,1,2,'2025-12-28 20:35:12',NULL,'12345678'),(9,1,'credito','NINGUNO','001-001-0000036','2025-12-28',35,7000.00,'teste ',1,1,2,'2025-12-28 20:37:05',NULL,'87654321'),(10,1,'debito','NINGUNO','001-001-0000037','2025-12-30',36,246000.00,'eters',1,1,2,'2025-12-28 20:40:42',NULL,'12345678'),(11,1,'credito','NINGUNO','001-001-0000056','2026-01-03',47,95000.00,'anulacion de factura por cambio',1,1,2,'2026-01-03 21:35:51',NULL,'12345687'),(12,1,'credito','NINGUNO','12312312312','2026-01-03',38,27775.00,'213sfdfds',1,1,2,'2026-01-03 21:39:37',NULL,'123123123'),(13,1,'credito','NINGUNO','123123213','2026-01-03',44,540589.00,'asdfsdf',0,1,2,'2026-01-03 21:45:30','2026-01-03 22:05:33','123123213'),(14,1,'credito','NINGUNO','001-001-0000034','2026-01-03',35,39592.00,'test',0,1,2,'2026-01-03 21:46:58','2026-01-03 22:07:01','87654321'),(15,1,'credito','NINGUNO','123123123','2026-01-03',36,1062996.00,'wdas',1,1,2,'2026-01-03 21:50:19',NULL,'21312313'),(16,1,'credito','NINGUNO','001-001-0000045','2026-01-03',42,2741870.00,'test',1,1,2,'2026-01-03 21:52:36',NULL,'12345678'),(17,1,'credito','NINGUNO','12312313','2026-01-03',48,118500.00,'sdff',1,1,2,'2026-01-03 21:54:01',NULL,'2131231312'),(18,1,'credito','NINGUNO','34143242423','2026-01-03',48,118500.00,'sadfsdf',1,1,2,'2026-01-03 21:56:16',NULL,'2132313'),(19,1,'debito','NINGUNO','123123','2026-01-03',48,25000.00,'asdasd',0,1,2,'2026-01-03 21:59:29','2026-01-03 22:07:23','123123123'),(20,1,'credito','NINGUNO','001-001-9092345','2026-01-03',48,25000.00,'asdasd',1,1,2,'2026-01-03 22:03:12',NULL,'12345667'),(21,1,'credito','NINGUNO','001-001-0000045','2026-01-04',33,1963000.00,'asdasd',1,1,2,'2026-01-04 20:19:27',NULL,'12345678'),(22,1,'credito','NINGUNO','001-001-0000036','2026-01-04',46,114438.00,'asd',1,1,2,'2026-01-04 20:32:31',NULL,'123456768'),(23,1,'credito','NINGUNO','001-001-2342342','2026-01-04',45,62000.00,'dsf',1,1,2,'2026-01-04 20:33:40',NULL,'213434'),(24,4,'credito','DEVOLUCION','001-002-0000001','2026-01-14',51,32000.00,'faltante en entrega',0,7,2,'2026-01-14 14:20:57','2026-01-14 14:32:03','32165498'),(25,4,'debito','NINGUNO','001-002-0000002','2026-01-14',51,45000.00,'aumento',1,7,2,'2026-01-14 14:46:54',NULL,'32165498'),(26,4,'debito','NINGUNO','001-002-0000002','2026-01-14',51,30000.00,'aumento',1,7,2,'2026-01-14 14:48:58',NULL,'32165498');
/*!40000 ALTER TABLE `nota_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_compra_detalle`
--

DROP TABLE IF EXISTS `nota_compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_compra_detalle` (
  `idnota_compra` bigint(20) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cantidad` decimal(12,2) DEFAULT 0.00,
  `precio_unitario` decimal(12,2) DEFAULT 0.00,
  `subtotal` decimal(12,2) DEFAULT 0.00,
  PRIMARY KEY (`idnota_compra`,`id_articulo`),
  KEY `fk_nota_idx` (`idnota_compra`),
  KEY `fk_articulo_idx` (`id_articulo`),
  CONSTRAINT `fk_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_nota` FOREIGN KEY (`idnota_compra`) REFERENCES `nota_compra` (`idnota_compra`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra_detalle`
--

LOCK TABLES `nota_compra_detalle` WRITE;
/*!40000 ALTER TABLE `nota_compra_detalle` DISABLE KEYS */;
INSERT INTO `nota_compra_detalle` VALUES (3,10,'test de update',10.00,56000.00,560000.00),(3,11,'dsada',10.00,45000.00,450000.00),(4,10,'test de update',23.00,56000.00,1288000.00),(4,11,'dsada',15.00,45000.00,675000.00),(5,10,'test de update',10.00,56000.00,560000.00),(6,10,'test de update',5.00,56000.00,280000.00),(7,10,'test de update',7.00,1500.00,10500.00),(8,10,'test de update',7.00,1000.00,7000.00),(9,10,'test de update',7.00,1000.00,7000.00),(10,6,'fdsf',15.00,10000.00,150000.00),(10,10,'test de update',12.00,8000.00,96000.00),(11,10,'test de update',10.00,5000.00,50000.00),(11,13,'test',10.00,4500.00,45000.00),(12,8,'Gaseosa 2L',5.00,5555.00,27775.00),(13,6,'fdsf',5.00,45666.00,228330.00),(13,8,'Gaseosa 2L',23.00,13333.00,306659.00),(13,10,'test de update',1.00,5600.00,5600.00),(14,10,'test de update',7.00,5656.00,39592.00),(15,6,'fdsf',15.00,45000.00,675000.00),(15,10,'test de update',12.00,32333.00,387996.00),(16,8,'Gaseosa 2L',15.00,5555.00,83325.00),(16,10,'test de update',20.00,5656.00,113120.00),(16,11,'dsada',45.00,56565.00,2545425.00),(17,8,'Gaseosa 2L',10.00,5600.00,56000.00),(17,10,'test de update',10.00,5000.00,50000.00),(17,11,'dsada',5.00,2500.00,12500.00),(18,8,'Gaseosa 2L',10.00,5600.00,56000.00),(18,10,'test de update',10.00,5000.00,50000.00),(18,11,'dsada',5.00,2500.00,12500.00),(19,8,'Gaseosa 2L',10.00,1000.00,10000.00),(19,10,'test de update',10.00,1000.00,10000.00),(19,11,'dsada',5.00,1000.00,5000.00),(20,8,'Gaseosa 2L',10.00,1000.00,10000.00),(20,10,'test de update',10.00,1000.00,10000.00),(20,11,'dsada',5.00,1000.00,5000.00),(21,10,'test de update',23.00,56000.00,1288000.00),(21,11,'dsada',15.00,45000.00,675000.00),(22,7,'dsadasd',10.00,5000.00,50000.00),(22,8,'Gaseosa 2L',23.00,2222.00,51106.00),(22,10,'test de update',3.00,4444.00,13332.00),(23,7,'dsadasd',10.00,3000.00,30000.00),(23,8,'Gaseosa 2L',23.00,1000.00,23000.00),(23,10,'test de update',3.00,3000.00,9000.00),(24,15,'Bujias',1.00,32000.00,32000.00),(25,15,'Bujias',30.00,1500.00,45000.00),(26,15,'Bujias',30.00,1000.00,30000.00);
/*!40000 ALTER TABLE `nota_compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision`
--

DROP TABLE IF EXISTS `nota_remision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision` (
  `idnota_remision` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int(10) unsigned DEFAULT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha_emision` datetime NOT NULL DEFAULT current_timestamp(),
  `nro_remision` varchar(30) NOT NULL,
  `nombre_transpo` varchar(120) NOT NULL,
  `ci_transpo` varchar(20) DEFAULT NULL,
  `cel_transpo` varchar(60) DEFAULT NULL,
  `transportista` varchar(60) DEFAULT NULL,
  `ruc_transport` varchar(20) DEFAULT NULL,
  `vehimarca` varchar(60) DEFAULT NULL,
  `vehimodelo` varchar(60) DEFAULT NULL,
  `vehichapa` varchar(60) DEFAULT NULL,
  `fechaenvio` date NOT NULL,
  `fechallegada` date NOT NULL,
  `motivo_remision` varchar(60) NOT NULL,
  `estado` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `updated` datetime DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  `tipo` varchar(30) NOT NULL,
  `idtransferencia` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idnota_remision`),
  KEY `idx_compra` (`idcompra_cabecera`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_remision_sucursal` (`id_sucursal`),
  CONSTRAINT `fk_remision_compra` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `fk_remision_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_remision_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision`
--

LOCK TABLES `nota_remision` WRITE;
/*!40000 ALTER TABLE `nota_remision` DISABLE KEYS */;
INSERT INTO `nota_remision` VALUES (7,33,1,NULL,'2025-12-02 00:00:00','2313231','dasdasd','1231231','3213123','dasdasd','13123123','sdfsdfds','sdfsdfds','131fdsf','2025-12-15','2025-12-15','compra',0,'2025-12-16 20:28:15',1,'recepcion compra',NULL),(8,33,1,NULL,'2025-12-02 00:00:00','2313231','dasdasd','1231231','3213123','dasdasd','13123123','sdfsdfds','sdfsdfds','131fdsf','2025-12-15','2025-12-15','compra',1,NULL,NULL,'recepcion compra',NULL),(9,32,1,NULL,'2025-12-15 00:00:00','1231','sdsdsdf','12312312','32132131','sdsdsdf','1313','adsad','adasd','12312ads','2025-12-15','2025-12-15','3123',0,'2025-12-16 20:26:20',1,'recepcion compra',NULL),(10,36,1,NULL,'2025-12-27 00:00:00','001-001-0000059','tu hermana','3456777','2143234233','tu hermana','80012394','toyota','fun cargo','asd123','2025-12-27','2025-12-28','trasldo',1,NULL,NULL,'recepcion compra',NULL),(12,NULL,1,2,'2025-12-31 21:58:34','001-001-0000001','jose campos','2342344','0986234945','eleuterio','435435345','toyota','fun cargo','asd234','2025-12-31','2026-01-03','test',1,NULL,NULL,'transferencia',10),(13,NULL,1,2,'2026-01-01 00:18:59','001-001-0000002','jose campos','2342344','0986234945','eleuterio','435435345','toyota','fun cargo','asd234','2025-12-31','2026-01-03','test',1,NULL,NULL,'transferencia',11),(14,NULL,1,2,'2026-01-01 00:21:18','001-001-0000003','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-01','2026-01-31','test2',1,NULL,NULL,'transferencia',12),(15,NULL,1,2,'2026-01-01 13:22:42','001-001-0000004','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-31','test 3',1,NULL,NULL,'transferencia',13),(16,NULL,1,2,'2026-01-01 14:15:02','001-001-0000005','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-01','2026-01-31','test impresion',1,NULL,NULL,'transferencia',14),(17,NULL,1,2,'2026-01-01 14:18:24','001-001-0000006','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-31','test impresion 2',1,NULL,NULL,'transferencia',15),(18,NULL,1,2,'2026-01-01 14:19:13','001-001-0000007','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-31','test',1,NULL,NULL,'transferencia',16),(19,NULL,1,2,'2026-01-01 14:28:52','001-001-0000008','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-01','2026-01-27','testea',1,NULL,NULL,'transferencia',17),(20,NULL,1,2,'2026-01-01 14:30:40','001-001-0000009','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-31','asdsad',1,NULL,NULL,'transferencia',18),(21,NULL,1,2,'2026-01-01 18:46:46','001-001-0000010','jose campos','2342344','0986234945','eleuterio','','toyota','ractis','asd234','2026-01-01','2026-01-13','test',1,NULL,NULL,'transferencia',19),(22,NULL,1,2,'2026-01-01 18:48:03','001-001-0000011','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-16','test',1,NULL,NULL,'transferencia',20),(23,NULL,1,2,'2026-01-01 18:52:40','001-001-0000012','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-01','2026-01-22','test',1,NULL,NULL,'transferencia',21),(24,NULL,1,2,'2026-01-01 18:56:45','001-001-0000013','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-01','2026-01-22','test1',1,NULL,NULL,'transferencia',22),(25,NULL,1,2,'2026-01-01 21:16:42','001-001-0000014','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-01','2026-01-30','test',1,NULL,NULL,'transferencia',23),(26,NULL,1,2,'2026-01-02 08:09:12','001-001-0000015','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-31','asdasd',1,NULL,NULL,'transferencia',24),(27,NULL,1,2,'2026-01-02 13:10:55','001-001-0000016','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-02','2026-01-22','test2',1,NULL,NULL,'transferencia',25),(28,NULL,1,2,'2026-01-02 13:21:27','001-001-0000017','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-23','test',1,NULL,NULL,'transferencia',26),(29,NULL,1,2,'2026-01-02 19:34:55','001-001-0000018','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-22','test',1,NULL,NULL,'transferencia',27),(30,NULL,1,2,'2026-01-02 19:52:06','001-001-0000019','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-22','test',1,NULL,NULL,'transferencia',29),(31,NULL,1,2,'2026-01-02 20:01:12','001-001-0000020','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-02','2026-01-22','test',1,NULL,NULL,'transferencia',31),(32,NULL,1,2,'2026-01-02 20:11:16','001-001-0000021','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-23','test',1,NULL,NULL,'transferencia',33),(33,NULL,1,2,'2026-01-02 20:24:43','001-001-0000022','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','ractis','asd234','2026-01-02','2026-01-15','test final',1,NULL,NULL,'transferencia',35),(34,NULL,1,2,'2026-01-02 20:26:15','001-001-0000023','jose campos','2342344','0986234945','test','8765425345','toyota','ractis','asd234','2026-01-02','2026-01-13','trest',1,NULL,NULL,'transferencia',37),(35,NULL,1,2,'2026-01-02 20:47:45','001-001-0000024','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-01-02','2026-01-20','test',1,NULL,NULL,'transferencia',39),(36,48,1,2,'2026-01-03 00:00:00','001-001-0000071','rey','123124','2131321','rey','32423424','toyota','tractor','123asdas','2026-01-01','2026-01-03','compra',0,'2026-01-03 15:57:20',1,'recepcion compra',NULL),(37,51,7,2,'2026-01-14 00:00:00','001-002-0000001','Jose Estigarribia','1266598','0985123654','Jose Estigarribia','800123657','Toyota','Yaris','ABC123','2026-01-14','2026-01-14','traslado',1,NULL,NULL,'recepcion compra',NULL),(38,NULL,7,2,'2026-01-14 15:09:20','001-001-0000025','jose campos','2342344','0986234945','TSI','8765420','nissan','navara','asd234','2026-01-14','2026-01-14','re abastacimiento de stock',1,NULL,NULL,'transferencia',41);
/*!40000 ALTER TABLE `nota_remision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision_detalle`
--

DROP TABLE IF EXISTS `nota_remision_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision_detalle` (
  `idnota_remision` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` decimal(12,4) NOT NULL,
  `costo` decimal(14,2) NOT NULL,
  `subtotal` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`idnota_remision`,`id_articulo`),
  KEY `idx_nota` (`idnota_remision`),
  KEY `idx_articulo` (`id_articulo`),
  CONSTRAINT `fk_remision_detalle_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_remision_detalle_nota` FOREIGN KEY (`idnota_remision`) REFERENCES `nota_remision` (`idnota_remision`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision_detalle`
--

LOCK TABLES `nota_remision_detalle` WRITE;
/*!40000 ALTER TABLE `nota_remision_detalle` DISABLE KEYS */;
INSERT INTO `nota_remision_detalle` VALUES (7,10,23.0000,56000.00,1288000.00),(7,11,15.0000,45000.00,675000.00),(8,10,23.0000,56000.00,1288000.00),(8,11,15.0000,45000.00,675000.00),(9,10,23.0000,56000.00,1288000.00),(9,11,15.0000,45000.00,675000.00),(10,6,15.0000,45000.00,675000.00),(10,10,12.0000,32333.00,387996.00),(15,9,10.0000,2500.00,25000.00),(15,10,1.0000,1555.00,1555.00),(16,9,2.0000,2500.00,5000.00),(17,13,1.0000,5000.00,5000.00),(18,13,1.0000,5000.00,5000.00),(19,13,2.0000,5000.00,10000.00),(20,13,1.0000,5000.00,5000.00),(21,13,1.0000,5000.00,5000.00),(22,13,1.0000,5000.00,5000.00),(23,13,1.0000,5000.00,5000.00),(24,13,1.0000,5000.00,5000.00),(25,13,1.0000,5000.00,5000.00),(26,10,1.0000,1555.00,1555.00),(27,9,1.0000,2500.00,2500.00),(28,9,1.0000,2500.00,2500.00),(29,9,3.0000,2500.00,7500.00),(30,9,2.0000,2500.00,5000.00),(31,9,2.0000,2500.00,5000.00),(32,9,2.0000,2500.00,5000.00),(33,9,2.0000,2500.00,5000.00),(34,9,2.0000,2500.00,5000.00),(35,7,2.0000,4500.00,9000.00),(35,9,2.0000,2500.00,5000.00),(36,8,10.0000,5600.00,56000.00),(36,10,10.0000,5000.00,50000.00),(36,11,5.0000,2500.00,12500.00),(37,15,30.0000,32000.00,960000.00),(38,15,5.0000,35000.00,175000.00);
/*!40000 ALTER TABLE `nota_remision_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_compra`
--

DROP TABLE IF EXISTS `orden_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_compra` (
  `idorden_compra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedores` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `presupuestoid` int(10) DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idorden_compra`),
  KEY `orden_compra_FKIndex1` (`id_usuario`),
  KEY `orden_compra_FKIndex2` (`idproveedores`),
  KEY `idx_orden_compra_sucursal` (`id_sucursal`),
  KEY `idx_orden_compra_fecha_estado_sucursal` (`fecha`,`estado`,`id_sucursal`),
  CONSTRAINT `fk_orden_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `orden_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `orden_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra`
--

LOCK TABLES `orden_compra` WRITE;
/*!40000 ALTER TABLE `orden_compra` DISABLE KEYS */;
INSERT INTO `orden_compra` VALUES (1,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(2,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(3,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(4,2,1,2,'2025-12-03 00:00:00',1,'2025-12-25',22,NULL,NULL),(5,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(6,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',18,NULL,NULL),(7,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(8,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(9,2,1,2,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(10,2,1,2,'2025-12-04 00:00:00',2,'2025-12-25',18,1,'2025-12-12 21:47:32'),(11,1,1,2,'2025-12-04 00:00:00',1,'2025-12-25',19,NULL,NULL),(12,1,1,2,'2025-12-04 00:00:00',2,'2025-12-25',23,NULL,NULL),(13,1,1,2,'2025-12-04 00:00:00',1,'2025-12-25',10,NULL,NULL),(14,1,1,2,'2025-12-04 00:00:00',1,'2025-12-25',5,NULL,NULL),(15,1,1,2,'2025-12-04 00:00:00',1,'2025-12-25',23,NULL,NULL),(16,1,1,2,'2025-12-04 00:00:00',1,'2025-12-25',23,NULL,NULL),(17,1,1,2,'2025-12-04 20:40:59',1,'2025-12-25',23,NULL,NULL),(18,1,1,2,'2025-12-04 20:51:48',1,'2025-12-25',23,NULL,NULL),(19,1,1,2,'2025-12-04 20:52:00',1,'2025-12-25',21,NULL,NULL),(20,1,1,2,'2025-12-04 20:53:57',0,'2025-12-25',23,1,'2025-12-04 22:19:40'),(21,1,1,2,'2025-12-05 20:04:15',1,'2025-12-13',NULL,NULL,NULL),(22,1,1,2,'2025-12-05 20:08:23',1,'2025-12-12',NULL,NULL,NULL),(23,1,1,2,'2025-12-05 20:18:07',1,'2025-12-12',NULL,NULL,NULL),(24,1,1,2,'2025-12-05 20:20:07',1,'2025-12-19',NULL,NULL,NULL),(25,1,1,2,'2025-12-05 20:22:25',1,'2025-12-05',NULL,NULL,NULL),(26,1,1,2,'2025-12-28 20:08:49',0,'2026-01-02',NULL,1,'2026-01-03 14:11:30'),(27,1,1,2,'2025-12-28 20:09:55',2,'2025-12-28',35,1,'2026-01-02 21:56:43'),(28,1,1,2,'2025-12-28 20:10:29',2,'2025-12-28',35,1,'2026-01-02 21:39:46'),(29,1,1,2,'2025-12-28 20:10:47',2,'2025-12-28',35,1,'2025-12-28 20:15:16'),(30,1,1,2,'2026-01-03 14:14:03',2,'2026-01-03',37,1,'2026-01-03 15:16:46'),(31,2,1,2,'2026-01-03 14:15:14',1,'2026-01-06',NULL,NULL,NULL),(32,1,1,2,'2026-01-08 20:40:10',2,'2026-01-08',37,1,'2026-01-12 18:11:37'),(33,1,1,2,'2026-01-13 16:53:44',1,'2026-01-31',37,NULL,NULL),(34,4,7,2,'2026-01-14 13:43:34',2,'2026-01-14',38,7,'2026-01-14 13:45:49');
/*!40000 ALTER TABLE `orden_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_compra_detalle`
--

DROP TABLE IF EXISTS `orden_compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_compra_detalle` (
  `idorden_compra` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` bigint(20) DEFAULT NULL,
  `precio_unitario` int(10) unsigned DEFAULT NULL,
  `cantidad_pendiente` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idorden_compra`,`id_articulo`),
  KEY `orden_compra_has_presupuesto_detalle_FKIndex1` (`idorden_compra`),
  KEY `orden_compra_detalle_FKIndex2` (`id_articulo`),
  KEY `idx_orden_compra_detalle_cabecera` (`idorden_compra`),
  CONSTRAINT `orden_compra_detalle_ibfk_1` FOREIGN KEY (`idorden_compra`) REFERENCES `orden_compra` (`idorden_compra`),
  CONSTRAINT `orden_compra_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra_detalle`
--

LOCK TABLES `orden_compra_detalle` WRITE;
/*!40000 ALTER TABLE `orden_compra_detalle` DISABLE KEYS */;
INSERT INTO `orden_compra_detalle` VALUES (10,9,6,4500,0),(11,7,45,4500,45),(11,13,3,8000,3),(12,10,23,56000,0),(12,11,15,45000,0),(14,13,23,5600,23),(15,10,3,56000,3),(16,10,4,56000,4),(17,10,2,56000,2),(18,10,45,56000,45),(19,13,23,0,23),(20,10,56,56000,56),(23,10,34,45678,34),(24,10,44,4344,44),(24,13,3,2333,3),(25,10,2,2333,2),(26,6,12,6786,12),(26,10,1,5600,1),(26,13,1,4546,1),(27,8,15,5555,0),(27,10,20,5656,0),(27,11,45,56565,0),(28,8,5,5555,0),(29,10,7,5656,0),(30,10,10,5000,0),(30,13,10,4500,0),(31,9,3,3455,3),(32,10,10,5000,0),(33,10,10,5000,10),(33,13,15,4500,15),(34,15,30,32000,0);
/*!40000 ALTER TABLE `orden_compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo`
--

DROP TABLE IF EXISTS `orden_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo` (
  `idorden_trabajo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idpresupuesto_servicio` int(10) unsigned NOT NULL,
  `idrecepcion` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `idtrabajos` int(10) unsigned DEFAULT NULL,
  `tecnico_responsable` int(10) unsigned DEFAULT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_fin` datetime DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `observacion` text DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idorden_trabajo`),
  KEY `fk_ot_presupuesto` (`idpresupuesto_servicio`),
  KEY `fk_ot_usuario` (`id_usuario`),
  KEY `fk_ot_equipo` (`idtrabajos`),
  KEY `fk_ot_tecnico` (`tecnico_responsable`),
  KEY `idx_ot_fecha_estado` (`fecha_inicio`,`estado`),
  KEY `idx_ot_recepcion` (`idrecepcion`),
  CONSTRAINT `fk_ot_equipo` FOREIGN KEY (`idtrabajos`) REFERENCES `equipo_trabajo` (`id_equipo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ot_presupuesto` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_ot_recepcion` FOREIGN KEY (`idrecepcion`) REFERENCES `recepcion_servicio` (`idrecepcion`),
  CONSTRAINT `fk_ot_tecnico` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_ot_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo`
--

LOCK TABLES `orden_trabajo` WRITE;
/*!40000 ALTER TABLE `orden_trabajo` DISABLE KEYS */;
INSERT INTO `orden_trabajo` VALUES (1,3,1,1,2,NULL,'2025-12-24 14:24:13',NULL,1,'OT generada desde presupuesto',NULL,''),(2,9,1,1,2,NULL,'2025-12-24 15:19:00',NULL,1,'OT generada desde presupuesto',NULL,''),(3,10,1,1,2,NULL,'2025-12-24 15:19:06',NULL,0,'OT generada desde presupuesto','2025-12-27 16:29:38','1'),(6,20,1,1,2,NULL,'2025-12-27 15:26:57',NULL,0,'tes','2025-12-27 16:27:22','1'),(7,18,1,1,2,NULL,'2025-12-27 15:30:07',NULL,0,'test','2025-12-27 16:26:52','1'),(8,11,1,1,2,NULL,'2025-12-27 15:35:26',NULL,0,'test','2025-12-27 16:26:46','1'),(9,12,1,1,2,NULL,'2025-12-27 15:44:33',NULL,0,'test de bnuebvi','2025-12-27 16:20:57','1'),(10,17,1,1,2,NULL,'2025-12-27 15:48:38',NULL,0,'OT generada desde presupuesto','2025-12-27 16:19:49',NULL),(11,12,1,1,NULL,NULL,'2025-12-27 16:25:46',NULL,0,'OT generada desde presupuesto','2025-12-27 16:55:45','1'),(12,3,1,1,2,NULL,'2025-12-27 17:12:55','2025-12-27 17:18:19',3,'OT generada desde presupuesto',NULL,NULL),(13,9,1,1,2,NULL,'2025-12-27 21:20:14',NULL,2,'OT generada desde presupuesto',NULL,NULL),(14,10,1,1,2,NULL,'2025-12-27 21:31:46','2026-01-04 09:27:58',3,'OT generada desde presupuesto','2026-01-04 09:27:58','1'),(15,21,2,1,2,NULL,'2025-12-28 21:03:29','2025-12-28 21:44:44',3,'OT generada desde presupuesto','2025-12-28 21:44:44','1'),(16,22,3,1,2,NULL,'2026-01-04 08:44:08','2026-01-04 08:59:16',3,'OT generada desde presupuesto','2026-01-04 08:59:16','1'),(17,20,1,1,1,NULL,'2026-01-04 16:14:37',NULL,2,'OT generada desde presupuesto',NULL,NULL),(18,19,1,1,2,NULL,'2026-01-04 16:46:45',NULL,0,'TEST','2026-01-04 18:34:07','1'),(19,18,1,1,1,3,'2026-01-04 16:49:01','2026-01-11 20:41:18',3,'OT generada desde presupuesto','2026-01-11 20:41:18','1'),(20,17,1,1,1,NULL,'2026-01-04 16:49:03',NULL,0,'OT generada desde presupuesto','2026-01-04 18:34:00','1'),(21,23,4,1,1,NULL,'2026-01-09 21:34:20',NULL,2,'cambio de amortiguadores, presupuesto aprobado.',NULL,NULL),(22,24,6,1,3,NULL,'2026-01-11 19:50:57','2026-01-12 22:03:15',3,'OT generada desde presupuesto','2026-01-12 22:03:15','1'),(23,16,1,1,1,3,'2026-01-11 20:31:05','2026-01-11 20:32:36',3,'test','2026-01-11 20:32:36','1'),(24,25,7,8,2,6,'2026-01-14 20:32:32','2026-01-14 20:33:17',3,'Cambio de cremallera','2026-01-14 20:33:17','8');
/*!40000 ALTER TABLE `orden_trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo_detalle`
--

DROP TABLE IF EXISTS `orden_trabajo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo_detalle` (
  `idorden_trabajo` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`idorden_trabajo`,`id_articulo`),
  KEY `fk_ot_detalle_articulo` (`id_articulo`),
  KEY `idx_ot_detalle` (`idorden_trabajo`),
  CONSTRAINT `fk_ot_detalle_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_ot_detalle_ot` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo_detalle`
--

LOCK TABLES `orden_trabajo_detalle` WRITE;
/*!40000 ALTER TABLE `orden_trabajo_detalle` DISABLE KEYS */;
INSERT INTO `orden_trabajo_detalle` VALUES (1,6,50,6800.00,340000.00),(2,6,1,6800.00,6800.00),(3,6,1,6800.00,6800.00),(6,6,1,6800.00,6800.00),(6,8,1,5000.00,5000.00),(7,8,1,5000.00,5000.00),(8,6,1,6800.00,6800.00),(9,6,1,6800.00,6800.00),(10,13,1,3000.00,3000.00),(11,6,1,6800.00,6800.00),(12,6,50,6800.00,340000.00),(13,6,1,6800.00,6800.00),(14,6,1,6800.00,6800.00),(15,7,1,6890.00,6890.00),(15,8,4,5000.00,20000.00),(15,11,10,6554.00,65540.00),(16,8,4,10000.00,40000.00),(16,10,4,6000.00,24000.00),(17,6,1,6800.00,6800.00),(17,8,1,5000.00,5000.00),(18,8,1,5000.00,5000.00),(19,8,1,5000.00,5000.00),(20,13,1,3000.00,3000.00),(21,8,4,900.00,3600.00),(22,7,1,6890.00,6890.00),(23,13,1,3000.00,3000.00),(24,16,1,350000.00,350000.00),(24,17,1,1080000.00,1080000.00);
/*!40000 ALTER TABLE `orden_trabajo_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ordencompra_compra`
--

DROP TABLE IF EXISTS `ordencompra_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ordencompra_compra` (
  `idorden_compra` int(10) unsigned NOT NULL,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idorden_compra`,`idcompra_cabecera`),
  KEY `orden_compra_has_compra_cabecera_FKIndex1` (`idorden_compra`),
  KEY `orden_compra_has_compra_cabecera_FKIndex2` (`idcompra_cabecera`),
  CONSTRAINT `ordencompra_compra_ibfk_1` FOREIGN KEY (`idorden_compra`) REFERENCES `orden_compra` (`idorden_compra`),
  CONSTRAINT `ordencompra_compra_ibfk_2` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ordencompra_compra`
--

LOCK TABLES `ordencompra_compra` WRITE;
/*!40000 ALTER TABLE `ordencompra_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `ordencompra_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_cabecera`
--

DROP TABLE IF EXISTS `pedido_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_cabecera` (
  `idpedido_cabecera` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `id_proveedor` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`),
  KEY `pedido_cabecera_FKIndex1` (`id_usuario`),
  KEY `idx_pedido_sucursal` (`id_sucursal`),
  KEY `idx_pedido_fecha_estado_sucursal` (`fecha`,`estado`,`id_sucursal`),
  CONSTRAINT `fk_pedido_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `pedido_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cabecera`
--

LOCK TABLES `pedido_cabecera` WRITE;
/*!40000 ALTER TABLE `pedido_cabecera` DISABLE KEYS */;
INSERT INTO `pedido_cabecera` VALUES (2,1,2,'2025-11-18 20:37:45',0,1,NULL,NULL),(3,1,2,'2025-11-18 20:38:44',2,1,NULL,NULL),(4,1,2,'2025-11-18 20:39:34',0,1,NULL,NULL),(5,1,2,'2025-11-18 20:41:33',0,1,NULL,NULL),(6,1,2,'2025-11-18 20:43:18',0,1,NULL,NULL),(7,1,2,'2025-11-18 20:45:07',0,2,'2025-11-21 18:15:52','2'),(8,1,2,'2025-11-18 20:54:46',2,1,'2025-11-28 20:01:13','2'),(9,1,2,'2025-11-18 21:20:45',0,1,'2026-01-03 13:40:43','2'),(10,1,2,'2025-11-19 20:37:43',0,2,'2026-01-03 13:41:11','2'),(11,1,2,'2025-11-19 20:57:08',0,1,'2026-01-03 13:41:36','2'),(12,1,2,'2025-11-19 20:57:27',0,2,NULL,NULL),(13,1,2,'2025-11-21 18:12:17',1,1,NULL,NULL),(14,1,2,'2025-11-21 21:19:39',2,2,'2025-12-04 21:30:40','1'),(15,1,2,'2025-11-22 20:31:00',1,2,NULL,NULL),(16,4,2,'2025-11-22 20:31:01',2,1,'2025-12-04 21:29:03','1'),(17,1,2,'2025-12-28 19:32:40',2,1,'2025-12-28 19:34:24','1'),(18,1,2,'2025-12-28 19:35:23',2,1,'2025-12-28 19:35:50','1'),(19,1,2,'2025-12-28 19:36:41',2,1,'2025-12-28 20:07:08','1'),(20,1,2,'2026-01-03 13:36:05',1,1,NULL,NULL),(21,1,2,'2026-01-05 22:32:03',1,1,NULL,NULL),(22,7,2,'2026-01-05 22:32:16',2,2,NULL,NULL),(23,1,2,'2026-01-08 20:18:21',1,1,NULL,NULL),(24,7,2,'2026-01-14 13:42:19',1,4,NULL,NULL);
/*!40000 ALTER TABLE `pedido_cabecera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_detalle`
--

DROP TABLE IF EXISTS `pedido_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_detalle` (
  `idpedido_cabecera` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`,`id_articulo`),
  KEY `pedido_cabecera_has_articulos_FKIndex1` (`idpedido_cabecera`),
  KEY `pedido_cabecera_has_articulos_FKIndex2` (`id_articulo`),
  KEY `idx_pedido_detalle_cabecera` (`idpedido_cabecera`),
  CONSTRAINT `pedido_detalle_ibfk_1` FOREIGN KEY (`idpedido_cabecera`) REFERENCES `pedido_cabecera` (`idpedido_cabecera`),
  CONSTRAINT `pedido_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_detalle`
--

LOCK TABLES `pedido_detalle` WRITE;
/*!40000 ALTER TABLE `pedido_detalle` DISABLE KEYS */;
INSERT INTO `pedido_detalle` VALUES (2,6,1),(2,7,1),(2,10,1),(3,6,1),(3,7,1),(3,10,1),(4,6,1),(4,7,1),(4,10,1),(5,6,1),(5,7,1),(5,10,1),(6,7,1),(6,10,23),(7,9,1),(8,10,1),(9,10,1),(10,9,1),(11,10,1),(12,9,1),(13,7,1),(13,8,1),(13,13,7),(14,9,3),(15,9,1),(16,8,1),(17,8,1),(17,10,1),(18,13,15),(19,8,55),(19,10,20),(19,11,10),(20,6,1),(20,10,1),(21,10,1),(22,9,1),(23,10,1),(24,15,15);
/*!40000 ALTER TABLE `pedido_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id_permiso` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'servicio.recepcion.crear','Registrar solicitud de servicio'),(2,'servicio.recepcion.ver','Ver recepciones de servicio'),(3,'servicio.presupuesto.crear','Crear presupuesto de servicio'),(4,'servicio.presupuesto.aprobar','Aprobar presupuesto de servicio'),(5,'servicio.ot.generar','Generar orden de trabajo'),(6,'servicio.ot.asignar_tecnico','Asignar técnico a OT'),(7,'servicio.registro.crear','Registrar servicio finalizado'),(8,'servicio.registro.anular','Anular registro de servicio'),(9,'servicio.reclamo.crear','Registrar reclamo de cliente'),(10,'servicio.reclamo.cerrar','Cerrar reclamo de cliente'),(11,'servicio.reclamo.ver','Ver reclamos de clientes'),(12,'usuarios.ver','Ver usuarios'),(13,'usuarios.crear','Crear usuarios'),(14,'usuarios.editar','Editar usuarios'),(15,'usuarios.eliminar','Eliminar usuarios'),(16,'seguridad.roles.ver','Ver roles'),(17,'seguridad.roles.editar','Asignar y editar roles'),(18,'empresa.ver','Ver datos de la empresa'),(19,'empresa.editar','Editar datos de la empresa'),(20,'sucursal.ver','Ver sucursales'),(21,'sucursal.editar','Editar sucursales'),(22,'cliente.ver','Ver clientes'),(23,'cliente.crear','Registrar clientes'),(24,'cliente.editar','Editar clientes'),(25,'vehiculo.ver','Ver vehículos'),(26,'vehiculo.crear','Registrar vehículos'),(27,'vehiculo.editar','Editar vehículos'),(47,'compra.crear','Registrar compra'),(48,'compra.editar','Editar documentos de compra'),(49,'compra.anular','Anular documentos de compra'),(50,'compra.ver','Ver compras'),(51,'proveedor.ver','Ver proveedores'),(52,'proveedor.crear','Registrar proveedores'),(53,'proveedor.editar','Editar proveedores'),(54,'stock.ver','Ver stock'),(55,'stock.ajustar','Ajustar stock'),(56,'stock.movimiento.ver','Ver movimientos de stock'),(57,'servicio.reportes.ver','Ver reportes de servicios'),(58,'compra.reportes.ver','Ver reportes de compras'),(59,'stock.reportes.ver','Ver reportes de stock'),(60,'servicio.presupuesto.ver','Ver presupuestos de servicio'),(61,'servicio.ot.ver','Ver órdenes de trabajo'),(62,'servicio.registro.ver','Ver registros de servicio'),(63,'servicio.ot.cerrar','Cerrar orden de trabajo'),(64,'servicio.ot.anular','Anular orden de trabajo'),(71,'stock.administrar','Administrar parámetros de stock'),(120,'compra.pedido.ver','Ver pedidos de compra'),(121,'compra.pedido.crear','Crear pedidos de compra'),(122,'compra.presupuesto.ver','Ver presupuestos de compra'),(123,'compra.presupuesto.crear','Crear presupuesto de compra'),(124,'compra.oc.ver','Ver órdenes de compra'),(125,'compra.oc.crear','Crear órdenes de compra'),(126,'compra.factura.ver','Ver facturas de compra'),(127,'compra.factura.crear','Registrar facturas de compra'),(128,'compra.remision.ver','Ver remisiones'),(129,'compra.remision.crear','Registrar remisiones'),(130,'compra.nota.ver','Ver notas de crédito y débito'),(131,'compra.nota.crear','Registrar notas de crédito y débito'),(160,'inventario.ver','Ver inventarios'),(161,'inventario.crear','Generar Inventarios'),(162,'inventario.editar','Editar inventarios'),(164,'compra.presupuesto.anular','Anular Presupuesto de compra'),(165,'servicio.presupuesto.anular','Anular Presupuesto de servicio'),(166,'servicio.promocion.ver','Ver promociones'),(167,'servicio.descuento.ver','Ver descuentos'),(168,'compra.transferencia.crear','Crear transferencias'),(169,'compra.transferencia.ver','Ver transferencias'),(170,'compra.transferencia.anular','Anular transferencias'),(171,'articulo.crear','Crear articulo'),(172,'articulo.ver','Listar articulos'),(173,'articulo.editar','Editar articulos'),(175,'articulo.eliminar','Eliminar articulos'),(176,'sucursal.crear','Crear Sucursales'),(177,'sucursal.eliminar','Eliminar Sucursales'),(178,'proveedor.eliminar','Eliminar proveedores'),(179,'cliente.eliminar','Eliminar clientes'),(180,'vehiculo.eliminar','Eliminar vehículo'),(181,'empleado.ver','Ver empleados'),(182,'empleado.editar','Editar empleados'),(183,'empleado.crear','Crear empleados'),(184,'empleado.eliminar','Eliminar empleados'),(185,'usuarios.asignarlocal','Asignar local a usuarios'),(186,'usuarios.asignarrol','Asignar rol a usuarios'),(187,'usuarios.permisos_por_roles','Asignar permisos a roles '),(188,'compra.pedido.anular','Anular Pedidos de Compra'),(189,'compra.oc.anular','Anular órdenes de compra'),(190,'compra.factura.anular','Anular facturas de compra'),(191,'compra.nota.anular','Anular notas de crédito y débito'),(192,'compra.remision.anular','Anular remisiones'),(193,'compra.transferencia.recibir','Recibir transferencias'),(194,'inventario.ajustar','Ajustar stock en inventarios'),(195,'servicio.descuento.editar','Editar descuentos'),(196,'servicio.descuento.asignarClientes','Asignar descuentos a Clientes'),(197,'servicio.descuento.crear','Crear descuentos'),(198,'servicio.promocion.editar','Editar promociones'),(199,'servicio.ver','Ver Servicios'),(200,'mantenimiento.ver','Mantenimiento de referenciales'),(201,'servicio.promocion.crear','Crear Promociones'),(202,'servicio.reclamo.anular','Anular reclamo de cliente');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_compra`
--

DROP TABLE IF EXISTS `presupuesto_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_compra` (
  `idpresupuesto_compra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedores` int(10) unsigned DEFAULT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `fecha_venc` date DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `idPedido` int(10) DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_compra`),
  KEY `presupuesto_compra_FKIndex1` (`id_usuario`),
  KEY `presupuesto_compra_FKIndex2` (`idproveedores`),
  KEY `idx_presupuesto_sucursal` (`id_sucursal`),
  KEY `idx_presupuesto_compra_fecha_estado_sucursal` (`fecha`,`estado`,`id_sucursal`),
  CONSTRAINT `fk_presupuesto_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `presupuesto_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `presupuesto_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_compra`
--

LOCK TABLES `presupuesto_compra` WRITE;
/*!40000 ALTER TABLE `presupuesto_compra` DISABLE KEYS */;
INSERT INTO `presupuesto_compra` VALUES (1,1,1,2,'2025-11-25 00:00:00',0,'2025-11-25','1','2025-12-04 21:31:28',414016.00,NULL),(2,1,1,2,'2025-11-25 00:00:00',1,'2027-12-25',NULL,NULL,2222.00,NULL),(3,1,1,2,'2025-11-27 00:00:00',1,'2025-12-06',NULL,NULL,51000.00,NULL),(4,1,1,2,'2025-11-27 00:00:00',1,'2025-12-31',NULL,NULL,51000.00,NULL),(5,1,1,2,'2025-11-27 00:00:00',1,'2025-12-06',NULL,NULL,11200.00,NULL),(6,1,2,2,'2025-11-27 20:08:12',1,'2026-02-04',NULL,NULL,20334.00,NULL),(7,1,2,2,'2025-11-27 20:16:29',1,'2025-11-29',NULL,NULL,3333.00,NULL),(8,1,1,2,'2025-11-27 20:21:45',1,'2025-12-04',NULL,NULL,3333.00,NULL),(9,1,1,2,'2025-11-27 21:50:57',0,'2025-11-29','1','2025-12-24 14:11:36',545000.00,NULL),(10,1,1,2,'2025-11-27 21:53:30',1,'2025-11-28',NULL,NULL,0.00,NULL),(11,1,1,2,'2025-11-27 21:55:00',1,'2025-11-28',NULL,NULL,0.00,NULL),(12,2,1,2,'2025-11-27 21:56:15',1,'2025-11-28',NULL,NULL,0.00,NULL),(13,2,1,2,'2025-11-27 21:58:06',1,'2025-11-28',NULL,NULL,0.00,NULL),(14,2,1,2,'2025-11-27 22:00:20',0,'2025-11-28','1','2026-01-04 18:08:19',0.00,NULL),(15,2,1,2,'2025-11-27 22:03:03',1,'2025-12-06',NULL,NULL,0.00,NULL),(16,2,1,2,'2025-11-27 22:04:18',0,'2025-11-29','1','2026-01-04 18:06:18',0.00,14),(17,2,1,2,'2025-11-27 22:07:25',1,'2025-11-29',NULL,NULL,13500.00,14),(18,2,1,2,'2025-11-27 22:08:29',1,'2025-11-25',NULL,NULL,13500.00,14),(19,1,1,2,'2025-11-27 22:08:59',1,'2025-11-25',NULL,NULL,116500.00,13),(20,1,1,2,'2025-11-27 22:22:54',1,'2025-12-06',NULL,NULL,47390.00,13),(21,1,1,2,'2025-11-27 22:22:59',1,'2025-12-06',NULL,NULL,47390.00,13),(22,2,1,2,'2025-11-27 22:23:41',1,'2025-11-28',NULL,NULL,15000.00,14),(23,1,1,2,'2025-11-28 20:01:13',1,'2025-12-01',NULL,NULL,56000.00,8),(24,1,1,2,'2025-12-04 21:29:03',1,'2025-12-04',NULL,NULL,23233.00,16),(25,2,1,2,'2025-12-04 21:30:40',1,'2025-12-05',NULL,NULL,130635.00,14),(26,1,1,2,'2025-12-28 19:33:32',1,'2026-01-01',NULL,NULL,60000.00,17),(27,1,1,2,'2025-12-28 19:34:24',1,'2025-12-29',NULL,NULL,60000.00,17),(28,1,1,2,'2025-12-28 19:35:50',1,'2026-01-01',NULL,NULL,1800000.00,18),(29,1,1,2,'2025-12-28 19:37:17',1,'2026-02-07',NULL,NULL,68000.00,19),(30,1,1,2,'2025-12-28 19:43:49',1,'2026-01-10',NULL,NULL,157200.00,NULL),(31,1,1,2,'2025-12-28 19:51:47',1,'2026-01-09',NULL,NULL,50000.00,19),(32,1,1,2,'2025-12-28 19:56:15',1,'2026-01-10',NULL,NULL,70000.00,19),(33,1,1,2,'2025-12-28 19:58:33',1,'2025-12-28',NULL,NULL,70000.00,19),(34,1,1,2,'2025-12-28 20:03:51',1,'2026-01-01',NULL,NULL,0.00,19),(35,1,1,2,'2025-12-28 20:07:08',1,'2025-12-30',NULL,NULL,984295.00,19),(36,1,1,2,'2026-01-03 13:53:26',1,'2026-01-15','',NULL,66200.00,13),(37,1,1,2,'2026-01-03 13:54:39',2,'2026-01-15',NULL,NULL,33500.00,NULL),(38,4,7,2,'2026-01-14 13:42:50',2,'2026-01-24',NULL,NULL,480000.00,24);
/*!40000 ALTER TABLE `presupuesto_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_descuento`
--

DROP TABLE IF EXISTS `presupuesto_descuento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_descuento` (
  `id_presupuesto` int(10) unsigned NOT NULL,
  `id_descuento` int(10) unsigned DEFAULT NULL,
  `tipo` enum('PORCENTAJE','MONTO_FIJO') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `monto_aplicado` decimal(10,2) NOT NULL,
  `motivo` varchar(255) DEFAULT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_presupuesto`,`fecha`),
  KEY `fk_pres_desc_desc` (`id_descuento`),
  KEY `fk_pres_desc_user` (`id_usuario`),
  CONSTRAINT `fk_pres_desc_desc` FOREIGN KEY (`id_descuento`) REFERENCES `descuentos` (`id_descuento`),
  CONSTRAINT `fk_pres_desc_pres` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_pres_desc_user` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_descuento`
--

LOCK TABLES `presupuesto_descuento` WRITE;
/*!40000 ALTER TABLE `presupuesto_descuento` DISABLE KEYS */;
INSERT INTO `presupuesto_descuento` VALUES (3,3,'PORCENTAJE',10.00,34000.00,'vip',1,'2025-12-22 22:17:20'),(4,3,'PORCENTAJE',10.00,12013.00,'vip',1,'2025-12-22 22:20:11'),(5,3,'PORCENTAJE',10.00,3020.00,'vip',1,'2025-12-22 22:22:40'),(6,3,'PORCENTAJE',10.00,6902.00,'vip',1,'2025-12-22 22:23:47'),(7,3,'PORCENTAJE',10.00,6902.00,'vip',1,'2025-12-22 22:28:40'),(8,3,'PORCENTAJE',10.00,11800.00,'vip',1,'2025-12-22 22:30:12'),(9,3,'PORCENTAJE',10.00,680.00,'vip',1,'2025-12-22 22:49:14'),(10,3,'PORCENTAJE',10.00,680.00,'vip',1,'2025-12-22 22:49:58'),(15,3,'PORCENTAJE',10.00,989.00,'vip',1,'2025-12-22 23:08:51'),(16,3,'PORCENTAJE',10.00,300.00,'vip',1,'2025-12-22 23:10:18'),(19,3,'PORCENTAJE',10.00,500.00,'vip',1,'2025-12-22 23:14:16'),(20,3,'PORCENTAJE',10.00,1180.00,'vip',1,'2025-12-22 23:17:22'),(21,3,'PORCENTAJE',10.00,9243.00,'vip',1,'2025-12-28 21:01:27'),(22,3,'PORCENTAJE',10.00,6400.00,'vip',1,'2026-01-04 08:20:45'),(23,3,'PORCENTAJE',10.00,360.00,'vip',1,'2026-01-09 21:33:18'),(25,3,'PORCENTAJE',10.00,143000.00,'vip',8,'2026-01-14 20:31:19');
/*!40000 ALTER TABLE `presupuesto_descuento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_detalle`
--

DROP TABLE IF EXISTS `presupuesto_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_detalle` (
  `idpresupuesto_compra` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_compra`,`id_articulo`),
  KEY `pedido_detalle_has_presupuesto_compra_FKIndex2` (`idpresupuesto_compra`),
  KEY `presupuesto_detalle_FKIndex2` (`id_articulo`),
  KEY `idx_presupuesto_detalle_cabecera` (`idpresupuesto_compra`),
  CONSTRAINT `presupuesto_detalle_ibfk_1` FOREIGN KEY (`idpresupuesto_compra`) REFERENCES `presupuesto_compra` (`idpresupuesto_compra`),
  CONSTRAINT `presupuesto_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalle`
--

LOCK TABLES `presupuesto_detalle` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalle` DISABLE KEYS */;
INSERT INTO `presupuesto_detalle` VALUES (1,6,6.00,67688.00,406128.00),(1,13,1.00,7888.00,7888.00),(2,10,1.00,2222.00,2222.00),(3,10,3.00,7000.00,21000.00),(3,13,6.00,5000.00,30000.00),(4,10,3.00,7000.00,21000.00),(4,13,6.00,5000.00,30000.00),(5,13,2.00,5600.00,11200.00),(6,9,3.00,6778.00,20334.00),(7,9,1.00,3333.00,3333.00),(8,10,1.00,3333.00,3333.00),(12,9,1.00,5000.00,5000.00),(13,9,3.00,5000.00,15000.00),(14,9,3.00,7500.00,22500.00),(15,9,3.00,8400.00,25200.00),(16,9,3.00,15000.00,45000.00),(17,9,3.00,4500.00,13500.00),(18,9,3.00,4500.00,13500.00),(19,7,1.00,4500.00,4500.00),(19,8,1.00,56000.00,56000.00),(19,13,7.00,8000.00,56000.00),(20,13,7.00,0.00,0.00),(21,13,7.00,0.00,0.00),(22,9,3.00,5000.00,15000.00),(23,10,1.00,56000.00,56000.00),(24,8,1.00,23233.00,23233.00),(25,9,3.00,43545.00,130635.00),(26,8,1.00,0.00,0.00),(27,8,1.00,0.00,0.00),(28,13,15.00,120000.00,1800000.00),(29,11,10.00,0.00,0.00),(30,10,15.00,6000.00,90000.00),(30,13,12.00,5600.00,67200.00),(31,11,10.00,0.00,0.00),(32,11,30.00,0.00,0.00),(33,11,10.00,0.00,0.00),(34,8,55.00,0.00,0.00),(34,10,20.00,0.00,0.00),(34,11,10.00,0.00,0.00),(35,8,55.00,5555.00,305525.00),(35,10,20.00,5656.00,113120.00),(35,11,10.00,56565.00,565650.00),(36,7,1.00,5000.00,5000.00),(36,8,1.00,6600.00,6600.00),(36,13,7.00,7800.00,54600.00),(37,10,4.00,5000.00,20000.00),(37,13,3.00,4500.00,13500.00),(38,15,15.00,32000.00,480000.00);
/*!40000 ALTER TABLE `presupuesto_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_detalleservicio`
--

DROP TABLE IF EXISTS `presupuesto_detalleservicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_detalleservicio` (
  `id_articulo` int(10) unsigned NOT NULL,
  `idpresupuesto_servicio` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `preciouni` int(10) unsigned NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_articulo`,`idpresupuesto_servicio`),
  KEY `presupuesto_has_articulos_FKIndex2` (`id_articulo`),
  KEY `presupuesto_has_articulos_FKIndex3` (`idpresupuesto_servicio`),
  KEY `idx_presupuesto_servicio_detalle` (`idpresupuesto_servicio`),
  CONSTRAINT `presupuesto_detalleservicio_ibfk_1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `presupuesto_detalleservicio_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalleservicio`
--

LOCK TABLES `presupuesto_detalleservicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` DISABLE KEYS */;
INSERT INTO `presupuesto_detalleservicio` VALUES (1,4,6,15555,93330.00),(1,6,4,15555,62220.00),(1,7,4,15555,62220.00),(6,3,50,6800,340000.00),(6,4,1,6800,6800.00),(6,5,4,6800,27200.00),(6,6,1,6800,6800.00),(6,7,1,6800,6800.00),(6,8,10,6800,68000.00),(6,9,1,6800,6800.00),(6,10,1,6800,6800.00),(6,11,1,6800,6800.00),(6,12,1,6800,6800.00),(6,20,1,6800,6800.00),(7,13,1,6890,6890.00),(7,14,1,6890,6890.00),(7,15,1,6890,6890.00),(7,21,1,6890,6890.00),(7,24,1,6890,6890.00),(8,4,1,5000,5000.00),(8,8,10,5000,50000.00),(8,18,1,5000,5000.00),(8,19,1,5000,5000.00),(8,20,1,5000,5000.00),(8,21,4,5000,20000.00),(8,22,4,10000,40000.00),(8,23,4,900,3600.00),(9,4,5,3000,15000.00),(9,5,1,3000,3000.00),(10,22,4,6000,24000.00),(11,21,10,6554,65540.00),(13,15,1,3000,3000.00),(13,16,1,3000,3000.00),(13,17,1,3000,3000.00),(16,25,1,350000,350000.00),(17,25,1,1080000,1080000.00);
/*!40000 ALTER TABLE `presupuesto_detalleservicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_promocion`
--

DROP TABLE IF EXISTS `presupuesto_promocion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_promocion` (
  `idpresupuesto_servicio` int(10) unsigned NOT NULL,
  `id_promocion` int(10) unsigned NOT NULL,
  `monto_aplicado` decimal(10,2) NOT NULL,
  `fecha_aplicacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idpresupuesto_servicio`,`id_promocion`),
  KEY `fk_presupuesto_promocion_promocion` (`id_promocion`),
  CONSTRAINT `fk_presupuesto_promocion_presupuesto` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_presupuesto_promocion_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_promocion`
--

LOCK TABLES `presupuesto_promocion` WRITE;
/*!40000 ALTER TABLE `presupuesto_promocion` DISABLE KEYS */;
INSERT INTO `presupuesto_promocion` VALUES (3,5,60000.00,'2025-12-22 22:17:20'),(5,5,4800.00,'2025-12-22 22:22:40'),(8,1,50000.00,'2025-12-22 22:30:12'),(8,5,12000.00,'2025-12-22 22:30:12'),(20,1,5000.00,'2025-12-22 23:17:22'),(20,5,1200.00,'2025-12-22 23:17:22'),(21,1,20000.00,'2025-12-28 21:01:27'),(23,13,36400.00,'2026-01-09 21:33:18'),(25,14,120000.00,'2026-01-14 20:31:19');
/*!40000 ALTER TABLE `presupuesto_promocion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_servicio`
--

DROP TABLE IF EXISTS `presupuesto_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_servicio` (
  `idpresupuesto_servicio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `fecha_venc` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_final` decimal(12,2) NOT NULL DEFAULT 0.00,
  `idrecepcion` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_servicio`),
  KEY `presupuesto_FKIndex3` (`id_usuario`),
  KEY `idx_presupuesto_servicio_fecha_estado` (`fecha`,`estado`),
  KEY `idx_presupuesto_servicio_recepcion` (`idrecepcion`),
  CONSTRAINT `presupuesto_servicio_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_servicio`
--

LOCK TABLES `presupuesto_servicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_servicio` DISABLE KEYS */;
INSERT INTO `presupuesto_servicio` VALUES (3,1,'2025-12-22',3,'2025-12-31',340000.00,34000.00,306000.00,1),(4,1,'2025-12-22',3,'2025-12-25',120130.00,12013.00,108117.00,1),(5,1,'2025-12-22',3,'2025-12-31',30200.00,3020.00,27180.00,1),(6,1,'2025-12-22',3,'2025-12-24',69020.00,6902.00,62118.00,1),(7,1,'2025-12-22',3,'2025-12-31',69020.00,6902.00,62118.00,1),(8,1,'2025-12-22',3,'2025-12-31',118000.00,11800.00,106200.00,1),(9,1,'2025-12-22',3,'2025-12-27',6800.00,680.00,6120.00,1),(10,1,'2025-12-22',3,'2025-12-31',6800.00,680.00,6120.00,1),(11,1,'2025-12-22',2,'2025-12-31',6800.00,0.00,6800.00,1),(12,1,'2025-12-22',2,'2025-12-31',6800.00,0.00,6800.00,1),(13,1,'2025-12-22',1,'2025-12-24',6890.00,0.00,6890.00,1),(14,1,'2025-12-22',0,'2025-12-22',6890.00,0.00,6890.00,1),(15,1,'2025-12-22',2,'2025-12-22',9890.00,989.00,8901.00,1),(16,1,'2025-12-22',3,'2025-12-23',3000.00,300.00,2700.00,1),(17,1,'2025-12-22',2,'2025-12-31',3000.00,0.00,3000.00,1),(18,1,'2025-12-22',3,'2025-12-22',5000.00,0.00,5000.00,1),(19,1,'2025-12-22',2,'2026-01-01',5000.00,500.00,4500.00,1),(20,1,'2025-12-22',3,'2026-01-10',11800.00,1180.00,10620.00,1),(21,1,'2025-12-28',3,'2025-12-31',92430.00,9243.00,83187.00,2),(22,1,'2026-01-04',3,'2026-01-10',64000.00,6400.00,57600.00,3),(23,1,'2026-01-09',3,'2026-01-16',3600.00,360.00,3240.00,4),(24,1,'2026-01-11',3,'2026-01-14',6890.00,0.00,6890.00,6),(25,8,'2026-01-14',3,'2026-01-31',1430000.00,143000.00,1287000.00,7);
/*!40000 ALTER TABLE `presupuesto_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promocion_producto`
--

DROP TABLE IF EXISTS `promocion_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promocion_producto` (
  `id_promocion` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_promocion`,`id_articulo`),
  KEY `fk_promo_articulos` (`id_articulo`),
  CONSTRAINT `fk_promo_articulos` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_promo_producto_promo` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promocion_producto`
--

LOCK TABLES `promocion_producto` WRITE;
/*!40000 ALTER TABLE `promocion_producto` DISABLE KEYS */;
INSERT INTO `promocion_producto` VALUES (3,1),(4,1),(6,1),(11,1),(5,6),(1,8),(7,8),(8,8),(9,8),(10,8),(13,8),(6,9),(2,10),(4,11),(1,13),(2,13),(12,13),(14,17);
/*!40000 ALTER TABLE `promocion_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones` (
  `id_promocion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('PORCENTAJE','MONTO_FIJO','PRECIO_FIJO') NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `id_usuario_crea` int(10) unsigned DEFAULT NULL,
  `id_usuario_modifica` int(10) unsigned DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_promocion`),
  KEY `fk_promociones_usuario_crea` (`id_usuario_crea`),
  KEY `fk_promociones_usuario_modifica` (`id_usuario_modifica`),
  CONSTRAINT `fk_promociones_usuario_crea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_promociones_usuario_modifica` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES (1,'Promo verano test','50% de descuento en productos seleccionados','PORCENTAJE',50.00,'2025-12-20','2025-12-31',1,1,NULL,'2025-12-20 23:18:26',NULL),(2,'test','testmonto','MONTO_FIJO',10000.00,'2025-12-20','2025-12-31',1,1,NULL,'2025-12-20 23:27:03',NULL),(3,'teste2','test2','PRECIO_FIJO',5000.00,'2025-12-20','2025-12-20',1,1,NULL,'2025-12-20 23:28:35',NULL),(4,'21414','wrewerwe','PRECIO_FIJO',15555.00,'2025-12-20','2025-12-26',1,1,NULL,'2025-12-20 23:30:08',NULL),(5,'5435','wetrwfe','PORCENTAJE',15.00,'2025-12-20','2025-12-25',1,1,NULL,'2025-12-20 23:32:31',NULL),(6,'4234','twetse','PORCENTAJE',25.00,'2025-12-20','2025-12-20',1,1,NULL,'2025-12-20 23:39:20',NULL),(7,'test4','teste4','PORCENTAJE',15.00,'2025-12-21','2025-12-26',1,1,NULL,'2025-12-21 07:34:56',NULL),(8,'test4','teste4','PORCENTAJE',15.00,'2025-12-21','2025-12-26',1,1,NULL,'2025-12-21 07:35:05',NULL),(9,'test4','teste4','PORCENTAJE',15.00,'2025-12-21','2025-12-26',1,1,NULL,'2025-12-21 07:37:19',NULL),(10,'test4','teste4','PORCENTAJE',15.00,'2025-12-21','2025-12-26',1,1,NULL,'2025-12-21 07:41:03',NULL),(11,'asdasdas','adsfsf','PORCENTAJE',22.00,'2025-12-22','2025-12-31',0,1,NULL,'2025-12-21 07:41:33','2025-12-21 09:00:17'),(12,'sadd','asdasd','PORCENTAJE',45.00,'2025-12-22','2025-12-22',0,1,NULL,'2025-12-22 22:54:41','2026-01-04 20:00:16'),(13,'monto fijo test','monto fijo test','PRECIO_FIJO',900.00,'2026-01-09','2026-01-09',1,1,NULL,'2026-01-09 19:47:35','2026-01-09 20:10:35'),(14,'Promo Verano','Promo Verano 2026','PORCENTAJE',10.00,'2026-01-01','2026-01-31',1,8,NULL,'2026-01-14 20:30:21',NULL);
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `idproveedores` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ciudad` int(10) unsigned NOT NULL,
  `razon_social` varchar(70) DEFAULT NULL,
  `ruc` varchar(15) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(120) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idproveedores`),
  KEY `proveedores_FKIndex1` (`id_ciudad`),
  CONSTRAINT `proveedores_ibfk_1` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,2,'CHACOMER S.A.E.C.A.','80015635-7','12345678','ruta PY 02 km 20','chacomer@chacomer.com',1),(2,1,'test','80006895-7','32423432','ruta PY 02 km 20','test@test.com',1),(4,2,'Mercotec','80012345-7','021456987','capiata','Mercotec@gmail.com',1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recaudacion_deposito`
--

DROP TABLE IF EXISTS `recaudacion_deposito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recaudacion_deposito` (
  `idrecaudacion_deposito` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int(10) unsigned NOT NULL,
  `idforma_cobro` int(10) unsigned NOT NULL,
  `monto` int(10) unsigned DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`idrecaudacion_deposito`),
  KEY `recaudacion_deposito_FKIndex1` (`idforma_cobro`,`idcobros`),
  CONSTRAINT `recaudacion_deposito_ibfk_1` FOREIGN KEY (`idforma_cobro`, `idcobros`) REFERENCES `cobro_detalle` (`idforma_cobro`, `idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recaudacion_deposito`
--

LOCK TABLES `recaudacion_deposito` WRITE;
/*!40000 ALTER TABLE `recaudacion_deposito` DISABLE KEYS */;
/*!40000 ALTER TABLE `recaudacion_deposito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recepcion_servicio`
--

DROP TABLE IF EXISTS `recepcion_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recepcion_servicio` (
  `idrecepcion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_cliente` int(10) unsigned NOT NULL,
  `id_vehiculo` int(10) unsigned NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `kilometraje` int(10) unsigned NOT NULL,
  `observacion` text NOT NULL,
  `estado` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idrecepcion`),
  KEY `idx_cliente` (`id_cliente`),
  KEY `idx_vehiculo` (`id_vehiculo`),
  KEY `idx_usuario` (`id_usuario`),
  KEY `idx_estado` (`estado`),
  KEY `idx_recepcion_sucursal` (`id_sucursal`),
  KEY `idx_recepcion_fecha_estado_sucursal` (`fecha_ingreso`,`estado`,`id_sucursal`),
  CONSTRAINT `fk_recepcion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_recepcion_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_recepcion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_recepcion_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion_servicio`
--

LOCK TABLES `recepcion_servicio` WRITE;
/*!40000 ALTER TABLE `recepcion_servicio` DISABLE KEYS */;
INSERT INTO `recepcion_servicio` VALUES (1,1,3,1,'2025-12-20 21:43:00','2026-01-11 20:41:18',1200000,'manteni',3,'2025-12-20 21:44:43','2026-01-11 20:41:18',2),(2,1,1,2,'2025-12-28 20:58:00',NULL,45000,'Mantenimiento completo',2,'2025-12-28 20:58:57','2025-12-28 21:01:27',2),(3,1,1,2,'2026-01-03 23:11:00',NULL,150000,'mantenimiento completo, mas revision de amortiguadores',2,'2026-01-03 23:12:16','2026-01-04 08:20:45',2),(4,1,1,2,'2026-01-09 19:53:00',NULL,155000,'CAMBIO DE AMORTIGUADORES',2,'2026-01-09 19:54:08','2026-01-09 21:33:18',2),(5,1,1,2,'2026-01-11 19:44:00',NULL,2323333,'test',1,'2026-01-11 19:44:30',NULL,2),(6,1,1,2,'2026-01-11 19:45:00','2026-01-12 22:03:15',347777,'sdfsdfsdf',3,'2026-01-11 19:45:44','2026-01-12 22:03:15',2),(7,8,1,4,'2026-01-14 20:25:00','2026-01-14 20:33:17',250000,'solicitud de cambio de cremallera',3,'2026-01-14 20:25:40','2026-01-14 20:33:17',2),(8,8,1,2,'2026-01-14 22:07:00',NULL,50000,'cambio de cubiertas',1,'2026-01-14 22:08:05',NULL,2);
/*!40000 ALTER TABLE `recepcion_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamo_servicio`
--

DROP TABLE IF EXISTS `reclamo_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamo_servicio` (
  `idreclamo_servicio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idregistro_servicio` int(10) unsigned NOT NULL,
  `fecha_reclamo` datetime NOT NULL,
  `descripcion` text NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `usuario_registra` int(10) unsigned NOT NULL,
  `usuario_cierre` int(10) unsigned DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observacion_cierre` text DEFAULT NULL,
  PRIMARY KEY (`idreclamo_servicio`),
  KEY `fk_reclamo_registro` (`idregistro_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio`
--

LOCK TABLES `reclamo_servicio` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio` DISABLE KEYS */;
INSERT INTO `reclamo_servicio` VALUES (1,3,'2025-12-27 22:16:41','ndoikoi la nde rekaka, arreglame',0,1,8,'2026-01-14 21:20:17','Anulado'),(2,10,'2025-12-28 21:46:44','no anda nada tavysho',0,1,8,'2026-01-14 21:20:24','Anulado'),(3,12,'2026-01-09 21:48:29','fallo en uno de los amortiguadores',1,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `reclamo_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamos_servicio`
--

DROP TABLE IF EXISTS `reclamos_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamos_servicio` (
  `cantidad` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamos_servicio`
--

LOCK TABLES `reclamos_servicio` WRITE;
/*!40000 ALTER TABLE `reclamos_servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `reclamos_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_servicio`
--

DROP TABLE IF EXISTS `registro_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_servicio` (
  `idregistro_servicio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idorden_trabajo` int(10) unsigned NOT NULL,
  `fecha_ejecucion` date NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `tecnico_responsable` int(10) unsigned DEFAULT NULL,
  `usuario_registra` int(10) unsigned NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `observacion` text DEFAULT NULL,
  `ip_registro` varchar(45) DEFAULT NULL,
  `user_agent` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idregistro_servicio`),
  KEY `fk_rs_usuario` (`usuario_registra`),
  KEY `idx_rs_pk` (`idregistro_servicio`),
  KEY `idx_rs_ot` (`idorden_trabajo`),
  KEY `fk_rs_tecnico` (`tecnico_responsable`),
  KEY `idx_registro_fecha_estado` (`fecha_ejecucion`,`estado`),
  KEY `idx_registro_ot` (`idorden_trabajo`),
  CONSTRAINT `fk_rs_ot` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_rs_tecnico` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_rs_usuario` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio`
--

LOCK TABLES `registro_servicio` WRITE;
/*!40000 ALTER TABLE `registro_servicio` DISABLE KEYS */;
INSERT INTO `registro_servicio` VALUES (1,12,'2025-12-27','2025-12-27 17:18:19',3,1,1,'test','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(3,13,'2025-12-27','2025-12-27 21:24:03',3,1,0,'finalizado','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(4,14,'2025-12-27','2025-12-27 21:32:20',3,1,0,'finalizado','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(5,15,'2025-12-28','2025-12-28 21:15:47',3,1,0,'finalizado','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(10,15,'2025-12-28','2025-12-28 21:44:44',3,1,1,'finalizado','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(12,16,'2026-01-04','2026-01-04 08:59:16',3,1,1,'FINALIZADO','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(13,14,'2026-01-04','2026-01-04 09:27:58',3,1,1,'finalizado','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(15,23,'2026-01-11','2026-01-11 20:32:36',3,1,1,'test','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(16,19,'2026-01-11','2026-01-11 20:41:18',5,1,1,'finalizado el cambio','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(17,22,'2026-01-12','2026-01-12 22:03:15',NULL,1,1,'ass','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36'),(18,24,'2026-01-14','2026-01-14 20:33:17',6,8,1,'se realizo el cambio de cremallera, y posterior validacion un uso del mismo.','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36');
/*!40000 ALTER TABLE `registro_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_servicio_detalle`
--

DROP TABLE IF EXISTS `registro_servicio_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_servicio_detalle` (
  `idregistro_servicio` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `origen` varchar(20) NOT NULL DEFAULT 'OT',
  `fecha_copia` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idregistro_servicio`,`id_articulo`),
  KEY `fk_rs_detalle_articulo` (`id_articulo`),
  KEY `idx_registro_detalle` (`idregistro_servicio`),
  CONSTRAINT `fk_rs_detalle_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio_detalle`
--

LOCK TABLES `registro_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `registro_servicio_detalle` DISABLE KEYS */;
INSERT INTO `registro_servicio_detalle` VALUES (1,6,50.00,6800.00,340000.00,'OT','2025-12-27 17:18:19'),(3,6,1.00,6800.00,6800.00,'OT','2025-12-27 21:24:03'),(4,6,1.00,6800.00,6800.00,'OT','2025-12-27 21:32:20'),(5,7,1.00,6890.00,6890.00,'OT','2025-12-28 21:15:47'),(5,8,4.00,5000.00,20000.00,'OT','2025-12-28 21:15:47'),(5,11,10.00,6554.00,65540.00,'OT','2025-12-28 21:15:47'),(10,7,1.00,6890.00,6890.00,'OT','2025-12-28 21:44:44'),(10,8,4.00,5000.00,20000.00,'OT','2025-12-28 21:44:44'),(10,11,10.00,6554.00,65540.00,'OT','2025-12-28 21:44:44'),(12,8,4.00,10000.00,40000.00,'OT','2026-01-04 08:59:16'),(12,10,4.00,6000.00,24000.00,'OT','2026-01-04 08:59:16'),(13,6,1.00,6800.00,6800.00,'OT','2026-01-04 09:27:58'),(15,13,1.00,3000.00,3000.00,'OT','2026-01-11 20:32:36'),(16,8,1.00,5000.00,5000.00,'OT','2026-01-11 20:41:18'),(17,7,1.00,6890.00,6890.00,'OT','2026-01-12 22:03:15'),(18,16,1.00,350000.00,350000.00,'OT','2026-01-14 20:33:17'),(18,17,1.00,1080000.00,1080000.00,'OT','2026-01-14 20:33:17');
/*!40000 ALTER TABLE `registro_servicio_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permiso` (
  `id_rol` int(10) unsigned NOT NULL,
  `id_permiso` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `fk_rp_permiso` (`id_permiso`),
  CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`),
  CONSTRAINT `fk_rp_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permiso`
--

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` VALUES (7,1),(8,1),(9,1),(10,1),(7,2),(8,2),(9,2),(10,2),(14,2),(7,3),(8,3),(9,3),(10,3),(7,4),(8,4),(10,4),(7,5),(8,5),(10,5),(11,5),(7,6),(8,6),(10,6),(11,6),(7,7),(8,7),(10,7),(7,8),(8,8),(10,8),(7,9),(8,9),(9,9),(10,9),(7,10),(8,10),(10,10),(7,11),(8,11),(9,11),(10,11),(14,11),(7,12),(8,12),(14,12),(7,13),(8,13),(7,14),(8,14),(10,14),(13,14),(7,15),(8,15),(7,16),(8,16),(14,16),(7,17),(8,17),(7,18),(8,18),(14,18),(7,19),(8,19),(7,20),(8,20),(13,20),(14,20),(7,21),(8,21),(13,21),(7,22),(8,22),(9,22),(10,22),(14,22),(7,23),(8,23),(9,23),(10,23),(7,24),(8,24),(10,24),(7,25),(8,25),(9,25),(10,25),(14,25),(7,26),(8,26),(9,26),(10,26),(7,27),(8,27),(10,27),(7,47),(8,47),(12,47),(13,47),(7,48),(8,48),(13,48),(7,49),(8,49),(13,49),(7,50),(8,50),(12,50),(13,50),(14,50),(7,51),(8,51),(12,51),(13,51),(14,51),(7,52),(8,52),(12,52),(13,52),(7,53),(8,53),(13,53),(7,54),(8,54),(12,54),(14,54),(7,55),(8,55),(7,56),(8,56),(14,56),(7,57),(8,57),(10,57),(14,57),(7,58),(8,58),(13,58),(14,58),(7,59),(8,59),(14,59),(7,60),(8,60),(10,60),(7,61),(8,61),(10,61),(11,61),(7,62),(8,62),(10,62),(7,63),(8,63),(10,63),(7,64),(8,64),(10,64),(11,64),(7,71),(8,71),(7,120),(8,120),(13,120),(7,121),(8,121),(13,121),(7,122),(8,122),(13,122),(7,123),(8,123),(13,123),(7,124),(8,124),(13,124),(7,125),(8,125),(13,125),(7,126),(8,126),(13,126),(7,127),(8,127),(13,127),(7,128),(8,128),(13,128),(7,129),(8,129),(13,129),(7,130),(8,130),(13,130),(7,131),(8,131),(13,131),(7,160),(8,160),(13,160),(7,161),(8,161),(13,161),(7,162),(8,162),(13,162),(7,164),(13,164),(7,165),(10,165),(7,166),(10,166),(7,167),(10,167),(7,168),(13,168),(7,169),(13,169),(7,170),(13,170),(7,171),(13,171),(7,172),(13,172),(7,173),(13,173),(7,175),(13,175),(7,176),(13,176),(7,177),(13,177),(13,178),(10,179),(10,180),(7,181),(10,181),(7,182),(10,182),(7,183),(10,183),(7,184),(10,184),(7,185),(7,186),(7,187),(7,188),(13,188),(7,189),(13,189),(7,190),(13,190),(7,191),(13,191),(7,192),(13,192),(7,193),(13,193),(7,194),(13,194),(7,195),(10,195),(7,196),(10,196),(7,197),(10,197),(7,198),(10,198),(7,199),(10,199),(7,200),(10,200),(13,200),(7,201),(10,201),(10,202);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Personal de Recepción','Encargado de recpecionar las solicitudes de clientes',1),(7,'Super Administrador','Control total del sistema',1),(8,'Administrador','Administrador general del sistema',1),(9,'Recepción','Recepción de vehículos y atención al cliente',1),(10,'Encargado de Servicios','Gestión completa del área de servicios',1),(11,'Técnico','Ejecución de órdenes de trabajo',1),(12,'Personal de Compras','Registro de compras y proveedores',1),(13,'Encargado de Compras','Gestión y aprobación de compras',1),(14,'Auditor','Solo lectura y reportes',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snc_compras`
--

DROP TABLE IF EXISTS `snc_compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `snc_compras` (
  `id_snc_compras` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  `idproveedores` int(10) unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_snc_compras`),
  KEY `snc_compras_FKIndex1` (`idproveedores`),
  KEY `snc_compras_FKIndex2` (`id_sucursal`),
  KEY `snc_compras_FKIndex3` (`id_usuario`),
  CONSTRAINT `snc_compras_ibfk_1` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `snc_compras_ibfk_2` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `snc_compras_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snc_compras`
--

LOCK TABLES `snc_compras` WRITE;
/*!40000 ALTER TABLE `snc_compras` DISABLE KEYS */;
/*!40000 ALTER TABLE `snc_compras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snc_compras_detalle`
--

DROP TABLE IF EXISTS `snc_compras_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `snc_compras_detalle` (
  `id_articulo` int(10) unsigned NOT NULL,
  `id_snc_compras` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  `precio` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`id_snc_compras`),
  KEY `articulos_has_snc_compras_FKIndex1` (`id_articulo`),
  KEY `articulos_has_snc_compras_FKIndex2` (`id_snc_compras`),
  CONSTRAINT `snc_compras_detalle_ibfk_1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `snc_compras_detalle_ibfk_2` FOREIGN KEY (`id_snc_compras`) REFERENCES `snc_compras` (`id_snc_compras`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snc_compras_detalle`
--

LOCK TABLES `snc_compras_detalle` WRITE;
/*!40000 ALTER TABLE `snc_compras_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `snc_compras_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `snc_compras_diferencia`
--

DROP TABLE IF EXISTS `snc_compras_diferencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `snc_compras_diferencia` (
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `id_snc_compras` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idcompra_cabecera`,`id_snc_compras`),
  KEY `compra_cabecera_has_snc_compras_FKIndex1` (`idcompra_cabecera`),
  KEY `compra_cabecera_has_snc_compras_FKIndex2` (`id_snc_compras`),
  CONSTRAINT `snc_compras_diferencia_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `snc_compras_diferencia_ibfk_2` FOREIGN KEY (`id_snc_compras`) REFERENCES `snc_compras` (`id_snc_compras`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `snc_compras_diferencia`
--

LOCK TABLES `snc_compras_diferencia` WRITE;
/*!40000 ALTER TABLE `snc_compras_diferencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `snc_compras_diferencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `id_sucursal` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `stockcant_max` int(10) unsigned DEFAULT NULL,
  `stockcant_min` int(10) unsigned DEFAULT NULL,
  `stockDisponible` decimal(12,4) NOT NULL,
  `stockUltActualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  `stockUsuActualizacion` bigint(20) NOT NULL,
  `stockultimoIdActualizacion` int(10) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`,`id_articulo`),
  KEY `sucursales_has_stock_FKIndex1` (`id_sucursal`),
  KEY `articulos_has_stock_FKIndex2` (`id_articulo`),
  CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `stock_ibfk_3` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` VALUES (2,1,200,15,10.0000,'2026-01-03 22:22:41',1,51),(2,6,200,15,26.0000,'2026-01-04 18:51:07',1,226),(2,7,200,15,27.0000,'2026-01-12 22:03:15',1,234),(2,8,200,15,94.0000,'2026-01-11 20:41:18',1,229),(2,9,200,15,14.0000,'2026-01-03 22:33:01',1,52),(2,10,200,15,43.0000,'2026-01-12 20:56:35',1,50),(2,11,200,15,50.0000,'2026-01-03 22:22:41',1,51),(2,13,200,15,10.0000,'2026-01-12 15:37:32',7,56),(2,15,200,15,25.0000,'2026-01-14 14:32:03',7,51),(2,17,200,15,2.0000,'2026-01-14 20:33:17',8,240),(3,8,200,15,6.0000,'2026-01-02 08:06:25',7,11),(3,9,200,15,24.0000,'2026-01-02 20:26:30',7,37),(3,10,200,15,22.0000,'2026-01-02 08:09:49',7,24),(3,13,200,15,10.0000,'2026-01-02 07:42:25',7,15),(5,15,200,15,5.0000,'2026-01-14 15:40:12',10,41);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucmovimientostock`
--

DROP TABLE IF EXISTS `sucmovimientostock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucmovimientostock` (
  `MovStockId` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(10) unsigned NOT NULL,
  `TipoMovStockId` varchar(30) DEFAULT NULL,
  `MovStockProductoId` varchar(16) DEFAULT NULL,
  `MovStockCantidad` decimal(12,4) NOT NULL,
  `MovStockPrecioVenta` decimal(14,2) NOT NULL,
  `MovStockCosto` decimal(14,2) NOT NULL,
  `MovStockFechaHora` datetime NOT NULL,
  `MovStockNroTicket` varchar(80) DEFAULT NULL,
  `MovStockPOS` varchar(80) DEFAULT NULL,
  `MovStockUsuario` bigint(20) NOT NULL,
  `MovStockSigno` smallint(6) NOT NULL,
  `MovStockReferencia` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`MovStockId`),
  KEY `idx_sucursal` (`id_sucursal`),
  CONSTRAINT `fk_movstock_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=241 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucmovimientostock`
--

LOCK TABLES `sucmovimientostock` WRITE;
/*!40000 ALTER TABLE `sucmovimientostock` DISABLE KEYS */;
INSERT INTO `sucmovimientostock` VALUES (18,2,'RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:13:11','001-001-0000001',NULL,1,1,'30'),(19,2,'RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:13:11','001-001-0000001',NULL,1,1,'30'),(22,2,'ANULACION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:25:53','ANUL_COMPRA# 30',NULL,1,-1,'ANUL_COMPRA# 30'),(23,2,'ANULACION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:25:53','ANUL_COMPRA# 30',NULL,1,-1,'ANUL_COMPRA# 30'),(24,2,'RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:36:22','001-001-0000002',NULL,1,1,'31'),(25,2,'RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:36:22','001-001-0000002',NULL,1,1,'31'),(26,2,'RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:40:08','001-001-0000003',NULL,1,1,'32'),(27,2,'RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:40:08','001-001-0000003',NULL,1,1,'32'),(28,2,'RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:43:34','001-001-0000004',NULL,1,1,'33'),(29,2,'RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:43:34','001-001-0000004',NULL,1,1,'33'),(30,2,'RECEPCION COMPRA','9',6.0000,0.00,4500.00,'2025-12-12 21:47:32','010-001-0000001',NULL,1,1,'34'),(31,2,'AJUSTE_INV','9',84.0000,0.00,2500.00,'2025-12-14 16:18:28',NULL,NULL,1,1,'AJUSTE #25'),(32,2,'AJUSTE_INV','10',24.0000,0.00,1555.00,'2025-12-14 16:18:28',NULL,NULL,1,-1,'AJUSTE #25'),(33,2,'AJUSTE_INV','11',135.0000,0.00,1555.00,'2025-12-14 16:18:28',NULL,NULL,1,1,'AJUSTE #25'),(34,2,'AJUSTE_INV','9',84.0000,0.00,2500.00,'2025-12-14 16:20:33',NULL,NULL,1,1,'AJUSTE #25'),(35,2,'AJUSTE_INV','10',24.0000,0.00,1555.00,'2025-12-14 16:20:33',NULL,NULL,1,-1,'AJUSTE #25'),(36,2,'AJUSTE_INV','11',135.0000,0.00,1555.00,'2025-12-14 16:20:33',NULL,NULL,1,1,'AJUSTE #25'),(37,2,'AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:25:16',NULL,NULL,1,-1,'AJUSTE #33'),(38,2,'AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:25:16',NULL,NULL,1,1,'AJUSTE #33'),(39,2,'AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:25:16',NULL,NULL,1,-1,'AJUSTE #33'),(40,2,'AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:26:00',NULL,NULL,1,-1,'AJUSTE #33'),(41,2,'AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:26:00',NULL,NULL,1,1,'AJUSTE #33'),(42,2,'AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:26:00',NULL,NULL,1,-1,'AJUSTE #33'),(43,2,'AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:27:22',NULL,NULL,1,1,'AJUSTE #34'),(44,2,'AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:27:22',NULL,NULL,1,-1,'AJUSTE #34'),(45,2,'AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:27:22',NULL,NULL,1,1,'AJUSTE #34'),(46,2,'AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:31:24',NULL,NULL,1,1,'AJUSTE #34'),(47,2,'AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:31:24',NULL,NULL,1,-1,'AJUSTE #34'),(48,2,'AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:31:24',NULL,NULL,1,1,'AJUSTE #34'),(49,2,'AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:31:35',NULL,NULL,1,1,'AJUSTE #34'),(50,2,'AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:31:35',NULL,NULL,1,-1,'AJUSTE #34'),(51,2,'AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:31:35',NULL,NULL,1,1,'AJUSTE #34'),(52,2,'AJUSTE_INV','9',148.0000,0.00,2500.00,'2025-12-14 16:36:12',NULL,NULL,1,-1,'AJUSTE #35'),(53,2,'AJUSTE_INV','10',158.0000,0.00,1555.00,'2025-12-14 16:36:12',NULL,NULL,1,1,'AJUSTE #35'),(54,2,'AJUSTE_INV','11',430.0000,0.00,1555.00,'2025-12-14 16:36:12',NULL,NULL,1,-1,'AJUSTE #35'),(55,2,'AJUSTE_INV','9',10.0000,0.00,2500.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(56,2,'AJUSTE_INV','10',10.0000,0.00,1555.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(57,2,'AJUSTE_INV','11',10.0000,0.00,1555.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(58,2,'AJUSTE_INV','9',85.0000,0.00,2500.00,'2025-12-14 16:38:57',NULL,NULL,1,-1,'AJUSTE #37'),(59,2,'AJUSTE_INV','9',5.0000,0.00,2500.00,'2025-12-14 16:57:11',NULL,NULL,1,1,'AJUSTE #39'),(60,2,'AJUSTE_INV','10',80.0000,0.00,1555.00,'2025-12-14 16:57:11',NULL,NULL,1,-1,'AJUSTE #39'),(61,2,'AJUSTE_INV','11',80.0000,0.00,1555.00,'2025-12-14 16:57:11',NULL,NULL,1,-1,'AJUSTE #39'),(62,2,'AJUSTE_INV','9',80.0000,0.00,2500.00,'2025-12-14 17:16:33',NULL,NULL,1,-1,'AJUSTE #37'),(63,2,'AJUSTE_INV','9',80.0000,0.00,2500.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(64,2,'AJUSTE_INV','10',0.0000,0.00,1555.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(65,2,'AJUSTE_INV','11',0.0000,0.00,1555.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(66,2,'AJUSTE_INV','9',10.0000,0.00,2500.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(67,2,'AJUSTE_INV','10',10.0000,0.00,1555.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(68,2,'AJUSTE_INV','11',10.0000,0.00,1555.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(69,2,'AJUSTE_INV','9',30.0000,0.00,2500.00,'2025-12-14 23:00:11',NULL,NULL,1,1,'AJUSTE #46'),(70,2,'ANULACION_AJUSTE_INV','9',10.0000,0.00,2500.00,'2025-12-15 20:07:49',NULL,NULL,1,-1,'Anulación ajuste inventario #41'),(71,2,'ANULACION_AJUSTE_INV','10',10.0000,0.00,1555.00,'2025-12-15 20:07:49',NULL,NULL,1,-1,'Anulación ajuste inventario #41'),(72,2,'ANULACION_AJUSTE_INV','11',10.0000,0.00,1555.00,'2025-12-15 20:07:49',NULL,NULL,1,-1,'Anulación ajuste inventario #41'),(73,2,'AJUSTE_INV','1',10.0000,0.00,6000.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(74,2,'AJUSTE_INV','6',10.0000,0.00,5000.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(75,2,'AJUSTE_INV','7',10.0000,0.00,4500.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(76,2,'AJUSTE_INV','8',10.0000,0.00,7500.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(77,2,'AJUSTE_INV','9',30.0000,0.00,2500.00,'2025-12-15 20:10:06',NULL,NULL,1,-1,'AJUSTE #47'),(78,2,'AJUSTE_INV','10',0.0000,0.00,1555.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(79,2,'AJUSTE_INV','11',0.0000,0.00,1555.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(80,2,'AJUSTE_INV','13',10.0000,0.00,5000.00,'2025-12-15 20:10:06',NULL,NULL,1,1,'AJUSTE #47'),(81,2,'ANULACION_AJUSTE_INV','1',10.0000,0.00,6000.00,'2025-12-15 20:11:49',NULL,NULL,1,-1,'Anulación ajuste inventario #47'),(82,2,'ANULACION_AJUSTE_INV','6',10.0000,0.00,5000.00,'2025-12-15 20:11:49',NULL,NULL,1,-1,'Anulación ajuste inventario #47'),(83,2,'ANULACION_AJUSTE_INV','7',10.0000,0.00,4500.00,'2025-12-15 20:11:49',NULL,NULL,1,-1,'Anulación ajuste inventario #47'),(84,2,'ANULACION_AJUSTE_INV','8',10.0000,0.00,7500.00,'2025-12-15 20:11:49',NULL,NULL,1,-1,'Anulación ajuste inventario #47'),(85,2,'ANULACION_AJUSTE_INV','9',30.0000,0.00,2500.00,'2025-12-15 20:11:49',NULL,NULL,1,1,'Anulación ajuste inventario #47'),(86,2,'ANULACION_AJUSTE_INV','10',0.0000,0.00,1555.00,'2025-12-15 20:11:49',NULL,NULL,1,1,'Anulación ajuste inventario #47'),(87,2,'ANULACION_AJUSTE_INV','11',0.0000,0.00,1555.00,'2025-12-15 20:11:49',NULL,NULL,1,1,'Anulación ajuste inventario #47'),(88,2,'ANULACION_AJUSTE_INV','13',10.0000,0.00,5000.00,'2025-12-15 20:11:49',NULL,NULL,1,-1,'Anulación ajuste inventario #47'),(89,2,'AJUSTE_INV','1',10.0000,0.00,6000.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(90,2,'AJUSTE_INV','6',10.0000,0.00,5000.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(91,2,'AJUSTE_INV','7',10.0000,0.00,4500.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(92,2,'AJUSTE_INV','8',10.0000,0.00,7500.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(93,2,'AJUSTE_INV','9',0.0000,0.00,2500.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(94,2,'AJUSTE_INV','10',0.0000,0.00,1555.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(95,2,'AJUSTE_INV','11',0.0000,0.00,1555.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(96,2,'AJUSTE_INV','13',10.0000,0.00,5000.00,'2025-12-15 20:22:26',NULL,NULL,1,1,'AJUSTE #48'),(97,2,'AJUSTE_INV','1',90.0000,0.00,6000.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(98,2,'AJUSTE_INV','6',90.0000,0.00,5000.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(99,2,'AJUSTE_INV','7',90.0000,0.00,4500.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(100,2,'AJUSTE_INV','8',90.0000,0.00,7500.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(101,2,'AJUSTE_INV','9',360.0000,0.00,2500.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(102,2,'AJUSTE_INV','10',90.0000,0.00,1555.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(103,2,'AJUSTE_INV','11',90.0000,0.00,1555.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(104,2,'AJUSTE_INV','13',90.0000,0.00,5000.00,'2025-12-15 20:23:13',NULL,NULL,1,1,'AJUSTE #49'),(105,2,'ANULACION_AJUSTE_INV','1',90.0000,0.00,6000.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(106,2,'ANULACION_AJUSTE_INV','6',90.0000,0.00,5000.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(107,2,'ANULACION_AJUSTE_INV','7',90.0000,0.00,4500.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(108,2,'ANULACION_AJUSTE_INV','8',90.0000,0.00,7500.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(109,2,'ANULACION_AJUSTE_INV','9',360.0000,0.00,2500.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(110,2,'ANULACION_AJUSTE_INV','10',90.0000,0.00,1555.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(111,2,'ANULACION_AJUSTE_INV','11',90.0000,0.00,1555.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(112,2,'ANULACION_AJUSTE_INV','13',90.0000,0.00,5000.00,'2025-12-15 20:24:11',NULL,NULL,1,-1,'Anulación ajuste inventario #49'),(113,2,'REG. SERVICIO','6',1.0000,6800.00,0.00,'2025-12-27 21:32:20',NULL,NULL,1,-1,'REG_SERV #4'),(114,2,'ANULACION REG. SERVICIO','6',1.0000,6800.00,0.00,'2025-12-27 21:54:14',NULL,NULL,1,1,'ANUL_REG_SERV #4'),(115,2,'RECEPCION COMPRA','10',7.0000,0.00,5656.00,'2025-12-28 20:15:16','001-001-0000056',NULL,1,1,'35'),(116,2,'RECEPCION COMPRA','6',15.0000,0.00,45000.00,'2025-12-28 20:20:52','001-001-0000058',NULL,1,1,'36'),(117,2,'RECEPCION COMPRA','10',12.0000,0.00,32333.00,'2025-12-28 20:20:52','001-001-0000058',NULL,1,1,'36'),(118,2,'AJUSTE_INV','6',4.0000,0.00,5000.00,'2025-12-28 20:42:34',NULL,NULL,1,-1,'AJUSTE #50'),(119,2,'AJUSTE_INV','7',1.0000,0.00,4500.00,'2025-12-28 20:42:34',NULL,NULL,1,1,'AJUSTE #50'),(120,2,'AJUSTE_INV','8',1.0000,0.00,7500.00,'2025-12-28 20:42:34',NULL,NULL,1,1,'AJUSTE #50'),(121,2,'AJUSTE_INV','10',7.0000,0.00,1555.00,'2025-12-28 20:42:34',NULL,NULL,1,-1,'AJUSTE #50'),(122,2,'REG. SERVICIO','7',1.0000,6890.00,0.00,'2025-12-28 21:15:47',NULL,NULL,1,-1,'REG_SERV #5'),(123,2,'REG. SERVICIO','8',4.0000,5000.00,0.00,'2025-12-28 21:15:47',NULL,NULL,1,-1,'REG_SERV #5'),(124,2,'REG. SERVICIO','11',10.0000,6554.00,0.00,'2025-12-28 21:15:47',NULL,NULL,1,-1,'REG_SERV #5'),(125,2,'ANULACION REG. SERVICIO','7',1.0000,6890.00,0.00,'2025-12-28 21:18:14',NULL,NULL,1,1,'ANUL_REG_SERV #5'),(126,2,'ANULACION REG. SERVICIO','8',4.0000,5000.00,0.00,'2025-12-28 21:18:14',NULL,NULL,1,1,'ANUL_REG_SERV #5'),(127,2,'ANULACION REG. SERVICIO','11',10.0000,6554.00,0.00,'2025-12-28 21:18:14',NULL,NULL,1,1,'ANUL_REG_SERV #5'),(134,2,'REG. SERVICIO','7',1.0000,6890.00,0.00,'2025-12-28 21:44:44',NULL,NULL,1,-1,'REG_SERV #10'),(135,2,'REG. SERVICIO','8',4.0000,5000.00,0.00,'2025-12-28 21:44:44',NULL,NULL,1,-1,'REG_SERV #10'),(136,2,'REG. SERVICIO','11',10.0000,6554.00,0.00,'2025-12-28 21:44:44',NULL,NULL,1,-1,'REG_SERV #10'),(137,2,'TRANSFERENCIA_SALIDA','10',1.0000,0.00,1555.00,'2026-01-01 13:22:42','001-001-0000004',NULL,1,-1,'TRANSFERENCIA #13'),(138,2,'TRANSFERENCIA_SALIDA','9',10.0000,0.00,2500.00,'2026-01-01 13:22:42','001-001-0000004',NULL,1,-1,'TRANSFERENCIA #13'),(139,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-01 14:15:02','001-001-0000005',NULL,1,-1,'TRANSFERENCIA #14'),(140,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 14:18:24','001-001-0000006',NULL,1,-1,'TRANSFERENCIA #15'),(141,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 14:19:13','001-001-0000007',NULL,1,-1,'TRANSFERENCIA #16'),(142,2,'TRANSFERENCIA_SALIDA','13',2.0000,0.00,5000.00,'2026-01-01 14:28:52','001-001-0000008',NULL,1,-1,'TRANSFERENCIA #17'),(143,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 14:30:40','001-001-0000009',NULL,1,-1,'TRANSFERENCIA #18'),(144,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 18:46:46','001-001-0000010',NULL,1,-1,'TRANSFERENCIA #19'),(145,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 18:48:03','001-001-0000011',NULL,1,-1,'TRANSFERENCIA #20'),(146,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 18:52:40','001-001-0000012',NULL,1,-1,'TRANSFERENCIA #21'),(147,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 18:56:45','001-001-0000013',NULL,1,-1,'TRANSFERENCIA #22'),(148,2,'TRANSFERENCIA_SALIDA','13',1.0000,0.00,5000.00,'2026-01-01 21:16:42','001-001-0000014',NULL,1,-1,'TRANSFERENCIA #23'),(149,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 21:50:53',NULL,NULL,7,1,'TRANSFERENCIA #20'),(150,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 21:54:26',NULL,NULL,7,1,'TRANSFERENCIA #23'),(151,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 21:55:16',NULL,NULL,7,1,'TRANSFERENCIA #22'),(152,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 21:55:55',NULL,NULL,7,1,'TRANSFERENCIA #18'),(153,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 21:57:02',NULL,NULL,7,1,'TRANSFERENCIA #21'),(154,3,'TRANSFERENCIA_ENTRADA','8',2.0000,0.00,0.00,'2026-01-01 21:58:13',NULL,NULL,7,1,'TRANSFERENCIA #10'),(155,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-01 22:04:19',NULL,NULL,7,1,'TRANSFERENCIA #19'),(156,3,'TRANSFERENCIA_ENTRADA','13',2.0000,0.00,0.00,'2026-01-01 22:05:20',NULL,NULL,7,1,'TRANSFERENCIA #17'),(157,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-02 07:40:54',NULL,NULL,7,1,'TRANSFERENCIA #16'),(158,3,'TRANSFERENCIA_ENTRADA','13',1.0000,0.00,0.00,'2026-01-02 07:42:25',NULL,NULL,7,1,'TRANSFERENCIA #15'),(159,3,'TRANSFERENCIA_ENTRADA','9',2.0000,0.00,0.00,'2026-01-02 07:55:18',NULL,NULL,7,1,'TRANSFERENCIA #14'),(160,3,'TRANSFERENCIA_ENTRADA','9',10.0000,0.00,0.00,'2026-01-02 07:57:03',NULL,NULL,7,1,'TRANSFERENCIA #13'),(161,3,'TRANSFERENCIA_ENTRADA','10',1.0000,0.00,0.00,'2026-01-02 07:57:03',NULL,NULL,7,1,'TRANSFERENCIA #13'),(162,3,'TRANSFERENCIA_ENTRADA','8',1.0000,0.00,0.00,'2026-01-02 08:00:03',NULL,NULL,7,1,'TRANSFERENCIA #12'),(163,3,'TRANSFERENCIA_ENTRADA','9',3.0000,0.00,0.00,'2026-01-02 08:00:03',NULL,NULL,7,1,'TRANSFERENCIA #12'),(164,3,'TRANSFERENCIA_ENTRADA','10',20.0000,0.00,0.00,'2026-01-02 08:00:03',NULL,NULL,7,1,'TRANSFERENCIA #12'),(165,3,'TRANSFERENCIA_ENTRADA','8',3.0000,0.00,0.00,'2026-01-02 08:06:25',NULL,NULL,7,1,'TRANSFERENCIA #11'),(166,2,'TRANSFERENCIA_SALIDA','10',1.0000,0.00,1555.00,'2026-01-02 08:09:12','001-001-0000015',NULL,1,-1,'TRANSFERENCIA #24'),(167,3,'TRANSFERENCIA_ENTRADA','10',1.0000,0.00,0.00,'2026-01-02 08:09:49',NULL,NULL,7,1,'TRANSFERENCIA #24'),(168,2,'TRANSFERENCIA_SALIDA','9',1.0000,0.00,2500.00,'2026-01-02 13:10:55','001-001-0000016',NULL,1,-1,'TRANSFERENCIA #25'),(169,3,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 13:11:57',NULL,NULL,7,1,'TRANSFERENCIA #25'),(170,2,'TRANSFERENCIA_SALIDA','9',1.0000,0.00,2500.00,'2026-01-02 13:21:27','001-001-0000017',NULL,1,-1,'TRANSFERENCIA #26'),(171,3,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 13:21:58',NULL,NULL,7,1,'TRANSFERENCIA #26'),(172,2,'TRANSFERENCIA_SALIDA','9',3.0000,0.00,2500.00,'2026-01-02 19:34:55','001-001-0000018',NULL,1,-1,'TRANSFERENCIA #27'),(176,3,'TRANSFERENCIA_ENTRADA','9',2.0000,0.00,0.00,'2026-01-02 19:39:35',NULL,NULL,7,1,'TRANSFERENCIA #27'),(177,2,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 19:50:00',NULL,NULL,1,1,'TRANSFERENCIA #28'),(178,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 19:52:06','001-001-0000019',NULL,1,-1,'TRANSFERENCIA #29'),(179,3,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 19:52:33',NULL,NULL,7,1,'TRANSFERENCIA #29'),(180,2,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 19:53:57',NULL,NULL,1,1,'TRANSFERENCIA #30'),(181,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 20:01:12','001-001-0000020',NULL,1,-1,'TRANSFERENCIA #31'),(182,3,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 20:02:32',NULL,NULL,7,1,'TRANSFERENCIA #31'),(183,2,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 20:05:13',NULL,NULL,1,1,'TRANSFERENCIA #32'),(184,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 20:11:16','001-001-0000021',NULL,1,-1,'TRANSFERENCIA #33'),(185,3,'TRANSFERENCIA_ENTRADA','9',1.0000,0.00,0.00,'2026-01-02 20:16:21',NULL,NULL,7,1,'TRANSFERENCIA #33'),(186,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 20:24:43','001-001-0000022',NULL,1,-1,'TRANSFERENCIA #35'),(187,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 20:26:15','001-001-0000023',NULL,1,-1,'TRANSFERENCIA #37'),(188,2,'TRANSFERENCIA_SALIDA','9',2.0000,0.00,2500.00,'2026-01-02 20:47:45','001-001-0000024',NULL,1,-1,'TRANSFERENCIA #39'),(189,2,'TRANSFERENCIA_SALIDA','7',2.0000,0.00,4500.00,'2026-01-02 20:47:45','001-001-0000024',NULL,1,-1,'TRANSFERENCIA #39'),(190,2,'RECEPCION COMPRA','8',5.0000,0.00,5555.00,'2026-01-02 21:39:46','001-001-0000049',NULL,1,1,'38'),(191,2,'RECEPCION COMPRA','8',15.0000,0.00,5555.00,'2026-01-02 21:56:43','001-001-0000055',NULL,1,1,'42'),(192,2,'RECEPCION COMPRA','10',20.0000,0.00,5656.00,'2026-01-02 21:56:43','001-001-0000055',NULL,1,1,'42'),(193,2,'RECEPCION COMPRA','11',45.0000,0.00,56565.00,'2026-01-02 21:56:43','001-001-0000055',NULL,1,1,'42'),(194,2,'RECEPCION COMPRA','10',1.0000,0.00,5600.00,'2026-01-02 22:03:55','001-001-0000050',NULL,1,1,'44'),(195,2,'RECEPCION COMPRA','8',23.0000,0.00,13333.00,'2026-01-02 22:03:55','001-001-0000050',NULL,1,1,'44'),(196,2,'RECEPCION COMPRA','6',5.0000,0.00,45666.00,'2026-01-02 22:03:55','001-001-0000050',NULL,1,1,'44'),(197,2,'RECEPCION COMPRA','8',23.0000,0.00,2222.00,'2026-01-02 22:12:56','001-002-0000052',NULL,1,1,'45'),(198,2,'RECEPCION COMPRA','7',10.0000,0.00,5000.00,'2026-01-02 22:12:56','001-002-0000052',NULL,1,1,'45'),(199,2,'RECEPCION COMPRA','10',3.0000,0.00,4444.00,'2026-01-02 22:12:56','001-002-0000052',NULL,1,1,'45'),(200,2,'RECEPCION COMPRA','8',23.0000,0.00,2222.00,'2026-01-02 22:14:19','001-002-0000052',NULL,1,1,'46'),(201,2,'RECEPCION COMPRA','7',10.0000,0.00,5000.00,'2026-01-02 22:14:19','001-002-0000052',NULL,1,1,'46'),(202,2,'RECEPCION COMPRA','10',3.0000,0.00,4444.00,'2026-01-02 22:14:19','001-002-0000052',NULL,1,1,'46'),(203,2,'RECEPCION COMPRA','10',10.0000,0.00,5000.00,'2026-01-03 15:16:46','001-001-0000069',NULL,1,1,'47'),(204,2,'RECEPCION COMPRA','13',10.0000,0.00,4500.00,'2026-01-03 15:16:46','001-001-0000069',NULL,1,1,'47'),(205,2,'RECEPCION COMPRA','10',10.0000,0.00,5000.00,'2026-01-03 15:25:25','001-001-0000070',NULL,1,1,'48'),(206,2,'RECEPCION COMPRA','8',10.0000,0.00,5600.00,'2026-01-03 15:25:25','001-001-0000070',NULL,1,1,'48'),(207,2,'RECEPCION COMPRA','11',5.0000,0.00,2500.00,'2026-01-03 15:25:25','001-001-0000070',NULL,1,1,'48'),(208,2,'ANULACION COMPRA','10',10.0000,0.00,5000.00,'2026-01-03 16:03:23','ANUL_COMPRA# 47',NULL,1,-1,'ANUL_COMPRA# 47'),(209,2,'ANULACION COMPRA','13',10.0000,0.00,4500.00,'2026-01-03 16:03:23','ANUL_COMPRA# 47',NULL,1,-1,'ANUL_COMPRA# 47'),(210,2,'AJUSTE_INV','1',0.0000,0.00,6000.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(211,2,'AJUSTE_INV','6',0.0000,0.00,5000.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(212,2,'AJUSTE_INV','7',0.0000,0.00,4500.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(213,2,'AJUSTE_INV','8',0.0000,0.00,7500.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(214,2,'AJUSTE_INV','9',0.0000,0.00,2500.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(215,2,'AJUSTE_INV','10',0.0000,0.00,1555.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(216,2,'AJUSTE_INV','11',0.0000,0.00,1555.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(217,2,'AJUSTE_INV','13',10.0000,0.00,5000.00,'2026-01-03 22:22:41',NULL,NULL,1,1,'AJUSTE #51'),(218,2,'AJUSTE_INV','8',1.0000,0.00,7500.00,'2026-01-03 22:26:44',NULL,NULL,1,1,'AJUSTE #52'),(219,2,'AJUSTE_INV','9',1.0000,0.00,2500.00,'2026-01-03 22:26:44',NULL,NULL,1,1,'AJUSTE #52'),(220,2,'AJUSTE_INV','10',1.0000,0.00,1555.00,'2026-01-03 22:26:44',NULL,NULL,1,-1,'AJUSTE #52'),(221,2,'ANULACION_AJUSTE_INV','8',1.0000,0.00,7500.00,'2026-01-03 22:33:01',NULL,NULL,1,-1,'Anulación ajuste inventario #52'),(222,2,'ANULACION_AJUSTE_INV','9',1.0000,0.00,2500.00,'2026-01-03 22:33:01',NULL,NULL,1,-1,'Anulación ajuste inventario #52'),(223,2,'ANULACION_AJUSTE_INV','10',1.0000,0.00,1555.00,'2026-01-03 22:33:01',NULL,NULL,1,1,'Anulación ajuste inventario #52'),(224,2,'REG. SERVICIO','8',4.0000,10000.00,0.00,'2026-01-04 08:59:16',NULL,NULL,1,-1,'REG_SERV #12'),(225,2,'REG. SERVICIO','10',4.0000,6000.00,0.00,'2026-01-04 08:59:16',NULL,NULL,1,-1,'REG_SERV #12'),(226,2,'REG. SERVICIO','6',1.0000,6800.00,0.00,'2026-01-04 09:27:58',NULL,NULL,1,-1,'REG_SERV #13'),(227,2,'ANULACION REG. SERVICIO','6',1.0000,6800.00,0.00,'2026-01-04 18:51:07',NULL,NULL,1,1,'ANUL_REG_SERV #3'),(228,2,'REG. SERVICIO','13',1.0000,3000.00,0.00,'2026-01-11 20:32:36',NULL,NULL,1,-1,'REG_SERV #15'),(229,2,'REG. SERVICIO','8',1.0000,5000.00,0.00,'2026-01-11 20:41:18',NULL,NULL,1,-1,'REG_SERV #16'),(230,2,'AJUSTE_INV','13',1.0000,0.00,5000.00,'2026-01-12 15:37:32',NULL,NULL,7,1,'AJUSTE #56'),(231,2,'RECEPCION COMPRA','10',10.0000,0.00,5000.00,'2026-01-12 17:48:25','001-001-0000007',NULL,1,1,'49'),(232,2,'RECEPCION COMPRA','10',10.0000,0.00,5000.00,'2026-01-12 18:11:37','001-001-0000045',NULL,1,1,'50'),(233,2,'ANULACION COMPRA','10',10.0000,0.00,5000.00,'2026-01-12 20:56:35','ANUL_COMPRA# 50',NULL,1,-1,'ANUL_COMPRA# 50'),(234,2,'REG. SERVICIO','7',1.0000,6890.00,0.00,'2026-01-12 22:03:15',NULL,NULL,1,-1,'REG_SERV #17'),(235,2,'RECEPCION COMPRA','15',30.0000,0.00,32000.00,'2026-01-14 13:45:49','001-002-0000001',NULL,7,1,'51'),(236,2,'NC_COMPRA_DEV','15',1.0000,0.00,32000.00,'2026-01-14 14:20:57',NULL,NULL,7,-1,'NC 001-002-0000001'),(237,2,'ANULA_NC_COMPRA','15',1.0000,0.00,32000.00,'2026-01-14 14:32:03',NULL,NULL,7,1,'ANULA NC 001-002-0000001'),(238,2,'TRANSFERENCIA_SALIDA','15',5.0000,0.00,35000.00,'2026-01-14 15:09:20','001-001-0000025',NULL,7,-1,'TRANSFERENCIA #41'),(239,2,'AJUSTE_INV','17',3.0000,0.00,900000.00,'2026-01-14 20:29:04',NULL,NULL,7,1,'AJUSTE #57'),(240,2,'REG. SERVICIO','17',1.0000,1080000.00,0.00,'2026-01-14 20:33:17',NULL,NULL,8,-1,'REG_SERV #18');
/*!40000 ALTER TABLE `sucmovimientostock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursal_documento`
--

DROP TABLE IF EXISTS `sucursal_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursal_documento` (
  `id_documento` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(10) unsigned NOT NULL,
  `tipo_documento` enum('remision','factura','nota_credito','nota_debito') NOT NULL,
  `establecimiento` varchar(10) NOT NULL,
  `punto_expedicion` varchar(10) NOT NULL,
  `numero_actual` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursal_documento`
--

LOCK TABLES `sucursal_documento` WRITE;
/*!40000 ALTER TABLE `sucursal_documento` DISABLE KEYS */;
INSERT INTO `sucursal_documento` VALUES (1,2,'remision','001','001',25,1);
/*!40000 ALTER TABLE `sucursal_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursal_timbrado`
--

DROP TABLE IF EXISTS `sucursal_timbrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursal_timbrado` (
  `id_timbrado` int(11) NOT NULL AUTO_INCREMENT,
  `id_sucursal` int(10) unsigned NOT NULL,
  `timbrado` varchar(20) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_timbrado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursal_timbrado`
--

LOCK TABLES `sucursal_timbrado` WRITE;
/*!40000 ALTER TABLE `sucursal_timbrado` DISABLE KEYS */;
INSERT INTO `sucursal_timbrado` VALUES (1,2,'12345687','2025-12-01','2026-12-31',1);
/*!40000 ALTER TABLE `sucursal_timbrado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursales` (
  `id_sucursal` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int(10) unsigned NOT NULL,
  `suc_descri` varchar(50) DEFAULT NULL,
  `suc_direccion` varchar(120) DEFAULT NULL,
  `suc_telefono` varchar(50) DEFAULT NULL,
  `nro_establecimiento` int(10) unsigned DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`),
  KEY `sucursales_FKIndex1` (`id_empresa`),
  CONSTRAINT `sucursales_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (2,2,'lubriReducto 1','san lorenzo','021567834',1,1),(3,2,'lubriReducto 2','capiata','021567833',2,1),(5,2,'lubriReducto 3','Itaugua','021567838',3,1),(6,2,'LubriReducto 4','limpio','021203431',4,1);
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timbrado`
--

DROP TABLE IF EXISTS `timbrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timbrado` (
  `idtimbrado` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `numero_tim` int(10) unsigned NOT NULL,
  `fecha_ini` date NOT NULL,
  `fecha_venc` date NOT NULL,
  `nro_fac_desde` int(10) unsigned NOT NULL,
  `nro_fac_hasta` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idtimbrado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timbrado`
--

LOCK TABLES `timbrado` WRITE;
/*!40000 ALTER TABLE `timbrado` DISABLE KEYS */;
/*!40000 ALTER TABLE `timbrado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_impuesto`
--

DROP TABLE IF EXISTS `tipo_impuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_impuesto` (
  `idiva` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo_impuesto_descri` varchar(20) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `ratevalueiva` double DEFAULT NULL,
  `divisor` double DEFAULT NULL,
  PRIMARY KEY (`idiva`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_impuesto`
--

LOCK TABLES `tipo_impuesto` WRITE;
/*!40000 ALTER TABLE `tipo_impuesto` DISABLE KEYS */;
INSERT INTO `tipo_impuesto` VALUES (1,'5%',1,0.05,21),(2,'10%',1,0.1,11),(3,'EXENTO',1,0,0);
/*!40000 ALTER TABLE `tipo_impuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencia_stock`
--

DROP TABLE IF EXISTS `transferencia_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencia_stock` (
  `idtransferencia` bigint(20) NOT NULL AUTO_INCREMENT,
  `idtransferencia_origen` int(11) DEFAULT NULL,
  `sucursal_origen` bigint(20) NOT NULL,
  `sucursal_destino` bigint(20) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` datetime DEFAULT NULL,
  `estado` varchar(30) NOT NULL,
  `observacion` text DEFAULT NULL,
  `usuario_envia` bigint(20) NOT NULL,
  `usuario_recibe` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`idtransferencia`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock`
--

LOCK TABLES `transferencia_stock` WRITE;
/*!40000 ALTER TABLE `transferencia_stock` DISABLE KEYS */;
INSERT INTO `transferencia_stock` VALUES (10,NULL,2,3,'2025-12-31 21:58:34',NULL,'recibido','test',1,7),(11,NULL,2,3,'2026-01-01 00:18:59',NULL,'recibido','test',1,7),(12,NULL,2,3,'2026-01-01 00:21:18',NULL,'recibido','test2',1,7),(13,NULL,2,3,'2026-01-01 13:22:42',NULL,'recibido','test 3',1,7),(14,NULL,2,3,'2026-01-01 14:15:02',NULL,'recibido','test impresion',1,7),(15,NULL,2,3,'2026-01-01 14:18:24',NULL,'recibido','test impresion 2',1,7),(16,NULL,2,3,'2026-01-01 14:19:13',NULL,'recibido','test',1,7),(17,NULL,2,3,'2026-01-01 14:28:52',NULL,'recibido','testea',1,7),(18,NULL,2,3,'2026-01-01 14:30:40',NULL,'recibido','asdsad',1,7),(19,NULL,2,3,'2026-01-01 18:46:46',NULL,'recibido','test',1,7),(20,NULL,2,3,'2026-01-01 18:48:03',NULL,'recibido','test',1,7),(21,NULL,2,3,'2026-01-01 18:52:40',NULL,'recibido','test',1,7),(22,NULL,2,3,'2026-01-01 18:56:45',NULL,'recibido','test1',1,7),(23,NULL,2,3,'2026-01-01 21:16:42',NULL,'recibido','test',1,7),(24,NULL,2,3,'2026-01-02 08:09:12',NULL,'recibido','asdasd',1,7),(25,NULL,2,3,'2026-01-02 13:10:55',NULL,'recibido','test2',1,7),(26,NULL,2,3,'2026-01-02 13:21:27',NULL,'recibido','test',1,7),(27,NULL,2,3,'2026-01-02 19:34:55','2026-01-02 19:39:35','recibido_parcial','test',1,7),(28,27,3,2,'2026-01-02 19:39:35','2026-01-02 19:50:00','recibido',NULL,1,1),(29,NULL,2,3,'2026-01-02 19:52:06','2026-01-02 19:52:33','recibido_parcial','test',1,7),(30,29,3,2,'2026-01-02 19:52:33','2026-01-02 19:53:57','recibido',NULL,1,1),(31,NULL,2,3,'2026-01-02 20:01:12','2026-01-02 20:02:32','recibido_parcial','test',1,7),(32,31,3,2,'2026-01-02 20:02:32','2026-01-02 20:05:13','recibido',NULL,1,1),(33,NULL,2,3,'2026-01-02 20:11:16','2026-01-02 20:16:21','recibido_parcial','test',1,7),(34,33,3,2,'2026-01-02 20:16:21','2026-01-02 20:23:48','recibido',NULL,1,1),(35,NULL,2,3,'2026-01-02 20:24:43','2026-01-02 20:24:54','recibido_parcial','test final',1,7),(36,35,3,2,'2026-01-02 20:24:54','2026-01-02 20:25:37','recibido',NULL,1,1),(37,NULL,2,3,'2026-01-02 20:26:15','2026-01-02 20:26:30','recibido_parcial','trest',1,7),(38,37,3,2,'2026-01-02 20:26:30','2026-01-02 20:26:49','recibido',NULL,1,1),(39,NULL,2,3,'2026-01-02 20:47:45',NULL,'en_transito','test',1,NULL),(41,NULL,2,5,'2026-01-14 15:09:20','2026-01-14 15:40:12','recibido','re abastacimiento de stock',7,10);
/*!40000 ALTER TABLE `transferencia_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencia_stock_detalle`
--

DROP TABLE IF EXISTS `transferencia_stock_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencia_stock_detalle` (
  `idtransferencia` bigint(20) NOT NULL,
  `id_articulo` bigint(20) NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `cantidad_recibida` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`idtransferencia`,`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock_detalle`
--

LOCK TABLES `transferencia_stock_detalle` WRITE;
/*!40000 ALTER TABLE `transferencia_stock_detalle` DISABLE KEYS */;
INSERT INTO `transferencia_stock_detalle` VALUES (10,8,3.00,2.00),(11,8,3.00,3.00),(12,8,1.00,1.00),(12,9,3.00,3.00),(12,10,20.00,20.00),(13,9,10.00,10.00),(13,10,1.00,1.00),(14,9,2.00,2.00),(15,13,1.00,1.00),(16,13,1.00,1.00),(17,13,2.00,2.00),(18,13,1.00,1.00),(19,13,1.00,1.00),(20,13,1.00,1.00),(21,13,1.00,1.00),(22,13,1.00,1.00),(23,13,1.00,1.00),(24,10,1.00,1.00),(25,9,1.00,1.00),(26,9,1.00,1.00),(27,9,3.00,2.00),(28,9,1.00,1.00),(29,9,2.00,1.00),(30,9,1.00,1.00),(31,9,2.00,1.00),(32,9,1.00,1.00),(33,9,2.00,1.00),(34,9,1.00,1.00),(35,9,2.00,1.00),(36,9,1.00,1.00),(37,9,2.00,1.00),(38,9,1.00,1.00),(39,7,2.00,NULL),(39,9,2.00,NULL),(41,15,5.00,5.00);
/*!40000 ALTER TABLE `transferencia_stock_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `idunidad_medida` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `medida` varchar(20) NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idunidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES (1,'Unidad',1),(2,'Litros',1);
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_rol`
--

DROP TABLE IF EXISTS `usuario_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_rol` (
  `id_usuario` int(10) unsigned NOT NULL,
  `id_rol` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_rol`),
  KEY `fk_ur_rol` (`id_rol`),
  CONSTRAINT `fk_ur_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `fk_ur_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_rol`
--

LOCK TABLES `usuario_rol` WRITE;
/*!40000 ALTER TABLE `usuario_rol` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usu_nombre` varchar(50) DEFAULT NULL,
  `usu_clave` varchar(70) DEFAULT NULL,
  `usu_nivel` int(10) unsigned DEFAULT NULL,
  `usu_estado` int(10) unsigned DEFAULT NULL,
  `usu_nick` varchar(20) DEFAULT NULL,
  `usu_apellido` varchar(50) DEFAULT NULL,
  `usu_email` varchar(50) DEFAULT NULL,
  `usu_telefono` varchar(50) DEFAULT NULL,
  `usu_ci` varchar(10) DEFAULT NULL,
  `sucursalid` int(10) unsigned DEFAULT NULL,
  `id_rol` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuarios_sucursales` (`sucursalid`),
  KEY `fk_usuarios_roles` (`id_rol`),
  CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `fk_usuarios_sucursales` FOREIGN KEY (`sucursalid`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,1,'admin','Del Sistema','admins@admin.com.py','0986203431','1234567',2,7),(2,'jfigueredo','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,1,'admins','Del Sistema','admin1@admin.com.py','0981111111','11111111',2,NULL),(4,'Diegoa','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',3,1,'dmendieta','Mendietaa','dmendietaa@admin.com','0985123453','987654321',2,7),(5,'sins','TTd2RFZQUXgxak4rN1RlWHh4bndxUT09',1,1,'noadmins','nivels','noadmins@admin.com','0981222223','123456789',2,NULL),(6,'test','TTd2RFZQUXgxak4rN1RlWHh4bndxUT09',2,1,'test','test','test@gmail.com','','6543214',2,NULL),(7,'Testuser','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',2,1,'ucompra','Comrpa','ucompra@reducto.com.py','09862349732','1234566',2,13),(8,'user','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',2,1,'uservicio','Servicio','uservicio@reducto.com.py','0986234973','1234560',2,10),(9,'Testuser','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',2,1,'uventas','Ventas','uventas@reducto.com.py','09862349732','1234561',2,NULL),(10,'Jorge','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,1,'jdure','Dure','jdure@gmail.com','0985123654','5326548',5,13),(11,'Angel','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',2,1,'adure','Dure','adure@admin.com','0985123654','5456789',NULL,NULL);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_color` int(10) unsigned NOT NULL,
  `id_modeloauto` int(10) unsigned NOT NULL,
  `nro_serie` varchar(50) DEFAULT NULL,
  `placa` varchar(20) DEFAULT NULL,
  `anho` varchar(4) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `id_cliente` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_vehiculo`),
  KEY `vehiculos_FKIndex1` (`id_modeloauto`),
  KEY `vehiculos_FKIndex2` (`id_color`),
  KEY `vehiculos_FKIndex3` (`id_color`),
  KEY `vehiculos_clientes_FK` (`id_cliente`),
  CONSTRAINT `vehiculos_clientes_FK` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_modeloauto`) REFERENCES `modelo_auto` (`id_modeloauto`),
  CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,1,1,'12314324324','BGL513','2001',1,3),(2,2,3,'3243145436df','AAZ780','2010',1,1),(4,20,8,'1236549874','ABd124','2023',1,1);
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'bd_reduc'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-14 22:22:44
