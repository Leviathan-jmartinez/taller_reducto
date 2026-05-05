-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: bd_reduc
-- ------------------------------------------------------
-- Server version	8.0.45

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
  `idajuste_inventario` int unsigned NOT NULL AUTO_INCREMENT,
  `sucursal_id` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `estado` int unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo_inv` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ajuste` date DEFAULT NULL,
  `ajustadoPor` int DEFAULT NULL,
  PRIMARY KEY (`idajuste_inventario`),
  KEY `ajuste_inventario_FKIndex2` (`id_usuario`),
  KEY `ajuste_inventario_FKIndex3` (`sucursal_id`),
  CONSTRAINT `ajuste_inventarioSucu` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `ajuste_inventarioUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
INSERT INTO `ajuste_inventario` VALUES (1,1,1,3,'2026-04-20','Producto','test insumo','2026-04-20',1),(2,1,1,3,'2026-04-23','Producto','test','2026-04-23',1),(3,1,1,3,'2026-04-25','General','teste general','2026-04-25',1),(5,1,1,3,'2026-04-30','General','testgeneral','2026-04-30',1),(6,1,1,1,'2026-05-03','General','',NULL,NULL),(7,1,1,1,'2026-05-03','General','testge',NULL,NULL),(8,2,10,1,'2026-05-03','General','teste jdure',NULL,NULL),(9,1,10,1,'2026-05-03','General','test jdure general',NULL,NULL),(10,1,10,1,'2026-05-03','General','asdasdsa',NULL,NULL),(11,1,10,1,'2026-05-03','Proveedor','tees proveedor',NULL,NULL),(12,1,10,1,'2026-05-03','Proveedor','teste2',NULL,NULL),(13,1,10,0,'2026-05-03','Proveedor','test3','2026-05-03',10),(14,1,10,2,'2026-05-03','General','test',NULL,NULL),(15,1,10,3,'2026-05-03','Producto','test insumo','2026-05-03',10);
/*!40000 ALTER TABLE `ajuste_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ajuste_inventario_detalle`
--

DROP TABLE IF EXISTS `ajuste_inventario_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ajuste_inventario_detalle` (
  `id_articulo` int unsigned NOT NULL,
  `idajuste_inventario` int unsigned NOT NULL,
  `cantidad_teorica` double NOT NULL,
  `cantidad_fisica` double NOT NULL,
  `costo` double NOT NULL,
  `diferencia` double DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`idajuste_inventario`),
  KEY `ajuste_inventario_detalle_FKIndex1` (`id_articulo`),
  KEY `ajuste_inventario_detalle_FKIndex2` (`idajuste_inventario`),
  CONSTRAINT `fk_ajusteAJ` FOREIGN KEY (`idajuste_inventario`) REFERENCES `ajuste_inventario` (`idajuste_inventario`),
  CONSTRAINT `fk_articulosAJ` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario_detalle`
--

LOCK TABLES `ajuste_inventario_detalle` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario_detalle` DISABLE KEYS */;
INSERT INTO `ajuste_inventario_detalle` VALUES (2,2,0,10,30000,10),(2,3,10,5,30000,-5),(2,5,3,3,30000,0),(2,7,13,13,30000,NULL),(2,8,0,0,30000,NULL),(2,9,13,13,30000,NULL),(2,10,13,13,30000,NULL),(2,11,13,13,30000,NULL),(2,13,13,10,30000,-3),(2,14,13,13,30000,0),(3,3,0,10,40000,10),(3,5,10,10,40000,0),(3,7,10,10,40000,NULL),(3,8,0,0,40000,NULL),(3,9,10,10,40000,NULL),(3,10,10,10,40000,NULL),(3,11,10,10,40000,NULL),(3,13,10,10,40000,0),(3,14,10,10,40000,0),(4,3,0,0,80000,0),(4,5,0,10,80000,10),(4,7,10,10,80000,NULL),(4,8,0,0,80000,NULL),(4,9,10,10,80000,NULL),(4,10,10,10,80000,NULL),(4,12,10,10,80000,NULL),(4,14,10,10,80000,0),(5,3,0,0,200000,0),(5,5,0,0,200000,0),(5,7,0,0,200000,NULL),(5,8,0,0,200000,NULL),(5,9,0,0,200000,NULL),(5,10,0,0,200000,NULL),(5,12,0,0,200000,NULL),(5,14,0,0,200000,0),(6,3,0,0,280000,0),(6,5,0,0,280000,0),(6,7,0,0,280000,NULL),(6,8,0,0,280000,NULL),(6,9,0,0,280000,NULL),(6,10,0,0,280000,NULL),(6,14,0,0,280000,0),(7,3,0,0,350000,0),(7,5,0,0,350000,0),(7,7,0,0,350000,NULL),(7,8,0,0,350000,NULL),(7,9,0,0,350000,NULL),(7,10,0,0,350000,NULL),(7,14,0,0,350000,0),(8,3,0,0,15000,0),(8,5,0,0,15000,0),(8,7,0,0,15000,NULL),(8,8,0,0,15000,NULL),(8,9,0,0,15000,NULL),(8,10,0,0,15000,NULL),(8,14,0,0,15000,0),(9,3,0,0,450000,0),(9,5,0,0,450000,0),(9,7,0,0,450000,NULL),(9,8,0,0,450000,NULL),(9,9,0,0,450000,NULL),(9,10,0,0,450000,NULL),(9,14,0,0,450000,0),(10,3,0,0,500000,0),(10,5,0,0,500000,0),(10,7,0,0,500000,NULL),(10,8,0,0,500000,NULL),(10,9,0,0,500000,NULL),(10,10,0,0,500000,NULL),(10,14,0,0,500000,0),(11,3,0,0,320000,0),(11,5,0,0,320000,0),(11,7,0,0,320000,NULL),(11,8,0,0,320000,NULL),(11,9,0,0,320000,NULL),(11,10,0,0,320000,NULL),(11,14,0,0,320000,0),(12,3,0,0,60000,0),(12,5,0,0,60000,0),(12,7,0,0,60000,NULL),(12,8,0,0,60000,NULL),(12,9,0,0,60000,NULL),(12,10,0,0,60000,NULL),(12,14,0,0,60000,0),(18,6,0,0,25000,NULL),(19,6,0,0,30000,NULL),(19,15,0,10,30000,10),(20,6,0,0,40000,NULL),(21,1,0,10,35000,10),(22,6,0,0,20000,NULL),(23,6,0,0,45000,NULL),(24,6,15,15,30000,NULL),(25,6,0,0,50000,NULL),(26,6,0,0,40000,NULL),(27,6,0,0,5000,NULL),(28,14,0,0,10000,0),(29,14,0,0,8000,0),(30,14,0,0,15000,0),(31,13,0,10,5000,10),(31,14,0,0,5000,0),(32,5,0,0,1500,0),(32,7,0,0,1500,NULL),(32,8,0,0,1500,NULL),(32,9,0,0,1500,NULL),(32,10,0,0,1500,NULL),(32,14,0,0,1500,0),(33,7,0,0,0,NULL),(33,8,0,0,0,NULL),(33,9,0,0,0,NULL),(33,10,0,0,0,NULL),(33,14,0,0,0,0);
/*!40000 ALTER TABLE `ajuste_inventario_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apercier_cajas`
--

DROP TABLE IF EXISTS `apercier_cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apercier_cajas` (
  `nroapercier_cajas` int unsigned NOT NULL AUTO_INCREMENT,
  `id_caja` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha_aper` datetime NOT NULL,
  `monto_aper` int unsigned NOT NULL,
  `fecha_cierre` datetime NOT NULL,
  `monto_cierre` int unsigned NOT NULL,
  `monto_efectivo` int unsigned NOT NULL,
  `monto_cheque` int unsigned NOT NULL,
  `monto_tarjeta` int unsigned NOT NULL,
  `nrofac_ini` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nrofac_fin` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned NOT NULL,
  PRIMARY KEY (`nroapercier_cajas`),
  KEY `apercier_cajas_FKIndex1` (`id_usuario`),
  KEY `apercier_cajas_FKIndex2` (`id_caja`),
  CONSTRAINT `apercier_cajasCaja` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id_caja`),
  CONSTRAINT `apercier_cajasUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apercier_cajas`
--

LOCK TABLES `apercier_cajas` WRITE;
/*!40000 ALTER TABLE `apercier_cajas` DISABLE KEYS */;
/*!40000 ALTER TABLE `apercier_cajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arqueos`
--

DROP TABLE IF EXISTS `arqueos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arqueos` (
  `id_arqueo` int unsigned NOT NULL AUTO_INCREMENT,
  `nroapercier_cajas` int unsigned NOT NULL,
  `fecha_creacion` date DEFAULT NULL,
  `monto_efectivo` int unsigned DEFAULT NULL,
  `monto_cheque` int unsigned DEFAULT NULL,
  `monto_tarjeta` int unsigned DEFAULT NULL,
  `total` int unsigned DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_arqueo`),
  KEY `arqueos_FKIndex1` (`nroapercier_cajas`),
  CONSTRAINT `arqueosApertura` FOREIGN KEY (`nroapercier_cajas`) REFERENCES `apercier_cajas` (`nroapercier_cajas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arqueos`
--

LOCK TABLES `arqueos` WRITE;
/*!40000 ALTER TABLE `arqueos` DISABLE KEYS */;
/*!40000 ALTER TABLE `arqueos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articulo_proveedor`
--

DROP TABLE IF EXISTS `articulo_proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulo_proveedor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_articulo` int unsigned NOT NULL,
  `idproveedores` int unsigned NOT NULL,
  `precio_compra` decimal(14,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_art_prov` (`id_articulo`,`idproveedores`),
  KEY `idx_articulo` (`id_articulo`),
  KEY `idx_proveedor` (`idproveedores`),
  CONSTRAINT `fk_ap_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_ap_proveedor` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulo_proveedor`
--

LOCK TABLES `articulo_proveedor` WRITE;
/*!40000 ALTER TABLE `articulo_proveedor` DISABLE KEYS */;
INSERT INTO `articulo_proveedor` VALUES (1,2,8,30000.00,1),(2,3,8,40000.00,1),(3,4,2,80000.00,1),(4,5,2,200000.00,1),(5,6,6,280000.00,1),(6,7,3,350000.00,1),(7,8,3,15000.00,1),(8,9,3,450000.00,1),(9,10,5,500000.00,1),(10,11,6,320000.00,1),(11,12,4,60000.00,1),(12,18,1,25000.00,1),(13,19,1,30000.00,1),(14,20,1,40000.00,1),(15,21,1,35000.00,1),(16,22,1,20000.00,1),(17,23,1,45000.00,1),(18,24,1,30000.00,1),(19,25,1,50000.00,1),(20,26,1,40000.00,1),(21,27,1,5000.00,1),(22,28,1,10000.00,1),(23,29,1,8000.00,1),(24,30,10,15000.00,1),(32,31,8,5000.00,1),(33,32,10,1500.00,1),(34,2,5,100000.00,1),(35,24,5,100000.00,1),(36,30,7,5000.00,1),(37,32,7,1500.00,1);
/*!40000 ALTER TABLE `articulo_proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articulos`
--

DROP TABLE IF EXISTS `articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulos` (
  `id_articulo` int unsigned NOT NULL AUTO_INCREMENT,
  `id_categoria` int unsigned NOT NULL,
  `idunidad_medida` int unsigned NOT NULL,
  `idiva` int unsigned NOT NULL,
  `id_marcas` int unsigned DEFAULT NULL,
  `desc_articulo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_venta` double DEFAULT NULL,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_articulo`),
  KEY `articulos_FKIndex1` (`id_marcas`),
  KEY `articulos_FKIndex2` (`idiva`),
  KEY `articulos_FKIndex3` (`idunidad_medida`),
  KEY `articulos_FKIndex5` (`id_categoria`),
  CONSTRAINT `articulosCat` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `articulosIVA` FOREIGN KEY (`idiva`) REFERENCES `tipo_impuesto` (`idiva`),
  CONSTRAINT `articulosMarca` FOREIGN KEY (`id_marcas`) REFERENCES `marcas` (`id_marcas`),
  CONSTRAINT `articulosUmedida` FOREIGN KEY (`idunidad_medida`) REFERENCES `unidad_medida` (`idunidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (2,2,1,2,2,'Filtro de Aceite Bosch',45000,'FILBOS01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(3,2,1,2,2,'Filtro de Aire Toyota',60000,'FILTOY02',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(4,3,4,2,8,'Pastillas de Freno Delanteras',120000,'FREN001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(5,3,1,2,8,'Disco de Freno Ventilado',250000,'DISC001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(6,4,3,2,7,'Amortiguadores Delanteros (Par)',350000,'AMORT01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(7,5,5,2,3,'Kit de Distribución',450000,'KITDIST01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(8,5,1,2,3,'Bujía NGK',25000,'BUJNGK01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(9,6,1,2,8,'Batería 12V 60Ah',550000,'BAT001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(10,7,5,2,7,'Kit de Embrague',650000,'EMB001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(11,8,1,2,4,'Cubierta 185/65R14 Michelin',400000,'NEU001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(12,9,2,2,10,'Refrigerante 1L',90000,'REF001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(13,10,6,3,NULL,'Cambio de Filtro',80000,'SERV001',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(14,10,6,3,NULL,'Alineación y Balanceo',100000,'SERV002',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(15,10,6,3,NULL,'Diagnóstico Computarizado',70000,'SERV003',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(17,7,1,1,15,'asdas',654,'123456',1,'2026-04-12 16:47:36','2026-04-12 16:47:36','servicio'),(18,1,1,1,NULL,'Desengrasante de motor',0,'INS-001',1,NULL,'2026-04-20 21:43:48','insumo'),(19,1,1,1,NULL,'Limpiador de frenos spray',0,'INS-002',1,NULL,'2026-04-20 21:43:48','insumo'),(20,1,1,1,NULL,'Limpiador de inyectores',0,'INS-003',1,NULL,'2026-04-20 21:43:48','insumo'),(21,1,1,1,NULL,'Lubricante WD-40',0,'INS-004',0,NULL,'2026-04-20 21:43:48','insumo'),(22,1,1,1,NULL,'Grasa multiuso',0,'INS-005',1,NULL,'2026-04-20 21:43:48','insumo'),(23,1,1,1,NULL,'Silicona RTV alta temperatura',0,'INS-006',1,NULL,'2026-04-20 21:43:48','insumo'),(24,1,1,1,NULL,'Sellador de roscas',0,'INS-007',1,NULL,'2026-04-20 21:43:48','insumo'),(25,1,1,1,NULL,'Refrigerante',0,'INS-008',1,NULL,'2026-04-20 21:43:48','insumo'),(26,1,1,1,NULL,'Líquido de frenos',0,'INS-009',1,NULL,'2026-04-20 21:43:48','insumo'),(27,1,1,1,NULL,'Trapo industrial',0,'INS-010',1,NULL,'2026-04-20 21:43:48','insumo'),(28,1,1,1,14,'Guantes descartables',0,'12345678',1,'2026-04-26 10:23:36','2026-04-20 21:43:48','producto'),(29,1,1,1,2,'Cinta aislante',80000,'012',1,'2026-04-28 21:09:10','2026-04-20 21:43:48','producto'),(30,6,1,2,4,'testtest',2000,'98764531',1,'2026-04-28 22:03:39','2026-04-26 11:57:00','producto'),(31,2,1,1,5,'test1',35000,'12345600',1,'2026-04-28 22:00:54','2026-04-28 22:00:54','producto'),(32,5,1,1,2,'test',2000,'12365400',1,'2026-04-28 22:01:43','2026-04-28 22:01:43','producto'),(33,3,1,2,13,'tesa2',35000,'123450015',1,'2026-04-30 12:54:59','2026-04-30 12:54:59','producto');
/*!40000 ALTER TABLE `articulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bancos` (
  `idbancos` int unsigned NOT NULL AUTO_INCREMENT,
  `banc_nomb` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banc_direcc` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banc_tele` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idbancos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `id_caja` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `caj_descri` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned NOT NULL,
  PRIMARY KEY (`id_caja`),
  KEY `cajas_FKIndex1` (`id_sucursal`),
  CONSTRAINT `cajasSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `idcargos` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcargos`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cargos`
--

LOCK TABLES `cargos` WRITE;
/*!40000 ALTER TABLE `cargos` DISABLE KEYS */;
INSERT INTO `cargos` VALUES (1,'Administrador del Sistemas',1),(2,'Propietario',1),(3,'Encargado de Compras',1),(4,'Personal de Compras',1),(5,'Encargado de Servicios',1),(6,'Personal de Recepción',1),(7,'Cajera',1),(8,'Supervisor de Cajas',1),(9,'Mecánico',1),(10,'Auxiliar Mecánicos',1),(11,'test',1),(12,'tests',1),(13,'test3',1);
/*!40000 ALTER TABLE `cargos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_descri` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Aceites',1),(2,'Filtros',1),(3,'Frenos',1),(4,'Suspensión',1),(5,'Motor',1),(6,'Electricidad',1),(7,'Transmisión',1),(8,'Neumáticos',1),(9,'Refrigeración',1),(10,'Servicios',1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cheque_detalle`
--

DROP TABLE IF EXISTS `cheque_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cheque_detalle` (
  `idcobros` int unsigned NOT NULL,
  `idcobro_cheque` int unsigned NOT NULL,
  `monto` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobros`,`idcobro_cheque`),
  KEY `cobros_has_cobro_cheque_FKIndex1` (`idcobros`),
  KEY `cobros_has_cobro_cheque_FKIndex2` (`idcobro_cheque`),
  CONSTRAINT `cheque_detalleCheque` FOREIGN KEY (`idcobro_cheque`) REFERENCES `cobro_cheque` (`idcobro_cheque`),
  CONSTRAINT `cheque_detalleCobro` FOREIGN KEY (`idcobros`) REFERENCES `cobros` (`idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cheque_detalle`
--

LOCK TABLES `cheque_detalle` WRITE;
/*!40000 ALTER TABLE `cheque_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `cheque_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ciudades`
--

DROP TABLE IF EXISTS `ciudades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ciudades` (
  `id_ciudad` int unsigned NOT NULL AUTO_INCREMENT,
  `id_departamento` int NOT NULL,
  `ciu_descri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_ciudad`),
  KEY `fk_ciudades_departamentos1_idx` (`id_departamento`),
  CONSTRAINT `fk_ciudades_departamentos1` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ciudades`
--

LOCK TABLES `ciudades` WRITE;
/*!40000 ALTER TABLE `ciudades` DISABLE KEYS */;
INSERT INTO `ciudades` VALUES (1,1,'Concepción',1),(2,1,'Belén',1),(3,1,'Horqueta',1),(4,1,'Loreto',1),(5,1,'San Carlos del Apa',1),(6,1,'San Lázaro',1),(7,1,'Yby Yaú',1),(8,1,'Azotey',1),(9,2,'San Pedro',1),(10,2,'Antequera',1),(11,2,'Choré',1),(12,2,'General Aquino',1),(13,2,'Guayaibí',1),(14,2,'Itacurubí del Rosario',1),(15,2,'Lima',1),(16,2,'Nueva Germania',1),(17,2,'San Estanislao',1),(18,2,'Tacuatí',1),(19,2,'Unión',1),(20,2,'Villa del Rosario',1),(21,2,'Yataity del Norte',1),(22,2,'25 de Diciembre',1),(23,3,'Caacupé',1),(24,3,'Altos',1),(25,3,'Arroyos y Esteros',1),(26,3,'Atyrá',1),(27,3,'Caraguatay',1),(28,3,'Emboscada',1),(29,3,'Eusebio Ayala',1),(30,3,'Isla Pucú',1),(31,3,'Itacurubí de la Cordillera',1),(32,3,'Juan de Mena',1),(33,3,'Loma Grande',1),(34,3,'Mbocayaty del Yhaguy',1),(35,3,'Nueva Colombia',1),(36,3,'Piribebuy',1),(37,3,'Primero de Marzo',1),(38,3,'San Bernardino',1),(39,3,'Santa Elena',1),(40,3,'Tobatí',1),(41,3,'Valenzuela',1),(42,4,'Villarrica',1),(43,4,'Borja',1),(44,4,'Capitán Mauricio José Troche',1),(45,4,'Coronel Martínez',1),(46,4,'Félix Pérez Cardozo',1),(47,4,'General Eugenio A. Garay',1),(48,4,'Independencia',1),(49,4,'Itapé',1),(50,4,'Iturbe',1),(51,4,'José Fassardi',1),(52,4,'Mbocayaty',1),(53,4,'Natalicio Talavera',1),(54,4,'Ñumí',1),(55,4,'San Salvador',1),(56,4,'Yataity',1),(57,4,'Dr. Bottrell',1),(58,5,'Coronel Oviedo',1),(59,5,'Caaguazú',1),(60,5,'Carayaó',1),(61,5,'Dr. Cecilio Báez',1),(62,5,'Dr. Juan Manuel Frutos',1),(63,5,'Dr. J. Eulogio Estigarribia',1),(64,5,'José Domingo Ocampos',1),(65,5,'La Pastora',1),(66,5,'Mariscal López',1),(67,5,'Nueva Londres',1),(68,5,'Raúl Arsenio Oviedo',1),(69,5,'Repatriación',1),(70,5,'R.I. 3 Corrales',1),(71,5,'San Joaquín',1),(72,5,'San José de los Arroyos',1),(73,5,'Santa Rosa del Mbutuy',1),(74,5,'Simón Bolívar',1),(75,5,'Tembiaporá',1),(76,5,'Vaquería',1),(77,5,'Yhú',1),(78,6,'Caazapá',1),(79,6,'Abaí',1),(80,6,'Buena Vista',1),(81,6,'Dr. Moisés Bertoni',1),(82,6,'General Higinio Morínigo',1),(83,6,'Maciel',1),(84,6,'San Juan Nepomuceno',1),(85,6,'Tavaí',1),(86,6,'Yegros',1),(87,6,'Yuty',1),(88,7,'Encarnación',1),(89,7,'Bella Vista',1),(90,7,'Cambyretá',1),(91,7,'Capitán Meza',1),(92,7,'Capitán Miranda',1),(93,7,'Carlos Antonio López',1),(94,7,'Carmen del Paraná',1),(95,7,'Coronel Bogado',1),(96,7,'Edelira',1),(97,7,'Fram',1),(98,7,'General Artigas',1),(99,7,'General Delgado',1),(100,7,'Hohenau',1),(101,7,'Itapúa Poty',1),(102,7,'Jesús',1),(103,7,'La Paz',1),(104,7,'Leandro Oviedo',1),(105,7,'Mayor Otaño',1),(106,7,'Natalio',1),(107,7,'Nueva Alborada',1),(108,7,'Obligado',1),(109,7,'Pirapó',1),(110,7,'San Cosme y Damián',1),(111,7,'San Juan del Paraná',1),(112,7,'San Pedro del Paraná',1),(113,7,'San Rafael del Paraná',1),(114,7,'Tomás Romero Pereira',1),(115,7,'Trinidad',1),(116,8,'San Juan Bautista',1),(117,8,'Ayolas',1),(118,8,'San Ignacio',1),(119,8,'San Miguel',1),(120,8,'San Patricio',1),(121,8,'Santa María',1),(122,8,'Santa Rosa',1),(123,8,'Santiago',1),(124,8,'Villa Florida',1),(125,8,'Yabebyry',1),(126,9,'Paraguarí',1),(127,9,'Acahay',1),(128,9,'Caapucú',1),(129,9,'Carapeguá',1),(130,9,'Escobar',1),(131,9,'General Bernardino Caballero',1),(132,9,'La Colmena',1),(133,9,'Mbuyapey',1),(134,9,'Pirayú',1),(135,9,'Quiindy',1),(136,9,'Quyquyhó',1),(137,9,'San Roque González',1),(138,9,'Sapucai',1),(139,9,'Tebicuarymí',1),(140,9,'Yaguarón',1),(141,9,'Ybycuí',1),(142,10,'Ciudad del Este',1),(143,10,'Domingo Martínez de Irala',1),(144,10,'Dr. Juan León Mallorquín',1),(145,10,'Hernandarias',1),(146,10,'Itakyry',1),(147,10,'Juan Emilio O Leary',1),(148,10,'Los Cedrales',1),(149,10,'Mbaracayú',1),(150,10,'Minga Guazú',1),(151,10,'Minga Porá',1),(152,10,'Naranjal',1),(153,10,'Ñacunday',1),(154,10,'Presidente Franco',1),(155,10,'San Alberto',1),(156,10,'Santa Fe del Paraná',1),(157,10,'Santa Rita',1),(158,10,'Santa Rosa del Monday',1),(159,10,'Tavapy',1),(160,10,'Yguazú',1),(161,11,'Areguá',1),(162,11,'Capiatá',1),(163,11,'Fernando de la Mora',1),(164,11,'Guarambaré',1),(165,11,'Itá',1),(166,11,'Itauguá',1),(167,11,'J. Augusto Saldívar',1),(168,11,'Lambaré',1),(169,11,'Limpio',1),(170,11,'Luque',1),(171,11,'Mariano Roque Alonso',1),(172,11,'Nueva Italia',1),(173,11,'Ñemby',1),(174,11,'San Antonio',1),(175,11,'San Lorenzo',1),(176,11,'Villa Elisa',1),(177,11,'Villeta',1),(178,11,'Ypacaraí',1),(179,11,'Ypané',1),(180,12,'Pilar',1),(181,12,'Alberdi',1),(182,12,'Cerrito',1),(183,12,'Desmochados',1),(184,12,'General Díaz',1),(185,12,'Guazú Cuá',1),(186,12,'Humaitá',1),(187,12,'Isla Umbú',1),(188,12,'Laureles',1),(189,12,'Mayor José D. Martínez',1),(190,12,'Paso de Patria',1),(191,12,'San Juan Bautista de Ñeembucú',1),(192,12,'Tacuaras',1),(193,12,'Villa Franca',1),(194,12,'Villa Oliva',1),(195,12,'Villalbín',1),(196,13,'Pedro Juan Caballero',1),(197,13,'Bella Vista Norte',1),(198,13,'Capitán Bado',1),(199,13,'Karapaí',1),(200,13,'Zanja Pytã',1),(201,14,'Salto del Guairá',1),(202,14,'Corpus Christi',1),(203,14,'Curuguaty',1),(204,14,'General Francisco Caballero Álvarez',1),(205,14,'Itanará',1),(206,14,'Katueté',1),(207,14,'La Paloma',1),(208,14,'Maracaná',1),(209,14,'Nueva Esperanza',1),(210,14,'Villa Ygatimí',1),(211,14,'Yasy Cañy',1),(212,14,'Ybyrarobaná',1),(213,14,'Ypejhú',1),(214,15,'Villa Hayes',1),(215,15,'Benjamín Aceval',1),(216,15,'Campo Aceval',1),(217,15,'José Falcón',1),(218,15,'Nanawa',1),(219,15,'Nueva Asunción',1),(220,15,'Pozo Colorado',1),(221,15,'Puerto Pinasco',1),(222,15,'Teniente Irala Fernández',1),(223,16,'Fuerte Olimpo',1),(224,16,'Bahía Negra',1),(225,16,'Puerto Casado',1),(226,16,'Puerto Guaraní',1),(227,17,'Filadelfia',1),(228,17,'Loma Plata',1),(229,17,'Mariscal Estigarribia',1),(230,17,'Neuland',1);
/*!40000 ALTER TABLE `ciudades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int unsigned NOT NULL AUTO_INCREMENT,
  `id_ciudad` int unsigned NOT NULL,
  `doc_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_cliente` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido_cliente` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_cliente` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular_cliente` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `digito_v` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_cliente` tinyint unsigned DEFAULT NULL,
  `doc_type` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_cliente` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `clientes_FKIndex1` (`id_ciudad`),
  CONSTRAINT `fk_ciudadesCli` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,1,'1234567','Juan','González','Barrio San Vicente','0981123456','3','Soltero',1,'CI','juan.gonzalez@gmail.com'),(2,1,'2345678','María','López','Av. Eusebio Ayala','0982234567','1','Soltero',1,'CI','maria.lopez@gmail.com'),(3,182,'3216478','Carloss','Ramírezv','Barrio Obrero','0983345677','5','Soltero',1,'CI','carlos.ramirez@gmail.com'),(4,3,'4567890','Hanaa','Martínez','Avda. Mariscal López','0984456788','3','Soltero',1,'CI','ana.martinez@gmail.com'),(5,1,'5678901','Luis','Fernández','Barrio Trinidad','0985567890','7','Soltero',1,'CI','luis.fernandez@gmail.com'),(6,1,'6789012','Sofía','Benítez','Av. Artigas','0986678901','4','Soltero',1,'CI','sofia.benitez@gmail.com'),(7,1,'7890123','Miguel','Duarte','Barrio Sajonia','0987789012','9','Soltero',1,'CI','miguel.duarte@gmail.com'),(8,1,'8901234','Laura','Giménez','Av. Boggiani','0988890123','6','Soltero',1,'CI','laura.gimenez@gmail.com'),(9,1,'9012345','Pedro','Vera','Barrio Lambaré','0989901234','8','Soltero',0,'CI','pedro.vera@gmail.com'),(10,1,'1122334','Carolina','Rojas','Av. Fernando de la Mora','0981012345','0','Soltero',1,'PASAPORTE','carolina.rojas@gmail.com'),(11,2,'4964127','Juan Angel','Figueredo Martinez','Avda Cerro Patiño','0986203431','1','Soltero',1,'CI','juanmartinez076@gmail.com'),(12,166,'1299450','Gricelda','Martinez','Itaugua km 31 - Avda Cerro patiño','0985518660','','Soltero',0,'CI','Griceldamar@gmail.com'),(13,79,'1236487','asdas','asdas','asdasd','64789','','Viudo',1,'CI','asdas@admin.com'),(14,10,'1236452','tses','adsd','dsfsdfsd','03215987','','Soltero',1,'CI','testasd@admin.com'),(15,24,'80016096','ertca','asdsdasd','asdasd','asdsad','7','Casado',1,'RUC','asdasd@admin.com'),(16,10,'9745612','testse','asdasdas','sdasd','4568971','5','Divorciado',1,'CI','asdasd@admin.com');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobro_cheque`
--

DROP TABLE IF EXISTS `cobro_cheque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobro_cheque` (
  `idcobro_cheque` int unsigned NOT NULL AUTO_INCREMENT,
  `idbancos` int unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `titular` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobro_cheque`),
  KEY `cobro_cheque_FKIndex1` (`idbancos`),
  CONSTRAINT `fk_bancos` FOREIGN KEY (`idbancos`) REFERENCES `bancos` (`idbancos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cobro_cheque`
--

LOCK TABLES `cobro_cheque` WRITE;
/*!40000 ALTER TABLE `cobro_cheque` DISABLE KEYS */;
/*!40000 ALTER TABLE `cobro_cheque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cobros`
--

DROP TABLE IF EXISTS `cobros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cobros` (
  `idcobros` int unsigned NOT NULL AUTO_INCREMENT,
  `nroapercier_cajas` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `id_mp` int unsigned NOT NULL,
  `cobro_fecha` date NOT NULL,
  `cobro_estado` int unsigned NOT NULL,
  `cobro_monto` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobros`),
  KEY `cobros_FKIndex1` (`id_usuario`),
  KEY `cobros_FKIndex2` (`nroapercier_cajas`),
  KEY `fk_cobros_medios_pago1_idx` (`id_mp`),
  CONSTRAINT `cobrosApertura` FOREIGN KEY (`nroapercier_cajas`) REFERENCES `apercier_cajas` (`nroapercier_cajas`),
  CONSTRAINT `cobrosUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_cobros_medios_pago1` FOREIGN KEY (`id_mp`) REFERENCES `medios_pago` (`id_mp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `id_color` int unsigned NOT NULL AUTO_INCREMENT,
  `col_descripcion` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_color`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colores`
--

LOCK TABLES `colores` WRITE;
/*!40000 ALTER TABLE `colores` DISABLE KEYS */;
INSERT INTO `colores` VALUES (1,'Blanco',1),(2,'Negro',1),(3,'Gris',1),(4,'Plata',1),(5,'Rojo',1),(6,'Azul',1),(7,'Verde',1),(8,'Amarillo',1),(9,'Beige',1),(10,'Marrón',1),(11,'Naranja',1),(12,'Vino',1),(13,'Celeste',1),(14,'Dorado',1),(15,'Champagne',1);
/*!40000 ALTER TABLE `colores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra_cabecera`
--

DROP TABLE IF EXISTS `compra_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra_cabecera` (
  `idcompra_cabecera` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `idproveedores` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `nro_factura` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `nro_timbrado` int unsigned DEFAULT NULL,
  `vencimiento_timbrado` date DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `total_compra` int unsigned DEFAULT NULL,
  `condicion` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `compra_intervalo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idOcompra` int unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcompra_cabecera`),
  KEY `compra_cabecera_FKIndex1` (`id_usuario`),
  KEY `compra_cabecera_FKIndex2` (`idproveedores`),
  KEY `compra_cabecera_FKIndex3` (`id_sucursal`),
  CONSTRAINT `compra_cabeceraPro` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `compra_cabeceraSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `compra_cabeceraUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_cabecera`
--

LOCK TABLES `compra_cabecera` WRITE;
/*!40000 ALTER TABLE `compra_cabecera` DISABLE KEYS */;
INSERT INTO `compra_cabecera` VALUES (1,1,5,1,'2026-04-30 12:46:46','001-001-0000001','2026-04-30',12345678,'2026-12-30',1,2500000,'credito','15',6,NULL,NULL),(2,1,7,1,'2026-05-02 17:19:12','001-002-0000003','2026-05-02',12345678,'2026-05-31',0,1500,'contado','0',9,'2026-05-02 18:19:18',1);
/*!40000 ALTER TABLE `compra_cabecera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra_detalle`
--

DROP TABLE IF EXISTS `compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra_detalle` (
  `idcompra_cabecera` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `cantidad_recibida` double NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tipo_iva` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ivaPro` decimal(12,2) NOT NULL,
  PRIMARY KEY (`idcompra_cabecera`,`id_articulo`),
  KEY `compra_cabecera_has_orden_compra_detalle_FKIndex1` (`idcompra_cabecera`),
  KEY `compra_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `compra_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `compra_detalleCab` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_detalle`
--

LOCK TABLES `compra_detalle` WRITE;
/*!40000 ALTER TABLE `compra_detalle` DISABLE KEYS */;
INSERT INTO `compra_detalle` VALUES (1,2,100000.00,10,1000000.00,'2',90909.09),(1,24,100000.00,15,1500000.00,'1',71428.57),(2,32,1500.00,1,1500.00,'1',71.43);
/*!40000 ALTER TABLE `compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_a_pagar`
--

DROP TABLE IF EXISTS `cuentas_a_pagar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_a_pagar` (
  `idcuentas_a_pagar` int unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `tipo_movimiento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_tipo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_id` int unsigned DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT NULL,
  `saldo` decimal(12,2) DEFAULT NULL,
  `nro_cuotas` int unsigned DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  `observacion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_a_pagar`,`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex1` (`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex2` (`id_sucursal`),
  CONSTRAINT `cuentas_a_pagarCompra` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `cuentas_a_pagarSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_a_pagar`
--

LOCK TABLES `cuentas_a_pagar` WRITE;
/*!40000 ALTER TABLE `cuentas_a_pagar` DISABLE KEYS */;
INSERT INTO `cuentas_a_pagar` VALUES (1,1,1,'COMPRA','INGRESO_COMPRA',NULL,1250000.00,1250000.00,1,'2026-05-15','2026-04-30 12:46:46','Factura 001-001-0000001',1),(2,1,1,'COMPRA','INGRESO_COMPRA',NULL,1250000.00,1250000.00,2,'2026-05-30','2026-04-30 12:46:46','Factura 001-001-0000001',1),(3,1,1,'credito','nota_compra',1,-200000.00,-200000.00,NULL,NULL,'2026-04-30 13:00:12','Nota credito 001-001-0000001',1),(4,1,1,'credito','nota_compra',2,-2175000.00,-2175000.00,NULL,NULL,'2026-04-30 13:03:49','Nota credito 001-001-0000002',1),(5,1,1,'anulacion','nota_compra',1,200000.00,200000.00,NULL,NULL,'2026-05-03 10:19:16','Anulación Nota credito',1);
/*!40000 ALTER TABLE `cuentas_a_pagar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_cobrar`
--

DROP TABLE IF EXISTS `cuentas_cobrar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_cobrar` (
  `idcuentas_cobrar` int unsigned NOT NULL AUTO_INCREMENT,
  `idcobros` int unsigned NOT NULL,
  `fecha_reg` date DEFAULT NULL,
  `fecha_venc` int unsigned DEFAULT NULL,
  `cuotas_acobrar` int unsigned DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_cobrar`),
  KEY `cuentas_cobrar_FKIndex1` (`idcobros`),
  CONSTRAINT `cuentas_cobrarCobro` FOREIGN KEY (`idcobros`) REFERENCES `cobros` (`idcobros`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_cobrar`
--

LOCK TABLES `cuentas_cobrar` WRITE;
/*!40000 ALTER TABLE `cuentas_cobrar` DISABLE KEYS */;
/*!40000 ALTER TABLE `cuentas_cobrar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `dto_descripcion` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dto_state` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

LOCK TABLES `departamentos` WRITE;
/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` VALUES (1,'Concepción',1),(2,'San Pedro',1),(3,'Cordillera',1),(4,'Guairá',1),(5,'Caaguazú',1),(6,'Caazapá',1),(7,'Itapúa',1),(8,'Misiones',1),(9,'Paraguarí',1),(10,'Alto Paraná',1),(11,'Central',1),(12,'Ñeembucú',1),(13,'Amambay',1),(14,'Canindeyú',1),(15,'Presidente Hayes',1),(16,'Alto Paraguay',1),(17,'Boquerón',1);
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuento_cliente`
--

DROP TABLE IF EXISTS `descuento_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descuento_cliente` (
  `id_cliente` int unsigned NOT NULL,
  `id_descuento` int unsigned NOT NULL,
  PRIMARY KEY (`id_cliente`,`id_descuento`),
  KEY `clientes_has_descuentos_FKIndex1` (`id_cliente`),
  KEY `clientes_has_descuentos_FKIndex2` (`id_descuento`),
  CONSTRAINT `descuento_clienteCli` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `descuento_clienteDes` FOREIGN KEY (`id_descuento`) REFERENCES `descuentos` (`id_descuento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuento_cliente`
--

LOCK TABLES `descuento_cliente` WRITE;
/*!40000 ALTER TABLE `descuento_cliente` DISABLE KEYS */;
/*!40000 ALTER TABLE `descuento_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descuentos`
--

DROP TABLE IF EXISTS `descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descuentos` (
  `id_descuento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario_modifica` int unsigned NOT NULL,
  `id_usuario_crea` int unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `es_reutilizable` tinyint unsigned NOT NULL,
  `estado` tinyint unsigned NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_descuento`),
  KEY `descuentos_FKIndex1` (`id_usuario_crea`),
  KEY `descuentos_FKIndex2` (`id_usuario_modifica`),
  CONSTRAINT `descuentosUsucrea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `descuentosUsumodi` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos`
--

LOCK TABLES `descuentos` WRITE;
/*!40000 ALTER TABLE `descuentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `descuentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_fac`
--

DROP TABLE IF EXISTS `detalle_fac`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_fac` (
  `idfactura` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `det_cant` int unsigned NOT NULL,
  `det_prec` int unsigned NOT NULL,
  PRIMARY KEY (`idfactura`,`id_articulo`),
  KEY `detalle_fac_FKIndex2` (`idfactura`),
  KEY `detalle_fac_FKIndex3` (`id_articulo`),
  CONSTRAINT `detalle_facArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `detalle_facCab` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_fac`
--

LOCK TABLES `detalle_fac` WRITE;
/*!40000 ALTER TABLE `detalle_fac` DISABLE KEYS */;
/*!40000 ALTER TABLE `detalle_fac` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostico_detalle`
--

DROP TABLE IF EXISTS `diagnostico_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostico_detalle` (
  `id_diagnostico_detalle` int NOT NULL AUTO_INCREMENT,
  `id_diagnostico` int NOT NULL,
  `sistema` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `problema` text COLLATE utf8mb4_unicode_ci,
  `gravedad` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solucion_propuesta` text COLLATE utf8mb4_unicode_ci,
  `requiere_repuesto` tinyint(1) DEFAULT '0',
  `requiere_mano_obra` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_diagnostico_detalle`),
  KEY `fk_diagnostico_detalle_diagnostico_servicio1` (`id_diagnostico`),
  CONSTRAINT `fk_diagnostico_detalle_diagnostico_servicio1` FOREIGN KEY (`id_diagnostico`) REFERENCES `diagnostico_servicio` (`id_diagnostico`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_detalle`
--

LOCK TABLES `diagnostico_detalle` WRITE;
/*!40000 ALTER TABLE `diagnostico_detalle` DISABLE KEYS */;
INSERT INTO `diagnostico_detalle` VALUES (1,1,'direccion dura al girar','falta de fluido','media','recarga de fluido',1,0),(2,2,'motor','inconveniente con filtro desgastado','leve','cambio de filtro',1,1),(3,3,'tes','tes','leve','cambio de auto',1,1),(4,4,'transmision','repuesto defectuoso','leve','cambio de filtro',1,1),(5,5,'transmision','dureza en el volante','media','falta de fluido de direccion',1,1),(6,6,'motor','cambio de aceite','leve','mantenimiento de caja',1,1);
/*!40000 ALTER TABLE `diagnostico_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostico_servicio`
--

DROP TABLE IF EXISTS `diagnostico_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostico_servicio` (
  `id_diagnostico` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `idrecepcion` int unsigned NOT NULL,
  `id_equipo` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `fecha_diagnostico` datetime NOT NULL,
  `descripcion_cliente` text COLLATE utf8mb4_unicode_ci,
  `diagnostico_general` text COLLATE utf8mb4_unicode_ci,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `es_garantia` tinyint(1) DEFAULT '0',
  `es_reclamo_valido` tinyint(1) DEFAULT '1',
  `requiere_cobro` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_diagnostico`),
  KEY `fk_diagnostico_servicio_usuarios1_idx` (`id_usuario`),
  KEY `fk_diagnostico_servicio_recepcion_servicio1_idx` (`idrecepcion`),
  KEY `fk_diagnostico_servicio_equipo_trabajo1_idx` (`id_equipo`),
  CONSTRAINT `fk_diagnostico_servicio_equipo_trabajo1` FOREIGN KEY (`id_equipo`) REFERENCES `equipo_trabajo` (`id_equipo`),
  CONSTRAINT `fk_diagnostico_servicio_recepcion_servicio1` FOREIGN KEY (`idrecepcion`) REFERENCES `recepcion_servicio` (`idrecepcion`),
  CONSTRAINT `fk_diagnostico_servicio_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_servicio`
--

LOCK TABLES `diagnostico_servicio` WRITE;
/*!40000 ALTER TABLE `diagnostico_servicio` DISABLE KEYS */;
INSERT INTO `diagnostico_servicio` VALUES (1,1,1,1,0,'2026-04-17 22:35:00',NULL,NULL,2,'test','2026-04-18 01:36:17','2026-04-19 19:55:45',0,1,0),(2,1,2,2,0,'2026-04-18 14:32:00',NULL,NULL,2,'cambio de filtro','2026-04-18 17:32:50','2026-04-19 20:41:22',0,1,0),(3,1,1,1,1,'2026-04-19 09:15:00',NULL,NULL,0,'test','2026-04-19 12:15:52','2026-04-19 13:52:10',0,1,0),(4,1,3,2,1,'2026-04-22 20:54:00',NULL,NULL,1,'','2026-04-22 23:57:04','2026-04-23 00:34:52',1,1,0),(5,1,4,2,1,'2026-04-22 21:25:00',NULL,NULL,1,'','2026-04-23 00:26:32','2026-04-23 00:34:52',1,1,0),(6,1,5,1,1,'2026-04-23 23:02:00',NULL,NULL,2,'test','2026-04-24 02:03:13','2026-04-24 02:03:57',1,1,0);
/*!40000 ALTER TABLE `diagnostico_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `idempleados` int unsigned NOT NULL AUTO_INCREMENT,
  `idcargos` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `nombre` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_cedula` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_civil` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idempleados`),
  UNIQUE KEY `nro_cedula` (`nro_cedula`),
  KEY `personas_FKIndex2` (`id_sucursal`),
  KEY `personas_FKIndex3` (`idcargos`),
  CONSTRAINT `fk_cargosEm` FOREIGN KEY (`idcargos`) REFERENCES `cargos` (`idcargos`),
  CONSTRAINT `fk_sucursalesEm` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,1,1,'Carlos','Gómez','Asunción Centro','0981123456','1234567','Casado',1),(2,2,1,'Ricardo','Martínez','Luque','0982234567','2345678','Casado',1),(3,3,2,'Pedro','Benítez','San Lorenzo','0983345678','3456785','Soltero/a',1),(4,4,1,'Luis','Fernández','Capiatá','0984456789','4567890','Soltero',1),(5,6,1,'Ana','Rojas','Fernando de la Mora','0985567890','5678901','Soltero',1),(6,5,1,'Jorge','Duarte','Ñemby','0986678901','6789012','Casado',1),(7,9,1,'Miguel','Vera','Villa Elisa','0987789012','7890123','Casado',1),(8,9,1,'Diego','López','Asunción','0988890123','8901234','Soltero',1),(9,10,1,'Andrés','Giménez','Lambaré','0989901234','9012345','Soltero',1),(10,7,1,'Sofía','Morales','San Lorenzo','0981012345','1122334','Soltero',1),(11,8,1,'María','González','Luque','0982123456','2233445','Casado',1),(12,10,1,'javier','martinez','asdasd','098465412','4964654','Casado/a',0);
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id_empresa` int unsigned NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `email_empresa` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_empresa` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (2,'Lubri Reducto S.A.','Avda. de la Victoria esq./18 de octubre','80016096-7',NULL,'lubrireducto@gmail.com','(021)586 636');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entidad_emisora`
--

DROP TABLE IF EXISTS `entidad_emisora`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entidad_emisora` (
  `identidad_emisora` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estadi` int unsigned DEFAULT NULL,
  PRIMARY KEY (`identidad_emisora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `idempleados` int unsigned NOT NULL,
  `id_equipo` int unsigned NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idempleados`,`id_equipo`),
  KEY `empleados_has_equipo_trabajo_FKIndex1` (`idempleados`),
  KEY `equipo_empleado_FKIndex2` (`id_equipo`),
  CONSTRAINT `fk_empleadosEqu` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_equipoEqu` FOREIGN KEY (`id_equipo`) REFERENCES `equipo_trabajo` (`id_equipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_empleado`
--

LOCK TABLES `equipo_empleado` WRITE;
/*!40000 ALTER TABLE `equipo_empleado` DISABLE KEYS */;
INSERT INTO `equipo_empleado` VALUES (1,2,'Miembro',1),(2,1,'Miembro',1),(4,2,'Miembro',1),(6,1,'Miembro',1),(6,2,'Miembro',1),(7,1,'Miembro',1),(8,1,'Miembro',0),(9,2,'Miembro',1);
/*!40000 ALTER TABLE `equipo_empleado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipo_trabajo`
--

DROP TABLE IF EXISTS `equipo_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_trabajo` (
  `id_equipo` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned DEFAULT NULL,
  `descripcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `equipo_trabajo_FKIndex1` (`id_sucursal`),
  CONSTRAINT `equipo_trabajoSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_trabajo`
--

LOCK TABLES `equipo_trabajo` WRITE;
/*!40000 ALTER TABLE `equipo_trabajo` DISABLE KEYS */;
INSERT INTO `equipo_trabajo` VALUES (1,1,'Equipo A',1,'Mecánica general'),(2,1,'Equipo B',1,'Electricidad automotriz'),(3,1,'Equipo C',1,'Electricidad');
/*!40000 ALTER TABLE `equipo_trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura`
--

DROP TABLE IF EXISTS `factura`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura` (
  `idfactura` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `id_cliente` int unsigned DEFAULT NULL,
  `fac_fecha` date NOT NULL,
  `fac_hora` time NOT NULL,
  `fac_tipo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fac_total` int unsigned DEFAULT NULL,
  `fac_estado` int unsigned NOT NULL,
  `fac_interv` int unsigned NOT NULL,
  `fac_numero` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idfactura`),
  KEY `factura_FKIndex2` (`id_cliente`),
  KEY `factura_FKIndex3` (`id_usuario`),
  CONSTRAINT `fk_clienteFac` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_usuarioFac` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura`
--

LOCK TABLES `factura` WRITE;
/*!40000 ALTER TABLE `factura` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libro_compra`
--

DROP TABLE IF EXISTS `libro_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libro_compra` (
  `idlibro_compra` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `idcompra_cabecera` int unsigned NOT NULL,
  `fecha` date NOT NULL,
  `tipo_comprobante` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_comprobante` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idproveedores` int unsigned NOT NULL,
  `proveedor_nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor_ruc` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exenta` decimal(14,2) DEFAULT NULL,
  `gravada_5` decimal(14,2) DEFAULT NULL,
  `iva_5` decimal(14,2) DEFAULT NULL,
  `gravada_10` decimal(14,2) DEFAULT NULL,
  `iva_10` decimal(14,2) DEFAULT NULL,
  `total` decimal(14,2) NOT NULL,
  `estado` tinyint unsigned NOT NULL,
  `fecha_registro` datetime NOT NULL,
  PRIMARY KEY (`idlibro_compra`),
  KEY `Libro_compra_FKIndex1` (`idcompra_cabecera`),
  KEY `Libro_compra_FKIndex2` (`id_sucursal`),
  CONSTRAINT `libro_compraCab` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `libro_compraSuc` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_compra`
--

LOCK TABLES `libro_compra` WRITE;
/*!40000 ALTER TABLE `libro_compra` DISABLE KEYS */;
INSERT INTO `libro_compra` VALUES (1,1,1,'2026-04-30','factura','001-001','001-001-0000001',5,'Distribuidora del Automotor','80056789-0',0.00,1428571.43,71428.57,909090.91,90909.09,2500000.00,1,'2026-04-30 12:46:46'),(2,1,1,'2026-04-30','NC','001-001','001-001-0000001',5,'Distribuidora del Automotor','80056789-0',0.00,-95238.10,-4761.90,-90909.09,-9090.91,-200000.00,0,'2026-04-30 13:00:12'),(3,1,1,'2026-04-30','NC','001-001','001-001-0000002',5,'Distribuidora del Automotor','80056789-0',0.00,-1214285.71,-60714.29,-818181.82,-81818.18,-2175000.00,1,'2026-04-30 13:03:49'),(4,1,2,'2026-05-02','factura','001-002','001-002-0000003',7,'Repuestos Japón Import','80078901-2',0.00,1428.57,71.43,0.00,0.00,1500.00,0,'2026-05-02 17:19:12');
/*!40000 ALTER TABLE `libro_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `libro_venta`
--

DROP TABLE IF EXISTS `libro_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `libro_venta` (
  `idlibro_venta` int unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo` int unsigned NOT NULL,
  `idfactura` int unsigned NOT NULL,
  `iva5venta` int unsigned NOT NULL,
  `iva10venta` int unsigned NOT NULL,
  `exentaventa` int unsigned NOT NULL,
  `montoventa` int unsigned NOT NULL,
  PRIMARY KEY (`idlibro_venta`),
  KEY `libro_venta_FKIndex1` (`idfactura`),
  KEY `libro_ventaArt_idx` (`id_articulo`),
  CONSTRAINT `libro_ventaArt` FOREIGN KEY (`id_articulo`) REFERENCES `detalle_fac` (`id_articulo`),
  CONSTRAINT `libro_ventaCab` FOREIGN KEY (`idfactura`) REFERENCES `detalle_fac` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `id_marcas` int unsigned NOT NULL AUTO_INCREMENT,
  `mar_descri` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_marcas`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Toyota',1),(2,'Bosch',1),(3,'NGK',1),(4,'Michelin',1),(5,'Castrol',1),(6,'Mobil',1),(7,'SKF',1),(8,'Valeo',1),(9,'Pirelli',1),(10,'Shell',1),(11,'Nissan',1),(12,'Chevrolet',1),(13,'Kia',1),(14,'Hyundai',1),(15,'Volkswagen',1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medios_pago`
--

DROP TABLE IF EXISTS `medios_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medios_pago` (
  `id_mp` int unsigned NOT NULL AUTO_INCREMENT,
  `mp_descri` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mp_escombinable` tinyint unsigned DEFAULT NULL,
  `mp_aceptavuelto` tinyint DEFAULT NULL,
  `mp_estado` tinyint DEFAULT NULL,
  PRIMARY KEY (`id_mp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medios_pago`
--

LOCK TABLES `medios_pago` WRITE;
/*!40000 ALTER TABLE `medios_pago` DISABLE KEYS */;
/*!40000 ALTER TABLE `medios_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modelo_auto`
--

DROP TABLE IF EXISTS `modelo_auto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modelo_auto` (
  `id_modeloauto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_marcas` int unsigned NOT NULL,
  `mod_descri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_modeloauto`),
  KEY `modelo_auto_FKIndex1` (`id_marcas`),
  CONSTRAINT `fk_marcas` FOREIGN KEY (`id_marcas`) REFERENCES `marcas` (`id_marcas`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelo_auto`
--

LOCK TABLES `modelo_auto` WRITE;
/*!40000 ALTER TABLE `modelo_auto` DISABLE KEYS */;
INSERT INTO `modelo_auto` VALUES (1,1,'Corolla',1),(2,1,'Hilux',1),(3,1,'Fortuner',1),(4,1,'Yaris',1),(5,1,'Land Cruiser',1),(6,1,'Prado',1),(7,1,'Etios',1),(8,1,'Raize',1),(9,1,'Avanza',1),(10,1,'RAV4',1),(11,11,'Frontier',1),(12,11,'Versa',1),(13,12,'Onix',1),(14,12,'S10',1),(15,13,'Sportage',1),(16,14,'Tucson',1),(17,15,'Gol',1),(18,15,'Amarok',1);
/*!40000 ALTER TABLE `modelo_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientostock`
--

DROP TABLE IF EXISTS `movimientostock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientostock` (
  `MovStockId` bigint NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `TipoMovStockId` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockArticuloId` int unsigned NOT NULL,
  `MovStockCantidad` decimal(12,4) NOT NULL,
  `MovStockPrecioVenta` decimal(14,2) NOT NULL,
  `MovStockCosto` decimal(14,2) NOT NULL,
  `MovStockFechaHora` datetime NOT NULL,
  `MovStockNroTicket` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockPOS` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockUsuario` bigint NOT NULL,
  `MovStockSigno` smallint NOT NULL,
  `MovStockReferencia` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MovStockId`),
  KEY `sucmovimientostock_FKIndex1` (`id_sucursal`),
  KEY `fk_mov_articulo` (`MovStockArticuloId`),
  CONSTRAINT `fk_mov_articulo` FOREIGN KEY (`MovStockArticuloId`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_sucursalesSucmo` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientostock`
--

LOCK TABLES `movimientostock` WRITE;
/*!40000 ALTER TABLE `movimientostock` DISABLE KEYS */;
INSERT INTO `movimientostock` VALUES (1,1,'AJUSTE_INV',21,10.0000,0.00,35000.00,'2026-04-20 21:10:52',NULL,NULL,1,1,'AJUSTE #1'),(2,1,'REG. SERVICIO',21,1.0000,0.00,0.00,'2026-04-20 22:15:57',NULL,NULL,1,-1,'REG_SERV #9'),(3,1,'ANULACION REG. SERVICIO',21,1.0000,0.00,0.00,'2026-04-20 22:21:23',NULL,NULL,1,1,'ANUL_REG_SERV #9'),(4,1,'AJUSTE_INV',2,10.0000,0.00,30000.00,'2026-04-23 15:21:11',NULL,NULL,1,1,'AJUSTE #2'),(5,1,'AJUSTE_INV',2,5.0000,0.00,30000.00,'2026-04-25 08:30:51',NULL,NULL,1,-1,'AJUSTE #3'),(6,1,'AJUSTE_INV',3,10.0000,0.00,40000.00,'2026-04-25 08:30:51',NULL,NULL,1,1,'AJUSTE #3'),(11,1,'REG. SERVICIO',2,1.0000,22500.00,0.00,'2026-04-25 17:13:46',NULL,NULL,1,-1,'REG_SERV #13'),(12,1,'REG. SERVICIO',21,1.0000,0.00,0.00,'2026-04-25 17:13:46',NULL,NULL,1,-1,'REG_SERV #13'),(13,1,'REG. SERVICIO',2,1.0000,22500.00,0.00,'2026-04-25 17:21:57',NULL,NULL,1,-1,'REG_SERV #14'),(14,1,'REG. SERVICIO',21,1.0000,0.00,0.00,'2026-04-25 17:21:57',NULL,NULL,1,-1,'REG_SERV #14'),(15,1,'TRANSFERENCIA_SALIDA',21,1.0000,0.00,35000.00,'2026-04-26 20:21:27','001-002-0000002',NULL,1,-1,'TRANSFERENCIA #1'),(16,1,'TRANSFERENCIA_SALIDA',21,1.0000,0.00,35000.00,'2026-04-26 21:00:05','001-002-0000003',NULL,1,-1,'TRANSFERENCIA #2'),(17,2,'TRANSFERENCIA_ENTRADA',21,1.0000,0.00,0.00,'2026-04-26 21:00:23',NULL,NULL,7,1,'TRF-2'),(18,2,'TRANSFERENCIA_ENTRADA',21,1.0000,0.00,0.00,'2026-04-26 21:00:23',NULL,NULL,7,1,'TRF-1'),(19,1,'AJUSTE_INV',4,10.0000,0.00,80000.00,'2026-04-30 11:24:20',NULL,NULL,1,1,'AJUSTE #5'),(20,1,'RECEPCION COMPRA',2,10.0000,0.00,100000.00,'2026-04-30 12:46:46','001-001-0000001',NULL,1,1,'1'),(21,1,'RECEPCION COMPRA',24,15.0000,0.00,100000.00,'2026-04-30 12:46:46','001-001-0000001',NULL,1,1,'1'),(22,1,'NC_COMPRA_DEV',2,1.0000,0.00,100000.00,'2026-04-30 13:00:12',NULL,NULL,1,-1,'NC 001-001-0000001'),(23,1,'NC_COMPRA_DEV',24,1.0000,0.00,100000.00,'2026-04-30 13:00:12',NULL,NULL,1,-1,'NC 001-001-0000001'),(24,1,'RECEPCION COMPRA',32,1.0000,0.00,1500.00,'2026-05-02 17:19:12','001-002-0000003',NULL,1,1,'2'),(25,1,'ANULACION COMPRA',32,1.0000,0.00,1500.00,'2026-05-02 18:19:18','ANUL_COMPRA# 2',NULL,1,-1,'ANUL_COMPRA# 2'),(26,1,'ANULA_NC_COMPRA',2,1.0000,0.00,100000.00,'2026-05-03 10:19:16',NULL,NULL,1,1,'ANULA NC 001-001-0000001'),(27,1,'ANULA_NC_COMPRA',24,1.0000,0.00,100000.00,'2026-05-03 10:19:16',NULL,NULL,1,1,'ANULA NC 001-001-0000001'),(28,1,'TRANSFERENCIA_SALIDA',21,1.0000,0.00,35000.00,'2026-05-03 11:16:34','001-002-0000004',NULL,1,-1,'TRANSFERENCIA #3'),(29,2,'TRANSFERENCIA_ENTRADA',21,1.0000,0.00,0.00,'2026-05-03 15:17:23',NULL,NULL,7,1,'TRF-3'),(30,1,'TRANSFERENCIA_SALIDA',21,1.0000,0.00,35000.00,'2026-05-03 15:27:07','001-002-0000005',NULL,1,-1,'TRANSFERENCIA #4'),(31,2,'TRANSFERENCIA_ENTRADA',21,1.0000,0.00,0.00,'2026-05-03 15:27:20',NULL,NULL,7,1,'TRF-4'),(32,1,'TRANSFERENCIA_SALIDA',21,2.0000,0.00,35000.00,'2026-05-03 15:28:26','001-002-0000006',NULL,1,-1,'TRANSFERENCIA #5'),(33,2,'TRANSFERENCIA_ENTRADA',21,1.0000,0.00,0.00,'2026-05-03 15:28:38',NULL,NULL,7,1,'TRF-5'),(34,1,'AJUSTE_INV',2,3.0000,0.00,30000.00,'2026-05-03 19:18:46',NULL,NULL,10,-1,'AJUSTE #13'),(35,1,'AJUSTE_INV',31,10.0000,0.00,5000.00,'2026-05-03 19:18:46',NULL,NULL,10,1,'AJUSTE #13'),(36,1,'AJUSTE_INV',19,10.0000,0.00,30000.00,'2026-05-03 20:02:05',NULL,NULL,10,1,'AJUSTE #15'),(37,1,'ANULACION_AJUSTE_INV',2,3.0000,0.00,30000.00,'2026-05-03 22:00:52',NULL,NULL,10,1,'Anulación ajuste inventario #13'),(38,1,'ANULACION_AJUSTE_INV',31,10.0000,0.00,5000.00,'2026-05-03 22:00:52',NULL,NULL,10,-1,'Anulación ajuste inventario #13');
/*!40000 ALTER TABLE `movimientostock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_compra`
--

DROP TABLE IF EXISTS `nota_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_compra` (
  `idnota_compra` bigint NOT NULL AUTO_INCREMENT,
  `idusuario` int unsigned NOT NULL,
  `idcompra_cabecera` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `idproveedor` bigint DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `movimiento_stock` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` int unsigned DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `timbrado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idnota_compra`),
  KEY `nota_compra_FKIndex1` (`idusuario`),
  KEY `nota_compra_FKIndex2` (`id_sucursal`),
  KEY `nota_compra_FKIndex3` (`idcompra_cabecera`),
  CONSTRAINT `nota_compraCompra` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `nota_compraSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `nota_compraUsu` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra`
--

LOCK TABLES `nota_compra` WRITE;
/*!40000 ALTER TABLE `nota_compra` DISABLE KEYS */;
INSERT INTO `nota_compra` VALUES (1,1,1,1,5,'credito','DEVOLUCION','001-001-0000001','2026-04-30',200000.00,'',0,'2026-04-30 13:00:12','2026-05-03 10:19:16','12345678'),(2,1,1,1,5,'credito','NINGUNO','001-001-0000002','2026-04-30',2175000.00,'',1,'2026-04-30 13:03:49',NULL,'12345678');
/*!40000 ALTER TABLE `nota_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_compra_detalle`
--

DROP TABLE IF EXISTS `nota_compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_compra_detalle` (
  `idnota_compra` bigint NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(12,2) DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`idnota_compra`,`id_articulo`),
  KEY `nota_compra_has_articulos_FKIndex1` (`idnota_compra`),
  KEY `nota_compra_has_articulos_FKIndex2` (`id_articulo`),
  CONSTRAINT `nota_compra_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `nota_compra_detalleCab` FOREIGN KEY (`idnota_compra`) REFERENCES `nota_compra` (`idnota_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra_detalle`
--

LOCK TABLES `nota_compra_detalle` WRITE;
/*!40000 ALTER TABLE `nota_compra_detalle` DISABLE KEYS */;
INSERT INTO `nota_compra_detalle` VALUES (1,2,'Filtro de Aceite Bosch',1.00,100000.00,100000.00),(1,24,'Sellador de roscas',1.00,100000.00,100000.00),(2,2,'Filtro de Aceite Bosch',10.00,90000.00,900000.00),(2,24,'Sellador de roscas',15.00,85000.00,1275000.00);
/*!40000 ALTER TABLE `nota_compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision`
--

DROP TABLE IF EXISTS `nota_remision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision` (
  `idnota_remision` int unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int unsigned DEFAULT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha_emision` datetime NOT NULL,
  `nro_remision` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_transpo` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_transpo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel_transpo` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportista` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc_transport` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehimarca` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehimodelo` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehichapa` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaenvio` date NOT NULL,
  `fechallegada` date NOT NULL,
  `motivo_remision` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  `tipo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idtransferencia` bigint DEFAULT NULL,
  PRIMARY KEY (`idnota_remision`),
  KEY `nota_remision_FKIndex1` (`id_usuario`),
  KEY `nota_remision_FKIndex2` (`id_sucursal`),
  KEY `nota_remision_FKIndex3` (`idcompra_cabecera`),
  CONSTRAINT `nota_remisionCab` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `nota_remisionSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `nota_remisionUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision`
--

LOCK TABLES `nota_remision` WRITE;
/*!40000 ALTER TABLE `nota_remision` DISABLE KEYS */;
INSERT INTO `nota_remision` VALUES (1,NULL,1,1,'2026-04-26 20:21:27','001-002-0000002','jose campos','2342344','0986234945','eleuterio','8765420','toyota','fun cargo','asd234','2026-04-26','2026-04-26','test',NULL,NULL,NULL,'transferencia',1),(2,NULL,1,1,'2026-04-26 21:00:05','001-002-0000003','jose campos','2342344','0986234945','eleuterio','8765420','nissan','ractis','asd234','2026-04-26','2026-04-26','asd',NULL,NULL,NULL,'transferencia',2),(3,1,1,1,'2026-05-02 00:00:00','001-001-0000002','tesre','456321','4569745','tesre','800123654','test','tes','etes456','2026-05-01','2026-05-02','traslado',1,NULL,NULL,'recepcion compra',NULL),(4,NULL,1,1,'2026-05-03 11:16:34','001-002-0000004','jose campos','2342344','0986234945','eleuterio','8765425345','toyota','fun cargo','asd234','2026-05-03','2026-05-03','ASDAS',NULL,NULL,NULL,'transferencia',3),(5,NULL,1,1,'2026-05-03 15:27:07','001-002-0000005','jose campos','2342344','0986234945','test','8765420','nissan','navara','asd234','2026-05-03','2026-05-03','asd',NULL,NULL,NULL,'transferencia',4),(6,NULL,1,1,'2026-05-03 15:28:26','001-002-0000006','jose campos','2342344','0986234945','test','8765420','asdas','asd','asd234','2026-05-03','2026-05-03','dfsdf',NULL,NULL,NULL,'transferencia',5);
/*!40000 ALTER TABLE `nota_remision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision_detalle`
--

DROP TABLE IF EXISTS `nota_remision_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision_detalle` (
  `id_articulo` int unsigned NOT NULL,
  `idnota_remision` int unsigned NOT NULL,
  `cantidad` decimal(14,2) NOT NULL,
  `costo` decimal(14,2) NOT NULL,
  `subtotal` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`idnota_remision`),
  KEY `nota_remision_detalle_FKIndex1` (`id_articulo`),
  KEY `nota_remision_detalle_FKIndex2` (`idnota_remision`),
  CONSTRAINT `nota_remision_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `nota_remision_detalleCAb` FOREIGN KEY (`idnota_remision`) REFERENCES `nota_remision` (`idnota_remision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision_detalle`
--

LOCK TABLES `nota_remision_detalle` WRITE;
/*!40000 ALTER TABLE `nota_remision_detalle` DISABLE KEYS */;
INSERT INTO `nota_remision_detalle` VALUES (2,3,10.00,100000.00,1000000.00),(21,1,1.00,35000.00,35000.00),(21,2,1.00,35000.00,35000.00),(21,4,1.00,35000.00,35000.00),(21,5,1.00,35000.00,35000.00),(21,6,2.00,35000.00,70000.00),(24,3,15.00,100000.00,1500000.00);
/*!40000 ALTER TABLE `nota_remision_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_compra`
--

DROP TABLE IF EXISTS `orden_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_compra` (
  `idorden_compra` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `presupuestoid` int unsigned DEFAULT NULL,
  `idproveedores` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idorden_compra`),
  KEY `orden_compra_FKIndex1` (`id_usuario`),
  KEY `orden_compra_FKIndex2` (`idproveedores`),
  KEY `orden_compra_FKIndex3` (`presupuestoid`),
  KEY `orden_compra_FKIndex4` (`id_sucursal`),
  CONSTRAINT `orden_compraPre` FOREIGN KEY (`presupuestoid`) REFERENCES `presupuesto_compra` (`idpresupuesto_compra`),
  CONSTRAINT `orden_compraPro` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `orden_compraSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `orden_compraUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra`
--

LOCK TABLES `orden_compra` WRITE;
/*!40000 ALTER TABLE `orden_compra` DISABLE KEYS */;
INSERT INTO `orden_compra` VALUES (1,1,2,3,1,'2026-04-14',1,'2026-04-15',NULL,NULL),(2,1,3,2,1,'2026-04-24',1,'2026-04-24',NULL,NULL),(3,1,4,1,1,'2026-04-25',1,'2026-04-25',NULL,NULL),(4,1,4,1,1,'2026-04-25',1,'2026-04-25',NULL,NULL),(5,1,NULL,8,1,'2026-04-25',1,'2026-04-30',NULL,NULL),(6,1,6,5,1,'2026-04-30',2,'2026-05-09',1,'2026-04-30 12:46:46'),(7,1,NULL,7,1,'2026-05-01',1,'2026-05-01',NULL,NULL),(8,1,5,7,1,'2026-05-01',1,'2026-05-01',NULL,NULL),(9,1,NULL,7,1,'2026-05-01',1,'2026-05-31',1,'2026-05-02 18:19:18'),(10,1,5,7,1,'2026-05-01',0,'2026-05-01',1,'2026-05-02 08:07:13'),(11,1,3,2,1,'2026-05-01',0,'2026-05-01',1,'2026-05-02 08:07:05'),(12,1,5,7,1,'2026-05-02',0,'2026-05-16',1,'2026-05-02 11:35:39');
/*!40000 ALTER TABLE `orden_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_compra_detalle`
--

DROP TABLE IF EXISTS `orden_compra_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_compra_detalle` (
  `idorden_compra` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` bigint DEFAULT NULL,
  `precio_unitario` bigint DEFAULT NULL,
  `cantidad_pendiente` bigint DEFAULT NULL,
  PRIMARY KEY (`idorden_compra`,`id_articulo`),
  KEY `orden_compra_has_presupuesto_detalle_FKIndex1` (`idorden_compra`),
  KEY `orden_compra_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `orden_compra_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `orden_compra_detalleCompra` FOREIGN KEY (`idorden_compra`) REFERENCES `orden_compra` (`idorden_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra_detalle`
--

LOCK TABLES `orden_compra_detalle` WRITE;
/*!40000 ALTER TABLE `orden_compra_detalle` DISABLE KEYS */;
INSERT INTO `orden_compra_detalle` VALUES (1,9,4,250000,4),(2,4,2,125000,2),(3,3,5,50000,5),(4,3,22,50000,22),(5,2,1,56000,1),(6,2,10,100000,0),(6,24,15,100000,0),(7,30,1,5000,1),(9,32,1,1500,1),(10,27,50,5000,50),(11,4,10,125000,10),(11,12,15,45000,15),(12,27,10,5000,10);
/*!40000 ALTER TABLE `orden_compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo`
--

DROP TABLE IF EXISTS `orden_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo` (
  `idorden_trabajo` int unsigned NOT NULL AUTO_INCREMENT,
  `idtrabajos` int unsigned DEFAULT NULL,
  `tecnico_responsable` int unsigned DEFAULT NULL,
  `idpresupuesto_servicio` int unsigned DEFAULT NULL,
  `id_usuario` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `origen` enum('NORMAL','RECLAMO') COLLATE utf8mb4_unicode_ci DEFAULT 'NORMAL',
  `idreclamo_servicio` int DEFAULT NULL,
  PRIMARY KEY (`idorden_trabajo`),
  KEY `orden_trabajo_FKIndex1` (`id_usuario`),
  KEY `orden_trabajo_FKIndex2` (`idpresupuesto_servicio`),
  KEY `orden_trabajo_FKIndex4` (`tecnico_responsable`),
  KEY `orden_trabajo_FKIndex5` (`idtrabajos`),
  CONSTRAINT `fk_empleados` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_equipoTrabajo` FOREIGN KEY (`idtrabajos`) REFERENCES `equipo_trabajo` (`id_equipo`),
  CONSTRAINT `fk_prespuestoServicio` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo`
--

LOCK TABLES `orden_trabajo` WRITE;
/*!40000 ALTER TABLE `orden_trabajo` DISABLE KEYS */;
INSERT INTO `orden_trabajo` VALUES (2,1,2,1,1,1,'2026-04-18 19:17:10',NULL,0,'asda','2026-04-19 23:06:59',NULL,'2026-04-18 19:17:10','NORMAL',NULL),(3,2,6,5,1,1,'2026-04-19 17:50:57',NULL,0,'test','2026-04-19 23:04:56',NULL,'2026-04-19 17:50:57','NORMAL',NULL),(4,1,2,6,1,1,'2026-04-19 20:08:53',NULL,0,'dasd','2026-04-19 23:13:23',1,'2026-04-19 20:08:53','NORMAL',NULL),(5,2,4,4,1,1,'2026-04-19 20:42:19',NULL,0,'dtes','2026-04-19 23:43:19',1,'2026-04-19 20:42:19','NORMAL',NULL),(6,2,6,2,1,1,'2026-04-19 20:43:13','2026-04-21 20:09:20',2,'testss','2026-04-21 23:09:20',1,'2026-04-19 20:43:13','NORMAL',NULL),(13,2,1,NULL,1,1,'2026-04-23 10:52:11',NULL,2,'test','2026-04-24 01:50:34',NULL,'2026-04-23 10:52:11','RECLAMO',1),(14,1,2,7,1,1,'2026-04-23 23:04:55','2026-04-25 17:21:57',2,'test','2026-04-25 20:21:57',1,'2026-04-23 23:04:55','NORMAL',NULL);
/*!40000 ALTER TABLE `orden_trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo_detalle`
--

DROP TABLE IF EXISTS `orden_trabajo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo_detalle` (
  `id_detalle_ot` int NOT NULL AUTO_INCREMENT,
  `cantidad` int unsigned NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `idorden_trabajo` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  PRIMARY KEY (`id_detalle_ot`),
  KEY `fk_orden_trabajo_detalle_orden_trabajo1_idx` (`idorden_trabajo`),
  KEY `fk_orden_trabajo_detalle_articulos1_idx` (`id_articulo`),
  CONSTRAINT `fk_orden_trabajo_detalle_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_orden_trabajo_detalle_orden_trabajo1` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo_detalle`
--

LOCK TABLES `orden_trabajo_detalle` WRITE;
/*!40000 ALTER TABLE `orden_trabajo_detalle` DISABLE KEYS */;
INSERT INTO `orden_trabajo_detalle` VALUES (1,1,22500.00,22500.00,2,2),(2,1,80000.00,80000.00,2,13),(4,1,80000.00,80000.00,3,13),(5,1,80000.00,80000.00,4,13),(6,1,80000.00,80000.00,5,13),(7,1,80000.00,80000.00,6,13),(31,1,0.00,0.00,13,2),(32,1,0.00,0.00,13,13),(33,1,100000.00,100000.00,14,14),(34,1,22500.00,22500.00,14,2);
/*!40000 ALTER TABLE `orden_trabajo_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_cabecera`
--

DROP TABLE IF EXISTS `pedido_cabecera`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_cabecera` (
  `idpedido_cabecera` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`),
  KEY `pedido_cabecera_FKIndex1` (`id_usuario`),
  KEY `pedido_cabecera_FKIndex2` (`id_sucursal`),
  CONSTRAINT `fk_sucursalesPedCab` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_usuarioPedCab` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cabecera`
--

LOCK TABLES `pedido_cabecera` WRITE;
/*!40000 ALTER TABLE `pedido_cabecera` DISABLE KEYS */;
INSERT INTO `pedido_cabecera` VALUES (1,1,1,'2026-03-26 23:25:15',2,'2026-03-26 23:25:51','1'),(2,1,1,'2026-04-14 20:28:43',2,'2026-04-14 20:29:19','1'),(3,1,1,'2026-04-16 21:15:14',2,'2026-04-16 22:11:39','1'),(4,1,1,'2026-04-25 09:32:06',2,'2026-04-25 09:33:32','1'),(5,1,1,'2026-04-30 08:43:01',2,'2026-04-30 08:53:01','1'),(6,1,1,'2026-04-30 12:39:30',2,'2026-04-30 12:40:43','1'),(7,1,1,'2026-05-01 16:12:25',2,'2026-05-01 16:13:04','1'),(8,1,1,'2026-05-01 16:54:07',0,'2026-05-01 16:54:21','1'),(9,1,1,'2026-05-01 16:55:52',1,NULL,NULL),(10,1,1,'2026-05-01 17:35:30',1,'2026-05-01 18:50:33','1');
/*!40000 ALTER TABLE `pedido_cabecera` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido_detalle`
--

DROP TABLE IF EXISTS `pedido_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido_detalle` (
  `idpedido_cabecera` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`,`id_articulo`),
  KEY `pedido_cabecera_has_articulos_FKIndex1` (`idpedido_cabecera`),
  KEY `pedido_cabecera_has_articulos_FKIndex2` (`id_articulo`),
  CONSTRAINT `fk_articulodet` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_pedidodet` FOREIGN KEY (`idpedido_cabecera`) REFERENCES `pedido_cabecera` (`idpedido_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_detalle`
--

LOCK TABLES `pedido_detalle` WRITE;
/*!40000 ALTER TABLE `pedido_detalle` DISABLE KEYS */;
INSERT INTO `pedido_detalle` VALUES (1,13,1),(2,9,4),(3,4,10),(3,12,15),(4,3,1),(5,27,1),(6,2,1),(6,24,1),(7,4,1),(8,30,1),(9,30,1),(10,28,1),(10,30,1);
/*!40000 ALTER TABLE `pedido_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id_permiso` int unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=212 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'servicio.recepcion.crear','Registrar solicitud de servicio'),(2,'servicio.recepcion.ver','Ver recepciones de servicio'),(3,'servicio.presupuesto.crear','Crear presupuesto de servicio'),(4,'servicio.presupuesto.aprobar','Aprobar presupuesto de servicio'),(5,'servicio.ot.generar','Generar orden de trabajo'),(6,'servicio.ot.asignar_tecnico','Asignar técnico a OT'),(7,'servicio.registro.crear','Registrar servicio finalizado'),(8,'servicio.registro.anular','Anular registro de servicio'),(9,'servicio.reclamo.crear','Registrar reclamo de cliente'),(10,'servicio.reclamo.cerrar','Cerrar reclamo de cliente'),(11,'servicio.reclamo.ver','Ver reclamos de clientes'),(12,'usuarios.ver','Ver usuarios'),(13,'usuarios.crear','Crear usuarios'),(14,'usuarios.editar','Editar usuarios'),(15,'usuarios.eliminar','Eliminar usuarios'),(16,'roles.ver','Ver roles'),(17,'roles.editar','Editar roles'),(18,'empresa.ver','Ver datos de la empresa'),(19,'empresa.editar','Editar datos de la empresa'),(20,'sucursal.ver','Ver sucursales'),(21,'sucursal.editar','Editar sucursales'),(22,'cliente.ver','Ver clientes'),(23,'cliente.crear','Registrar clientes'),(24,'cliente.editar','Editar clientes'),(25,'vehiculo.ver','Ver vehículos'),(26,'vehiculo.crear','Registrar vehículos'),(27,'vehiculo.editar','Editar vehículos'),(47,'compra.crear','Registrar compra'),(48,'compra.editar','Editar documentos de compra'),(49,'compra.anular','Anular documentos de compra'),(50,'compra.ver','Ver compras'),(51,'proveedor.ver','Ver proveedores'),(52,'proveedor.crear','Registrar proveedores'),(53,'proveedor.editar','Editar proveedores'),(54,'stock.ver','Ver stock'),(55,'stock.ajustar','Ajustar stock'),(56,'stock.movimiento.ver','Ver movimientos de stock'),(57,'servicio.reportes.ver','Ver reportes de servicios'),(58,'compra.reportes.ver','Ver reportes de compras'),(59,'stock.reportes.ver','Ver reportes de stock'),(60,'servicio.presupuesto.ver','Ver presupuestos de servicio'),(61,'servicio.ot.ver','Ver órdenes de trabajo'),(62,'servicio.registro.ver','Ver registros de servicio'),(63,'servicio.ot.cerrar','Cerrar orden de trabajo'),(64,'servicio.ot.anular','Anular orden de trabajo'),(71,'stock.administrar','Administrar parámetros de stock'),(120,'compra.pedido.ver','Ver pedidos de compra'),(121,'compra.pedido.crear','Crear pedidos de compra'),(122,'compra.presupuesto.ver','Ver presupuestos de compra'),(123,'compra.presupuesto.crear','Crear presupuesto de compra'),(124,'compra.oc.ver','Ver órdenes de compra'),(125,'compra.oc.crear','Crear órdenes de compra'),(126,'compra.factura.ver','Ver facturas de compra'),(127,'compra.factura.crear','Registrar facturas de compra'),(128,'compra.remision.ver','Ver remisiones'),(129,'compra.remision.crear','Registrar remisiones'),(130,'compra.nota.ver','Ver notas de crédito y débito'),(131,'compra.nota.crear','Registrar notas de crédito y débito'),(160,'inventario.ver','Ver inventarios'),(161,'inventario.crear','Generar Inventarios'),(162,'inventario.editar','Editar inventarios'),(164,'compra.presupuesto.anular','Anular Presupuesto de compra'),(165,'servicio.presupuesto.anular','Anular Presupuesto de servicio'),(166,'servicio.promocion.ver','Ver promociones'),(167,'servicio.descuento.ver','Ver descuentos'),(168,'compra.transferencia.crear','Crear transferencias'),(169,'compra.transferencia.ver','Ver transferencias'),(170,'compra.transferencia.anular','Anular transferencias'),(171,'articulo.crear','Crear articulo'),(172,'articulo.ver','Listar articulos'),(173,'articulo.editar','Editar articulos'),(175,'articulo.eliminar','Eliminar articulos'),(176,'sucursal.crear','Crear Sucursales'),(177,'sucursal.eliminar','Eliminar Sucursales'),(178,'proveedor.eliminar','Eliminar proveedores'),(179,'cliente.eliminar','Eliminar clientes'),(180,'vehiculo.eliminar','Eliminar vehículo'),(181,'empleado.ver','Ver empleados'),(182,'empleado.editar','Editar empleados'),(183,'empleado.crear','Crear empleados'),(184,'empleado.eliminar','Eliminar empleados'),(185,'usuarios.asignarlocal','Asignar local a usuarios'),(186,'usuarios.asignarrol','Asignar rol a usuarios'),(187,'usuarios.permisos_por_roles','Asignar permisos a roles '),(188,'compra.pedido.anular','Anular Pedidos de Compra'),(189,'compra.oc.anular','Anular órdenes de compra'),(190,'compra.factura.anular','Anular facturas de compra'),(191,'compra.nota.anular','Anular notas de crédito y débito'),(192,'compra.remision.anular','Anular remisiones'),(193,'compra.transferencia.recibir','Recibir transferencias'),(194,'inventario.ajustar','Ajustar stock en inventarios'),(195,'servicio.descuento.editar','Editar descuentos'),(196,'servicio.descuento.asignarClientes','Asignar descuentos a Clientes'),(197,'servicio.descuento.crear','Crear descuentos'),(198,'servicio.promocion.editar','Editar promociones'),(199,'servicio.ver','Ver Servicios'),(200,'mantenimiento.ver','Mantenimiento de referenciales'),(201,'servicio.promocion.crear','Crear Promociones'),(202,'servicio.reclamo.anular','Anular reclamo de cliente'),(203,'servicio.diagnostico.crear','Crear diagnostico'),(204,'servicio.diagnostico.ver','Ver Diagnostico'),(205,'servicio.diagnostico.anular','Anular Diagnostico'),(206,'inventario.anular','Anualar inventario'),(207,'servicio.recepcion.anular','Anular recepciones de servicio'),(208,'cargo.crear','Registrar cargos'),(209,'cargo.editar','Editar cargos'),(210,'cargo.eliminar','Eliminar cargos'),(211,'cargo.ver','Ver cargos');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_compra`
--

DROP TABLE IF EXISTS `presupuesto_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_compra` (
  `idpresupuesto_compra` int unsigned NOT NULL AUTO_INCREMENT,
  `id_sucursal` int unsigned NOT NULL,
  `idproveedores` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `fecha_venc` date DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `total` decimal(12,2) DEFAULT NULL,
  `idPedido` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_compra`),
  KEY `presupuesto_compra_FKIndex1` (`id_usuario`),
  KEY `presupuesto_compra_FKIndex2` (`idproveedores`),
  KEY `presupuesto_compra_FKIndex3` (`id_sucursal`),
  CONSTRAINT `fk_proveedorPrC` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `fk_sucursalesPrc` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_usuarioPrC` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_compra`
--

LOCK TABLES `presupuesto_compra` WRITE;
/*!40000 ALTER TABLE `presupuesto_compra` DISABLE KEYS */;
INSERT INTO `presupuesto_compra` VALUES (1,1,1,1,'2026-03-26',1,'2026-03-26',NULL,NULL,15000.00,1),(2,1,3,1,'2026-04-14',2,'2026-04-21',NULL,NULL,1000000.00,2),(3,1,2,1,'2026-04-16',2,'2026-04-24',NULL,NULL,1925000.00,3),(4,1,1,1,'2026-04-25',2,'2026-04-30',NULL,NULL,50000.00,4),(5,1,7,1,'2026-04-30',2,'2026-04-30',1,'2026-05-02 08:18:30',5000.00,5),(6,1,5,1,'2026-04-30',2,'2026-04-30',NULL,NULL,200000.00,6),(7,1,11,1,'2026-05-01',1,'2026-05-30',NULL,NULL,75000.00,7),(8,1,7,1,'2026-05-01',0,'2026-05-21',1,'2026-05-01 18:59:58',30000.00,10);
/*!40000 ALTER TABLE `presupuesto_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_descuento`
--

DROP TABLE IF EXISTS `presupuesto_descuento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_descuento` (
  `id_presupuesto` int unsigned NOT NULL,
  `id_descuento` int unsigned NOT NULL,
  `id_usuario` int unsigned NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `monto_aplicado` decimal(10,2) NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `fk_presupuesto_descuento_presupuesto_servicio1_idx` (`id_presupuesto`),
  KEY `fk_presupuesto_descuento_descuentos1_idx` (`id_descuento`),
  KEY `fk_presupuesto_descuento_usuarios1_idx` (`id_usuario`),
  CONSTRAINT `fk_presupuesto_descuento_descuentos1` FOREIGN KEY (`id_descuento`) REFERENCES `descuentos` (`id_descuento`),
  CONSTRAINT `fk_presupuesto_descuento_presupuesto_servicio1` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_presupuesto_descuento_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_descuento`
--

LOCK TABLES `presupuesto_descuento` WRITE;
/*!40000 ALTER TABLE `presupuesto_descuento` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuesto_descuento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_detalle`
--

DROP TABLE IF EXISTS `presupuesto_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_detalle` (
  `idpresupuesto_compra` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_compra`,`id_articulo`),
  KEY `pedido_detalle_has_presupuesto_compra_FKIndex2` (`idpresupuesto_compra`),
  KEY `presupuesto_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `presupuesto_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `presupuesto_detalleCab` FOREIGN KEY (`idpresupuesto_compra`) REFERENCES `presupuesto_compra` (`idpresupuesto_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalle`
--

LOCK TABLES `presupuesto_detalle` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalle` DISABLE KEYS */;
INSERT INTO `presupuesto_detalle` VALUES (1,13,1.00,15000.00,15000.00),(2,9,4.00,250000.00,1000000.00),(3,4,10.00,125000.00,1250000.00),(3,12,15.00,45000.00,675000.00),(4,3,1.00,50000.00,50000.00),(5,27,1.00,5000.00,5000.00),(6,2,1.00,100000.00,100000.00),(6,24,1.00,100000.00,100000.00),(7,4,1.00,75000.00,75000.00),(8,28,1.00,15000.00,15000.00),(8,30,1.00,15000.00,15000.00);
/*!40000 ALTER TABLE `presupuesto_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_detalleservicio`
--

DROP TABLE IF EXISTS `presupuesto_detalleservicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_detalleservicio` (
  `id_detalle_presupuesto` int NOT NULL AUTO_INCREMENT,
  `idpresupuesto_servicio` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `id_diagnostico_detalle` int DEFAULT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `preciouni` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle_presupuesto`),
  KEY `fk_presupuesto_detalleservicio_presupuesto_servicio1_idx` (`idpresupuesto_servicio`),
  KEY `fk_presupuesto_detalleservicio_articulos1_idx` (`id_articulo`),
  KEY `fk_presupuesto_detalleservicio_diagnostico_detalle1_idx` (`id_diagnostico_detalle`),
  CONSTRAINT `fk_presupuesto_detalleservicio_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_presupuesto_detalleservicio_diagnostico_detalle1` FOREIGN KEY (`id_diagnostico_detalle`) REFERENCES `diagnostico_detalle` (`id_diagnostico_detalle`) ON DELETE SET NULL,
  CONSTRAINT `fk_presupuesto_detalleservicio_presupuesto_servicio1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalleservicio`
--

LOCK TABLES `presupuesto_detalleservicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` DISABLE KEYS */;
INSERT INTO `presupuesto_detalleservicio` VALUES (1,1,2,NULL,1.00,22500.00,22500.00),(2,1,13,NULL,1.00,80000.00,80000.00),(3,2,13,NULL,1.00,80000.00,80000.00),(5,4,13,NULL,1.00,80000.00,80000.00),(6,5,13,NULL,1.00,80000.00,80000.00),(7,6,13,NULL,1.00,80000.00,80000.00),(8,7,14,NULL,1.00,100000.00,100000.00),(9,7,2,NULL,1.00,22500.00,22500.00);
/*!40000 ALTER TABLE `presupuesto_detalleservicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_promocion`
--

DROP TABLE IF EXISTS `presupuesto_promocion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_promocion` (
  `idpresupuesto_servicio` int unsigned NOT NULL,
  `id_promocion` int unsigned NOT NULL,
  `monto_aplicado` decimal(10,2) DEFAULT NULL,
  `fecha_aplicacion` datetime DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_servicio`,`id_promocion`),
  KEY `presupuesto_servicio_has_promociones_FKIndex1` (`idpresupuesto_servicio`),
  KEY `presupuesto_servicio_has_promociones_FKIndex2` (`id_promocion`),
  CONSTRAINT `presupuesto_promocionPro` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`),
  CONSTRAINT `presupuesto_promocionSer` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_promocion`
--

LOCK TABLES `presupuesto_promocion` WRITE;
/*!40000 ALTER TABLE `presupuesto_promocion` DISABLE KEYS */;
INSERT INTO `presupuesto_promocion` VALUES (1,1,22500.00,NULL);
/*!40000 ALTER TABLE `presupuesto_promocion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_servicio`
--

DROP TABLE IF EXISTS `presupuesto_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_servicio` (
  `idpresupuesto_servicio` int unsigned NOT NULL AUTO_INCREMENT,
  `id_diagnostico` int DEFAULT NULL,
  `id_usuario` int unsigned NOT NULL,
  `id_sucursal` int unsigned DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `fecha_venc` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_final` decimal(12,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`idpresupuesto_servicio`),
  KEY `presupuesto_FKIndex3` (`id_usuario`),
  KEY `fk_presupuesto_servicio_diagnostico_servicio1_idx` (`id_diagnostico`),
  KEY `fk_presupuesto_sucursal` (`id_sucursal`),
  CONSTRAINT `fk_presupuesto_servicio_diagnostico_servicio1` FOREIGN KEY (`id_diagnostico`) REFERENCES `diagnostico_servicio` (`id_diagnostico`) ON DELETE SET NULL,
  CONSTRAINT `fk_presupuesto_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_presupuesto_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_servicio`
--

LOCK TABLES `presupuesto_servicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_servicio` DISABLE KEYS */;
INSERT INTO `presupuesto_servicio` VALUES (1,2,1,1,'2026-04-18',2,'2026-04-25',102500.00,0.00,102500.00),(2,2,1,1,'2026-04-19',3,'2026-04-30',80000.00,0.00,80000.00),(4,2,1,1,'2026-04-19',2,'2026-04-20',80000.00,0.00,80000.00),(5,1,1,1,'2026-04-19',2,'2026-04-21',80000.00,0.00,80000.00),(6,2,1,1,'2026-04-19',2,'2026-04-19',80000.00,0.00,80000.00),(7,6,1,1,'2026-04-23',3,'2026-04-23',122500.00,0.00,122500.00);
/*!40000 ALTER TABLE `presupuesto_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promocion_producto`
--

DROP TABLE IF EXISTS `promocion_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promocion_producto` (
  `id_articulo` int unsigned NOT NULL,
  `id_promocion` int unsigned NOT NULL,
  PRIMARY KEY (`id_articulo`,`id_promocion`),
  KEY `articulos_has_promociones_FKIndex1` (`id_articulo`),
  KEY `articulos_has_promociones_FKIndex2` (`id_promocion`),
  CONSTRAINT `promocion_productoArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `promocion_productoPro` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promocion_producto`
--

LOCK TABLES `promocion_producto` WRITE;
/*!40000 ALTER TABLE `promocion_producto` DISABLE KEYS */;
INSERT INTO `promocion_producto` VALUES (2,1);
/*!40000 ALTER TABLE `promocion_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones`
--

DROP TABLE IF EXISTS `promociones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones` (
  `id_promocion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario_modifica` int unsigned DEFAULT NULL,
  `id_usuario_crea` int unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` tinyint unsigned NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_promocion`),
  KEY `promociones_FKIndex1` (`id_usuario_crea`),
  KEY `promociones_FKIndex2` (`id_usuario_modifica`),
  CONSTRAINT `promocionesUsucrea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `promocionesUsumodi` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES (1,NULL,1,'Filtros','Promocion agotar stock','PORCENTAJE',50.00,'2026-04-17','2026-12-31',1,'2026-04-18 14:30:15',NULL);
/*!40000 ALTER TABLE `promociones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `idproveedores` int unsigned NOT NULL AUTO_INCREMENT,
  `id_ciudad` int unsigned NOT NULL,
  `razon_social` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idproveedores`),
  KEY `proveedores_FKIndex1` (`id_ciudad`),
  CONSTRAINT `fk_ciudadesPro` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,1,'Repuestos Central S.A.','80012345-6','0981-123456','Av. Eusebio Ayala Km 5','ventas@repuestoscentral.com.py',1),(2,9,'Auto Partes Guaraní SRL','80023456-1','0982-234567','Av. Artigas 1234','contacto1@autopartesg.com.py',1),(3,1,'Importadora del Motor S.A.','80034567-8','0983-345678','Ruta Transchaco Km 10','info@importmotor.com.py',1),(4,1,'Lubricantes Paraguay SRL','80045678-9','0984-456789','Av. Madame Lynch 456','ventas@lubripar.com.py',1),(5,1,'Distribuidora del Automotor','80056789-0','0985-567890','Av. Mariscal López 789','contacto@distauto.com.py',1),(6,1,'Neumáticos del Sur S.A.','80067890-1','0986-678901','Av. Fernando de la Mora 321','ventas@neumaticosdelsur.com.py',1),(7,1,'Repuestos Japón Import','80078901-2','0981-789012','Barrio San Vicente','info@repuestosjapon.com.py',1),(8,1,'Casa del Filtro SRL','80089012-3','0982-890123','Av. Boggiani 654','ventas@casafiltro.com.py',1),(9,1,'MotorParts Paraguay','80090123-4','0983-901234','Zona Mercado 4','contacto@motorparts.com.py',1),(10,1,'Distribuidora Técnica Automotriz','80101234-5','0984-012345','Av. Defensores del Chaco 987','info@dta.com.py',1),(11,1,'Repuestos y Servicios del Este SRL','80123456-7','0985-112233','Av. Acceso Sur Km 12','ventas@repuestoseste.com.py',1),(13,197,'test','12345678','456789','asdads','asdasda@gmail.com',1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recaudacion_deposito`
--

DROP TABLE IF EXISTS `recaudacion_deposito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recaudacion_deposito` (
  `idrecaudacion_deposito` int unsigned NOT NULL AUTO_INCREMENT,
  `nroapercier_cajas` int unsigned NOT NULL,
  `monto` int unsigned DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`idrecaudacion_deposito`),
  KEY `recaudacion_deposito_FKIndex1` (`nroapercier_cajas`),
  CONSTRAINT `recaudacion_depositoApertura` FOREIGN KEY (`nroapercier_cajas`) REFERENCES `apercier_cajas` (`nroapercier_cajas`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recaudacion_deposito`
--

LOCK TABLES `recaudacion_deposito` WRITE;
/*!40000 ALTER TABLE `recaudacion_deposito` DISABLE KEYS */;
/*!40000 ALTER TABLE `recaudacion_deposito` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recepcion_fotos`
--

DROP TABLE IF EXISTS `recepcion_fotos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recepcion_fotos` (
  `id_foto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_recepcion` int unsigned NOT NULL,
  `ruta_foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL,
  PRIMARY KEY (`id_foto`),
  KEY `recepcion_fotos_FKIndex1` (`id_recepcion`),
  CONSTRAINT `fk_recepcionFo` FOREIGN KEY (`id_recepcion`) REFERENCES `recepcion_servicio` (`idrecepcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion_fotos`
--

LOCK TABLES `recepcion_fotos` WRITE;
/*!40000 ALTER TABLE `recepcion_fotos` DISABLE KEYS */;
/*!40000 ALTER TABLE `recepcion_fotos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recepcion_servicio`
--

DROP TABLE IF EXISTS `recepcion_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recepcion_servicio` (
  `idrecepcion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `id_vehiculo` int unsigned NOT NULL,
  `id_cliente` int unsigned NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `fecha_salida` datetime DEFAULT NULL,
  `kilometraje` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_combustible` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_exterior` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `objetos_vehiculo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_servicio` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_problema` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accesorios` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `estado` tinyint NOT NULL DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `origen` enum('NORMAL','RECLAMO') COLLATE utf8mb4_unicode_ci DEFAULT 'NORMAL',
  `idreclamo_servicio` int DEFAULT NULL,
  PRIMARY KEY (`idrecepcion`),
  KEY `recepcion_FKIndex2` (`id_cliente`),
  KEY `recepcion_FKIndex3` (`id_vehiculo`),
  KEY `recepcion_FKIndex4` (`id_usuario`),
  KEY `fk_recepcion_sucursales1_idx` (`id_sucursal`),
  CONSTRAINT `fk_clienteRS` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_recepcion_sucursales1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_usuariosRS` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_vehiculosRS` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion_servicio`
--

LOCK TABLES `recepcion_servicio` WRITE;
/*!40000 ALTER TABLE `recepcion_servicio` DISABLE KEYS */;
INSERT INTO `recepcion_servicio` VALUES (1,1,11,11,'2026-04-17 21:57:04',NULL,'155000','3/4','rayones','cedula verde, habilitacion','mantenimiento','direccion','normal','llave,llave_repuesto,herramientas,rueda_auxilio,baliza','verificacion',2,'2026-04-17 21:57:04','2026-04-20 22:21:23',1,'NORMAL',NULL),(2,1,11,11,'2026-04-18 14:31:55','2026-04-21 20:09:20','52000','3/4','sin_danos','cedula verde, habilitacion','reparacion','motor','normal','llave,llave_repuesto,baliza','cambio de filtro',3,'2026-04-18 14:31:55','2026-04-21 20:09:20',1,'NORMAL',NULL),(3,1,11,11,'2026-04-21 23:03:17',NULL,'12333','1/2','rayones','asdds','garantia','motor','normal','llave,baliza','test reclamo',1,'2026-04-21 23:03:17',NULL,1,'RECLAMO',1),(4,1,2,2,'2026-04-22 21:17:14',NULL,'50000','1/2','sin_danos','cedula verde','mantenimiento','direccion','urgente','llave,herramientas,rueda_auxilio,baliza','dureza al girar volante',1,'2026-04-22 21:17:14',NULL,1,'NORMAL',NULL),(5,1,2,2,'2026-04-23 23:02:30','2026-04-25 17:21:57','23333','1/4','rayones','cer','mantenimiento','motor','normal','llave,herramientas,rueda_auxilio,baliza','mantenimiento',3,'2026-04-23 23:02:30','2026-04-25 17:21:57',1,'NORMAL',NULL);
/*!40000 ALTER TABLE `recepcion_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamo_servicio`
--

DROP TABLE IF EXISTS `reclamo_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamo_servicio` (
  `idreclamo_servicio` int unsigned NOT NULL AUTO_INCREMENT,
  `idregistro_servicio` int unsigned NOT NULL,
  `id_sucursal` int NOT NULL,
  `fecha_reclamo` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `usuario_registra` int unsigned DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observacion_cierre` text COLLATE utf8mb4_unicode_ci,
  `idorden_trabajo` int unsigned DEFAULT NULL,
  `tipo_reclamo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origen` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` tinyint DEFAULT NULL,
  `requiere_garantia` tinyint DEFAULT '0',
  PRIMARY KEY (`idreclamo_servicio`),
  KEY `reclamos_FKIndex1` (`usuario_registra`),
  KEY `reclamos_FKIndex2` (`idregistro_servicio`),
  KEY `fk_reclamo_servicio_orden_trabajo1_idx` (`idorden_trabajo`),
  CONSTRAINT `fk_reclamo_servicio_orden_trabajo1` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_servicioReg` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`),
  CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio`
--

LOCK TABLES `reclamo_servicio` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio` DISABLE KEYS */;
INSERT INTO `reclamo_servicio` VALUES (1,10,1,'2026-04-21 21:04:30','fuga de filtro',2,1,NULL,NULL,NULL,'SERVICIO','CLIENTE',2,0);
/*!40000 ALTER TABLE `reclamo_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_servicio`
--

DROP TABLE IF EXISTS `registro_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_servicio` (
  `idregistro_servicio` int unsigned NOT NULL AUTO_INCREMENT,
  `idorden_trabajo` int unsigned NOT NULL,
  `id_sucursal` int NOT NULL,
  `fecha_ejecucion` date NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario_registra` int unsigned DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `observacion` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idregistro_servicio`),
  KEY `registro_servicio_FKIndex1` (`idorden_trabajo`),
  KEY `registro_servicio_FKIndex2` (`usuario_registra`),
  CONSTRAINT `fk_registro_servicioordenTrabajo` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_registro_serviciousuarios` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio`
--

LOCK TABLES `registro_servicio` WRITE;
/*!40000 ALTER TABLE `registro_servicio` DISABLE KEYS */;
INSERT INTO `registro_servicio` VALUES (3,6,1,'2026-04-19','2026-04-19 22:09:28',1,0,NULL),(4,6,1,'2026-04-19','2026-04-19 22:11:24',1,0,NULL),(5,6,1,'2026-04-19','2026-04-19 22:18:12',1,0,'test'),(6,6,1,'2026-04-19','2026-04-19 22:20:27',1,0,'finalizado'),(7,6,1,'2026-04-19','2026-04-19 22:24:29',1,0,'test'),(8,6,1,'2026-04-20','2026-04-20 22:11:53',1,0,'test'),(9,6,1,'2026-04-20','2026-04-20 22:15:57',1,0,'finalizado'),(10,6,1,'2026-04-21','2026-04-21 20:09:20',1,3,'test'),(14,14,1,'2026-04-25','2026-04-25 17:21:57',1,1,'finalizado el cambio');
/*!40000 ALTER TABLE `registro_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `registro_servicio_detalle`
--

DROP TABLE IF EXISTS `registro_servicio_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `registro_servicio_detalle` (
  `id_registro_servicio_detalle` int NOT NULL AUTO_INCREMENT,
  `cantidad` decimal(12,2) NOT NULL DEFAULT '0.00',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `origen` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idregistro_servicio` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  PRIMARY KEY (`id_registro_servicio_detalle`),
  KEY `fk_registro_servicio_detalle_registro_servicio1_idx` (`idregistro_servicio`),
  KEY `fk_registro_servicio_detalle_articulos1_idx` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_registro_servicio1` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio_detalle`
--

LOCK TABLES `registro_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `registro_servicio_detalle` DISABLE KEYS */;
INSERT INTO `registro_servicio_detalle` VALUES (2,1.00,80000.00,80000.00,'OT',3,13),(3,1.00,80000.00,80000.00,'OT',4,13),(4,1.00,80000.00,80000.00,'OT',5,13),(5,1.00,80000.00,80000.00,'OT',6,13),(6,1.00,80000.00,80000.00,'OT',7,13),(7,1.00,80000.00,80000.00,'OT',8,13),(8,1.00,80000.00,80000.00,'OT',9,13),(9,1.00,0.00,0.00,'INSUMO',9,21),(10,1.00,80000.00,80000.00,'OT',10,13),(23,1.00,100000.00,100000.00,'OT',14,14),(24,1.00,22500.00,22500.00,'OT',14,2),(26,1.00,0.00,0.00,'INSUMO',14,21);
/*!40000 ALTER TABLE `registro_servicio_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permiso`
--

DROP TABLE IF EXISTS `rol_permiso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rol_permiso` (
  `id_rol` int unsigned NOT NULL,
  `id_permiso` int unsigned NOT NULL,
  PRIMARY KEY (`id_rol`,`id_permiso`),
  KEY `roles_has_permisos_FKIndex1` (`id_rol`),
  KEY `roles_has_permisos_FKIndex2` (`id_permiso`),
  CONSTRAINT `fk_permisosUsupe` FOREIGN KEY (`id_permiso`) REFERENCES `permisos` (`id_permiso`),
  CONSTRAINT `fk_rolesUsupe` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permiso`
--

LOCK TABLES `rol_permiso` WRITE;
/*!40000 ALTER TABLE `rol_permiso` DISABLE KEYS */;
INSERT INTO `rol_permiso` VALUES (1,1),(1,2),(1,7),(1,9),(1,11),(1,62),(1,171),(1,199),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,9),(7,10),(7,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,47),(7,48),(7,49),(7,50),(7,51),(7,52),(7,53),(7,54),(7,55),(7,56),(7,57),(7,58),(7,59),(7,60),(7,61),(7,62),(7,63),(7,64),(7,71),(7,120),(7,121),(7,122),(7,123),(7,124),(7,125),(7,126),(7,127),(7,128),(7,129),(7,130),(7,131),(7,160),(7,161),(7,162),(7,164),(7,165),(7,166),(7,167),(7,168),(7,169),(7,170),(7,171),(7,172),(7,173),(7,175),(7,176),(7,177),(7,178),(7,179),(7,180),(7,181),(7,182),(7,183),(7,184),(7,185),(7,186),(7,187),(7,188),(7,189),(7,190),(7,191),(7,192),(7,193),(7,194),(7,195),(7,196),(7,197),(7,198),(7,199),(7,200),(7,201),(7,202),(7,203),(7,204),(7,205),(7,206),(7,207),(7,208),(7,209),(7,210),(7,211),(8,1),(8,2),(8,3),(8,4),(8,5),(8,6),(8,7),(8,8),(8,9),(8,10),(8,11),(8,12),(8,13),(8,14),(8,15),(8,16),(8,17),(8,18),(8,19),(8,20),(8,21),(8,22),(8,23),(8,24),(8,25),(8,26),(8,27),(8,47),(8,48),(8,49),(8,50),(8,51),(8,52),(8,53),(8,54),(8,55),(8,56),(8,57),(8,58),(8,59),(8,60),(8,61),(8,62),(8,63),(8,64),(8,71),(8,120),(8,121),(8,122),(8,123),(8,124),(8,125),(8,126),(8,127),(8,128),(8,129),(8,130),(8,131),(8,160),(8,161),(8,162),(9,1),(9,2),(9,3),(9,9),(9,11),(9,22),(9,23),(9,25),(9,26),(10,1),(10,2),(10,3),(10,4),(10,5),(10,6),(10,7),(10,8),(10,9),(10,10),(10,11),(10,14),(10,22),(10,23),(10,24),(10,25),(10,26),(10,27),(10,57),(10,60),(10,61),(10,62),(10,63),(10,64),(10,165),(10,166),(10,167),(10,179),(10,180),(10,181),(10,182),(10,183),(10,184),(10,195),(10,196),(10,197),(10,198),(10,199),(10,201),(10,202),(11,5),(11,6),(11,61),(11,64),(12,47),(12,50),(12,51),(12,52),(12,54),(12,120),(12,121),(12,126),(12,127),(12,128),(12,129),(12,130),(12,131),(12,168),(12,169),(12,193),(13,14),(13,20),(13,21),(13,47),(13,48),(13,49),(13,50),(13,51),(13,52),(13,53),(13,58),(13,120),(13,121),(13,122),(13,123),(13,124),(13,125),(13,126),(13,127),(13,128),(13,129),(13,130),(13,131),(13,160),(13,161),(13,162),(13,164),(13,168),(13,169),(13,170),(13,176),(13,177),(13,178),(13,188),(13,189),(13,190),(13,191),(13,192),(13,193),(13,194),(14,2),(14,11),(14,12),(14,16),(14,18),(14,20),(14,22),(14,25),(14,50),(14,51),(14,54),(14,56),(14,57),(14,58),(14,59);
/*!40000 ALTER TABLE `rol_permiso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Personal de Recepción','Encargado de recpecionar las solicitudes de clientes',1),(7,'Super Administrador','Control total del sistema',1),(8,'Administrador','Administrador general del sistema',1),(9,'Recepción','Recepción de vehículos y atención al cliente',1),(10,'Encargado de Servicios','Gestión completa del área de servicios',1),(11,'Técnico','Ejecución de órdenes de trabajo',1),(12,'Personal de Compras','Registro de compras y proveedores',1),(13,'Encargado de Compras','Gestión y aprobación de compras',1),(14,'Auditor','Solo lectura y reportes',1),(15,'Tester','Encargado de realizacion de pruebas',1),(16,'Tester 2','Encargado de realizacion de pruebas 2',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock`
--

DROP TABLE IF EXISTS `stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock` (
  `id_articulo` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `stockcant_max` int unsigned DEFAULT NULL,
  `stockcant_min` int unsigned DEFAULT NULL,
  `stockDisponible` decimal(12,4) NOT NULL,
  `stockUltActualizacion` datetime NOT NULL,
  `stockUsuActualizacion` bigint DEFAULT NULL,
  `stockultimoIdActualizacion` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`id_sucursal`),
  KEY `deposito_has_articulos_FKIndex2` (`id_articulo`),
  KEY `stock_FKIndex2` (`id_sucursal`),
  CONSTRAINT `stockArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `stockSuc` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock`
--

LOCK TABLES `stock` WRITE;
/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO `stock` VALUES (2,1,200,15,13.0000,'2026-05-03 22:00:52',10,13),(3,1,200,15,10.0000,'2026-04-25 08:30:51',1,3),(4,1,200,15,10.0000,'2026-04-30 11:24:20',1,5),(19,1,200,15,10.0000,'2026-05-03 20:02:05',10,15),(21,1,200,15,2.0000,'2026-04-25 17:21:57',1,14),(21,2,200,15,5.0000,'2026-05-03 15:28:38',7,5),(24,1,200,15,15.0000,'2026-05-03 10:19:16',1,1),(31,1,200,15,0.0000,'2026-05-03 22:00:52',10,13),(32,1,200,15,0.0000,'2026-05-02 18:19:18',1,2);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursal_documento`
--

DROP TABLE IF EXISTS `sucursal_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursal_documento` (
  `id_documento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_caja` int unsigned DEFAULT NULL,
  `id_timbrado` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `tipo_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `establecimiento` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `punto_expedicion` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_actual` bigint NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `sucursal_documento_FKIndex1` (`id_timbrado`),
  KEY `sucursal_documento_FKIndex2` (`id_caja`),
  KEY `sucursal_documento_index5794` (`id_sucursal`,`id_caja`,`tipo_documento`),
  CONSTRAINT `sucursal_documentoCaja` FOREIGN KEY (`id_caja`) REFERENCES `cajas` (`id_caja`),
  CONSTRAINT `sucursal_documentoTimbrado` FOREIGN KEY (`id_timbrado`) REFERENCES `timbrado` (`id_timbrado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursal_documento`
--

LOCK TABLES `sucursal_documento` WRITE;
/*!40000 ALTER TABLE `sucursal_documento` DISABLE KEYS */;
INSERT INTO `sucursal_documento` VALUES (1,NULL,1,1,'remision','001','002',6,1);
/*!40000 ALTER TABLE `sucursal_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sucursales`
--

DROP TABLE IF EXISTS `sucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sucursales` (
  `id_sucursal` int unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int unsigned NOT NULL,
  `suc_descri` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suc_direccion` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suc_telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_establecimiento` int unsigned DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`),
  KEY `sucursales_FKIndex1` (`id_empresa`),
  CONSTRAINT `fk_empresaSu` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (1,2,'lubriReducto 1','san lorenzo','0215678345',1,1),(2,2,'lubriReducto 2','capiata','021567833',2,1),(10,2,'lubriReducto 5','test','0982234561',5,0);
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjeta_detalle`
--

DROP TABLE IF EXISTS `tarjeta_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarjeta_detalle` (
  `idcobros` int unsigned NOT NULL,
  `id_tarj` int unsigned NOT NULL,
  `monto` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcobros`,`id_tarj`),
  KEY `cobros_has_cobro_tarjeta_FKIndex1` (`idcobros`),
  KEY `tarjeta_detalle_FKIndex2` (`id_tarj`),
  CONSTRAINT `tarjeta_detalleCobro` FOREIGN KEY (`idcobros`) REFERENCES `cobros` (`idcobros`),
  CONSTRAINT `tarjeta_detalleTarjeta` FOREIGN KEY (`id_tarj`) REFERENCES `tarjetas` (`id_tarj`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjeta_detalle`
--

LOCK TABLES `tarjeta_detalle` WRITE;
/*!40000 ALTER TABLE `tarjeta_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `tarjeta_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tarjetas`
--

DROP TABLE IF EXISTS `tarjetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tarjetas` (
  `id_tarj` int unsigned NOT NULL AUTO_INCREMENT,
  `identidad_emisora` int unsigned NOT NULL,
  `num_tar` int unsigned DEFAULT NULL,
  `titular_tar` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `est_tar` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_tarj`),
  KEY `tarjetas_FKIndex1` (`identidad_emisora`),
  CONSTRAINT `fk_entidadEmisora` FOREIGN KEY (`identidad_emisora`) REFERENCES `entidad_emisora` (`identidad_emisora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tarjetas`
--

LOCK TABLES `tarjetas` WRITE;
/*!40000 ALTER TABLE `tarjetas` DISABLE KEYS */;
/*!40000 ALTER TABLE `tarjetas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timbrado`
--

DROP TABLE IF EXISTS `timbrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timbrado` (
  `id_timbrado` int unsigned NOT NULL AUTO_INCREMENT,
  `timbrado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_timbrado`),
  UNIQUE KEY `sucursal_timbrado_uniqueIndex` (`timbrado`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timbrado`
--

LOCK TABLES `timbrado` WRITE;
/*!40000 ALTER TABLE `timbrado` DISABLE KEYS */;
INSERT INTO `timbrado` VALUES (1,'12345678','2025-01-01','2026-12-31',1);
/*!40000 ALTER TABLE `timbrado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_impuesto`
--

DROP TABLE IF EXISTS `tipo_impuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_impuesto` (
  `idiva` int unsigned NOT NULL AUTO_INCREMENT,
  `tipo_impuesto_descri` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `ratevalueiva` double DEFAULT NULL,
  `divisor` double DEFAULT NULL,
  PRIMARY KEY (`idiva`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_impuesto`
--

LOCK TABLES `tipo_impuesto` WRITE;
/*!40000 ALTER TABLE `tipo_impuesto` DISABLE KEYS */;
INSERT INTO `tipo_impuesto` VALUES (1,'5%',1,0.05,21),(2,'10%',1,0.1,11),(3,'EXENTO',1,0,1);
/*!40000 ALTER TABLE `tipo_impuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencia_stock`
--

DROP TABLE IF EXISTS `transferencia_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencia_stock` (
  `idtransferencia` bigint NOT NULL AUTO_INCREMENT,
  `sucursal_origen` bigint NOT NULL,
  `sucursal_destino` bigint NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text COLLATE utf8mb4_unicode_ci,
  `usuario_envia` bigint DEFAULT NULL,
  `usuario_recibe` bigint DEFAULT NULL,
  `idtransferencia_origen` int unsigned DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`idtransferencia`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock`
--

LOCK TABLES `transferencia_stock` WRITE;
/*!40000 ALTER TABLE `transferencia_stock` DISABLE KEYS */;
INSERT INTO `transferencia_stock` VALUES (1,1,2,'2026-04-26 20:21:27','recibido','test',1,7,NULL,'2026-04-26 20:52:02'),(2,1,2,'2026-04-26 21:00:05','recibido','asd',1,7,NULL,'2026-04-26 21:00:23'),(3,1,2,'2026-05-03 11:16:34','recibido','ASDAS',1,7,NULL,'2026-05-03 15:17:23'),(4,1,2,'2026-05-03 15:27:07','recibido','asd',1,7,NULL,'2026-05-03 15:27:20'),(5,1,2,'2026-05-03 15:28:26','recibido_parcial','dfsdf',1,7,NULL,'2026-05-03 15:28:38'),(6,2,1,'2026-05-03 15:28:38','en_transito',NULL,1,NULL,5,'2026-05-03 15:28:38');
/*!40000 ALTER TABLE `transferencia_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencia_stock_detalle`
--

DROP TABLE IF EXISTS `transferencia_stock_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencia_stock_detalle` (
  `idtransferencia` bigint NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` decimal(12,2) DEFAULT NULL,
  `cantidad_recibida` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`idtransferencia`,`id_articulo`),
  KEY `transferencia_stock_has_articulos_FKIndex1` (`idtransferencia`),
  KEY `transferencia_stock_has_articulos_FKIndex2` (`id_articulo`),
  CONSTRAINT `transferencia_stock_detalleArt` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `transferencia_stock_detalleCab` FOREIGN KEY (`idtransferencia`) REFERENCES `transferencia_stock` (`idtransferencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock_detalle`
--

LOCK TABLES `transferencia_stock_detalle` WRITE;
/*!40000 ALTER TABLE `transferencia_stock_detalle` DISABLE KEYS */;
INSERT INTO `transferencia_stock_detalle` VALUES (1,21,1.00,1.00),(2,21,1.00,1.00),(3,21,1.00,1.00),(4,21,1.00,1.00),(5,21,2.00,1.00),(6,21,1.00,NULL);
/*!40000 ALTER TABLE `transferencia_stock_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `idunidad_medida` int unsigned NOT NULL AUTO_INCREMENT,
  `medida` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idunidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES (1,'Unidad',1),(2,'Litro',1),(3,'Par',1),(4,'Juego',1),(5,'Kit',1),(6,'Servicio',1);
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario_rol`
--

DROP TABLE IF EXISTS `usuario_rol`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario_rol` (
  `id_usuario` int unsigned NOT NULL,
  `id_rol` int unsigned NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_rol`),
  KEY `usuarios_has_roles_FKIndex1` (`id_usuario`),
  KEY `usuarios_has_roles_FKIndex2` (`id_rol`),
  CONSTRAINT `usuario_rolRol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `usuario_rolUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario_rol`
--

LOCK TABLES `usuario_rol` WRITE;
/*!40000 ALTER TABLE `usuario_rol` DISABLE KEYS */;
INSERT INTO `usuario_rol` VALUES (1,7),(7,13),(10,7),(12,13);
/*!40000 ALTER TABLE `usuario_rol` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int unsigned NOT NULL AUTO_INCREMENT,
  `sucursalid` int unsigned DEFAULT NULL,
  `id_rol` int unsigned DEFAULT NULL,
  `usu_nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_clave` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_estado` int unsigned DEFAULT NULL,
  `usu_nick` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_apellido` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_email` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_ci` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `usuarios_FKIndex1` (`id_rol`),
  KEY `usuarios_FKIndex2` (`sucursalid`),
  CONSTRAINT `fk_rolesUsu` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`),
  CONSTRAINT `fk_sucursalesUsu` FOREIGN KEY (`sucursalid`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,7,'Administrador','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'admin','Del Sistema','admins@admin.com.py','0986203431','1234567'),(7,2,13,'User','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'ucompra','Compra','ucompra@reducto.com.py','09862349732','1234566'),(8,1,10,'user','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'uservicio','Servicio','uservicio@reducto.com.py','0986234973','1234560'),(10,1,8,'Jorge','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'jdure','Dure','jdure@gmail.com','0985123654','5326548'),(11,2,12,'Angel','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'adure','Dure','adure@reduc.com','0985123651','5456789'),(12,1,1,'Diego','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'ddure','Dure','ddure@admin.com','0985123654','6456789'),(13,1,10,'Rufino','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,'rdure','Dure','rdure@admin.com','0985123456','2456987'),(14,NULL,NULL,'test','bDBOQlFtKy9uMXVRUmNGWXcwWXVmdz09',1,'test','test','test@gmail.com','032589456','9876541'),(15,10,12,'joses','WWwwVE1Ed2NraEQzQTFRQVUxTXN3Zz09',1,'jperez','perezz','jperez@gmail.com','3054146477','1234997'),(16,NULL,NULL,'test','N1pkcVZEelJqbWN6SjlIQVZSOVZHZz09',1,'tprueba','prueba','tprueba@gmail.com','123456789','6465412');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehiculos` (
  `id_vehiculo` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cliente` int unsigned NOT NULL,
  `id_modeloauto` int unsigned NOT NULL,
  `nro_serie` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `placa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anho` year DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `color` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_vehiculo`),
  KEY `vehiculos_FKIndex1` (`id_modeloauto`),
  KEY `vehiculos_FKIndex3` (`id_cliente`),
  CONSTRAINT `fk_clientesVE` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_modeloAutoVe` FOREIGN KEY (`id_modeloauto`) REFERENCES `modelo_auto` (`id_modeloauto`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (1,1,1,'123456789asdf','ABC987',2020,1,NULL),(2,2,2,'8AJFR22G0L1234567','DEF456',2020,1,NULL),(3,3,3,'JTMBFREV10D098765','GHI789',2019,1,NULL),(4,4,4,'MR0BX3CD5JH123456','JKL321',2017,1,NULL),(5,5,5,'JTEBU5JR2K5678901','MNO654',2021,1,NULL),(6,6,6,'JTDBU4EE9B9123456','PQR987',2016,1,NULL),(7,7,7,'MR0EX32G3K1234567','STU741',2015,1,NULL),(8,8,8,'JTMBFREVXKJ654321','VWX852',2022,1,NULL),(9,9,9,'JTDKB20U793123456','YZA963',2014,1,NULL),(10,10,10,'JT2BG22K3V0123456','BCD159',2023,1,NULL),(11,11,2,'JT2BG22K3V0123458','ABC321',2026,1,NULL),(12,2,9,'4d65as4d6a465s4d','asd123',2025,1,NULL),(13,4,1,'adas4d56a4sd65','123asad',2025,1,NULL),(14,12,3,'asdasd456446','asdwqe77',2026,1,NULL),(15,11,18,'4846a4d64as6','asd6548',2026,1,'negro');
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

-- Dump completed on 2026-05-04 11:41:01
