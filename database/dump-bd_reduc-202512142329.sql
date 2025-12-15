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
  `sucursal_id` int(10) DEFAULT NULL,
  `ajustadoPor` int(10) DEFAULT NULL,
  PRIMARY KEY (`idajuste_inventario`),
  KEY `ajuste_inventario_FKIndex2` (`id_usuario`),
  CONSTRAINT `ajuste_inventario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
INSERT INTO `ajuste_inventario` VALUES (1,1,1,'2023-09-17 00:00:00','General','fdgdg',NULL,2,NULL),(4,1,1,'2025-12-13 00:00:00','Categoria','test2',NULL,2,NULL),(5,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(6,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(7,1,1,'2025-12-13 00:00:00','Proveedor','tes2',NULL,2,NULL),(8,1,1,'2025-12-13 00:00:00','Producto','test',NULL,2,NULL),(9,1,1,'2025-12-13 00:00:00','Producto','test24',NULL,2,NULL),(10,1,1,'2025-12-13 00:00:00','Producto','test4',NULL,2,NULL),(11,1,1,'2025-12-13 00:00:00','Producto','test6',NULL,2,NULL),(12,1,1,'2025-12-13 00:00:00','Producto','test8',NULL,2,NULL),(13,1,1,'2025-12-13 00:00:00','Categoria','test9',NULL,2,NULL),(14,1,1,'2025-12-13 00:00:00','Proveedor','test9',NULL,2,NULL),(15,1,1,'2025-12-13 00:00:00','General','test',NULL,2,NULL),(16,1,1,'2025-12-13 00:00:00','General','test10',NULL,2,NULL),(17,1,1,'2025-12-13 00:00:00','General','test11',NULL,2,NULL),(18,1,1,'2025-12-13 00:00:00','General','trest22',NULL,2,NULL),(19,1,1,'2025-12-13 00:00:00','General','test33',NULL,2,NULL),(20,1,1,'2025-12-13 00:00:00','General','trs23',NULL,2,NULL),(21,1,1,'2025-12-13 00:00:00','General','tes23',NULL,2,NULL),(22,1,1,'2025-12-13 00:00:00','General','test3',NULL,2,NULL),(23,1,1,'2025-12-13 00:00:00','General','test434',NULL,2,NULL),(24,1,0,'2025-12-14 00:00:00','General','rwa3453',NULL,2,NULL),(25,1,0,'2025-12-14 00:00:00','General','test12',NULL,2,NULL),(26,1,0,'2025-12-14 00:00:00','General','test3',NULL,2,NULL),(27,1,0,'2025-12-14 13:54:44','General','test1',NULL,2,NULL),(28,1,0,'2025-12-14 13:55:26','Categoria','teste122',NULL,2,NULL),(29,1,0,'2025-12-14 13:56:18','Proveedor','test344',NULL,2,NULL),(30,1,0,'2025-12-14 13:56:58','Producto','teste56',NULL,2,NULL),(31,1,1,'2025-12-14 15:36:07','General','test sin estado',NULL,2,NULL),(32,1,1,'2025-12-14 15:42:31','General','etes susucrsal',NULL,2,NULL),(33,1,1,'2025-12-14 16:24:44','General','test final',NULL,2,NULL),(34,1,1,'2025-12-14 16:26:44','General','test fina 2',NULL,2,NULL),(35,1,1,'2025-12-14 16:35:08','General','ajuste por error ',NULL,2,NULL),(36,1,1,'2025-12-14 16:38:00','General','test',NULL,2,NULL),(37,1,1,'2025-12-14 16:38:43','Producto','test',NULL,2,NULL),(38,1,1,'2025-12-14 16:56:22','General','final',NULL,2,NULL),(39,1,1,'2025-12-14 16:56:49','General','final final',NULL,2,NULL),(40,1,1,'2025-12-14 17:16:50','General','este',NULL,2,NULL),(41,1,2,'2025-12-14 17:21:39','General','test final','2025-12-14 17:22:03',2,1),(42,1,1,'2025-12-14 17:26:45','General','general final',NULL,2,NULL),(43,1,1,'2025-12-14 17:27:13','Categoria','categoria final',NULL,2,NULL),(44,1,1,'2025-12-14 17:27:53','Proveedor','proveedor final',NULL,2,NULL),(45,1,1,'2025-12-14 17:28:25','Producto','producto final',NULL,2,NULL),(46,1,2,'2025-12-14 22:59:50','Producto','test anular','2025-12-14 23:00:11',2,1);
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
INSERT INTO `ajuste_inventario_detalle` VALUES (10,9,6,6,2500,1),(11,9,6,6,2500,1),(12,9,6,6,2500,1),(12,10,69,69,1555,1),(12,11,45,45,1555,1),(13,9,6,6,2500,1),(13,11,45,45,1555,1),(14,10,69,69,1555,1),(14,11,45,45,1555,1),(15,9,6,6,2500,1),(15,10,69,69,1555,1),(15,11,45,45,1555,1),(16,9,6,6,2500,1),(16,10,69,69,1555,1),(16,11,45,45,1555,1),(17,9,6,6,2500,1),(17,10,69,69,1555,1),(17,11,45,45,1555,1),(18,9,6,6,2500,1),(18,10,69,69,1555,1),(18,11,45,45,1555,1),(19,9,6,6,2500,1),(19,10,69,69,1555,1),(19,11,45,45,1555,1),(20,9,6,6,2500,1),(20,10,69,69,1555,1),(20,11,45,45,1555,1),(21,9,6,6,2500,1),(21,10,69,69,1555,1),(21,11,45,45,1555,1),(22,9,6,6,2500,1),(22,10,69,69,1555,1),(22,11,45,45,1555,1),(23,9,6,6,2500,1),(23,10,69,69,1555,1),(23,11,45,45,1555,1),(24,9,6,6,2500,1),(24,10,69,69,1555,1),(24,11,45,45,1555,1),(25,9,6,90,2500,84),(25,10,69,45,1555,-24),(25,11,45,180,1555,135),(26,9,6,6,2500,1),(26,10,69,69,1555,1),(26,11,45,45,1555,1),(27,9,6,6,2500,1),(27,10,69,69,1555,1),(27,11,45,45,1555,1),(28,9,6,6,2500,1),(28,11,45,45,1555,1),(29,10,69,69,1555,1),(29,11,45,45,1555,1),(30,9,6,6,2500,1),(31,9,6,6,2500,NULL),(31,10,69,69,1555,NULL),(31,11,45,45,1555,NULL),(32,9,6,6,2500,NULL),(32,10,69,69,1555,NULL),(32,11,45,45,1555,NULL),(33,9,174,100,2500,-74),(33,10,21,100,1555,79),(33,11,315,100,1555,-215),(34,9,26,100,2500,74),(34,10,179,100,1555,-79),(34,11,-115,100,1555,215),(35,9,248,100,2500,-148),(35,10,-58,100,1555,158),(35,11,530,100,1555,-430),(36,9,100,90,2500,-10),(36,10,100,90,1555,-10),(36,11,100,90,1555,-10),(37,9,90,10,2500,-80),(38,9,5,5,2500,NULL),(38,10,90,90,1555,NULL),(38,11,90,90,1555,NULL),(39,9,5,10,2500,5),(39,10,90,10,1555,-80),(39,11,90,10,1555,-80),(40,9,-70,10,2500,80),(40,10,10,10,1555,0),(40,11,10,10,1555,0),(41,9,10,20,2500,10),(41,10,10,20,1555,10),(41,11,10,20,1555,10),(42,1,0,0,6000,NULL),(42,6,0,0,5000,NULL),(42,7,0,0,4500,NULL),(42,8,0,0,7500,NULL),(42,9,20,20,2500,NULL),(42,10,20,20,1555,NULL),(42,11,20,20,1555,NULL),(42,13,0,0,5000,NULL),(43,1,0,0,6000,NULL),(43,9,20,20,2500,NULL),(43,11,20,20,1555,NULL),(43,13,0,0,5000,NULL),(44,1,0,0,6000,NULL),(44,6,0,0,5000,NULL),(44,7,0,0,4500,NULL),(44,8,0,0,7500,NULL),(44,10,20,20,1555,NULL),(44,11,20,20,1555,NULL),(44,13,0,0,5000,NULL),(45,9,20,20,2500,NULL),(46,9,20,50,2500,30);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (1,1,1,1,1,1,'dedse',5000,6000,'123456687',1,'2025-07-23 21:17:00','2025-07-23 21:17:00'),(6,2,1,1,1,1,'fdsf',8000,5000,'12313213',1,'2025-07-23 21:38:44','2025-07-23 21:38:44'),(7,2,1,1,3,1,'dsadasd',6890,4500,'8888888',1,'2025-07-23 21:40:31','2025-07-23 21:40:31'),(8,2,1,1,2,1,'Gaseosa 2L',10000,7500,'7840058002105',1,'2025-07-24 14:00:19','2025-07-24 14:00:19'),(9,1,2,2,2,2,'prueba de cambio',3000,2500,'1234567',1,'2025-07-27 18:10:27','2025-07-24 14:03:25'),(10,2,1,1,2,1,'test de update',6000,1555,'123456',1,'2025-07-27 18:08:10','2025-07-24 14:16:35'),(11,1,1,1,1,1,'dsada',6554,1555,'13456',1,'2025-07-24 14:19:36','2025-07-24 14:19:36'),(13,1,1,1,1,1,'test',6000,5000,'16667',1,'2025-07-27 16:37:26','2025-07-27 16:37:26');
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
  `id_ciudad` int(10) unsigned NOT NULL,
  `doc_number` varchar(20) DEFAULT NULL,
  `nombre_cliente` varchar(30) DEFAULT NULL,
  `apellido_cliente` varchar(30) DEFAULT NULL,
  `direccion_cliente` varchar(50) DEFAULT NULL,
  `celular_cliente` varchar(15) DEFAULT NULL,
  `estado_civil` varchar(30) DEFAULT NULL,
  `estado_cliente` int(10) unsigned DEFAULT NULL,
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
INSERT INTO `clientes` VALUES (1,1,'4964127','Juan','Martinez','ruta 2','0986203431','Soltero/a',1,'1','CI','juanmartinez076@gmail.com'),(2,1,'80016096','Retail S.A.','','ruta 2','0986203431','',1,'7','RUC',NULL),(3,1,'1299450','Gricelda','Martinez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0985518660','Soltero/a',1,'1','CI','gmartinez076@gmail.com'),(4,1,'3216547','Jose','Perez','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0986203431','Soltero/a',1,'','CI','jperez076@gmail.com'),(5,1,'80005868','apolo','','asdasd','','Casado/a',1,'7','RUC',''),(6,1,'80002004','trebolin','','Ruta PY 02 km 31 - Avda Cerro Patiño M7L2','0986203431','',1,'5','RUC','trebolin076@gmail.com'),(8,1,'80019656','TEST','','dsfsdfsdfsdf','',NULL,1,'3','RUC',''),(10,1,'1456789','joselito','test','dsajdlasdjlsajdl','098456123',NULL,1,'','CI','dasdakljsdq@asdas.com'),(11,3,'1234567','asdasdsd','','asdasdasd','','Soltero/a',1,'8','RUC',''),(12,3,'1236547','pedro','perez','asdasd','0985123456','Soltero/a',1,'','CI','pedro@gmail.com'),(14,2,'321654712','cambio','cambio','cambio','0983123456','Soltero/a',1,'1','CC','cambio076@gmail.com'),(15,1,'1299451','Gricelda','Martinez','ruta py 02 km 28','0985518660','Soltero/a',1,'','CI','gmartinez@gmail.com');
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `colores`
--

LOCK TABLES `colores` WRITE;
/*!40000 ALTER TABLE `colores` DISABLE KEYS */;
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
  `fecha_creacion` datetime DEFAULT NULL,
  `nro_factura` varchar(30) DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `nro_timbrado` int(10) unsigned DEFAULT NULL,
  `vencimiento_timbrado` date DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `total_compra` double DEFAULT NULL,
  `condicion` varchar(20) DEFAULT NULL,
  `compra_intervalo` varchar(20) DEFAULT NULL,
  `idOcompra` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  PRIMARY KEY (`idcompra_cabecera`),
  KEY `compra_cabecera_FKIndex1` (`id_usuario`),
  KEY `compra_cabecera_FKIndex2` (`idproveedores`),
  CONSTRAINT `compra_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `compra_cabecera_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_cabecera`
--

LOCK TABLES `compra_cabecera` WRITE;
/*!40000 ALTER TABLE `compra_cabecera` DISABLE KEYS */;
INSERT INTO `compra_cabecera` VALUES (30,1,1,'2025-12-12 21:13:11','001-001-0000001','2025-12-12',12345678,'2025-12-31',0,1963000,'credito','15',12,'2025-12-12 21:25:53',1),(31,1,1,'2025-12-12 21:36:22','001-001-0000002','2025-12-12',12345678,'2025-12-31',1,1963000,'credito','15',12,NULL,NULL),(32,1,1,'2025-12-12 21:40:08','001-001-0000003','2025-12-12',12345678,'2025-12-31',1,1963000,'credito','10',12,NULL,NULL),(33,1,1,'2025-12-12 21:43:34','001-001-0000004','2025-12-12',12345678,'2025-12-31',1,1963000,'credito','5',12,NULL,NULL),(34,2,1,'2025-12-12 21:47:32','010-001-0000001','2025-12-12',12345687,'2025-12-31',1,27000,'credito','23',10,NULL,NULL);
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
  `precio_unitario` double NOT NULL,
  `cantidad_recibida` bigint(20) DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `ivaPro` double DEFAULT NULL,
  PRIMARY KEY (`idcompra_cabecera`,`id_articulo`),
  KEY `compra_cabecera_has_orden_compra_detalle_FKIndex1` (`idcompra_cabecera`),
  KEY `compra_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `compra_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_detalle`
--

LOCK TABLES `compra_detalle` WRITE;
/*!40000 ALTER TABLE `compra_detalle` DISABLE KEYS */;
INSERT INTO `compra_detalle` VALUES (30,10,56000,23,1288000,117090.90909091),(30,11,45000,15,675000,32142.857142857),(31,10,56000,23,1288000,117090.90909091),(31,11,45000,15,675000,32142.857142857),(32,10,56000,23,1288000,117090.90909091),(32,11,45000,15,675000,32142.857142857),(33,10,56000,23,1288000,117090.90909091),(33,11,45000,15,675000,32142.857142857),(34,9,4500,6,27000,2454.5454545455);
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
  `monto` int(10) unsigned DEFAULT NULL,
  `saldo` int(10) unsigned DEFAULT NULL,
  `nro_cuotas` int(10) unsigned DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_a_pagar`,`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex1` (`idcompra_cabecera`),
  CONSTRAINT `cuentas_a_pagar_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_a_pagar`
--

LOCK TABLES `cuentas_a_pagar` WRITE;
/*!40000 ALTER TABLE `cuentas_a_pagar` DISABLE KEYS */;
INSERT INTO `cuentas_a_pagar` VALUES (15,30,654333,654333,1,'2025-12-27',0),(16,30,654333,654333,2,'2026-01-11',0),(17,30,654333,654333,3,'2026-01-26',0),(18,31,654333,654333,1,'2025-12-27',1),(19,31,654333,654333,2,'2026-01-11',1),(20,31,654333,654333,3,'2026-01-26',1),(21,32,490750,490750,1,'2025-12-22',1),(22,32,490750,490750,2,'2026-01-01',1),(23,32,490750,490750,3,'2026-01-11',1),(24,32,490750,490750,4,'2026-01-21',1),(25,33,981500,981500,1,'2025-12-17',1),(26,33,981500,981500,2,'2025-12-22',1),(27,34,6750,6750,1,'2026-01-04',1),(28,34,6750,6750,2,'2026-01-27',1),(29,34,6750,6750,3,'2026-02-19',1),(30,34,6750,6750,4,'2026-03-14',1);
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
-- Table structure for table `diagnostico`
--

DROP TABLE IF EXISTS `diagnostico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostico` (
  `iddiagnostico` int(10) unsigned NOT NULL,
  `idrecepcion` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`iddiagnostico`),
  KEY `diagnostico_FKIndex2` (`idrecepcion`),
  CONSTRAINT `diagnostico_ibfk_1` FOREIGN KEY (`idrecepcion`) REFERENCES `recepcion` (`idrecepcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico`
--

LOCK TABLES `diagnostico` WRITE;
/*!40000 ALTER TABLE `diagnostico` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnostico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `diagnostico_detalle`
--

DROP TABLE IF EXISTS `diagnostico_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `diagnostico_detalle` (
  `iddiagnostico` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  `detalle_diagnostico` text NOT NULL,
  PRIMARY KEY (`iddiagnostico`),
  KEY `diagnostico_has_articulos_FKIndex1` (`iddiagnostico`),
  CONSTRAINT `diagnostico_detalle_ibfk_1` FOREIGN KEY (`iddiagnostico`) REFERENCES `diagnostico` (`iddiagnostico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_detalle`
--

LOCK TABLES `diagnostico_detalle` WRITE;
/*!40000 ALTER TABLE `diagnostico_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `diagnostico_detalle` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
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
-- Table structure for table `equipo_trabajo`
--

DROP TABLE IF EXISTS `equipo_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_trabajo` (
  `idtrabajos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idempleados` int(10) unsigned NOT NULL,
  `descripciontraba` varchar(100) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idtrabajos`),
  KEY `equipo_trabajo_FKIndex1` (`idempleados`),
  CONSTRAINT `equipo_trabajo_ibfk_1` FOREIGN KEY (`idempleados`) REFERENCES `empleados` (`idempleados`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_trabajo`
--

LOCK TABLES `equipo_trabajo` WRITE;
/*!40000 ALTER TABLE `equipo_trabajo` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipo_trabajo` ENABLE KEYS */;
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
  `idregistro_servicio` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idfactura`,`idregistro_servicio`),
  KEY `registro_servicio_has_factura_FKIndex2` (`idfactura`),
  KEY `factura_servicio_FKIndex2` (`idregistro_servicio`),
  CONSTRAINT `factura_servicio_ibfk_1` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`),
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
-- Table structure for table `factura_venta_remision`
--

DROP TABLE IF EXISTS `factura_venta_remision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_venta_remision` (
  `idfactura` int(10) unsigned NOT NULL,
  `idnota_remision` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idfactura`,`idnota_remision`),
  KEY `factura_has_nota_remision_FKIndex1` (`idfactura`),
  KEY `factura_has_nota_remision_FKIndex2` (`idnota_remision`),
  CONSTRAINT `factura_venta_remision_ibfk_1` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`),
  CONSTRAINT `factura_venta_remision_ibfk_2` FOREIGN KEY (`idnota_remision`) REFERENCES `nota_remision` (`idnota_remision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_venta_remision`
--

LOCK TABLES `factura_venta_remision` WRITE;
/*!40000 ALTER TABLE `factura_venta_remision` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura_venta_remision` ENABLE KEYS */;
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
  `idLibro_compra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `iva5` int(10) unsigned NOT NULL,
  `iva10` int(10) unsigned NOT NULL,
  `exenta` int(10) unsigned NOT NULL,
  `monto` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idLibro_compra`),
  KEY `Libro_compra_FKIndex1` (`idcompra_cabecera`),
  CONSTRAINT `libro_compra_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_compra`
--

LOCK TABLES `libro_compra` WRITE;
/*!40000 ALTER TABLE `libro_compra` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Castrol',1),(2,'Shell',1);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modelo_auto`
--

LOCK TABLES `modelo_auto` WRITE;
/*!40000 ALTER TABLE `modelo_auto` DISABLE KEYS */;
/*!40000 ALTER TABLE `modelo_auto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_credito_compra`
--

DROP TABLE IF EXISTS `nota_credito_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_credito_compra` (
  `idnota_creditocompra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_snc_compras` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `motivo` varchar(30) NOT NULL,
  `nro_notacre` varchar(20) DEFAULT NULL,
  `timbrado` int(10) unsigned DEFAULT NULL,
  `timbrado_venc` date DEFAULT NULL,
  `fecha_carga` date DEFAULT NULL,
  PRIMARY KEY (`idnota_creditocompra`),
  KEY `nota_credito_compra_FKIndex1` (`id_snc_compras`),
  KEY `nota_credito_compra_FKIndex2` (`id_usuario`),
  CONSTRAINT `nota_credito_compra_ibfk_1` FOREIGN KEY (`id_snc_compras`) REFERENCES `snc_compras` (`id_snc_compras`),
  CONSTRAINT `nota_credito_compra_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_credito_compra`
--

LOCK TABLES `nota_credito_compra` WRITE;
/*!40000 ALTER TABLE `nota_credito_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_credito_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_credito_compradetalle`
--

DROP TABLE IF EXISTS `nota_credito_compradetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_credito_compradetalle` (
  `idnota_creditocompra` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  `preciouni` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idnota_creditocompra`,`id_articulo`),
  KEY `nota_creditocompra_detalle_FKIndex1` (`idnota_creditocompra`),
  KEY `nota_creditocompra_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `nota_credito_compradetalle_ibfk_1` FOREIGN KEY (`idnota_creditocompra`) REFERENCES `nota_credito_compra` (`idnota_creditocompra`),
  CONSTRAINT `nota_credito_compradetalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_credito_compradetalle`
--

LOCK TABLES `nota_credito_compradetalle` WRITE;
/*!40000 ALTER TABLE `nota_credito_compradetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_credito_compradetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_credito_venta`
--

DROP TABLE IF EXISTS `nota_credito_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_credito_venta` (
  `idnota_credito_venta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idfactura` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `motivo` text NOT NULL,
  PRIMARY KEY (`idnota_credito_venta`),
  KEY `nota_credito_venta_FKIndex1` (`id_usuario`),
  KEY `nota_credito_venta_FKIndex2` (`idfactura`),
  CONSTRAINT `nota_credito_venta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `nota_credito_venta_ibfk_2` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_credito_venta`
--

LOCK TABLES `nota_credito_venta` WRITE;
/*!40000 ALTER TABLE `nota_credito_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_credito_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_credito_ventadetalle`
--

DROP TABLE IF EXISTS `nota_credito_ventadetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_credito_ventadetalle` (
  `idnota_credito_venta` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idnota_credito_venta`),
  KEY `nota_credito_ventadetalle_FKIndex1` (`idnota_credito_venta`),
  CONSTRAINT `nota_credito_ventadetalle_ibfk_1` FOREIGN KEY (`idnota_credito_venta`) REFERENCES `nota_credito_venta` (`idnota_credito_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_credito_ventadetalle`
--

LOCK TABLES `nota_credito_ventadetalle` WRITE;
/*!40000 ALTER TABLE `nota_credito_ventadetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_credito_ventadetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_debito_compra`
--

DROP TABLE IF EXISTS `nota_debito_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_debito_compra` (
  `idnota_debito_compra` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `motivo` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `nro_notadebi` int(10) unsigned DEFAULT NULL,
  `timbrado` int(10) unsigned DEFAULT NULL,
  `timbrado_venc` date DEFAULT NULL,
  PRIMARY KEY (`idnota_debito_compra`),
  KEY `nota_debito_compra_FKIndex1` (`idcompra_cabecera`),
  KEY `nota_debito_compra_FKIndex2` (`id_usuario`),
  CONSTRAINT `nota_debito_compra_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `nota_debito_compra_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_debito_compra`
--

LOCK TABLES `nota_debito_compra` WRITE;
/*!40000 ALTER TABLE `nota_debito_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_debito_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_debito_compradetalle`
--

DROP TABLE IF EXISTS `nota_debito_compradetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_debito_compradetalle` (
  `idnota_debito_compra` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned NOT NULL,
  KEY `nota_debito_compradetalle_FKIndex1` (`idnota_debito_compra`),
  KEY `nota_debito_compradetalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `nota_debito_compradetalle_ibfk_1` FOREIGN KEY (`idnota_debito_compra`) REFERENCES `nota_debito_compra` (`idnota_debito_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_debito_compradetalle`
--

LOCK TABLES `nota_debito_compradetalle` WRITE;
/*!40000 ALTER TABLE `nota_debito_compradetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_debito_compradetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_debito_venta`
--

DROP TABLE IF EXISTS `nota_debito_venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_debito_venta` (
  `idnota_debito_venta` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `idfactura` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `obs` text DEFAULT NULL,
  PRIMARY KEY (`idnota_debito_venta`),
  KEY `nota_debito_venta_FKIndex1` (`idfactura`),
  KEY `nota_debito_venta_FKIndex2` (`id_usuario`),
  CONSTRAINT `nota_debito_venta_ibfk_1` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`),
  CONSTRAINT `nota_debito_venta_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_debito_venta`
--

LOCK TABLES `nota_debito_venta` WRITE;
/*!40000 ALTER TABLE `nota_debito_venta` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_debito_venta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_debito_ventadetalle`
--

DROP TABLE IF EXISTS `nota_debito_ventadetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_debito_ventadetalle` (
  `idnota_debito_venta` int(10) unsigned NOT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idnota_debito_venta`),
  KEY `nota_debito_ventadetalle_FKIndex1` (`idnota_debito_venta`),
  CONSTRAINT `nota_debito_ventadetalle_ibfk_1` FOREIGN KEY (`idnota_debito_venta`) REFERENCES `nota_debito_venta` (`idnota_debito_venta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_debito_ventadetalle`
--

LOCK TABLES `nota_debito_ventadetalle` WRITE;
/*!40000 ALTER TABLE `nota_debito_ventadetalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_debito_ventadetalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision`
--

DROP TABLE IF EXISTS `nota_remision`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision` (
  `idnota_remision` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `nombre_transpo` varchar(120) NOT NULL,
  `ci_transpo` int(10) unsigned NOT NULL,
  `cel_transpo` varchar(60) NOT NULL,
  `fechaenvio` date NOT NULL,
  `fechallegada` date NOT NULL,
  `Vehimarca` varchar(60) NOT NULL,
  `Vehimodelo` varchar(60) NOT NULL,
  `Vehichapa` varchar(60) NOT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `transportista` varchar(30) DEFAULT NULL,
  `ruc_transport` varchar(20) DEFAULT NULL,
  `motivo_remision` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idnota_remision`),
  KEY `nota_remision_FKIndex1` (`id_usuario`),
  CONSTRAINT `nota_remision_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision`
--

LOCK TABLES `nota_remision` WRITE;
/*!40000 ALTER TABLE `nota_remision` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_remision` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nota_remision_compra`
--

DROP TABLE IF EXISTS `nota_remision_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_remision_compra` (
  `idnota_remision` int(10) unsigned NOT NULL,
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idnota_remision`,`idcompra_cabecera`),
  KEY `nota_remision_has_compra_cabecera_FKIndex1` (`idnota_remision`),
  KEY `nota_remision_has_compra_cabecera_FKIndex2` (`idcompra_cabecera`),
  CONSTRAINT `nota_remision_compra_ibfk_1` FOREIGN KEY (`idnota_remision`) REFERENCES `nota_remision` (`idnota_remision`),
  CONSTRAINT `nota_remision_compra_ibfk_2` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision_compra`
--

LOCK TABLES `nota_remision_compra` WRITE;
/*!40000 ALTER TABLE `nota_remision_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `nota_remision_compra` ENABLE KEYS */;
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
  `fecha` datetime DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `presupuestoid` int(10) DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`idorden_compra`),
  KEY `orden_compra_FKIndex1` (`id_usuario`),
  KEY `orden_compra_FKIndex2` (`idproveedores`),
  CONSTRAINT `orden_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `orden_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra`
--

LOCK TABLES `orden_compra` WRITE;
/*!40000 ALTER TABLE `orden_compra` DISABLE KEYS */;
INSERT INTO `orden_compra` VALUES (1,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(2,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(3,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(4,2,1,'2025-12-03 00:00:00',1,'2025-12-25',22,NULL,NULL),(5,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(6,2,1,'2025-12-04 00:00:00',1,'2025-12-25',18,NULL,NULL),(7,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(8,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(9,2,1,'2025-12-04 00:00:00',1,'2025-12-25',22,NULL,NULL),(10,2,1,'2025-12-04 00:00:00',2,'2025-12-25',18,1,'2025-12-12 21:47:32'),(11,1,1,'2025-12-04 00:00:00',1,'2025-12-25',19,NULL,NULL),(12,1,1,'2025-12-04 00:00:00',2,'2025-12-25',23,NULL,NULL),(13,1,1,'2025-12-04 00:00:00',1,'2025-12-25',10,NULL,NULL),(14,1,1,'2025-12-04 00:00:00',1,'2025-12-25',5,NULL,NULL),(15,1,1,'2025-12-04 00:00:00',1,'2025-12-25',23,NULL,NULL),(16,1,1,'2025-12-04 00:00:00',1,'2025-12-25',23,NULL,NULL),(17,1,1,'2025-12-04 20:40:59',1,'2025-12-25',23,NULL,NULL),(18,1,1,'2025-12-04 20:51:48',1,'2025-12-25',23,NULL,NULL),(19,1,1,'2025-12-04 20:52:00',1,'2025-12-25',21,NULL,NULL),(20,1,1,'2025-12-04 20:53:57',0,'2025-12-25',23,1,'2025-12-04 22:19:40'),(21,1,1,'2025-12-05 20:04:15',1,'2025-12-13',NULL,NULL,NULL),(22,1,1,'2025-12-05 20:08:23',1,'2025-12-12',NULL,NULL,NULL),(23,1,1,'2025-12-05 20:18:07',1,'2025-12-12',NULL,NULL,NULL),(24,1,1,'2025-12-05 20:20:07',1,'2025-12-19',NULL,NULL,NULL),(25,1,1,'2025-12-05 20:22:25',1,'2025-12-05',NULL,NULL,NULL);
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
  CONSTRAINT `orden_compra_detalle_ibfk_1` FOREIGN KEY (`idorden_compra`) REFERENCES `orden_compra` (`idorden_compra`),
  CONSTRAINT `orden_compra_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra_detalle`
--

LOCK TABLES `orden_compra_detalle` WRITE;
/*!40000 ALTER TABLE `orden_compra_detalle` DISABLE KEYS */;
INSERT INTO `orden_compra_detalle` VALUES (10,9,6,4500,0),(11,7,45,4500,45),(11,13,3,8000,3),(12,10,23,56000,0),(12,11,15,45000,0),(14,13,23,5600,23),(15,10,3,56000,3),(16,10,4,56000,4),(17,10,2,56000,2),(18,10,45,56000,45),(19,13,23,0,23),(20,10,56,56000,56),(23,10,34,45678,34),(24,10,44,4344,44),(24,13,3,2333,3),(25,10,2,2333,2);
/*!40000 ALTER TABLE `orden_compra_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo`
--

DROP TABLE IF EXISTS `orden_trabajo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo` (
  `idordentrabajo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idpresupuesto_servicio` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `tiposervicio` varchar(20) NOT NULL,
  `fechaingreso` date NOT NULL,
  `fecharetiro` date DEFAULT NULL,
  PRIMARY KEY (`idordentrabajo`),
  KEY `orden_trabajo_FKIndex2` (`id_usuario`),
  KEY `orden_trabajo_FKIndex3` (`idpresupuesto_servicio`),
  CONSTRAINT `orden_trabajo_ibfk_1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `orden_trabajo_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo`
--

LOCK TABLES `orden_trabajo` WRITE;
/*!40000 ALTER TABLE `orden_trabajo` DISABLE KEYS */;
/*!40000 ALTER TABLE `orden_trabajo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orden_trabajo_detalle`
--

DROP TABLE IF EXISTS `orden_trabajo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orden_trabajo_detalle` (
  `idordentrabajo` int(10) unsigned NOT NULL,
  `idtrabajos` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idordentrabajo`,`idtrabajos`,`id_articulo`),
  KEY `orden_trabajo_has_trabajos_FKIndex1` (`idordentrabajo`),
  KEY `orden_trabajo_has_trabajos_FKIndex2` (`idtrabajos`),
  KEY `orden_trabajo_detalle_FKIndex3` (`id_articulo`),
  CONSTRAINT `orden_trabajo_detalle_ibfk_1` FOREIGN KEY (`idordentrabajo`) REFERENCES `orden_trabajo` (`idordentrabajo`),
  CONSTRAINT `orden_trabajo_detalle_ibfk_2` FOREIGN KEY (`idtrabajos`) REFERENCES `equipo_trabajo` (`idtrabajos`),
  CONSTRAINT `orden_trabajo_detalle_ibfk_3` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo_detalle`
--

LOCK TABLES `orden_trabajo_detalle` WRITE;
/*!40000 ALTER TABLE `orden_trabajo_detalle` DISABLE KEYS */;
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
  `fecha` datetime DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `id_proveedor` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`),
  KEY `pedido_cabecera_FKIndex1` (`id_usuario`),
  CONSTRAINT `pedido_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cabecera`
--

LOCK TABLES `pedido_cabecera` WRITE;
/*!40000 ALTER TABLE `pedido_cabecera` DISABLE KEYS */;
INSERT INTO `pedido_cabecera` VALUES (2,1,'2025-11-18 20:37:45',0,1,NULL,NULL),(3,1,'2025-11-18 20:38:44',2,1,NULL,NULL),(4,1,'2025-11-18 20:39:34',0,1,NULL,NULL),(5,1,'2025-11-18 20:41:33',0,1,NULL,NULL),(6,1,'2025-11-18 20:43:18',0,1,NULL,NULL),(7,1,'2025-11-18 20:45:07',0,2,'2025-11-21 18:15:52','1'),(8,1,'2025-11-18 20:54:46',2,1,'2025-11-28 20:01:13','1'),(9,1,'2025-11-18 21:20:45',1,1,NULL,NULL),(10,1,'2025-11-19 20:37:43',1,2,NULL,NULL),(11,1,'2025-11-19 20:57:08',1,1,NULL,NULL),(12,1,'2025-11-19 20:57:27',0,2,NULL,NULL),(13,1,'2025-11-21 18:12:17',1,1,NULL,NULL),(14,1,'2025-11-21 21:19:39',2,2,'2025-12-04 21:30:40','1'),(15,1,'2025-11-22 20:31:00',1,2,NULL,NULL),(16,4,'2025-11-22 20:31:01',2,1,'2025-12-04 21:29:03','1');
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
  CONSTRAINT `pedido_detalle_ibfk_1` FOREIGN KEY (`idpedido_cabecera`) REFERENCES `pedido_cabecera` (`idpedido_cabecera`),
  CONSTRAINT `pedido_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_detalle`
--

LOCK TABLES `pedido_detalle` WRITE;
/*!40000 ALTER TABLE `pedido_detalle` DISABLE KEYS */;
INSERT INTO `pedido_detalle` VALUES (2,6,1),(2,7,1),(2,10,1),(3,6,1),(3,7,1),(3,10,1),(4,6,1),(4,7,1),(4,10,1),(5,6,1),(5,7,1),(5,10,1),(6,7,1),(6,10,23),(7,9,1),(8,10,1),(9,10,1),(10,9,1),(11,10,1),(12,9,1),(13,7,1),(13,8,1),(13,13,7),(14,9,3),(15,9,1),(16,8,1);
/*!40000 ALTER TABLE `pedido_detalle` ENABLE KEYS */;
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
  CONSTRAINT `presupuesto_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `presupuesto_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_compra`
--

LOCK TABLES `presupuesto_compra` WRITE;
/*!40000 ALTER TABLE `presupuesto_compra` DISABLE KEYS */;
INSERT INTO `presupuesto_compra` VALUES (1,1,1,'2025-11-25 00:00:00',0,'2025-11-25','1','2025-12-04 21:31:28',414016.00,NULL),(2,1,1,'2025-11-25 00:00:00',1,'2027-12-25',NULL,NULL,2222.00,NULL),(3,1,1,'2025-11-27 00:00:00',1,'2025-12-06',NULL,NULL,51000.00,NULL),(4,1,1,'2025-11-27 00:00:00',1,'2025-12-31',NULL,NULL,51000.00,NULL),(5,1,1,'2025-11-27 00:00:00',1,'2025-12-06',NULL,NULL,11200.00,NULL),(6,1,2,'2025-11-27 20:08:12',1,'2026-02-04',NULL,NULL,20334.00,NULL),(7,1,2,'2025-11-27 20:16:29',1,'2025-11-29',NULL,NULL,3333.00,NULL),(8,1,1,'2025-11-27 20:21:45',1,'2025-12-04',NULL,NULL,3333.00,NULL),(9,1,1,'2025-11-27 21:50:57',0,'2025-11-29','1','2025-11-28 20:40:26',545000.00,NULL),(10,1,1,'2025-11-27 21:53:30',1,'2025-11-28',NULL,NULL,0.00,NULL),(11,1,1,'2025-11-27 21:55:00',1,'2025-11-28',NULL,NULL,0.00,NULL),(12,2,1,'2025-11-27 21:56:15',1,'2025-11-28',NULL,NULL,0.00,NULL),(13,2,1,'2025-11-27 21:58:06',1,'2025-11-28',NULL,NULL,0.00,NULL),(14,2,1,'2025-11-27 22:00:20',1,'2025-11-28',NULL,NULL,0.00,NULL),(15,2,1,'2025-11-27 22:03:03',1,'2025-12-06',NULL,NULL,0.00,NULL),(16,2,1,'2025-11-27 22:04:18',1,'2025-11-29',NULL,NULL,0.00,14),(17,2,1,'2025-11-27 22:07:25',1,'2025-11-29',NULL,NULL,13500.00,14),(18,2,1,'2025-11-27 22:08:29',1,'2025-11-25',NULL,NULL,13500.00,14),(19,1,1,'2025-11-27 22:08:59',1,'2025-11-25',NULL,NULL,116500.00,13),(20,1,1,'2025-11-27 22:22:54',1,'2025-12-06',NULL,NULL,47390.00,13),(21,1,1,'2025-11-27 22:22:59',1,'2025-12-06',NULL,NULL,47390.00,13),(22,2,1,'2025-11-27 22:23:41',1,'2025-11-28',NULL,NULL,15000.00,14),(23,1,1,'2025-11-28 20:01:13',1,'2025-12-01',NULL,NULL,56000.00,8),(24,1,1,'2025-12-04 21:29:03',1,'2025-12-04',NULL,NULL,23233.00,16),(25,2,1,'2025-12-04 21:30:40',1,'2025-12-05',NULL,NULL,130635.00,14);
/*!40000 ALTER TABLE `presupuesto_compra` ENABLE KEYS */;
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
  CONSTRAINT `presupuesto_detalle_ibfk_1` FOREIGN KEY (`idpresupuesto_compra`) REFERENCES `presupuesto_compra` (`idpresupuesto_compra`),
  CONSTRAINT `presupuesto_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalle`
--

LOCK TABLES `presupuesto_detalle` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalle` DISABLE KEYS */;
INSERT INTO `presupuesto_detalle` VALUES (1,6,6.00,67688.00,406128.00),(1,13,1.00,7888.00,7888.00),(2,10,1.00,2222.00,2222.00),(3,10,3.00,7000.00,21000.00),(3,13,6.00,5000.00,30000.00),(4,10,3.00,7000.00,21000.00),(4,13,6.00,5000.00,30000.00),(5,13,2.00,5600.00,11200.00),(6,9,3.00,6778.00,20334.00),(7,9,1.00,3333.00,3333.00),(8,10,1.00,3333.00,3333.00),(12,9,1.00,5000.00,5000.00),(13,9,3.00,5000.00,15000.00),(14,9,3.00,7500.00,22500.00),(15,9,3.00,8400.00,25200.00),(16,9,3.00,15000.00,45000.00),(17,9,3.00,4500.00,13500.00),(18,9,3.00,4500.00,13500.00),(19,7,1.00,4500.00,4500.00),(19,8,1.00,56000.00,56000.00),(19,13,7.00,8000.00,56000.00),(20,13,7.00,0.00,0.00),(21,13,7.00,0.00,0.00),(22,9,3.00,5000.00,15000.00),(23,10,1.00,56000.00,56000.00),(24,8,1.00,23233.00,23233.00),(25,9,3.00,43545.00,130635.00);
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
  PRIMARY KEY (`id_articulo`,`idpresupuesto_servicio`),
  KEY `presupuesto_has_articulos_FKIndex2` (`id_articulo`),
  KEY `presupuesto_has_articulos_FKIndex3` (`idpresupuesto_servicio`),
  CONSTRAINT `presupuesto_detalleservicio_ibfk_1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `presupuesto_detalleservicio_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalleservicio`
--

LOCK TABLES `presupuesto_detalleservicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` ENABLE KEYS */;
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
  `iddiagnostico` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `fecha_venc` date DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_servicio`),
  KEY `presupuesto_FKIndex2` (`iddiagnostico`),
  KEY `presupuesto_FKIndex3` (`id_usuario`),
  CONSTRAINT `presupuesto_servicio_ibfk_1` FOREIGN KEY (`iddiagnostico`) REFERENCES `diagnostico` (`iddiagnostico`),
  CONSTRAINT `presupuesto_servicio_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_servicio`
--

LOCK TABLES `presupuesto_servicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_servicio` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuesto_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones_descuentos`
--

DROP TABLE IF EXISTS `promociones_descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones_descuentos` (
  `idpromociones_descuentos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`idpromociones_descuentos`),
  KEY `promociones_descuentos_FKIndex1` (`id_usuario`),
  CONSTRAINT `promociones_descuentos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones_descuentos`
--

LOCK TABLES `promociones_descuentos` WRITE;
/*!40000 ALTER TABLE `promociones_descuentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `promociones_descuentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promociones_descuentos_detalle`
--

DROP TABLE IF EXISTS `promociones_descuentos_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `promociones_descuentos_detalle` (
  `id_articulo` int(10) unsigned NOT NULL,
  `idpromociones_descuentos` int(10) unsigned NOT NULL,
  `descuento` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`idpromociones_descuentos`),
  KEY `promociones_descuentos_detalle_FKIndex1` (`id_articulo`),
  KEY `promociones_descuentos_detalle_FKIndex2` (`idpromociones_descuentos`),
  CONSTRAINT `promociones_descuentos_detalle_ibfk_1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `promociones_descuentos_detalle_ibfk_2` FOREIGN KEY (`idpromociones_descuentos`) REFERENCES `promociones_descuentos` (`idpromociones_descuentos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones_descuentos_detalle`
--

LOCK TABLES `promociones_descuentos_detalle` WRITE;
/*!40000 ALTER TABLE `promociones_descuentos_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `promociones_descuentos_detalle` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,2,'CHACOMER S.A.E.C.A.','80015635-7','12345678','ruta PY 02 km 20','chacomer@chacomer.com',1),(2,1,'test','80006895-7','32423432','ruta PY 02 km 20','test@test.com',1);
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
-- Table structure for table `recepcion`
--

DROP TABLE IF EXISTS `recepcion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recepcion` (
  `idrecepcion` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_vehiculo` int(10) unsigned NOT NULL,
  `id_cliente` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `observacion` text NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idrecepcion`),
  KEY `recepcion_FKIndex2` (`id_cliente`),
  KEY `recepcion_FKIndex3` (`id_vehiculo`),
  KEY `recepcion_FKIndex4` (`id_usuario`),
  CONSTRAINT `recepcion_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `recepcion_ibfk_2` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  CONSTRAINT `recepcion_ibfk_3` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion`
--

LOCK TABLES `recepcion` WRITE;
/*!40000 ALTER TABLE `recepcion` DISABLE KEYS */;
/*!40000 ALTER TABLE `recepcion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamos`
--

DROP TABLE IF EXISTS `reclamos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamos` (
  `idreclamos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_usuario` int(10) unsigned NOT NULL,
  `observacion` text DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`idreclamos`),
  KEY `reclamos_FKIndex1` (`id_usuario`),
  CONSTRAINT `reclamos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamos`
--

LOCK TABLES `reclamos` WRITE;
/*!40000 ALTER TABLE `reclamos` DISABLE KEYS */;
/*!40000 ALTER TABLE `reclamos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamos_servicio`
--

DROP TABLE IF EXISTS `reclamos_servicio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamos_servicio` (
  `idreclamos` int(10) unsigned NOT NULL,
  `idregistro_servicio` int(10) unsigned NOT NULL,
  `cantidad` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idreclamos`,`idregistro_servicio`),
  KEY `reclamos_has_registro_servicio_FKIndex1` (`idreclamos`),
  KEY `reclamos_servicio_FKIndex2` (`idregistro_servicio`),
  CONSTRAINT `reclamos_servicio_ibfk_1` FOREIGN KEY (`idreclamos`) REFERENCES `reclamos` (`idreclamos`),
  CONSTRAINT `reclamos_servicio_ibfk_2` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`)
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
  `id_usuario` int(10) unsigned NOT NULL,
  `idordentrabajo` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  PRIMARY KEY (`idregistro_servicio`),
  KEY `registro_servicio_FKIndex2` (`idordentrabajo`),
  KEY `registro_servicio_FKIndex3` (`id_usuario`),
  CONSTRAINT `registro_servicio_ibfk_1` FOREIGN KEY (`idordentrabajo`) REFERENCES `orden_trabajo` (`idordentrabajo`),
  CONSTRAINT `registro_servicio_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio`
--

LOCK TABLES `registro_servicio` WRITE;
/*!40000 ALTER TABLE `registro_servicio` DISABLE KEYS */;
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
  `cantidad` int(10) unsigned DEFAULT NULL,
  `precio_uni` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`idregistro_servicio`,`id_articulo`),
  KEY `registro_servicio_detalle_FKIndex1` (`idregistro_servicio`),
  KEY `registro_servicio_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `registro_servicio_detalle_ibfk_1` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`),
  CONSTRAINT `registro_servicio_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio_detalle`
--

LOCK TABLES `registro_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `registro_servicio_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `registro_servicio_detalle` ENABLE KEYS */;
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
  `stockUltActualizacion` datetime NOT NULL,
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
INSERT INTO `stock` VALUES (2,9,200,15,50.0000,'2025-12-14 23:00:11',1,46),(2,10,200,15,20.0000,'2025-12-14 17:22:03',1,41),(2,11,200,15,20.0000,'2025-12-14 17:22:03',1,41);
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
  `LocalId` varchar(16) NOT NULL,
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
  KEY `idx_local` (`LocalId`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucmovimientostock`
--

LOCK TABLES `sucmovimientostock` WRITE;
/*!40000 ALTER TABLE `sucmovimientostock` DISABLE KEYS */;
INSERT INTO `sucmovimientostock` VALUES (18,'2','RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:13:11','001-001-0000001',NULL,1,1,'30'),(19,'2','RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:13:11','001-001-0000001',NULL,1,1,'30'),(22,'2','ANULACION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:25:53','ANUL_COMPRA# 30',NULL,1,-1,'ANUL_COMPRA# 30'),(23,'2','ANULACION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:25:53','ANUL_COMPRA# 30',NULL,1,-1,'ANUL_COMPRA# 30'),(24,'2','RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:36:22','001-001-0000002',NULL,1,1,'31'),(25,'2','RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:36:22','001-001-0000002',NULL,1,1,'31'),(26,'2','RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:40:08','001-001-0000003',NULL,1,1,'32'),(27,'2','RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:40:08','001-001-0000003',NULL,1,1,'32'),(28,'2','RECEPCION COMPRA','10',23.0000,0.00,56000.00,'2025-12-12 21:43:34','001-001-0000004',NULL,1,1,'33'),(29,'2','RECEPCION COMPRA','11',15.0000,0.00,45000.00,'2025-12-12 21:43:34','001-001-0000004',NULL,1,1,'33'),(30,'2','RECEPCION COMPRA','9',6.0000,0.00,4500.00,'2025-12-12 21:47:32','010-001-0000001',NULL,1,1,'34'),(31,'2','AJUSTE_INV','9',84.0000,0.00,2500.00,'2025-12-14 16:18:28',NULL,NULL,1,1,'AJUSTE #25'),(32,'2','AJUSTE_INV','10',24.0000,0.00,1555.00,'2025-12-14 16:18:28',NULL,NULL,1,-1,'AJUSTE #25'),(33,'2','AJUSTE_INV','11',135.0000,0.00,1555.00,'2025-12-14 16:18:28',NULL,NULL,1,1,'AJUSTE #25'),(34,'2','AJUSTE_INV','9',84.0000,0.00,2500.00,'2025-12-14 16:20:33',NULL,NULL,1,1,'AJUSTE #25'),(35,'2','AJUSTE_INV','10',24.0000,0.00,1555.00,'2025-12-14 16:20:33',NULL,NULL,1,-1,'AJUSTE #25'),(36,'2','AJUSTE_INV','11',135.0000,0.00,1555.00,'2025-12-14 16:20:33',NULL,NULL,1,1,'AJUSTE #25'),(37,'2','AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:25:16',NULL,NULL,1,-1,'AJUSTE #33'),(38,'2','AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:25:16',NULL,NULL,1,1,'AJUSTE #33'),(39,'2','AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:25:16',NULL,NULL,1,-1,'AJUSTE #33'),(40,'2','AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:26:00',NULL,NULL,1,-1,'AJUSTE #33'),(41,'2','AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:26:00',NULL,NULL,1,1,'AJUSTE #33'),(42,'2','AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:26:00',NULL,NULL,1,-1,'AJUSTE #33'),(43,'2','AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:27:22',NULL,NULL,1,1,'AJUSTE #34'),(44,'2','AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:27:22',NULL,NULL,1,-1,'AJUSTE #34'),(45,'2','AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:27:22',NULL,NULL,1,1,'AJUSTE #34'),(46,'2','AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:31:24',NULL,NULL,1,1,'AJUSTE #34'),(47,'2','AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:31:24',NULL,NULL,1,-1,'AJUSTE #34'),(48,'2','AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:31:24',NULL,NULL,1,1,'AJUSTE #34'),(49,'2','AJUSTE_INV','9',74.0000,0.00,2500.00,'2025-12-14 16:31:35',NULL,NULL,1,1,'AJUSTE #34'),(50,'2','AJUSTE_INV','10',79.0000,0.00,1555.00,'2025-12-14 16:31:35',NULL,NULL,1,-1,'AJUSTE #34'),(51,'2','AJUSTE_INV','11',215.0000,0.00,1555.00,'2025-12-14 16:31:35',NULL,NULL,1,1,'AJUSTE #34'),(52,'2','AJUSTE_INV','9',148.0000,0.00,2500.00,'2025-12-14 16:36:12',NULL,NULL,1,-1,'AJUSTE #35'),(53,'2','AJUSTE_INV','10',158.0000,0.00,1555.00,'2025-12-14 16:36:12',NULL,NULL,1,1,'AJUSTE #35'),(54,'2','AJUSTE_INV','11',430.0000,0.00,1555.00,'2025-12-14 16:36:12',NULL,NULL,1,-1,'AJUSTE #35'),(55,'2','AJUSTE_INV','9',10.0000,0.00,2500.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(56,'2','AJUSTE_INV','10',10.0000,0.00,1555.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(57,'2','AJUSTE_INV','11',10.0000,0.00,1555.00,'2025-12-14 16:38:21',NULL,NULL,1,-1,'AJUSTE #36'),(58,'2','AJUSTE_INV','9',85.0000,0.00,2500.00,'2025-12-14 16:38:57',NULL,NULL,1,-1,'AJUSTE #37'),(59,'2','AJUSTE_INV','9',5.0000,0.00,2500.00,'2025-12-14 16:57:11',NULL,NULL,1,1,'AJUSTE #39'),(60,'2','AJUSTE_INV','10',80.0000,0.00,1555.00,'2025-12-14 16:57:11',NULL,NULL,1,-1,'AJUSTE #39'),(61,'2','AJUSTE_INV','11',80.0000,0.00,1555.00,'2025-12-14 16:57:11',NULL,NULL,1,-1,'AJUSTE #39'),(62,'2','AJUSTE_INV','9',80.0000,0.00,2500.00,'2025-12-14 17:16:33',NULL,NULL,1,-1,'AJUSTE #37'),(63,'2','AJUSTE_INV','9',80.0000,0.00,2500.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(64,'2','AJUSTE_INV','10',0.0000,0.00,1555.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(65,'2','AJUSTE_INV','11',0.0000,0.00,1555.00,'2025-12-14 17:17:12',NULL,NULL,1,1,'AJUSTE #40'),(66,'2','AJUSTE_INV','9',10.0000,0.00,2500.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(67,'2','AJUSTE_INV','10',10.0000,0.00,1555.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(68,'2','AJUSTE_INV','11',10.0000,0.00,1555.00,'2025-12-14 17:22:03',NULL,NULL,1,1,'AJUSTE #41'),(69,'2','AJUSTE_INV','9',30.0000,0.00,2500.00,'2025-12-14 23:00:11',NULL,NULL,1,1,'AJUSTE #46');
/*!40000 ALTER TABLE `sucmovimientostock` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (2,2,'lubriReducto 1','san lorenzo','021567834',1,1);
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
-- Table structure for table `transferencias`
--

DROP TABLE IF EXISTS `transferencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencias` (
  `idtransferencias` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `idnota_remision` int(10) unsigned NOT NULL,
  `id_usuario` int(10) unsigned NOT NULL,
  `id_sucursal` int(10) unsigned NOT NULL,
  `estado` int(10) unsigned NOT NULL,
  `destino` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`idtransferencias`),
  KEY `transferencias_FKIndex1` (`id_sucursal`),
  KEY `transferencias_FKIndex2` (`id_usuario`),
  KEY `transferencias_FKIndex3` (`idnota_remision`),
  CONSTRAINT `transferencias_ibfk_1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `transferencias_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `transferencias_ibfk_3` FOREIGN KEY (`idnota_remision`) REFERENCES `nota_remision` (`idnota_remision`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencias`
--

LOCK TABLES `transferencias` WRITE;
/*!40000 ALTER TABLE `transferencias` DISABLE KEYS */;
/*!40000 ALTER TABLE `transferencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transferencias_detalles`
--

DROP TABLE IF EXISTS `transferencias_detalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transferencias_detalles` (
  `id_articulo` int(10) unsigned NOT NULL,
  `idtransferencias` int(10) unsigned NOT NULL,
  `cantidad_transf` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_articulo`,`idtransferencias`),
  KEY `articulos_has_transferencias_FKIndex1` (`id_articulo`),
  KEY `articulos_has_transferencias_FKIndex2` (`idtransferencias`),
  CONSTRAINT `transferencias_detalles_ibfk_1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `transferencias_detalles_ibfk_2` FOREIGN KEY (`idtransferencias`) REFERENCES `transferencias` (`idtransferencias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencias_detalles`
--

LOCK TABLES `transferencias_detalles` WRITE;
/*!40000 ALTER TABLE `transferencias_detalles` DISABLE KEYS */;
/*!40000 ALTER TABLE `transferencias_detalles` ENABLE KEYS */;
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
  PRIMARY KEY (`id_usuario`),
  KEY `fk_usuarios_sucursales` (`sucursalid`),
  CONSTRAINT `fk_usuarios_sucursales` FOREIGN KEY (`sucursalid`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,1,'admin','Del Sistema','admins@admin.com.py','0856132156','1234567',2),(2,'jfigueredo','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',1,1,'admins','Del Sistema','admin1@admin.com.py','0981111111','11111111',2),(4,'Diego','L08weWh0UmVyUTJEYnErVUVBSFIrZz09',3,1,'dmendieta','Mendieta','dmendieta@admin.com','0985123456','987654321',2),(5,'sins','TTd2RFZQUXgxak4rN1RlWHh4bndxUT09',1,1,'noadmins','nivels','noadmins@admin.com','0981222223','123456789',2),(6,'test','TTd2RFZQUXgxak4rN1RlWHh4bndxUT09',2,1,'test','test','test@gmail.com','','6543214',2);
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
  `nro_serie` int(10) unsigned DEFAULT NULL,
  `placa` varchar(20) DEFAULT NULL,
  `anho` varchar(200) DEFAULT NULL,
  `estado` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id_vehiculo`),
  KEY `vehiculos_FKIndex1` (`id_modeloauto`),
  KEY `vehiculos_FKIndex2` (`id_color`),
  KEY `vehiculos_FKIndex3` (`id_color`),
  CONSTRAINT `vehiculos_ibfk_1` FOREIGN KEY (`id_modeloauto`) REFERENCES `modelo_auto` (`id_modeloauto`),
  CONSTRAINT `vehiculos_ibfk_2` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id_color`),
  CONSTRAINT `vehiculos_ibfk_3` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
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

-- Dump completed on 2025-12-14 23:29:54
