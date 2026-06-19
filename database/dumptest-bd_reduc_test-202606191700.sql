-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: bd_reduc_test
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
  `tipo_inv` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ajuste` date DEFAULT NULL,
  `ajustadoPor` int DEFAULT NULL,
  PRIMARY KEY (`idajuste_inventario`),
  KEY `ajuste_inventario_FKIndex2` (`id_usuario`),
  KEY `ajuste_inventario_FKIndex3` (`sucursal_id`),
  CONSTRAINT `ajuste_inventarioSucu` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `ajuste_inventarioUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
INSERT INTO `ajuste_inventario` VALUES (19,1,1,3,'2026-05-31','Producto','inv pastillas','2026-05-31',1),(20,1,1,3,'2026-06-02','Producto','alta stock aceite','2026-06-02',1),(21,1,1,3,'2026-06-03','Producto','alta wd40','2026-06-03',1),(22,1,1,3,'2026-06-07','Producto','aceites','2026-06-07',1),(23,1,1,0,'2026-06-08','Producto','test prod',NULL,NULL),(24,1,1,1,'2026-06-10','General','test',NULL,NULL),(25,1,1,3,'2026-06-14','Producto','alta stock','2026-06-14',1);
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
INSERT INTO `ajuste_inventario_detalle` VALUES (2,24,15,15,50000,NULL),(3,24,0,0,0,NULL),(4,19,0,10,0,10),(4,24,7,7,0,NULL),(5,24,0,0,0,NULL),(6,24,10,10,220000,NULL),(6,25,10,10,220000,0),(7,24,0,0,0,NULL),(8,24,0,0,0,NULL),(9,24,0,0,0,NULL),(10,24,0,0,0,NULL),(11,24,0,0,0,NULL),(12,24,0,0,0,NULL),(21,21,0,10,0,10),(28,24,0,0,0,NULL),(29,24,0,0,0,NULL),(30,24,0,0,4500,NULL),(31,24,0,0,0,NULL),(32,23,0,0,0,NULL),(32,24,0,0,0,NULL),(33,20,0,10,0,10),(33,22,10,8,0,-2),(33,24,8,8,0,NULL),(34,24,0,0,0,NULL),(41,25,0,10,28500,10),(46,25,0,10,704000,10);
/*!40000 ALTER TABLE `ajuste_inventario_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `anulacion_auditoria`
--

DROP TABLE IF EXISTS `anulacion_auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anulacion_auditoria` (
  `idanulacion` int unsigned NOT NULL AUTO_INCREMENT,
  `modulo` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tabla_afectada` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_registro` int unsigned NOT NULL,
  `id_sucursal` int unsigned DEFAULT NULL,
  `estado_anterior` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_nuevo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usuario_anula` int unsigned NOT NULL,
  `fecha_anulacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `referencia` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idanulacion`),
  KEY `idx_anulacion_modulo_registro` (`modulo`,`id_registro`),
  KEY `idx_anulacion_sucursal` (`id_sucursal`),
  KEY `idx_anulacion_usuario` (`usuario_anula`),
  CONSTRAINT `fk_anulacion_auditoria_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_anulacion_auditoria_usuario` FOREIGN KEY (`usuario_anula`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anulacion_auditoria`
--

LOCK TABLES `anulacion_auditoria` WRITE;
/*!40000 ALTER TABLE `anulacion_auditoria` DISABLE KEYS */;
INSERT INTO `anulacion_auditoria` VALUES (1,'pedido_compra','pedido_cabecera',9001,1,'1','0','test',1,'2026-06-07 15:03:06','PEDIDO #9001'),(2,'pedido_compra','pedido_cabecera',9005,2,'1','0','Prueba de anulacion',7,'2026-06-07 15:43:16','PEDIDO #9005'),(3,'registro_servicio','registro_servicio',9004,1,'1','0','test',1,'2026-06-08 21:20:36','REGISTRO_SERVICIO #9004'),(4,'orden_trabajo','orden_trabajo',9004,NULL,'1','0','test anulacoin',1,'2026-06-08 21:21:05','OT #9004'),(5,'presupuesto_servicio','presupuesto_servicio',21,1,'2','0','test presupuseto',1,'2026-06-08 21:21:27','PRESUPUESTO_SERVICIO #21'),(6,'diagnostico_servicio','diagnostico_servicio',28,1,'1','0','anulacion diagnostico',1,'2026-06-08 21:31:26','DIAGNOSTICO #28'),(7,'reclamo_servicio','reclamo_servicio',13,1,'1','0','anulacion reclamo',1,'2026-06-08 21:32:09','RECLAMO_SERVICIO #13'),(8,'ajuste_inventario','ajuste_inventario',23,1,'1','0','anulacion inventario',1,'2026-06-08 21:33:29','AJUSTE_INVENTARIO #23'),(9,'compra','compra_cabecera',9006,1,'3','0','test',1,'2026-06-09 22:18:08','COMPRA #9006'),(10,'diagnostico_servicio','diagnostico_servicio',30,1,'1','0','test',1,'2026-06-14 19:58:14','DIAGNOSTICO #30'),(11,'diagnostico_servicio','diagnostico_servicio',31,1,'1','0','datos incorrectos en la BD',1,'2026-06-14 20:07:49','DIAGNOSTICO #31'),(12,'nota_compra','nota_compra',3,1,'1','0','mal cargado',1,'2026-06-18 23:11:36','NOTA_COMPRA #3'),(13,'nota_compra','nota_compra',5,1,'1','0','test',1,'2026-06-18 23:17:39','NOTA_COMPRA #5');
/*!40000 ALTER TABLE `anulacion_auditoria` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulo_proveedor`
--

LOCK TABLES `articulo_proveedor` WRITE;
/*!40000 ALTER TABLE `articulo_proveedor` DISABLE KEYS */;
INSERT INTO `articulo_proveedor` VALUES (43,6,9001,220000.00,1),(44,21,9001,45000.00,1),(45,2,8,50000.00,1),(46,30,7,4500.00,1),(47,38,7,15700.00,1),(48,39,7,27000.00,1),(49,40,8,33000.00,1),(50,41,8,28500.00,1),(51,42,8,49000.00,1),(52,43,9002,135000.00,1),(53,44,9003,25500.00,1),(54,45,9003,25500.00,1),(55,46,9003,704000.00,1),(56,47,4,13500.00,1),(57,48,9,33750.00,1),(58,49,9,9000.00,1),(59,50,9,41000.00,1),(60,21,7,35000.00,1),(61,28,7,38000.00,1);
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
  `desc_articulo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_venta` double DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned NOT NULL,
  `date_updated` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_articulo`),
  KEY `articulos_FKIndex1` (`id_marcas`),
  KEY `articulos_FKIndex2` (`idiva`),
  KEY `articulos_FKIndex3` (`idunidad_medida`),
  KEY `articulos_FKIndex5` (`id_categoria`),
  CONSTRAINT `articulosCat` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `articulosIVA` FOREIGN KEY (`idiva`) REFERENCES `tipo_impuesto` (`idiva`),
  CONSTRAINT `articulosMarca` FOREIGN KEY (`id_marcas`) REFERENCES `marcas` (`id_marcas`),
  CONSTRAINT `articulosUmedida` FOREIGN KEY (`idunidad_medida`) REFERENCES `unidad_medida` (`idunidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (2,2,1,2,2,'Filtro de Aceite Bosch',45000,'FILBOS01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(3,2,1,2,2,'Filtro de Aire Toyota',60000,'FILTOY02',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(4,3,4,2,8,'Pastillas de Freno Delanteras',50000,'FREN001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(5,3,1,2,8,'Disco de Freno Ventilado',250000,'DISC001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(6,4,3,2,7,'Amortiguadores Delanteros (Par)',350000,'AMORT01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(7,5,5,2,3,'Kit de Distribución',450000,'KITDIST01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(8,5,1,2,3,'Bujía NGK',25000,'BUJNGK01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(9,6,1,2,8,'Batería 12V 60Ah',550000,'BAT001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(10,7,5,2,7,'Kit de Embrague',650000,'EMB001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(11,8,1,2,4,'Cubierta 185/65R14 Michelin',400000,'NEU001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(12,9,2,2,10,'Refrigerante 1L',90000,'REF001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(13,10,6,3,NULL,'Cambio de Filtro',50000,'SERV001',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(14,10,6,3,NULL,'Alineación y Balanceo',100000,'SERV002',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(15,10,6,3,NULL,'Diagnóstico Computarizado',70000,'SERV003',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(17,7,1,1,15,'Mantenimiento vehiculos pequeños',150000,'123456',1,'2026-04-12 16:47:36','2026-04-12 16:47:36','servicio'),(18,1,1,1,NULL,'Desengrasante de motor',0,'INS-001',1,NULL,'2026-04-20 21:43:48','insumo'),(19,1,1,1,NULL,'Limpiador de frenos spray',0,'INS-002',1,NULL,'2026-04-20 21:43:48','insumo'),(20,1,1,1,NULL,'Limpiador de inyectores',0,'INS-003',1,NULL,'2026-04-20 21:43:48','insumo'),(21,1,1,1,NULL,'Lubricante WD-40',0,'INS-004',1,NULL,'2026-04-20 21:43:48','insumo'),(22,1,1,1,NULL,'Grasa multiuso',0,'INS-005',1,NULL,'2026-04-20 21:43:48','insumo'),(23,1,1,1,NULL,'Silicona RTV alta temperatura',0,'INS-006',1,NULL,'2026-04-20 21:43:48','insumo'),(24,1,1,1,NULL,'Sellador de roscas',0,'INS-007',1,NULL,'2026-04-20 21:43:48','insumo'),(25,1,1,1,NULL,'Refrigerante',0,'INS-008',1,NULL,'2026-04-20 21:43:48','insumo'),(26,1,1,1,NULL,'Líquido de frenos',0,'INS-009',1,NULL,'2026-04-20 21:43:48','insumo'),(27,1,1,1,NULL,'Trapo industrial',0,'INS-010',1,NULL,'2026-04-20 21:43:48','insumo'),(28,1,1,1,14,'Guantes descartables',0,'12345678',1,'2026-04-26 10:23:36','2026-04-20 21:43:48','producto'),(29,1,1,1,2,'Cinta aislante',80000,'012',1,'2026-04-28 21:09:10','2026-04-20 21:43:48','producto'),(30,6,1,2,4,'testtest',2000,'98764531',1,'2026-04-28 22:03:39','2026-04-26 11:57:00','producto'),(31,2,1,1,5,'test1',35000,'12345600',1,'2026-04-28 22:00:54','2026-04-28 22:00:54','producto'),(32,5,1,1,2,'test',2000,'12365400',1,'2026-04-28 22:01:43','2026-04-28 22:01:43','producto'),(33,3,1,2,13,'Aceite 30 50w 1L',35000,'123450015',1,'2026-04-30 12:54:59','2026-04-30 12:54:59','producto'),(34,4,1,2,16,'AMORTIGUADOR DEL LH TOYOTA',280000,'1267896',1,'2026-05-29 16:28:29','2026-05-29 16:28:29','producto'),(35,10,6,2,NULL,'Cambio de amortiguadores',150000,'1',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio'),(36,10,6,2,NULL,'Cambio de Aceite',50000,'2',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio'),(37,10,6,2,NULL,'Mecanica ligera',80000,'3',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio'),(38,11,1,2,3,'BUJIA NGK NIQUEL CR7HSA',20900,'100001',1,NULL,'2026-06-14 16:30:23','producto'),(39,11,1,2,3,'BUJIA NGK G-POWER LFR5AGP',36100,'100002',1,NULL,'2026-06-14 16:30:23','producto'),(40,2,1,2,19,'FILTRO DE ACEITE MANN W712/75',45000,'100003',1,NULL,'2026-06-14 16:30:23','producto'),(41,2,1,2,20,'FILTRO DE ACEITE FRAM PH3614',38000,'100004',1,NULL,'2026-06-14 16:30:23','producto'),(42,2,1,2,2,'FILTRO DE AIRE BOSCH AP3580',65000,'100005',1,NULL,'2026-06-14 16:30:23','producto'),(43,3,1,2,2,'PASTILLA DE FRENO BOSCH DELANTERA',180000,'100006',1,NULL,'2026-06-14 16:30:23','producto'),(44,12,2,2,18,'ACEITE GULF MULTI G 15W-40 1L',34000,'200001',1,NULL,'2026-06-14 16:30:23','producto'),(45,12,2,2,18,'ACEITE GULF MULTI G 20W-50 1L',34000,'200002',1,NULL,'2026-06-14 16:30:23','producto'),(46,12,7,2,18,'ACEITE GULF GEAR MP 85W-140 20L',939000,'200003',1,NULL,'2026-06-14 16:30:23','producto'),(47,13,2,2,21,'REFRIGERANTE FREEZETONE COOLANT 1L',18000,'200004',1,NULL,'2026-06-14 16:30:23','producto'),(48,14,1,2,22,'WD-40 MULTIUSO 300ML',45000,'300001',1,NULL,'2026-06-14 16:30:23','insumo'),(49,14,1,2,23,'PAÑO MICROFIBRA PARA TALLER',12000,'300002',1,NULL,'2026-06-14 16:30:23','insumo'),(50,14,1,2,23,'LIMPIADOR DE FRENOS AEROSOL 500ML',55000,'300003',1,NULL,'2026-06-14 16:30:23','insumo'),(51,10,6,2,23,'CAMBIO DE ACEITE',80000,'400001',1,NULL,'2026-06-14 16:30:23','servicio'),(52,10,6,2,23,'DIAGNOSTICO GENERAL',100000,'400002',1,NULL,'2026-06-14 16:30:23','servicio'),(53,10,6,2,23,'REVISION DE FRENOS',70000,'400003',1,NULL,'2026-06-14 16:30:23','servicio'),(57,2,1,2,1,'TEST_DECIMAL_20260616_211949 ACEITE PRODUCTO',55000,'TDP659189',1,NULL,'2026-06-16 22:19:49','producto'),(58,2,1,2,1,'TEST_DECIMAL_20260616_211949 INSUMO LITRO',22000,'TDI659189',1,NULL,'2026-06-16 22:19:49','insumo'),(59,2,1,2,1,'TEST_DECIMAL_20260616_211949 SERVICIO CAMBIO',80000,'TDS659189',1,NULL,'2026-06-16 22:19:49','servicio'),(60,2,1,2,1,'TEST_DECIMAL_20260616_212134 ACEITE PRODUCTO',55000,'TDP659294',1,NULL,'2026-06-16 22:21:34','producto'),(61,2,1,2,1,'TEST_DECIMAL_20260616_212134 INSUMO LITRO',22000,'TDI659294',1,NULL,'2026-06-16 22:21:34','insumo'),(62,2,1,2,1,'TEST_DECIMAL_20260616_212134 SERVICIO CAMBIO',80000,'TDS659294',1,NULL,'2026-06-16 22:21:34','servicio'),(63,2,1,2,1,'TEST_DECIMAL_20260617_174232 ACEITE PRODUCTO',55000,'TDP732552',1,NULL,'2026-06-17 18:42:32','producto'),(64,2,1,2,1,'TEST_DECIMAL_20260617_174232 INSUMO LITRO',22000,'TDI732552',1,NULL,'2026-06-17 18:42:32','insumo'),(65,2,1,2,1,'TEST_DECIMAL_20260617_174232 SERVICIO CAMBIO',80000,'TDS732552',1,NULL,'2026-06-17 18:42:32','servicio'),(66,2,1,2,1,'TEST_DECIMAL_20260617_174425 ACEITE PRODUCTO',55000,'TDP732665',1,NULL,'2026-06-17 18:44:25','producto'),(67,2,1,2,1,'TEST_DECIMAL_20260617_174425 INSUMO LITRO',22000,'TDI732665',1,NULL,'2026-06-17 18:44:25','insumo'),(68,2,1,2,1,'TEST_DECIMAL_20260617_174425 SERVICIO CAMBIO',80000,'TDS732665',1,NULL,'2026-06-17 18:44:25','servicio'),(69,6,1,2,23,'test articulo 2',61000,'123456789',1,'2026-06-18 20:19:29','2026-06-18 20:19:12','producto');
/*!40000 ALTER TABLE `articulos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cargos`
--

DROP TABLE IF EXISTS `cargos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargos` (
  `idcargos` int unsigned NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `cat_descri` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Aceites',1),(2,'Filtros',1),(3,'Frenos',1),(4,'Suspensión',1),(5,'Motor',1),(6,'Electricidad',1),(7,'Transmisión',1),(8,'Neumáticos',1),(9,'Refrigeración',1),(10,'Servicios',1),(11,'Bujías',1),(12,'Lubricantes',1),(13,'Refrigerantes',1),(14,'Insumos',1);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
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
  `ciu_descri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `doc_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_cliente` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido_cliente` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion_cliente` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular_cliente` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `digito_v` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_civil` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_cliente` tinyint unsigned DEFAULT NULL,
  `doc_type` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_cliente` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  KEY `clientes_FKIndex1` (`id_ciudad`),
  CONSTRAINT `fk_ciudadesCli` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,1,'1234567','Juan','González','Barrio San Vicente','0981123456','3','Soltero',1,'CI','juan.gonzalez@gmail.com'),(2,1,'2345678','María','López','Av. Eusebio Ayala','0982234567','1','Soltero',1,'CI','maria.lopez@gmail.com'),(3,182,'3216478','Carloss','Ramírezv','Barrio Obrero','0983345677','5','Soltero',1,'CI','carlos.ramirez@gmail.com'),(4,3,'4567890','Hanaa','Martínez','Avda. Mariscal López','0984456788','3','Soltero',1,'CI','ana.martinez@gmail.com'),(5,1,'5678901','Luis','Fernández','Barrio Trinidad','0985567890','7','Soltero',1,'CI','luis.fernandez@gmail.com'),(6,1,'6789012','Sofía','Benítez','Av. Artigas','0986678901','4','Soltero',1,'CI','sofia.benitez@gmail.com'),(7,1,'7890123','Miguel','Duarte','Barrio Sajonia','0987789012','9','Soltero',1,'CI','miguel.duarte@gmail.com'),(8,1,'8901234','Laura','Giménez','Av. Boggiani','0988890123','6','Soltero',1,'CI','laura.gimenez@gmail.com'),(9,1,'9012345','Pedro','Vera','Barrio Lambaré','0989901234','8','Soltero',0,'CI','pedro.vera@gmail.com'),(10,1,'1122334','Carolina','Rojas','Av. Fernando de la Mora','0981012345','0','Soltero',1,'PASAPORTE','carolina.rojas@gmail.com'),(11,2,'4964127','Juan Angel','Figueredo Martinez','Avda Cerro Patiño','0986203431','1','Soltero',1,'CI','juanmartinez076@gmail.com'),(12,166,'1299450','Gricelda','Martinez','Itaugua km 31 - Avda Cerro patiño','0985518660','','Soltero',1,'CI','Griceldamar@gmail.com'),(13,79,'1236487','asdas','asdas','asdasd','64789','','Viudo',0,'CI','asdas@admin.com'),(14,10,'1236452','tses','adsd','dsfsdfsd','03215987','','Soltero',0,'CI','testasd@admin.com'),(15,24,'80016096','ertca','asdsdasd','asdasd','asdsad','7','Casado',0,'RUC','asdasd@admin.com'),(16,10,'9745612','testse','asdasdas','sdasd','4568971','5','Divorciado',0,'CI','asdasd@admin.com'),(17,22,'6547892','jesus','mendieta','asdasd','123654987','','',0,'CI','jsd@gmail.com'),(18,219,'80016095','retail sa',NULL,'','','7','',0,'RUC',''),(19,1,'4799780','Mauricio','Montiel','No informado','','','',1,'CI',''),(20,162,'80016094','Retail S.A.','','','','8','',0,'RUC',''),(21,175,'1254865','JOSE','PEREZ','','','','Soltero',0,'CI',''),(22,178,'654856','JAUN','LOVEZNO','','','','',0,'CI',''),(24,1,'7970558','TEST_DECIMAL_20260616_211949','Cliente','Direccion test','0981000001','1','Soltero',1,'CI','cliente.decimal@test.local'),(25,1,'7284558','TEST_DECIMAL_20260616_212134','Cliente','Direccion test','0981000001','1','Soltero',1,'CI','cliente.decimal@test.local'),(26,1,'7312367','TEST_DECIMAL_20260617_174232','Cliente','Direccion test','0981000001','1','Soltero',1,'CI','cliente.decimal@test.local'),(27,1,'7268667','TEST_DECIMAL_20260617_174425','Cliente','Direccion test','0981000001','1','Soltero',1,'CI','cliente.decimal@test.local'),(29,219,'80016097','retail sa',NULL,'','','7','',1,'RUC','');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
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
  `nro_factura` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_factura` date DEFAULT NULL,
  `nro_timbrado` int unsigned DEFAULT NULL,
  `vencimiento_timbrado` date DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `total_compra` int unsigned DEFAULT NULL,
  `condicion` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `compra_intervalo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=9013 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_cabecera`
--

LOCK TABLES `compra_cabecera` WRITE;
/*!40000 ALTER TABLE `compra_cabecera` DISABLE KEYS */;
INSERT INTO `compra_cabecera` VALUES (6,1,9001,1,'2026-06-06 08:56:45','001-001-0000056','2026-06-06',12345678,'2026-06-30',1,2290000,'contado','7',18,NULL,NULL),(9001,1,9002,1,'2026-02-20 09:30:00','001-001-009001','2026-02-20',12345678,'2026-12-31',2,2340000,'contado','0',9002,NULL,NULL),(9002,1,9001,1,'2026-04-28 10:00:00','001-001-009002','2026-04-28',12345678,'2026-12-31',2,3120000,'credito','30',9004,NULL,NULL),(9003,1,9003,1,'2026-05-16 11:20:00','001-001-009003','2026-05-16',12345678,'2026-12-31',1,980000,'contado','0',NULL,NULL,NULL),(9004,1,9002,1,'2026-06-03 08:45:00','001-001-009004','2026-06-03',12345678,'2026-12-31',0,620000,'contado','0',NULL,'2026-06-04 09:00:00',1),(9005,1,8,1,'2026-06-09 21:56:27','001-001-0000012','2026-06-09',12345678,'2026-06-30',1,750000,'contado','3',NULL,NULL,NULL),(9006,1,7,1,'2026-06-09 21:58:09','001-001-0000057','2026-06-09',12345678,'2026-06-30',0,4500,'contado','7',NULL,'2026-06-09 22:18:08',1),(9008,1,9005,1,'2026-06-16 22:19:49','TEST-81659189','2026-06-16',987654,'2026-07-16',1,70000,'contado','7',9007,NULL,NULL),(9009,1,9006,1,'2026-06-16 22:21:34','TEST-81659294','2026-06-16',987654,'2026-07-16',1,70000,'contado','7',9008,NULL,NULL),(9010,1,9007,1,'2026-06-17 18:42:32','TEST-81732552','2026-06-17',987654,'2026-07-17',1,70000,'contado','7',9009,NULL,NULL),(9011,1,9008,1,'2026-06-17 18:44:25','TEST-81732665','2026-06-17',987654,'2026-07-17',1,70000,'contado','7',9010,NULL,NULL),(9012,1,7,1,'2026-06-18 23:02:54','001-001-0000007','2026-06-18',12345678,'2026-12-31',0,555000,'contado','7',9011,'2026-06-18 23:18:43',1);
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
  `cantidad_facturada` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `cantidad_recibida` decimal(12,4) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tipo_iva` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `compra_detalle` VALUES (6,6,220000.00,10.0000,10.0000,2200000.00,'2',200000.00),(6,21,45000.00,2.0000,2.0000,90000.00,'1',4285.71),(9001,2,220000.00,8.0000,8.0000,1760000.00,'2',160000.00),(9001,3,145000.00,4.0000,4.0000,580000.00,'2',52727.27),(9002,2,190000.00,12.0000,12.0000,2280000.00,'2',207272.73),(9002,3,140000.00,6.0000,6.0000,840000.00,'2',76363.64),(9003,2,196000.00,5.0000,5.0000,980000.00,'2',89090.91),(9004,3,155000.00,4.0000,4.0000,620000.00,'2',56363.64),(9005,2,50000.00,15.0000,15.0000,750000.00,'2',68181.82),(9006,30,4500.00,1.0000,2.0000,4500.00,'2',409.09),(9008,57,40000.00,1.7500,1.2500,70000.00,'2',6363.64),(9009,60,40000.00,1.7500,1.2500,70000.00,'2',6363.64),(9010,63,40000.00,1.7500,1.2500,70000.00,'2',6363.64),(9011,66,40000.00,1.7500,1.2500,70000.00,'2',6363.64),(9012,21,35000.00,5.0000,4.0000,175000.00,'1',8333.00),(9012,28,38000.00,10.0000,10.0000,380000.00,'1',18095.00);
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
  `tipo_movimiento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_id` int unsigned DEFAULT NULL,
  `monto` decimal(12,2) DEFAULT NULL,
  `saldo` decimal(12,2) DEFAULT NULL,
  `nro_cuotas` int unsigned DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  `observacion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idcuentas_a_pagar`,`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex1` (`idcompra_cabecera`),
  KEY `cuentas_a_pagar_FKIndex2` (`id_sucursal`),
  CONSTRAINT `cuentas_a_pagarCompra` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `cuentas_a_pagarSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_a_pagar`
--

LOCK TABLES `cuentas_a_pagar` WRITE;
/*!40000 ALTER TABLE `cuentas_a_pagar` DISABLE KEYS */;
INSERT INTO `cuentas_a_pagar` VALUES (11,6,1,'COMPRA','INGRESO_COMPRA',NULL,2290000.00,2290000.00,1,'2026-06-13','2026-06-06 08:56:45','Factura 001-001-0000056',1),(12,9005,1,'COMPRA','INGRESO_COMPRA',NULL,750000.00,750000.00,1,'2026-06-12','2026-06-09 21:56:27','Factura 001-001-0000012',1),(13,9006,1,'COMPRA','INGRESO_COMPRA',NULL,4500.00,4500.00,1,'2026-06-16','2026-06-09 21:58:09','Factura 001-001-0000057',0),(14,9012,1,'COMPRA','INGRESO_COMPRA',NULL,555000.00,555000.00,1,'2026-06-25','2026-06-18 23:02:54','Factura 001-001-0000007',1),(15,9012,1,'credito','nota_compra',3,-35000.00,-35000.00,NULL,NULL,'2026-06-18 23:04:23','Nota credito 001-001-0000008',1),(16,9012,1,'anulacion','nota_compra',3,35000.00,35000.00,NULL,NULL,'2026-06-18 23:11:36','Anulación Nota credito',1),(17,9012,1,'credito','nota_compra',4,-35000.00,-35000.00,NULL,NULL,'2026-06-18 23:12:39','Nota credito 001-001-0000008',1),(18,9012,1,'credito','nota_compra',5,-520000.00,-520000.00,NULL,NULL,'2026-06-18 23:15:32','Nota credito 001-001-0000009',1),(19,9012,1,'anulacion','nota_compra',5,520000.00,520000.00,NULL,NULL,'2026-06-18 23:17:39','Anulación Nota credito',1),(20,9012,1,'credito','nota_compra',6,-520000.00,-520000.00,NULL,NULL,'2026-06-18 23:18:43','Nota credito 001-001-0000009',1);
/*!40000 ALTER TABLE `cuentas_a_pagar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departamentos`
--

DROP TABLE IF EXISTS `departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `dto_descripcion` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `descuento_cliente` VALUES (11,4),(11,5),(19,4),(21,4);
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
  `id_usuario_modifica` int unsigned DEFAULT NULL,
  `id_usuario_crea` int unsigned NOT NULL,
  `id_sucursal` int unsigned DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `aplica_a` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOTAL',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `es_reutilizable` tinyint unsigned NOT NULL,
  `estado` tinyint unsigned NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_descuento`),
  KEY `descuentos_FKIndex1` (`id_usuario_crea`),
  KEY `descuentos_FKIndex2` (`id_usuario_modifica`),
  KEY `fk_descuentos_sucursal` (`id_sucursal`),
  KEY `idx_descuentos_filtros` (`estado`,`fecha_inicio`,`fecha_fin`,`id_sucursal`),
  CONSTRAINT `descuentosUsucrea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `descuentosUsumodi` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_descuentos_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos`
--

LOCK TABLES `descuentos` WRITE;
/*!40000 ALTER TABLE `descuentos` DISABLE KEYS */;
INSERT INTO `descuentos` VALUES (4,1,1,NULL,'Descuento por Apertura','Promociones por apertura','PORCENTAJE',10.00,'TOTAL','2026-05-30','2026-06-30',1,1,'2026-05-30 19:43:56','2026-06-14 20:21:42'),(5,NULL,1,NULL,'test vip','test','PORCENTAJE',50.00,'PRODUCTO','2026-06-01','2026-06-30',1,1,'2026-06-01 23:10:19',NULL);
/*!40000 ALTER TABLE `descuentos` ENABLE KEYS */;
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
  `id_articulo_servicio` int unsigned DEFAULT NULL,
  `id_articulo_repuesto` int unsigned DEFAULT NULL,
  `cantidad_repuesto` decimal(12,4) DEFAULT '1.0000',
  `problema` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gravedad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repuesto_origen` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'TALLER',
  PRIMARY KEY (`id_diagnostico_detalle`),
  KEY `fk_diagnostico_detalle_diagnostico_servicio1` (`id_diagnostico`),
  KEY `idx_diag_det_servicio` (`id_articulo_servicio`),
  KEY `idx_diag_det_repuesto` (`id_articulo_repuesto`),
  CONSTRAINT `fk_diag_det_repuesto` FOREIGN KEY (`id_articulo_repuesto`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_diag_det_servicio` FOREIGN KEY (`id_articulo_servicio`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_diagnostico_detalle_diagnostico_servicio1` FOREIGN KEY (`id_diagnostico`) REFERENCES `diagnostico_servicio` (`id_diagnostico`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_detalle`
--

LOCK TABLES `diagnostico_detalle` WRITE;
/*!40000 ALTER TABLE `diagnostico_detalle` DISABLE KEYS */;
INSERT INTO `diagnostico_detalle` VALUES (28,25,37,4,2.0000,'cambio de pastillas, solo se verifica eso','media','TALLER'),(29,26,37,4,1.0000,'pastilla de freno cristalizada, se debe cambiar','media','TALLER'),(30,27,37,4,1.0000,'pastillas cristalizadas, defecto de fabrica','leve','TALLER'),(31,28,36,33,4.0000,'cambio de aceite preventivo','media','TALLER'),(32,28,37,NULL,0.0000,'ajuste de motor','media','NINGUNO'),(33,29,35,6,2.0000,'necesita cambio par delantero','media','TALLER'),(34,30,13,2,1.0000,'test','media','TALLER'),(35,31,36,NULL,0.0000,'test cambio aceite','media','TALLER'),(36,31,13,NULL,0.0000,'cambio de filtro por desgaste','media','TALLER'),(37,31,35,6,1.0000,'fallo en amortiguacion','media','TALLER'),(38,32,51,46,1.0000,'test cambio de aceite','media','TALLER'),(39,32,35,6,1.0000,'espiral rota','media','TALLER'),(40,32,13,41,1.0000,'rotura por impacto','media','TALLER'),(42,34,59,57,0.7500,'Aceite por litro','media','TALLER'),(43,35,62,60,0.7500,'Aceite por litro','media','TALLER'),(44,36,65,63,0.7500,'Aceite por litro','media','TALLER'),(45,37,68,66,0.7500,'Aceite por litro','media','TALLER');
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
  `descripcion_cliente` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `diagnostico_general` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_servicio`
--

LOCK TABLES `diagnostico_servicio` WRITE;
/*!40000 ALTER TABLE `diagnostico_servicio` DISABLE KEYS */;
INSERT INTO `diagnostico_servicio` VALUES (25,1,21,1,1,'2026-05-31 21:43:41',NULL,NULL,2,'cambio de pastillas normal','2026-06-01 00:43:41','2026-06-01 00:44:28',1,1,0),(26,1,22,1,1,'2026-06-01 20:53:20',NULL,NULL,0,'','2026-06-01 23:53:20','2026-06-01 23:55:00',1,1,0),(27,1,22,1,1,'2026-06-01 20:56:24',NULL,NULL,1,'test','2026-06-01 23:56:24','2026-06-01 23:56:24',1,1,0),(28,1,23,1,1,'2026-06-02 16:53:05',NULL,NULL,0,'verificacion de vehiculo, recibido se agrega detalle para presupuesto','2026-06-02 19:53:05','2026-06-09 00:31:26',1,1,0),(29,1,24,1,1,'2026-06-06 08:46:01',NULL,NULL,2,'Diagnostico pruea','2026-06-06 11:46:01','2026-06-06 23:42:25',1,1,0),(30,1,9001,1,1,'2026-06-14 19:55:00',NULL,NULL,0,'test','2026-06-14 22:55:00','2026-06-14 22:58:14',1,1,0),(31,1,9004,1,1,'2026-06-14 20:01:42',NULL,NULL,0,'test','2026-06-14 23:01:42','2026-06-14 23:07:49',1,1,0),(32,1,9004,1,1,'2026-06-14 20:09:26',NULL,NULL,2,'verificacion completa','2026-06-14 23:09:26','2026-06-14 23:22:18',1,1,0),(34,1,9006,1,1,'2026-06-16 22:19:49','TEST_DECIMAL_20260616_211949','Diagnostico decimal',2,'Observacion decimal','2026-06-17 01:19:49','2026-06-17 01:19:49',0,1,0),(35,1,9007,1,1,'2026-06-16 22:21:34','TEST_DECIMAL_20260616_212134','Diagnostico decimal',2,'Observacion decimal','2026-06-17 01:21:34','2026-06-17 01:21:34',0,1,0),(36,1,9008,1,1,'2026-06-17 18:42:32','TEST_DECIMAL_20260617_174232','Diagnostico decimal',2,'Observacion decimal','2026-06-17 21:42:32','2026-06-17 21:42:32',0,1,0),(37,1,9009,1,1,'2026-06-17 18:44:25','TEST_DECIMAL_20260617_174425','Diagnostico decimal',2,'Observacion decimal','2026-06-17 21:44:25','2026-06-17 21:44:25',0,1,0);
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
  `nombre` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apellido` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `celular` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_cedula` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_civil` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `empleados` VALUES (1,1,1,'Carlos','Gómez','Asunción Centro','0981123456','1234567','Casado',1),(2,2,1,'Ricardo','Martínez','Luque','0982234567','2345678','Casado',1),(3,3,2,'Pedro','Benítez','San Lorenzo','0983345678','3456785','Soltero/a',1),(4,4,1,'Luis','Fernández','Capiatá','0984456789','4567890','Soltero',0),(5,6,1,'Ana','Rojas','Fernando de la Mora','0985567890','5678901','Soltero',1),(6,5,1,'Jorge','Duarte','Ñemby','0986678901','6789012','Casado',1),(7,9,1,'Miguel','Vera','Villa Elisa','0987789012','7890123','Casado',1),(8,9,1,'Diego','López','Asunción','0988890123','8901234','Soltero',1),(9,10,1,'Andrés','Giménez','Lambaré','0989901234','9012345','Soltero',1),(11,8,1,'María','González','Luque','0982123456','2233445','Casado',1),(12,10,1,'javier','martinez','asdasd','098465412','4964654','Casado/a',0);
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
  `razon_social` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  `email_empresa` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono_empresa` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
-- Table structure for table `equipo_empleado`
--

DROP TABLE IF EXISTS `equipo_empleado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipo_empleado` (
  `idempleados` int unsigned NOT NULL,
  `id_equipo` int unsigned NOT NULL,
  `rol` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `equipo_empleado` VALUES (1,2,'Miembro',1),(2,1,'Miembro',0),(4,2,'Miembro',1),(5,1,'Miembro',0),(6,1,'Miembro',1),(6,2,'Miembro',1),(7,1,'Miembro',1),(8,1,'Miembro',0),(9,1,'Miembro',1),(9,2,'Miembro',1);
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
  `nombre` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned DEFAULT NULL,
  `descripcion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `equipo_trabajo_FKIndex1` (`id_sucursal`),
  CONSTRAINT `equipo_trabajoSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipo_trabajo`
--

LOCK TABLES `equipo_trabajo` WRITE;
/*!40000 ALTER TABLE `equipo_trabajo` DISABLE KEYS */;
INSERT INTO `equipo_trabajo` VALUES (1,1,'Equipo A',1,'Mecánica general'),(2,1,'Equipo B',1,'Electricidad automotriz'),(3,1,'Equipo C',1,'Electricidad'),(4,1,'tes',1,'test');
/*!40000 ALTER TABLE `equipo_trabajo` ENABLE KEYS */;
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
  `tipo_comprobante` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serie` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_comprobante` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idproveedores` int unsigned NOT NULL,
  `proveedor_nombre` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `proveedor_ruc` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=9012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_compra`
--

LOCK TABLES `libro_compra` WRITE;
/*!40000 ALTER TABLE `libro_compra` DISABLE KEYS */;
INSERT INTO `libro_compra` VALUES (8,1,6,'2026-06-06','factura','001-001','001-001-0000056',9001,'Repuestos Central S.A.','80012345-6',0.00,85714.29,4285.71,2000000.00,200000.00,2290000.00,1,'2026-06-06 08:56:45'),(9001,1,9001,'2026-02-20','factura','001-001','001-001-009001',9002,'Autopartes del Sur S.R.L.','80045678-1',0.00,0.00,0.00,2127272.73,212727.27,2340000.00,1,'2026-02-20 09:30:00'),(9002,1,9002,'2026-04-28','factura','001-001','001-001-009002',9001,'Repuestos Central S.A.','80012345-6',0.00,0.00,0.00,2836363.64,283636.36,3120000.00,1,'2026-04-28 10:00:00'),(9003,1,9003,'2026-05-16','factura','001-001','001-001-009003',9003,'Lubricantes Asuncion S.A.','80078912-3',0.00,0.00,0.00,890909.09,89090.91,980000.00,1,'2026-05-16 11:20:00'),(9004,1,9004,'2026-06-03','factura','001-001','001-001-009004',9002,'Autopartes del Sur S.R.L.','80045678-1',0.00,0.00,0.00,563636.36,56363.64,620000.00,0,'2026-06-03 08:45:00'),(9005,1,9005,'2026-06-09','factura','001-001','001-001-0000012',8,'Casa del Filtro SRL','80089012-3',0.00,0.00,0.00,681818.18,68181.82,750000.00,1,'2026-06-09 21:56:27'),(9006,1,9006,'2026-06-09','factura','001-001','001-001-0000057',7,'Repuestos Japón Import','80078901-2',0.00,0.00,0.00,4090.91,409.09,4500.00,0,'2026-06-09 21:58:09'),(9007,1,9012,'2026-06-18','factura','001-001','001-001-0000007',7,'Repuestos Japón Import','80078901-2',0.00,528572.00,26428.00,0.00,0.00,555000.00,1,'2026-06-18 23:02:54'),(9008,1,9012,'2026-06-18','NC','001-001','001-001-0000008',7,'Repuestos Japón Import','80078901-2',0.00,-33333.33,-1666.67,0.00,0.00,-35000.00,0,'2026-06-18 23:04:23'),(9009,1,9012,'2026-06-18','NC','001-001','001-001-0000008',7,'Repuestos Japón Import','80078901-2',0.00,-33333.33,-1666.67,0.00,0.00,-35000.00,1,'2026-06-18 23:12:39'),(9010,1,9012,'2026-06-18','NC','001-001','001-001-0000009',7,'Repuestos Japón Import','80078901-2',0.00,-495238.09,-24761.91,0.00,0.00,-520000.00,0,'2026-06-18 23:15:32'),(9011,1,9012,'2026-06-18','NC','001-001','001-001-0000009',7,'Repuestos Japón Import','80078901-2',0.00,-495238.09,-24761.91,0.00,0.00,-520000.00,1,'2026-06-18 23:18:43');
/*!40000 ALTER TABLE `libro_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id_marcas` int unsigned NOT NULL AUTO_INCREMENT,
  `mar_descri` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_marcas`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Toyota',1),(2,'Bosch',1),(3,'NGK',1),(4,'Michelin',1),(5,'Castrol',1),(6,'Mobil',1),(7,'SKF',1),(8,'Valeo',1),(9,'Pirelli',1),(10,'Shell',1),(11,'Nissan',1),(12,'Chevrolet',1),(13,'Kia',1),(14,'Hyundai',1),(15,'Volkswagen',1),(16,'KYB',1),(17,'Varios',1),(18,'GULF',1),(19,'MANN FILTER',1),(20,'FRAM',1),(21,'FREEZETONE',1),(22,'WD-40',1),(23,'GENÉRICO',1);
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;
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
  `mod_descri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `TipoMovStockId` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockArticuloId` int unsigned NOT NULL,
  `MovStockCantidad` decimal(12,4) NOT NULL,
  `MovStockPrecioVenta` decimal(14,2) NOT NULL,
  `MovStockCosto` decimal(14,2) NOT NULL,
  `MovStockFechaHora` datetime NOT NULL,
  `MovStockNroTicket` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockPOS` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MovStockUsuario` bigint NOT NULL,
  `MovStockSigno` smallint NOT NULL,
  `MovStockSaldoAnterior` decimal(12,4) DEFAULT NULL,
  `MovStockSaldoActual` decimal(12,4) DEFAULT NULL,
  `MovStockReferencia` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MovStockId`),
  KEY `sucmovimientostock_FKIndex1` (`id_sucursal`),
  KEY `fk_mov_articulo` (`MovStockArticuloId`),
  CONSTRAINT `fk_mov_articulo` FOREIGN KEY (`MovStockArticuloId`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_sucursalesSucmo` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=9052 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientostock`
--

LOCK TABLES `movimientostock` WRITE;
/*!40000 ALTER TABLE `movimientostock` DISABLE KEYS */;
INSERT INTO `movimientostock` VALUES (77,1,'AJUSTE_INV',4,10.0000,0.00,0.00,'2026-05-31 20:42:46',NULL,NULL,1,1,NULL,NULL,'AJUSTE #19'),(78,1,'REG. SERVICIO',4,2.0000,50000.00,0.00,'2026-05-31 21:46:00',NULL,NULL,1,-1,NULL,NULL,'REG_SERV #28'),(79,1,'AJUSTE_INV',33,10.0000,0.00,0.00,'2026-06-02 15:51:53',NULL,NULL,1,1,NULL,NULL,'AJUSTE #20'),(81,1,'REG. SERVICIO',4,1.0000,0.00,0.00,'2026-06-03 20:46:20',NULL,NULL,1,-1,NULL,NULL,'REG_SERV #30'),(82,1,'AJUSTE_INV',21,10.0000,0.00,0.00,'2026-06-03 21:12:20',NULL,NULL,1,1,NULL,NULL,'AJUSTE #21'),(83,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-06-03 22:12:48',NULL,NULL,1,-1,NULL,NULL,'SAL_INS #9'),(84,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-06-03 23:33:48',NULL,NULL,1,1,NULL,NULL,'ANUL_SAL_INS #9'),(85,1,'RECEPCION COMPRA',6,10.0000,0.00,220000.00,'2026-06-06 08:56:45','001-001-0000056',NULL,1,1,NULL,NULL,'6'),(86,1,'RECEPCION COMPRA',21,2.0000,0.00,45000.00,'2026-06-06 08:56:45','001-001-0000056',NULL,1,1,NULL,NULL,'6'),(9001,1,'RECEPCION COMPRA',2,8.0000,0.00,220000.00,'2026-02-20 09:30:00','001-001-009001',NULL,1,1,NULL,NULL,'COMPRA #9001'),(9002,1,'RECEPCION COMPRA',3,4.0000,0.00,145000.00,'2026-02-20 09:30:00','001-001-009001',NULL,1,1,NULL,NULL,'COMPRA #9001'),(9003,1,'TRANSFERENCIA ENVIO',2,6.0000,0.00,0.00,'2026-03-12 09:20:00',NULL,NULL,1,-1,NULL,NULL,'TRANSF #9002'),(9004,1,'REG. SERVICIO',3,2.0000,165000.00,0.00,'2026-05-22 17:10:00',NULL,NULL,1,-1,NULL,NULL,'REG_SERV #9002'),(9005,1,'REG. SERVICIO',6,2.0000,350000.00,0.00,'2026-06-06 20:43:35',NULL,NULL,1,-1,10.0000,8.0000,'REG_SERV #9004'),(9006,1,'REG. SERVICIO',4,2.0000,50000.00,0.00,'2026-06-06 20:43:35',NULL,NULL,1,-1,7.0000,5.0000,'REG_SERV #9004'),(9007,1,'SALIDA INSUMO',21,1.0000,0.00,45000.00,'2026-06-06 20:44:33',NULL,NULL,1,-1,12.0000,11.0000,'SAL_INS #10'),(9008,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,45000.00,'2026-06-06 20:49:48',NULL,NULL,1,1,11.0000,12.0000,'ANUL_SAL_INS #10'),(9009,1,'AJUSTE_INV',33,2.0000,0.00,0.00,'2026-06-07 08:40:26',NULL,NULL,1,-1,10.0000,8.0000,'AJUSTE #22'),(9010,1,'SALIDA INSUMO',21,1.0000,0.00,45000.00,'2026-06-08 21:17:11',NULL,NULL,1,-1,12.0000,11.0000,'SAL_INS #11'),(9011,1,'SALIDA INSUMO',21,2.0000,0.00,45000.00,'2026-06-08 21:19:24',NULL,NULL,1,-1,11.0000,9.0000,'SAL_INS #12'),(9014,1,'ANULACION REG. SERVICIO',6,2.0000,350000.00,0.00,'2026-06-08 21:20:36',NULL,NULL,1,1,8.0000,10.0000,'ANUL_REG_SERV #9004'),(9015,1,'ANULACION REG. SERVICIO',4,2.0000,50000.00,0.00,'2026-06-08 21:20:36',NULL,NULL,1,1,5.0000,7.0000,'ANUL_REG_SERV #9004'),(9016,1,'RECEPCION COMPRA',2,15.0000,0.00,50000.00,'2026-06-09 21:56:27','001-001-0000012',NULL,1,1,0.0000,15.0000,'9005'),(9017,1,'RECEPCION COMPRA',30,2.0000,0.00,4500.00,'2026-06-09 21:58:09','001-001-0000057',NULL,1,1,0.0000,2.0000,'9006'),(9018,1,'ANULACION COMPRA',30,2.0000,0.00,4500.00,'2026-06-09 22:18:08','ANUL_COMPRA# 9006',NULL,1,-1,2.0000,0.0000,'ANUL_COMPRA# 9006'),(9019,2,'TRANSFERENCIA_SALIDA',33,3.0000,0.00,0.00,'2026-06-14 13:01:31','002-002-0000002',NULL,7,-1,8.0000,5.0000,'TRANSFERENCIA #9004'),(9020,1,'TRANSFERENCIA_ENTRADA',33,2.0000,0.00,0.00,'2026-06-14 13:04:46',NULL,NULL,1,1,8.0000,10.0000,'TRF-9004'),(9021,1,'AJUSTE_INV',41,10.0000,0.00,28500.00,'2026-06-14 19:20:16',NULL,NULL,1,1,0.0000,10.0000,'AJUSTE #25'),(9022,1,'AJUSTE_INV',46,10.0000,0.00,704000.00,'2026-06-14 19:20:16',NULL,NULL,1,1,0.0000,10.0000,'AJUSTE #25'),(9028,1,'TEST STOCK INICIAL',57,10.7500,55000.00,40000.00,'2026-06-16 22:19:49',NULL,NULL,1,1,0.0000,10.7500,'TEST_DECIMAL_20260616_211949 PRODUCTO'),(9029,1,'TEST STOCK INICIAL',58,5.5000,22000.00,12000.00,'2026-06-16 22:19:49',NULL,NULL,1,1,0.0000,5.5000,'TEST_DECIMAL_20260616_211949 INSUMO'),(9030,1,'TEST RECEPCION COMPRA',57,1.2500,0.00,40000.00,'2026-06-16 22:19:49',NULL,NULL,1,1,10.7500,12.0000,'TEST_DECIMAL_20260616_211949 COMPRA #9008'),(9031,1,'TEST REGISTRO SERVICIO',57,0.7500,55000.00,40000.00,'2026-06-16 22:19:49',NULL,NULL,1,-1,12.0000,11.2500,'TEST_DECIMAL_20260616_211949 REG #9006'),(9032,1,'TEST SALIDA INSUMO',58,1.2500,22000.00,12000.00,'2026-06-16 22:19:49',NULL,NULL,1,-1,5.5000,4.2500,'TEST_DECIMAL_20260616_211949 SALIDA #14'),(9033,1,'TEST STOCK INICIAL',60,10.7500,55000.00,40000.00,'2026-06-16 22:21:34',NULL,NULL,1,1,0.0000,10.7500,'TEST_DECIMAL_20260616_212134 PRODUCTO'),(9034,1,'TEST STOCK INICIAL',61,5.5000,22000.00,12000.00,'2026-06-16 22:21:34',NULL,NULL,1,1,0.0000,5.5000,'TEST_DECIMAL_20260616_212134 INSUMO'),(9035,1,'TEST RECEPCION COMPRA',60,1.2500,0.00,40000.00,'2026-06-16 22:21:34',NULL,NULL,1,1,10.7500,12.0000,'TEST_DECIMAL_20260616_212134 COMPRA #9009'),(9036,1,'TEST REGISTRO SERVICIO',60,0.7500,55000.00,40000.00,'2026-06-16 22:21:34',NULL,NULL,1,-1,12.0000,11.2500,'TEST_DECIMAL_20260616_212134 REG #9007'),(9037,1,'TEST SALIDA INSUMO',61,1.2500,22000.00,12000.00,'2026-06-16 22:21:34',NULL,NULL,1,-1,5.5000,4.2500,'TEST_DECIMAL_20260616_212134 SALIDA #15'),(9038,1,'TEST STOCK INICIAL',63,10.7500,55000.00,40000.00,'2026-06-17 18:42:32',NULL,NULL,1,1,0.0000,10.7500,'TEST_DECIMAL_20260617_174232 PRODUCTO'),(9039,1,'TEST STOCK INICIAL',64,5.5000,22000.00,12000.00,'2026-06-17 18:42:32',NULL,NULL,1,1,0.0000,5.5000,'TEST_DECIMAL_20260617_174232 INSUMO'),(9040,1,'TEST RECEPCION COMPRA',63,1.2500,0.00,40000.00,'2026-06-17 18:42:32',NULL,NULL,1,1,10.7500,12.0000,'TEST_DECIMAL_20260617_174232 COMPRA #9010'),(9041,1,'TEST REGISTRO SERVICIO',63,0.7500,55000.00,40000.00,'2026-06-17 18:42:32',NULL,NULL,1,-1,12.0000,11.2500,'TEST_DECIMAL_20260617_174232 REG #9008'),(9042,1,'TEST SALIDA INSUMO',64,1.2500,22000.00,12000.00,'2026-06-17 18:42:32',NULL,NULL,1,-1,5.5000,4.2500,'TEST_DECIMAL_20260617_174232 SALIDA #16'),(9043,1,'TEST STOCK INICIAL',66,10.7500,55000.00,40000.00,'2026-06-17 18:44:25',NULL,NULL,1,1,0.0000,10.7500,'TEST_DECIMAL_20260617_174425 PRODUCTO'),(9044,1,'TEST STOCK INICIAL',67,5.5000,22000.00,12000.00,'2026-06-17 18:44:25',NULL,NULL,1,1,0.0000,5.5000,'TEST_DECIMAL_20260617_174425 INSUMO'),(9045,1,'TEST RECEPCION COMPRA',66,1.2500,0.00,40000.00,'2026-06-17 18:44:25',NULL,NULL,1,1,10.7500,12.0000,'TEST_DECIMAL_20260617_174425 COMPRA #9011'),(9046,1,'TEST REGISTRO SERVICIO',66,0.7500,55000.00,40000.00,'2026-06-17 18:44:25',NULL,NULL,1,-1,12.0000,11.2500,'TEST_DECIMAL_20260617_174425 REG #9009'),(9047,1,'TEST SALIDA INSUMO',67,1.2500,22000.00,12000.00,'2026-06-17 18:44:25',NULL,NULL,1,-1,5.5000,4.2500,'TEST_DECIMAL_20260617_174425 SALIDA #17'),(9048,1,'RECEPCION COMPRA',21,4.0000,0.00,35000.00,'2026-06-18 23:02:54','001-001-0000007',NULL,1,1,9.0000,13.0000,'9012'),(9049,1,'RECEPCION COMPRA',28,10.0000,0.00,38000.00,'2026-06-18 23:02:54','001-001-0000007',NULL,1,1,0.0000,10.0000,'9012'),(9050,1,'NC_COMPRA_DEV',21,4.0000,0.00,35000.00,'2026-06-18 23:18:43',NULL,NULL,1,-1,13.0000,9.0000,'NC 001-001-0000009'),(9051,1,'NC_COMPRA_DEV',28,10.0000,0.00,38000.00,'2026-06-18 23:18:43',NULL,NULL,1,-1,10.0000,0.0000,'NC 001-001-0000009');
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
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `movimiento_stock` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nro_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` date NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` int unsigned DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `timbrado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idnota_compra`),
  KEY `nota_compra_FKIndex1` (`idusuario`),
  KEY `nota_compra_FKIndex2` (`id_sucursal`),
  KEY `nota_compra_FKIndex3` (`idcompra_cabecera`),
  CONSTRAINT `nota_compraCompra` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `nota_compraSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `nota_compraUsu` FOREIGN KEY (`idusuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra`
--

LOCK TABLES `nota_compra` WRITE;
/*!40000 ALTER TABLE `nota_compra` DISABLE KEYS */;
INSERT INTO `nota_compra` VALUES (3,1,9012,1,7,'credito','NINGUNO','001-001-0000008','2026-06-18',35000.00,'[regularizar_diferencia] regularizacion de faltante',0,'2026-06-18 23:04:23','2026-06-18 23:11:36','12345678'),(4,1,9012,1,7,'credito','NINGUNO','001-001-0000008','2026-06-18',35000.00,'[regularizar_diferencia] test',1,'2026-06-18 23:12:39',NULL,'12345678'),(5,1,9012,1,7,'credito','NINGUNO','001-001-0000009','2026-06-18',520000.00,'[anulacion_total] test',0,'2026-06-18 23:15:32','2026-06-18 23:17:39','12345678'),(6,1,9012,1,7,'credito','DEVOLUCION','001-001-0000009','2026-06-18',520000.00,'[anulacion_total] test',1,'2026-06-18 23:18:43',NULL,'12345678');
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
  `descripcion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `nota_compra_detalle` VALUES (3,21,'Lubricante WD-40',1.00,35000.00,35000.00),(4,21,'Lubricante WD-40',1.00,35000.00,35000.00),(5,21,'Lubricante WD-40',4.00,35000.00,140000.00),(5,28,'Guantes descartables',10.00,38000.00,380000.00),(6,21,'Lubricante WD-40',4.00,35000.00,140000.00),(6,28,'Guantes descartables',10.00,38000.00,380000.00);
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
  `nro_remision` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_transpo` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci_transpo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel_transpo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transportista` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc_transport` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehimarca` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehimodelo` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehichapa` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechaenvio` date NOT NULL,
  `fechallegada` date NOT NULL,
  `motivo_remision` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int unsigned DEFAULT NULL,
  `tipo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idtransferencia` bigint DEFAULT NULL,
  PRIMARY KEY (`idnota_remision`),
  KEY `nota_remision_FKIndex1` (`id_usuario`),
  KEY `nota_remision_FKIndex2` (`id_sucursal`),
  KEY `nota_remision_FKIndex3` (`idcompra_cabecera`),
  CONSTRAINT `nota_remisionCab` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `nota_remisionSucu` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `nota_remisionUsu` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision`
--

LOCK TABLES `nota_remision` WRITE;
/*!40000 ALTER TABLE `nota_remision` DISABLE KEYS */;
INSERT INTO `nota_remision` VALUES (8,9003,1,1,'2026-06-08 00:00:00','001-001-0000021','adasd','465789','456789','test','800160967','test','stets','asda456','2026-06-09','2026-06-08','test',1,NULL,NULL,'recepcion compra',NULL),(9,NULL,2,7,'2026-06-14 13:01:31','002-002-0000002','jose campos','2342344','0986234945','eleuterio','8765425345','nissan','navara','asd234','2026-06-14','2026-06-14','test',NULL,NULL,NULL,'transferencia',9004);
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
INSERT INTO `nota_remision_detalle` VALUES (2,8,5.00,196000.00,980000.00),(33,9,3.00,0.00,0.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=9012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra`
--

LOCK TABLES `orden_compra` WRITE;
/*!40000 ALTER TABLE `orden_compra` DISABLE KEYS */;
INSERT INTO `orden_compra` VALUES (18,1,11,9001,1,'2026-06-06',2,'2026-06-06',1,'2026-06-06 08:56:45'),(9001,1,9001,9001,1,'2026-01-10',1,'2026-01-18',NULL,NULL),(9002,1,9002,9002,1,'2026-02-14',2,'2026-02-20',1,'2026-02-20 09:30:00'),(9003,1,9003,9003,1,'2026-03-24',0,'2026-03-30',1,'2026-03-25 15:10:00'),(9004,1,9004,9001,1,'2026-04-22',2,'2026-04-28',1,'2026-04-28 10:00:00'),(9005,1,11,9001,1,'2026-06-09',1,'2026-06-09',NULL,NULL),(9007,1,9006,9005,1,'2026-06-16',1,'2026-07-16',NULL,NULL),(9008,1,9007,9006,1,'2026-06-16',1,'2026-07-16',NULL,NULL),(9009,1,9008,9007,1,'2026-06-17',1,'2026-07-17',NULL,NULL),(9010,1,9009,9008,1,'2026-06-17',1,'2026-07-17',NULL,NULL),(9011,1,9010,7,1,'2026-06-18',1,'2026-06-18',NULL,NULL);
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
  `cantidad` decimal(12,4) DEFAULT NULL,
  `precio_unitario` bigint DEFAULT NULL,
  `cantidad_pendiente` decimal(12,4) DEFAULT NULL,
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
INSERT INTO `orden_compra_detalle` VALUES (18,6,10.0000,220000,0.0000),(18,21,2.0000,45000,0.0000),(9001,2,5.0000,180000,5.0000),(9001,3,2.0000,175000,2.0000),(9002,2,8.0000,220000,0.0000),(9002,3,4.0000,145000,0.0000),(9003,3,3.0000,150000,3.0000),(9004,2,12.0000,190000,0.0000),(9004,3,6.0000,140000,0.0000),(9005,6,10.0000,220000,10.0000),(9005,21,2.0000,45000,2.0000),(9007,57,1.7500,40000,0.5000),(9008,60,1.7500,40000,0.5000),(9009,63,1.7500,40000,0.5000),(9010,66,1.7500,40000,0.5000),(9011,21,5.0000,35000,1.0000),(9011,28,10.0000,38000,0.0000);
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
  `id_cliente` int unsigned NOT NULL,
  `id_vehiculo` int unsigned NOT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `origen` enum('NORMAL','RECLAMO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'NORMAL',
  `idreclamo_servicio` int DEFAULT NULL,
  PRIMARY KEY (`idorden_trabajo`),
  KEY `orden_trabajo_FKIndex1` (`id_usuario`),
  KEY `orden_trabajo_FKIndex2` (`idpresupuesto_servicio`),
  KEY `orden_trabajo_FKIndex4` (`tecnico_responsable`),
  KEY `orden_trabajo_FKIndex5` (`idtrabajos`),
  KEY `fk_orden_trabajo_clientes1_idx` (`id_cliente`),
  KEY `fk_orden_trabajo_vehiculos1_idx` (`id_vehiculo`),
  CONSTRAINT `fk_empleados` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_equipoTrabajo` FOREIGN KEY (`idtrabajos`) REFERENCES `equipo_trabajo` (`id_equipo`),
  CONSTRAINT `fk_orden_trabajo_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_orden_trabajo_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  CONSTRAINT `fk_prespuestoServicio` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9010 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo`
--

LOCK TABLES `orden_trabajo` WRITE;
/*!40000 ALTER TABLE `orden_trabajo` DISABLE KEYS */;
INSERT INTO `orden_trabajo` VALUES (27,1,6,18,1,11,17,1,'2026-05-31 21:45:22','2026-05-31 21:46:00',2,'cambio simple','2026-06-01 00:46:00',1,'2026-05-31 21:45:22','NORMAL',NULL),(28,NULL,NULL,NULL,1,11,17,1,'2026-06-01 20:53:22',NULL,0,NULL,'2026-06-01 23:54:35',1,'2026-06-01 20:53:22','RECLAMO',12),(29,NULL,NULL,NULL,1,11,17,1,'2026-06-01 20:54:46',NULL,0,NULL,'2026-06-01 23:54:52',1,'2026-06-01 20:54:46','RECLAMO',12),(30,1,6,NULL,1,11,17,1,'2026-06-01 20:56:26','2026-06-03 20:46:20',2,'asd','2026-06-03 23:46:20',1,'2026-06-01 20:56:26','RECLAMO',12),(31,1,6,21,1,19,18,1,'2026-06-02 22:01:11',NULL,0,'verificar y ajustar partes flojas en el motor','2026-06-03 01:11:51',1,'2026-06-02 22:01:11','NORMAL',NULL),(9001,1,1,9001,1,1,17,1,'2026-02-05 08:00:00',NULL,1,'Pendiente de asignacion final de repuestos','2026-02-05 11:00:00',1,'2026-02-05 08:00:00','NORMAL',NULL),(9002,1,1,9002,1,1,17,1,'2026-03-12 08:00:00','2026-03-12 17:00:00',2,'Trabajo terminado y entregado','2026-03-12 20:00:00',1,'2026-03-12 08:00:00','NORMAL',NULL),(9003,1,1,9003,1,1,17,1,'2026-04-16 13:30:00',NULL,0,'Orden anulada por presupuesto rechazado','2026-04-16 18:00:00',1,'2026-04-16 13:30:00','NORMAL',NULL),(9004,1,7,9004,1,11,17,1,'2026-06-06 20:42:57',NULL,0,'','2026-06-09 00:21:05',1,'2026-06-06 20:42:57','NORMAL',NULL),(9006,1,1,9007,1,24,22,1,'2026-06-16 22:19:49',NULL,1,'TEST_DECIMAL_20260616_211949 OT','2026-06-17 01:19:49',NULL,'2026-06-16 22:19:49','NORMAL',NULL),(9007,1,1,9008,1,25,23,1,'2026-06-16 22:21:34',NULL,1,'TEST_DECIMAL_20260616_212134 OT','2026-06-17 01:21:34',NULL,'2026-06-16 22:21:34','NORMAL',NULL),(9008,1,1,9009,1,26,24,1,'2026-06-17 18:42:32',NULL,1,'TEST_DECIMAL_20260617_174232 OT','2026-06-17 21:42:32',NULL,'2026-06-17 18:42:32','NORMAL',NULL),(9009,1,1,9010,1,27,25,1,'2026-06-17 18:44:25',NULL,1,'TEST_DECIMAL_20260617_174425 OT','2026-06-17 21:44:25',NULL,'2026-06-17 18:44:25','NORMAL',NULL);
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
  `cantidad` decimal(12,4) NOT NULL,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `idorden_trabajo` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  PRIMARY KEY (`id_detalle_ot`),
  KEY `fk_orden_trabajo_detalle_orden_trabajo1_idx` (`idorden_trabajo`),
  KEY `fk_orden_trabajo_detalle_articulos1_idx` (`id_articulo`),
  CONSTRAINT `fk_orden_trabajo_detalle_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_orden_trabajo_detalle_orden_trabajo1` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo_detalle`
--

LOCK TABLES `orden_trabajo_detalle` WRITE;
/*!40000 ALTER TABLE `orden_trabajo_detalle` DISABLE KEYS */;
INSERT INTO `orden_trabajo_detalle` VALUES (63,1.0000,80000.00,80000.00,27,37),(64,2.0000,50000.00,100000.00,27,4),(66,1.0000,50000.00,50000.00,31,36),(67,4.0000,35000.00,140000.00,31,33),(68,1.0000,80000.00,80000.00,31,37),(69,1.0000,0.00,0.00,30,4),(70,1.0000,0.00,0.00,30,37),(9001,1.0000,220000.00,220000.00,9001,2),(9002,2.0000,180000.00,360000.00,9001,3),(9003,2.0000,220000.00,440000.00,9002,2),(9004,2.0000,160000.00,320000.00,9002,3),(9005,3.0000,140000.00,420000.00,9003,3),(9006,1.0000,150000.00,150000.00,9004,35),(9007,2.0000,350000.00,700000.00,9004,6),(9008,1.0000,80000.00,80000.00,9004,37),(9009,2.0000,50000.00,100000.00,9004,4),(9013,1.0000,80000.00,80000.00,9006,59),(9014,0.7500,55000.00,41250.00,9006,57),(9016,1.0000,80000.00,80000.00,9007,62),(9017,0.7500,55000.00,41250.00,9007,60),(9019,1.0000,80000.00,80000.00,9008,65),(9020,0.7500,55000.00,41250.00,9008,63),(9022,1.0000,80000.00,80000.00,9009,68),(9023,0.7500,55000.00,41250.00,9009,66);
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
  `updatedby` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`idpedido_cabecera`),
  KEY `pedido_cabecera_FKIndex1` (`id_usuario`),
  KEY `pedido_cabecera_FKIndex2` (`id_sucursal`),
  CONSTRAINT `fk_sucursalesPedCab` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_usuarioPedCab` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9012 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cabecera`
--

LOCK TABLES `pedido_cabecera` WRITE;
/*!40000 ALTER TABLE `pedido_cabecera` DISABLE KEYS */;
INSERT INTO `pedido_cabecera` VALUES (14,1,1,'2026-06-06 08:52:23',2,'2026-06-06 08:53:54','1'),(9001,1,1,'2026-01-08 09:10:00',0,'2026-06-07 15:03:06','1'),(9002,1,1,'2026-02-12 10:30:00',2,'2026-02-13 08:00:00','1'),(9003,1,1,'2026-03-20 14:05:00',0,'2026-03-21 11:00:00','1'),(9004,1,1,'2026-04-18 16:22:00',2,'2026-04-19 09:40:00','1'),(9005,2,7,'2026-06-07 15:40:52',0,'2026-06-07 15:43:16','7'),(9007,1,1,'2026-06-16 22:19:49',1,NULL,NULL),(9008,1,1,'2026-06-16 22:21:34',1,NULL,NULL),(9009,1,1,'2026-06-17 18:42:32',1,NULL,NULL),(9010,1,1,'2026-06-17 18:44:25',1,NULL,NULL),(9011,1,1,'2026-06-18 21:12:11',2,'2026-06-18 21:16:34','1');
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
  `cantidad` decimal(12,4) DEFAULT NULL,
  `stock_actual` decimal(12,4) DEFAULT NULL,
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
INSERT INTO `pedido_detalle` VALUES (14,6,10.0000,0.0000),(14,21,2.0000,10.0000),(9001,2,6.0000,12.0000),(9001,3,3.0000,7.0000),(9002,2,10.0000,5.0000),(9002,3,4.0000,9.0000),(9003,2,2.0000,11.0000),(9004,3,12.0000,2.0000),(9005,27,1.0000,0.0000),(9007,57,1.7500,10.7500),(9008,60,1.7500,10.7500),(9009,63,1.7500,10.7500),(9010,66,1.7500,10.7500),(9011,21,5.0000,9.0000),(9011,28,10.0000,0.0000);
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
  `clave` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_permiso`)
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (1,'servicio.recepcion.crear','Registrar solicitud de servicio'),(2,'servicio.recepcion.ver','Ver recepciones de servicio'),(3,'servicio.presupuesto.crear','Crear presupuesto de servicio'),(4,'servicio.presupuesto.aprobar','Aprobar presupuesto de servicio'),(5,'servicio.ot.generar','Generar orden de trabajo'),(6,'servicio.ot.asignar_tecnico','Asignar técnico a OT'),(7,'servicio.registro.crear','Registrar servicio finalizado'),(8,'servicio.registro.anular','Anular registro de servicio'),(9,'servicio.reclamo.crear','Registrar reclamo de cliente'),(10,'servicio.reclamo.cerrar','Cerrar reclamo de cliente'),(11,'servicio.reclamo.ver','Ver reclamos de clientes'),(12,'usuarios.ver','Ver usuarios'),(13,'usuarios.crear','Crear usuarios'),(14,'usuarios.editar','Editar usuarios'),(15,'usuarios.eliminar','Eliminar usuarios'),(16,'roles.ver','Ver roles'),(17,'roles.editar','Editar roles'),(18,'empresa.ver','Ver datos de la empresa'),(19,'empresa.editar','Editar datos de la empresa'),(20,'sucursal.ver','Ver sucursales'),(21,'sucursal.editar','Editar sucursales'),(22,'cliente.ver','Ver clientes'),(23,'cliente.crear','Registrar clientes'),(24,'cliente.editar','Editar clientes'),(25,'vehiculo.ver','Ver vehículos'),(26,'vehiculo.crear','Registrar vehículos'),(27,'vehiculo.editar','Editar vehículos'),(47,'compra.crear','Registrar compra'),(48,'compra.editar','Editar documentos de compra'),(49,'compra.anular','Anular documentos de compra'),(50,'compra.ver','Ver compras'),(51,'proveedor.ver','Ver proveedores'),(52,'proveedor.crear','Registrar proveedores'),(53,'proveedor.editar','Editar proveedores'),(54,'stock.ver','Ver stock'),(55,'stock.ajustar','Ajustar stock'),(56,'stock.movimiento.ver','Ver movimientos de stock'),(57,'servicio.reportes.ver','Ver reportes de servicios'),(58,'compra.reportes.ver','Ver reportes de compras'),(59,'stock.reportes.ver','Ver reportes de stock'),(60,'servicio.presupuesto.ver','Ver presupuestos de servicio'),(61,'servicio.ot.ver','Ver órdenes de trabajo'),(62,'servicio.registro.ver','Ver registros de servicio'),(63,'servicio.ot.cerrar','Cerrar orden de trabajo'),(64,'servicio.ot.anular','Anular orden de trabajo'),(71,'stock.administrar','Administrar parámetros de stock'),(120,'compra.pedido.ver','Ver pedidos de compra'),(121,'compra.pedido.crear','Crear pedidos de compra'),(122,'compra.presupuesto.ver','Ver presupuestos de compra'),(123,'compra.presupuesto.crear','Crear presupuesto de compra'),(124,'compra.oc.ver','Ver órdenes de compra'),(125,'compra.oc.crear','Crear órdenes de compra'),(126,'compra.factura.ver','Ver facturas de compra'),(127,'compra.factura.crear','Registrar facturas de compra'),(128,'compra.remision.ver','Ver remisiones'),(129,'compra.remision.crear','Registrar remisiones'),(130,'compra.nota.ver','Ver notas de crédito y débito'),(131,'compra.nota.crear','Registrar notas de crédito y débito'),(160,'inventario.ver','Ver inventarios'),(161,'inventario.crear','Generar Inventarios'),(162,'inventario.editar','Editar inventarios'),(164,'compra.presupuesto.anular','Anular Presupuesto de compra'),(165,'servicio.presupuesto.anular','Anular Presupuesto de servicio'),(166,'servicio.promocion.ver','Ver promociones'),(167,'servicio.descuento.ver','Ver descuentos'),(168,'compra.transferencia.crear','Crear transferencias'),(169,'compra.transferencia.ver','Ver transferencias'),(170,'compra.transferencia.anular','Anular transferencias'),(171,'articulo.crear','Crear articulo'),(172,'articulo.ver','Listar articulos'),(173,'articulo.editar','Editar articulos'),(175,'articulo.eliminar','Eliminar articulos'),(176,'sucursal.crear','Crear Sucursales'),(177,'sucursal.eliminar','Eliminar Sucursales'),(178,'proveedor.eliminar','Eliminar proveedores'),(179,'cliente.eliminar','Eliminar clientes'),(180,'vehiculo.eliminar','Eliminar vehículo'),(181,'empleado.ver','Ver empleados'),(182,'empleado.editar','Editar empleados'),(183,'empleado.crear','Crear empleados'),(184,'empleado.eliminar','Eliminar empleados'),(185,'usuarios.asignarlocal','Asignar local a usuarios'),(186,'usuarios.asignarrol','Asignar rol a usuarios'),(187,'usuarios.permisos_por_roles','Asignar permisos a roles '),(188,'compra.pedido.anular','Anular Pedidos de Compra'),(189,'compra.oc.anular','Anular órdenes de compra'),(190,'compra.factura.anular','Anular facturas de compra'),(191,'compra.nota.anular','Anular notas de crédito y débito'),(192,'compra.remision.anular','Anular remisiones'),(193,'compra.transferencia.recibir','Recibir transferencias'),(194,'inventario.ajustar','Ajustar stock en inventarios'),(195,'servicio.descuento.editar','Editar descuentos'),(196,'servicio.descuento.asignarClientes','Asignar descuentos a Clientes'),(197,'servicio.descuento.crear','Crear descuentos'),(198,'servicio.promocion.editar','Editar promociones'),(199,'servicio.ver','Ver Servicios'),(200,'mantenimiento.ver','Mantenimiento de referenciales'),(201,'servicio.promocion.crear','Crear Promociones'),(202,'servicio.reclamo.anular','Anular reclamo de cliente'),(203,'servicio.diagnostico.crear','Crear diagnostico'),(204,'servicio.diagnostico.ver','Ver Diagnostico'),(205,'servicio.diagnostico.anular','Anular Diagnostico'),(206,'inventario.anular','Anualar inventario'),(207,'servicio.recepcion.anular','Anular recepciones de servicio'),(208,'cargo.crear','Registrar cargos'),(209,'cargo.editar','Editar cargos'),(210,'cargo.eliminar','Eliminar cargos'),(211,'cargo.ver','Ver cargos'),(212,'servicio.regla_comercial.ver','Ver reglas comerciales'),(213,'servicio.regla_comercial.crear','Crear reglas comerciales'),(214,'servicio.regla_comercial.editar','Editar reglas comerciales'),(215,'equipo.crear','Ver equipos'),(216,'equipo.editar','Editar equipos'),(217,'equipo.eliminar','Eliminar equipos'),(218,'roles.eliminar','Eliminar roles'),(219,'roles.crear','Crear roles'),(220,'permisos.asignar_permisos','Asignar permisos a roles '),(221,'reportes.articulos.ver','Ver informe de articulos'),(222,'reportes.proveedores.ver','Ver informe de proveedores'),(223,'reportes.sucursales.ver','Ver informe de sucursales'),(224,'reportes.clientes.ver','Ver informe de clientes'),(225,'reportes.vehiculos.ver','Ver informe de vehiculos'),(226,'reportes.empleados.ver','Ver informe de empleados'),(227,'reportes.pedidos.ver','Ver informe de pedidos'),(228,'reportes.presupuestos_compra.ver','Ver informe de presupuestos de compra'),(229,'reportes.ordenes_compra.ver','Ver informe de ordenes de compra'),(230,'reportes.compras.ver','Ver informe de compras'),(231,'reportes.libro_compras.ver','Ver informe libro de compras'),(232,'reportes.transferencias.ver','Ver informe de transferencias'),(233,'reportes.stock.ver','Ver informe de stock'),(234,'reportes.movimientos_stock.ver','Ver informe de movimientos de stock'),(235,'reportes.recepcion_servicio.ver','Ver informe de recepcion de servicios'),(236,'reportes.presupuesto_servicio.ver','Ver informe de presupuestos de servicio'),(237,'reportes.orden_trabajo.ver','Ver informe de ordenes de trabajo'),(238,'reportes.registro_servicio.ver','Ver informe de registros de servicio'),(239,'servicio.insumo.crear','Registrar insumos utilizados'),(240,'servicio.insumo.anular','Anular insumos utilizados'),(241,'servicio.insumo.ver','Ver insumos utilizados');
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
) ENGINE=InnoDB AUTO_INCREMENT=9011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_compra`
--

LOCK TABLES `presupuesto_compra` WRITE;
/*!40000 ALTER TABLE `presupuesto_compra` DISABLE KEYS */;
INSERT INTO `presupuesto_compra` VALUES (11,1,9001,1,'2026-06-06',2,'2026-06-18',NULL,NULL,2290000.00,14),(9001,1,9001,1,'2026-01-09',1,'2026-01-25',NULL,NULL,1250000.00,9001),(9002,1,9002,1,'2026-02-13',2,'2026-02-28',1,'2026-02-14 09:00:00',2340000.00,9002),(9003,1,9003,1,'2026-03-22',0,'2026-04-05',1,'2026-03-23 10:00:00',450000.00,9003),(9004,1,9001,1,'2026-04-20',2,'2026-05-05',1,'2026-04-21 08:30:00',3120000.00,9004),(9006,1,9005,1,'2026-06-16',1,'2026-07-16',NULL,NULL,70000.00,9007),(9007,1,9006,1,'2026-06-16',1,'2026-07-16',NULL,NULL,70000.00,9008),(9008,1,9007,1,'2026-06-17',1,'2026-07-17',NULL,NULL,70000.00,9009),(9009,1,9008,1,'2026-06-17',1,'2026-07-17',NULL,NULL,70000.00,9010),(9010,1,7,1,'2026-06-18',2,'2026-06-25',NULL,NULL,555000.00,9011);
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
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `aplica_a` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TOTAL',
  `base_aplicada` decimal(12,2) NOT NULL DEFAULT '0.00',
  `monto_aplicado` decimal(10,2) NOT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `presupuesto_descuento` VALUES (18,4,1,'PORCENTAJE',10.00,'TOTAL',0.00,16500.00,'Descuento por Apertura','2026-05-31 21:44:28'),(19,5,1,'PORCENTAJE',50.00,'PRODUCTO',75000.00,37500.00,'test vip','2026-06-01 23:34:50'),(9005,4,1,'PORCENTAJE',10.00,'TOTAL',511800.00,51180.00,'Descuento por Apertura','2026-06-14 20:22:18');
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
  `cantidad` decimal(12,4) DEFAULT NULL,
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
INSERT INTO `presupuesto_detalle` VALUES (11,6,10.0000,220000.00,2200000.00),(11,21,2.0000,45000.00,90000.00),(9001,2,5.0000,180000.00,900000.00),(9001,3,2.0000,175000.00,350000.00),(9002,2,8.0000,220000.00,1760000.00),(9002,3,4.0000,145000.00,580000.00),(9003,3,3.0000,150000.00,450000.00),(9004,2,12.0000,190000.00,2280000.00),(9004,3,6.0000,140000.00,840000.00),(9006,57,1.7500,40000.00,70000.00),(9007,60,1.7500,40000.00,70000.00),(9008,63,1.7500,40000.00,70000.00),(9009,66,1.7500,40000.00,70000.00),(9010,21,5.0000,35000.00,175000.00),(9010,28,10.0000,38000.00,380000.00);
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
  `cantidad` decimal(12,4) NOT NULL,
  `preciouni` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detalle_presupuesto`),
  KEY `fk_presupuesto_detalleservicio_presupuesto_servicio1_idx` (`idpresupuesto_servicio`),
  KEY `fk_presupuesto_detalleservicio_articulos1_idx` (`id_articulo`),
  KEY `fk_presupuesto_detalleservicio_diagnostico_detalle1_idx` (`id_diagnostico_detalle`),
  CONSTRAINT `fk_presupuesto_detalleservicio_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_presupuesto_detalleservicio_diagnostico_detalle1` FOREIGN KEY (`id_diagnostico_detalle`) REFERENCES `diagnostico_detalle` (`id_diagnostico_detalle`) ON DELETE SET NULL,
  CONSTRAINT `fk_presupuesto_detalleservicio_presupuesto_servicio1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9026 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalleservicio`
--

LOCK TABLES `presupuesto_detalleservicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` DISABLE KEYS */;
INSERT INTO `presupuesto_detalleservicio` VALUES (36,18,37,NULL,1.0000,80000.00,80000.00),(37,18,4,NULL,2.0000,50000.00,100000.00),(38,19,37,NULL,1.0000,80000.00,80000.00),(39,19,4,NULL,2.0000,50000.00,100000.00),(40,20,35,NULL,1.0000,150000.00,150000.00),(41,21,36,NULL,1.0000,50000.00,50000.00),(42,21,33,NULL,4.0000,35000.00,140000.00),(43,21,37,NULL,1.0000,80000.00,80000.00),(9001,9001,2,NULL,1.0000,220000.00,220000.00),(9002,9001,3,NULL,2.0000,180000.00,360000.00),(9003,9002,2,NULL,2.0000,220000.00,440000.00),(9004,9002,3,NULL,2.0000,160000.00,320000.00),(9005,9003,3,NULL,3.0000,140000.00,420000.00),(9006,9004,35,NULL,1.0000,150000.00,150000.00),(9007,9004,6,NULL,2.0000,350000.00,700000.00),(9008,9004,37,NULL,1.0000,80000.00,80000.00),(9009,9004,4,NULL,2.0000,50000.00,100000.00),(9010,9005,51,NULL,1.0000,80000.00,80000.00),(9011,9005,46,NULL,1.0000,939000.00,939000.00),(9012,9005,35,NULL,1.0000,150000.00,150000.00),(9013,9005,6,NULL,1.0000,350000.00,350000.00),(9014,9005,13,NULL,1.0000,50000.00,50000.00),(9015,9005,41,NULL,1.0000,38000.00,38000.00),(9018,9007,59,42,1.0000,80000.00,80000.00),(9019,9007,57,42,0.7500,55000.00,41250.00),(9020,9008,62,43,1.0000,80000.00,80000.00),(9021,9008,60,43,0.7500,55000.00,41250.00),(9022,9009,65,44,1.0000,80000.00,80000.00),(9023,9009,63,44,0.7500,55000.00,41250.00),(9024,9010,68,45,1.0000,80000.00,80000.00),(9025,9010,66,45,0.7500,55000.00,41250.00);
/*!40000 ALTER TABLE `presupuesto_detalleservicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto_promocion`
--

DROP TABLE IF EXISTS `presupuesto_promocion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_promocion` (
  `id_presupuesto_promocion` int NOT NULL AUTO_INCREMENT,
  `idpresupuesto_servicio` int unsigned NOT NULL,
  `id_detalle_presupuesto` int NOT NULL,
  `id_promocion` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` decimal(12,2) NOT NULL DEFAULT '1.00',
  `monto_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_aplicado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_aplicacion` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presupuesto_promocion`),
  UNIQUE KEY `uq_prespromo_detalle_promocion` (`id_detalle_presupuesto`,`id_promocion`),
  KEY `idx_presupuesto` (`idpresupuesto_servicio`),
  KEY `idx_detalle` (`id_detalle_presupuesto`),
  KEY `idx_promocion` (`id_promocion`),
  KEY `idx_articulo` (`id_articulo`),
  CONSTRAINT `fk_prespromo_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_prespromo_detalle` FOREIGN KEY (`id_detalle_presupuesto`) REFERENCES `presupuesto_detalleservicio` (`id_detalle_presupuesto`) ON DELETE CASCADE,
  CONSTRAINT `fk_prespromo_presupuesto` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_prespromo_promocion` FOREIGN KEY (`id_promocion`) REFERENCES `promociones` (`id_promocion`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_promocion`
--

LOCK TABLES `presupuesto_promocion` WRITE;
/*!40000 ALTER TABLE `presupuesto_promocion` DISABLE KEYS */;
INSERT INTO `presupuesto_promocion` VALUES (6,18,36,5,37,1.00,15000.00,15000.00,'2026-05-31 21:44:28'),(7,19,39,6,4,2.00,12500.00,25000.00,'2026-06-01 23:34:50'),(8,9004,9009,6,4,2.00,12500.00,25000.00,'2026-06-06 20:42:25'),(9,9005,9010,5,51,1.00,15000.00,15000.00,'2026-06-14 20:22:18'),(10,9005,9011,4,46,1.00,751200.00,751200.00,'2026-06-14 20:22:18'),(11,9005,9012,5,35,1.00,15000.00,15000.00,'2026-06-14 20:22:18'),(12,9005,9013,4,6,1.00,280000.00,280000.00,'2026-06-14 20:22:18'),(13,9005,9014,5,13,1.00,15000.00,15000.00,'2026-06-14 20:22:18'),(14,9005,9015,3,41,1.00,19000.00,19000.00,'2026-06-14 20:22:18');
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
  `id_cliente` int unsigned NOT NULL,
  `id_vehiculo` int unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `fecha_venc` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_descuento` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_final` decimal(12,2) NOT NULL DEFAULT '0.00',
  `origen` enum('PRELIMINAR','DIAGNOSTICO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DIAGNOSTICO',
  `convertido_desde` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idpresupuesto_servicio`),
  KEY `presupuesto_FKIndex3` (`id_usuario`),
  KEY `fk_presupuesto_servicio_diagnostico_servicio1_idx` (`id_diagnostico`),
  KEY `fk_presupuesto_sucursal` (`id_sucursal`),
  KEY `fk_presupuesto_servicio_clientes1_idx` (`id_cliente`),
  KEY `fk_presupuesto_servicio_vehiculos1_idx` (`id_vehiculo`),
  KEY `fk_presupuesto_convertido_desde` (`convertido_desde`),
  CONSTRAINT `fk_presupuesto_convertido_desde` FOREIGN KEY (`convertido_desde`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_presupuesto_servicio_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_presupuesto_servicio_diagnostico_servicio1` FOREIGN KEY (`id_diagnostico`) REFERENCES `diagnostico_servicio` (`id_diagnostico`) ON DELETE SET NULL,
  CONSTRAINT `fk_presupuesto_servicio_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  CONSTRAINT `fk_presupuesto_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_presupuesto_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9011 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_servicio`
--

LOCK TABLES `presupuesto_servicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_servicio` DISABLE KEYS */;
INSERT INTO `presupuesto_servicio` VALUES (18,25,1,1,11,17,'2026-05-31',3,'2026-06-06',180000.00,16500.00,148500.00,'DIAGNOSTICO',NULL),(19,NULL,1,1,11,17,'2026-06-01',1,'2026-06-30',180000.00,37500.00,117500.00,'PRELIMINAR',NULL),(20,NULL,1,1,19,18,'2026-06-02',1,'2026-06-02',150000.00,0.00,150000.00,'PRELIMINAR',NULL),(21,28,1,1,19,18,'2026-06-02',0,'2026-06-02',270000.00,0.00,270000.00,'DIAGNOSTICO',NULL),(9001,NULL,1,1,1,17,'2026-02-04',1,'2026-02-15',580000.00,0.00,580000.00,'DIAGNOSTICO',NULL),(9002,NULL,1,1,1,17,'2026-03-11',4,'2026-03-25',760000.00,50000.00,710000.00,'DIAGNOSTICO',NULL),(9003,NULL,1,1,1,17,'2026-04-15',0,'2026-04-30',420000.00,0.00,420000.00,'DIAGNOSTICO',NULL),(9004,29,1,1,11,17,'2026-06-06',2,'2026-06-06',1030000.00,0.00,1005000.00,'DIAGNOSTICO',NULL),(9005,32,1,1,19,18,'2026-06-14',1,'2026-06-20',1607000.00,51180.00,460620.00,'DIAGNOSTICO',NULL),(9007,34,1,1,24,22,'2026-06-16',2,'2026-07-16',121250.00,0.00,121250.00,'DIAGNOSTICO',NULL),(9008,35,1,1,25,23,'2026-06-16',2,'2026-07-16',121250.00,0.00,121250.00,'DIAGNOSTICO',NULL),(9009,36,1,1,26,24,'2026-06-17',2,'2026-07-17',121250.00,0.00,121250.00,'DIAGNOSTICO',NULL),(9010,37,1,1,27,25,'2026-06-17',2,'2026-07-17',121250.00,0.00,121250.00,'DIAGNOSTICO',NULL);
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
INSERT INTO `promocion_producto` VALUES (2,4),(4,6),(6,4),(13,5),(28,6),(31,6),(32,6),(33,3),(35,5),(36,5),(37,5),(41,3),(46,3),(46,4),(51,5);
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
  `id_sucursal` int unsigned DEFAULT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id_promocion`),
  KEY `promociones_FKIndex1` (`id_usuario_crea`),
  KEY `promociones_FKIndex2` (`id_usuario_modifica`),
  KEY `fk_promociones_sucursal` (`id_sucursal`),
  KEY `idx_promociones_filtros` (`estado`,`fecha_inicio`,`fecha_fin`,`id_sucursal`),
  CONSTRAINT `fk_promociones_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `promocionesUsucrea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `promocionesUsumodi` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES (3,1,1,NULL,'Aceites','Promo Aceites','PORCENTAJE',50.00,'2026-05-01','2026-06-30','2026-05-30 18:56:27','2026-06-14 20:20:38',1),(4,1,1,NULL,'Agotar stock','Agotar stock','PORCENTAJE',80.00,'2026-05-01','2026-06-30','2026-05-30 19:43:05','2026-06-14 20:17:34',1),(5,1,1,NULL,'promo Servicios','promo Servicios','MONTO_FIJO',15000.00,'2026-05-01','2026-06-30','2026-05-30 19:44:46','2026-06-14 20:13:55',1),(6,1,1,NULL,'Descuento sin frenos','Frenos menos 25%','PORCENTAJE',25.00,'2026-06-01','2026-06-30','2026-06-01 22:05:11','2026-06-14 15:13:10',0);
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
  `razon_social` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idproveedores`),
  KEY `proveedores_FKIndex1` (`id_ciudad`),
  CONSTRAINT `fk_ciudadesPro` FOREIGN KEY (`id_ciudad`) REFERENCES `ciudades` (`id_ciudad`)
) ENGINE=InnoDB AUTO_INCREMENT=9010 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (2,9,'Auto Partes Guaraní SRL','80023456-1','0982-234567','Av. Artigas 1234','contacto1@autopartesg.com.py',1),(3,1,'Importadora del Motor S.A.','80034567-8','0983-345678','Ruta Transchaco Km 10','info@importmotor.com.py',1),(4,1,'Lubricantes Paraguay SRL','80045678-9','0984-456789','Av. Madame Lynch 456','ventas@lubripar.com.py',1),(5,1,'Distribuidora del Automotor','80056789-0','0985-567890','Av. Mariscal López 789','contacto@distauto.com.py',1),(6,1,'Neumáticos del Sur S.A.','80067890-1','0986-678901','Av. Fernando de la Mora 321','ventas@neumaticosdelsur.com.py',1),(7,1,'Repuestos Japón Import','80078901-2','0981-789012','Barrio San Vicente','info@repuestosjapon.com.py',1),(8,1,'Casa del Filtro SRL','80089012-3','0982-890123','Av. Boggiani 654','ventas@casafiltro.com.py',1),(9,1,'MotorParts Paraguay','80090123-4','0983-901234','Zona Mercado 4','contacto@motorparts.com.py',1),(10,1,'Distribuidora Técnica Automotriz','80101234-5','0984-012345','Av. Defensores del Chaco 987','info@dta.com.py',1),(11,1,'Repuestos y Servicios del Este SRL','80123456-7','0985-112233','Av. Acceso Sur Km 12','ventas@repuestoseste.com.py',1),(9001,1,'Repuestos Central S.A.','80012345-6','021555100','Avda. Artigas 1540','ventas@repuestoscentral.com.py',0),(9002,1,'Autopartes del Sur S.R.L.','80045678-1','021555220','Ruta Acceso Sur Km 12','contacto@autopartesdelsur.com.py',1),(9003,1,'Lubricantes Asuncion S.A.','80078912-3','021555330','Avda. Eusebio Ayala 2990','pedidos@lubricantesasuncion.com.py',1),(9005,1,'TEST_DECIMAL_20260616_211949 PROVEEDOR','9211949-1','0981000000','Direccion test decimal','decimal@test.local',1),(9006,1,'TEST_DECIMAL_20260616_212134 PROVEEDOR','9212134-1','0981000000','Direccion test decimal','decimal@test.local',1),(9007,1,'TEST_DECIMAL_20260617_174232 PROVEEDOR','9174232-1','0981000000','Direccion test decimal','decimal@test.local',1),(9008,1,'TEST_DECIMAL_20260617_174425 PROVEEDOR','9174425-1','0981000000','Direccion test decimal','decimal@test.local',1);
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
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
  `ruta_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_subida` datetime DEFAULT NULL,
  PRIMARY KEY (`id_foto`),
  KEY `recepcion_fotos_FKIndex1` (`id_recepcion`),
  CONSTRAINT `fk_recepcionFo` FOREIGN KEY (`id_recepcion`) REFERENCES `recepcion_servicio` (`idrecepcion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `kilometraje` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nivel_combustible` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_exterior` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `objetos_vehiculo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_servicio` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `area_problema` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accesorios` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado` tinyint NOT NULL DEFAULT '1',
  `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `id_sucursal` int unsigned NOT NULL,
  `origen` enum('NORMAL','RECLAMO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'NORMAL',
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
) ENGINE=InnoDB AUTO_INCREMENT=9010 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion_servicio`
--

LOCK TABLES `recepcion_servicio` WRITE;
/*!40000 ALTER TABLE `recepcion_servicio` DISABLE KEYS */;
INSERT INTO `recepcion_servicio` VALUES (21,1,17,11,'2026-05-31 21:41:39','2026-05-31 21:46:00','155000','1/2','sin_danos','','reparacion','frenos','normal','llave,herramientas,rueda_auxilio','cambio de pastillas de freno',3,'2026-05-31 21:41:39','2026-05-31 21:46:00',1,'NORMAL',NULL),(22,1,17,11,'2026-05-31 21:48:18','2026-06-03 20:46:20','155070','3/4','sin_danos','','garantia','motor','normal','llave,herramientas','reclamo por problemas en repuesto',3,'2026-05-31 21:48:18','2026-06-03 20:46:20',1,'RECLAMO',12),(23,1,18,19,'2026-06-02 16:51:18',NULL,'95000','1/4','sin_danos','','mantenimiento','motor','normal','llave,herramientas,rueda_auxilio','mantenimiento del motor, cambio de aceite etc',1,'2026-06-02 16:51:18','2026-06-02 16:53:05',1,'NORMAL',NULL),(24,1,17,11,'2026-06-06 08:44:34',NULL,'55000','1/2','rayones','','diagnostico','motor','normal','llave,herramientas','test',2,'2026-06-06 08:44:34','2026-06-08 21:20:36',1,'NORMAL',NULL),(9001,1,17,1,'2026-02-03 08:15:00',NULL,'82000','1/2','sin_danos','','mantenimiento','motor','normal','llave,rueda_auxilio','Mantenimiento preventivo de 80.000 km',1,'2026-02-03 08:15:00','2026-06-14 19:55:00',1,'NORMAL',NULL),(9002,1,17,1,'2026-03-10 10:40:00','2026-03-12 17:00:00','83500','3/4','rayones_leves','','reparacion','frenos','alta','llave','Ruido al frenar y vibracion',3,'2026-03-10 10:40:00','2026-03-12 17:00:00',1,'NORMAL',NULL),(9003,1,17,1,'2026-04-14 09:30:00',NULL,'84850','1/4','sin_danos','','diagnostico','suspension','normal','llave','Revision por golpe en tren delantero',2,'2026-04-14 09:30:00','2026-04-14 11:00:00',1,'NORMAL',NULL),(9004,1,18,19,'2026-06-14 19:59:03',NULL,'95500','1/4','sin_danos','test','diagnostico','otros','normal','llave,llave_repuesto,rueda_auxilio','test circuito',2,'2026-06-14 19:59:03','2026-06-14 20:09:26',1,'NORMAL',NULL),(9006,1,22,24,'2026-06-16 22:19:49',NULL,'1000','1/2','sin_danos','','mantenimiento','motor','normal','llave','TEST_DECIMAL_20260616_211949 recepcion',1,'2026-06-16 22:19:49',NULL,1,'NORMAL',NULL),(9007,1,23,25,'2026-06-16 22:21:34',NULL,'1000','1/2','sin_danos','','mantenimiento','motor','normal','llave','TEST_DECIMAL_20260616_212134 recepcion',1,'2026-06-16 22:21:34',NULL,1,'NORMAL',NULL),(9008,1,24,26,'2026-06-17 18:42:32',NULL,'1000','1/2','sin_danos','','mantenimiento','motor','normal','llave','TEST_DECIMAL_20260617_174232 recepcion',1,'2026-06-17 18:42:32',NULL,1,'NORMAL',NULL),(9009,1,25,27,'2026-06-17 18:44:25',NULL,'1000','1/2','sin_danos','','mantenimiento','motor','normal','llave','TEST_DECIMAL_20260617_174425 recepcion',1,'2026-06-17 18:44:25',NULL,1,'NORMAL',NULL);
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
  `id_cliente` int unsigned DEFAULT NULL,
  `id_vehiculo` int unsigned DEFAULT NULL,
  `fecha_reclamo` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `usuario_registra` int unsigned DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `observacion_cierre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `idorden_trabajo` int unsigned DEFAULT NULL,
  `tipo_reclamo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origen` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prioridad` tinyint DEFAULT NULL,
  `requiere_garantia` tinyint DEFAULT '0',
  PRIMARY KEY (`idreclamo_servicio`),
  KEY `reclamos_FKIndex1` (`usuario_registra`),
  KEY `reclamos_FKIndex2` (`idregistro_servicio`),
  KEY `fk_reclamo_servicio_orden_trabajo1_idx` (`idorden_trabajo`),
  KEY `idx_reclamo_cliente` (`id_cliente`),
  KEY `idx_reclamo_vehiculo` (`id_vehiculo`),
  CONSTRAINT `fk_reclamo_servicio_orden_trabajo1` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_servicioReg` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`),
  CONSTRAINT `fk_usuario` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio`
--

LOCK TABLES `reclamo_servicio` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio` DISABLE KEYS */;
INSERT INTO `reclamo_servicio` VALUES (12,28,1,11,17,'2026-05-31 21:46:47','inconveniente reportado, sonidos al frenar',3,1,'2026-06-03 20:46:20','Servicio registrado',NULL,'REPUESTO','CLIENTE',2,1),(13,30,1,11,17,'2026-06-03 21:49:57','etste',1,1,'2026-06-08 21:32:09','Anulado',NULL,'REPUESTO','CLIENTE',2,1);
/*!40000 ALTER TABLE `reclamo_servicio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reclamo_servicio_detalle`
--

DROP TABLE IF EXISTS `reclamo_servicio_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamo_servicio_detalle` (
  `idreclamo_detalle` int unsigned NOT NULL AUTO_INCREMENT,
  `idreclamo_servicio` int unsigned NOT NULL,
  `id_registro_servicio_detalle` int NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `requiere_garantia` tinyint(1) DEFAULT '0',
  `estado` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idreclamo_detalle`),
  KEY `idx_reclamo_servicio` (`idreclamo_servicio`),
  KEY `idx_registro_servicio_detalle` (`id_registro_servicio_detalle`),
  CONSTRAINT `fk_reclamo_detalle_reclamo` FOREIGN KEY (`idreclamo_servicio`) REFERENCES `reclamo_servicio` (`idreclamo_servicio`),
  CONSTRAINT `fk_reclamo_detalle_registro_detalle` FOREIGN KEY (`id_registro_servicio_detalle`) REFERENCES `registro_servicio_detalle` (`id_registro_servicio_detalle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio_detalle`
--

LOCK TABLES `reclamo_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio_detalle` DISABLE KEYS */;
INSERT INTO `reclamo_servicio_detalle` VALUES (2,12,66,'lado izquierdo se escucha chillido al frenar',1,1),(3,13,71,'test',1,1);
/*!40000 ALTER TABLE `reclamo_servicio_detalle` ENABLE KEYS */;
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
  `id_vehiculo` int unsigned NOT NULL,
  `id_cliente` int unsigned NOT NULL,
  `id_sucursal` int NOT NULL,
  `fecha_servicio` date NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `kilometraje_salida` int unsigned DEFAULT NULL,
  `usuario_registra` int unsigned DEFAULT NULL,
  `estado` tinyint unsigned NOT NULL DEFAULT '1',
  `observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`idregistro_servicio`),
  KEY `registro_servicio_FKIndex1` (`idorden_trabajo`),
  KEY `registro_servicio_FKIndex2` (`usuario_registra`),
  KEY `fk_registro_servicio_vehiculos1_idx` (`id_vehiculo`),
  KEY `fk_registro_servicio_clientes1_idx` (`id_cliente`),
  CONSTRAINT `fk_registro_servicio_clientes1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_registro_servicio_vehiculos1` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  CONSTRAINT `fk_registro_servicioordenTrabajo` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_registro_serviciousuarios` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9010 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio`
--

LOCK TABLES `registro_servicio` WRITE;
/*!40000 ALTER TABLE `registro_servicio` DISABLE KEYS */;
INSERT INTO `registro_servicio` VALUES (28,27,17,11,1,'2026-05-31','2026-05-31 21:46:00',155001,1,3,'sin novedades en particular'),(30,30,17,11,1,'2026-06-03','2026-06-03 20:46:20',85000,1,1,'asd'),(9001,9002,17,1,1,'2026-03-12','2026-03-12 17:10:00',83600,1,1,'Servicio registrado y pendiente de facturacion'),(9002,9002,17,1,1,'2026-05-22','2026-05-22 17:10:00',85600,1,2,'Servicio facturado correctamente'),(9003,9002,17,1,1,'2026-06-01','2026-06-01 12:00:00',86100,1,3,'Cliente reporta reclamo por ruido residual'),(9004,9004,17,11,1,'2026-06-06','2026-06-06 20:43:35',55001,1,0,'finalizado el cambio'),(9006,9006,22,24,1,'2026-06-16','2026-06-16 22:19:49',1005,1,1,'TEST_DECIMAL_20260616_211949 registro'),(9007,9007,23,25,1,'2026-06-16','2026-06-16 22:21:34',1005,1,1,'TEST_DECIMAL_20260616_212134 registro'),(9008,9008,24,26,1,'2026-06-17','2026-06-17 18:42:32',1005,1,1,'TEST_DECIMAL_20260617_174232 registro'),(9009,9009,25,27,1,'2026-06-17','2026-06-17 18:44:25',1005,1,1,'TEST_DECIMAL_20260617_174425 registro');
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
  `cantidad` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `precio_unitario` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `origen` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idregistro_servicio` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  PRIMARY KEY (`id_registro_servicio_detalle`),
  KEY `fk_registro_servicio_detalle_registro_servicio1_idx` (`idregistro_servicio`),
  KEY `fk_registro_servicio_detalle_articulos1_idx` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_registro_servicio1` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=9025 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio_detalle`
--

LOCK TABLES `registro_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `registro_servicio_detalle` DISABLE KEYS */;
INSERT INTO `registro_servicio_detalle` VALUES (65,1.0000,80000.00,80000.00,'OT',28,37),(66,2.0000,50000.00,100000.00,'OT',28,4),(71,1.0000,0.00,0.00,'OT',30,4),(72,1.0000,0.00,0.00,'OT',30,37),(9001,2.0000,220000.00,440000.00,'OT',9001,2),(9002,2.0000,160000.00,320000.00,'OT',9001,3),(9003,1.0000,220000.00,220000.00,'OT',9002,2),(9004,2.0000,165000.00,330000.00,'OT',9002,3),(9005,1.0000,150000.00,150000.00,'OT',9003,3),(9006,1.0000,150000.00,150000.00,'OT',9004,35),(9007,2.0000,350000.00,700000.00,'OT',9004,6),(9008,1.0000,80000.00,80000.00,'OT',9004,37),(9009,2.0000,50000.00,100000.00,'OT',9004,4),(9013,1.0000,80000.00,80000.00,'OT',9006,59),(9014,0.7500,55000.00,41250.00,'OT',9006,57),(9016,1.0000,80000.00,80000.00,'OT',9007,62),(9017,0.7500,55000.00,41250.00,'OT',9007,60),(9019,1.0000,80000.00,80000.00,'OT',9008,65),(9020,0.7500,55000.00,41250.00,'OT',9008,63),(9022,1.0000,80000.00,80000.00,'OT',9009,68),(9023,0.7500,55000.00,41250.00,'OT',9009,66);
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
INSERT INTO `rol_permiso` VALUES (1,1),(1,2),(1,7),(1,9),(1,11),(1,62),(1,171),(1,199),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,9),(7,10),(7,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,47),(7,48),(7,49),(7,50),(7,51),(7,52),(7,53),(7,54),(7,55),(7,56),(7,57),(7,58),(7,59),(7,60),(7,61),(7,62),(7,63),(7,64),(7,71),(7,120),(7,121),(7,122),(7,123),(7,124),(7,125),(7,126),(7,127),(7,128),(7,129),(7,130),(7,131),(7,160),(7,161),(7,162),(7,164),(7,165),(7,166),(7,167),(7,168),(7,169),(7,170),(7,171),(7,172),(7,173),(7,175),(7,176),(7,177),(7,178),(7,179),(7,180),(7,181),(7,182),(7,183),(7,184),(7,185),(7,186),(7,187),(7,188),(7,189),(7,190),(7,191),(7,192),(7,193),(7,194),(7,195),(7,196),(7,197),(7,198),(7,199),(7,200),(7,201),(7,202),(7,203),(7,204),(7,205),(7,206),(7,207),(7,208),(7,209),(7,210),(7,211),(7,218),(7,219),(7,220),(7,221),(7,222),(7,223),(7,224),(7,225),(7,226),(7,227),(7,229),(7,230),(7,235),(7,236),(7,238),(7,239),(7,240),(7,241),(8,1),(8,2),(8,3),(8,4),(8,5),(8,6),(8,7),(8,8),(8,9),(8,10),(8,11),(8,12),(8,13),(8,14),(8,15),(8,16),(8,17),(8,18),(8,19),(8,20),(8,21),(8,22),(8,23),(8,24),(8,25),(8,26),(8,27),(8,47),(8,48),(8,49),(8,50),(8,51),(8,52),(8,53),(8,54),(8,55),(8,56),(8,57),(8,58),(8,59),(8,60),(8,61),(8,62),(8,63),(8,64),(8,71),(8,120),(8,121),(8,122),(8,123),(8,124),(8,125),(8,126),(8,127),(8,128),(8,129),(8,130),(8,131),(8,160),(8,161),(8,162),(9,1),(9,2),(9,3),(9,9),(9,11),(9,22),(9,23),(9,25),(9,26),(10,1),(10,2),(10,3),(10,4),(10,5),(10,6),(10,7),(10,8),(10,9),(10,10),(10,11),(10,14),(10,22),(10,23),(10,24),(10,25),(10,26),(10,27),(10,57),(10,60),(10,61),(10,62),(10,63),(10,64),(10,165),(10,166),(10,167),(10,179),(10,180),(10,181),(10,182),(10,183),(10,184),(10,195),(10,196),(10,197),(10,198),(10,199),(10,201),(10,202),(11,5),(11,6),(11,61),(11,64),(12,12),(12,13),(12,14),(12,15),(12,22),(12,23),(12,24),(12,47),(12,50),(12,51),(12,52),(12,53),(12,54),(12,56),(12,59),(12,120),(12,121),(12,122),(12,123),(12,124),(12,125),(12,126),(12,127),(12,128),(12,129),(12,130),(12,131),(12,167),(12,168),(12,169),(12,171),(12,172),(12,173),(12,178),(12,179),(12,185),(12,186),(12,187),(12,188),(12,193),(12,199),(12,200),(12,201),(12,223),(12,224),(12,227),(12,229),(12,230),(13,14),(13,20),(13,21),(13,47),(13,48),(13,49),(13,50),(13,51),(13,52),(13,53),(13,58),(13,120),(13,121),(13,122),(13,123),(13,124),(13,125),(13,126),(13,127),(13,128),(13,129),(13,130),(13,131),(13,160),(13,161),(13,162),(13,164),(13,168),(13,169),(13,170),(13,176),(13,177),(13,178),(13,188),(13,189),(13,190),(13,191),(13,192),(13,193),(13,194),(14,2),(14,11),(14,12),(14,16),(14,18),(14,20),(14,22),(14,25),(14,50),(14,51),(14,54),(14,56),(14,57),(14,58),(14,59);
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
  `nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint unsigned DEFAULT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Personal de Recepción','Encargado de recpecionar las solicitudes de clientes',1),(7,'Super Administrador','Control total del sistema',1),(8,'Administrador','Administrador general del sistema',1),(9,'Recepción','Recepción de vehículos y atención al cliente',1),(10,'Encargado de Servicios','Gestión completa del área de servicios',1),(11,'Técnico','Ejecución de órdenes de trabajo',1),(12,'Personal de Compras','Registro de compras y proveedores',1),(13,'Encargado de Compras','Gestión y aprobación de compras',1),(14,'Auditor','Solo lectura y reportes',1),(15,'Tester','Encargado de realizacion de pruebas',1),(16,'Tester 2','Encargado de realizacion de pruebas 2',1),(17,'test','teste',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salida_insumo`
--

DROP TABLE IF EXISTS `salida_insumo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salida_insumo` (
  `idsalida_insumo` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int unsigned NOT NULL,
  `id_tecnico` int unsigned NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacion` text,
  `estado` tinyint NOT NULL DEFAULT '1',
  `id_sucursal` int unsigned NOT NULL,
  PRIMARY KEY (`idsalida_insumo`),
  KEY `fk_salida_insumo_usuarios1_idx` (`id_usuario`),
  KEY `fk_salida_insumo_empleados1_idx` (`id_tecnico`),
  KEY `fk_salida_insumo_sucursales1_idx` (`id_sucursal`),
  CONSTRAINT `fk_salida_insumo_empleados1` FOREIGN KEY (`id_tecnico`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_salida_insumo_sucursales1` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_salida_insumo_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salida_insumo`
--

LOCK TABLES `salida_insumo` WRITE;
/*!40000 ALTER TABLE `salida_insumo` DISABLE KEYS */;
INSERT INTO `salida_insumo` VALUES (9,1,1,'2026-06-03 22:12:48','test',0,1),(10,1,1,'2026-06-06 20:44:33','test',0,1),(11,1,1,'2026-06-08 21:17:11','test',1,1),(12,1,2,'2026-06-08 21:19:24','estes 2',1,1),(14,1,1,'2026-06-16 22:19:49','TEST_DECIMAL_20260616_211949 salida insumo',1,1),(15,1,1,'2026-06-16 22:21:34','TEST_DECIMAL_20260616_212134 salida insumo',1,1),(16,1,1,'2026-06-17 18:42:32','TEST_DECIMAL_20260617_174232 salida insumo',1,1),(17,1,1,'2026-06-17 18:44:25','TEST_DECIMAL_20260617_174425 salida insumo',1,1);
/*!40000 ALTER TABLE `salida_insumo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salida_insumo_detalle`
--

DROP TABLE IF EXISTS `salida_insumo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salida_insumo_detalle` (
  `idsalida_insumo` int NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  `cantidad` decimal(12,4) NOT NULL,
  PRIMARY KEY (`idsalida_insumo`,`id_articulo`),
  KEY `fk_salida_insumo_has_articulos_articulos1_idx` (`id_articulo`),
  KEY `fk_salida_insumo_has_articulos_salida_insumo1_idx` (`idsalida_insumo`),
  CONSTRAINT `fk_salida_insumo_has_articulos_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_salida_insumo_has_articulos_salida_insumo1` FOREIGN KEY (`idsalida_insumo`) REFERENCES `salida_insumo` (`idsalida_insumo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salida_insumo_detalle`
--

LOCK TABLES `salida_insumo_detalle` WRITE;
/*!40000 ALTER TABLE `salida_insumo_detalle` DISABLE KEYS */;
INSERT INTO `salida_insumo_detalle` VALUES (9,21,1.0000),(10,21,1.0000),(11,21,1.0000),(12,21,2.0000),(14,58,1.2500),(15,61,1.2500),(16,64,1.2500),(17,67,1.2500);
/*!40000 ALTER TABLE `salida_insumo_detalle` ENABLE KEYS */;
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
INSERT INTO `stock` VALUES (2,1,200,15,15.0000,'2026-06-09 21:56:27',1,9016),(4,1,200,15,7.0000,'2026-06-08 21:20:36',1,9015),(6,1,200,15,10.0000,'2026-06-08 21:20:36',1,9014),(21,1,200,15,9.0000,'2026-06-18 23:18:43',1,9050),(28,1,200,15,0.0000,'2026-06-18 23:18:43',1,9051),(30,1,200,15,0.0000,'2026-06-09 22:18:08',1,9018),(33,1,200,15,10.0000,'2026-06-14 13:04:46',1,9020),(33,2,200,15,5.0000,'2026-06-14 13:01:31',7,9019),(41,1,200,15,10.0000,'2026-06-14 20:20:16',1,9021),(46,1,200,15,10.0000,'2026-06-14 20:20:16',1,9022),(57,1,200,15,11.2500,'2026-06-16 22:19:49',1,9031),(58,1,200,15,4.2500,'2026-06-16 22:19:49',1,9032),(60,1,200,15,11.2500,'2026-06-16 22:21:34',1,9036),(61,1,200,15,4.2500,'2026-06-16 22:21:34',1,9037),(63,1,200,15,11.2500,'2026-06-17 18:42:32',1,9041),(64,1,200,15,4.2500,'2026-06-17 18:42:32',1,9042),(66,1,200,15,11.2500,'2026-06-17 18:44:25',1,9046),(67,1,200,15,4.2500,'2026-06-17 18:44:25',1,9047);
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
  `tipo_documento` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `establecimiento` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `punto_expedicion` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_actual` bigint NOT NULL,
  `activo` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_documento`),
  KEY `sucursal_documento_FKIndex1` (`id_timbrado`),
  KEY `sucursal_documento_index5794` (`id_sucursal`,`id_caja`,`tipo_documento`),
  CONSTRAINT `sucursal_documentoTimbrado` FOREIGN KEY (`id_timbrado`) REFERENCES `timbrado` (`id_timbrado`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursal_documento`
--

LOCK TABLES `sucursal_documento` WRITE;
/*!40000 ALTER TABLE `sucursal_documento` DISABLE KEYS */;
INSERT INTO `sucursal_documento` VALUES (1,NULL,1,1,'remision','001','002',6,1),(2,NULL,1,2,'remision','002','002',2,1);
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
  `suc_descri` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suc_direccion` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suc_telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nro_establecimiento` int unsigned DEFAULT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`),
  KEY `sucursales_FKIndex1` (`id_empresa`),
  CONSTRAINT `fk_empresaSu` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sucursales`
--

LOCK TABLES `sucursales` WRITE;
/*!40000 ALTER TABLE `sucursales` DISABLE KEYS */;
INSERT INTO `sucursales` VALUES (1,2,'lubriReducto 6','san lorenzo','0215678345',6,0),(2,2,'lubriReducto 2','capiata','021567833',2,1),(12,2,'lubriReducto 1','Av. Artigas','0982234561',1,1);
/*!40000 ALTER TABLE `sucursales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timbrado`
--

DROP TABLE IF EXISTS `timbrado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timbrado` (
  `id_timbrado` int unsigned NOT NULL AUTO_INCREMENT,
  `timbrado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `tipo_impuesto_descri` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `usuario_envia` bigint DEFAULT NULL,
  `usuario_recibe` bigint DEFAULT NULL,
  `idtransferencia_origen` int unsigned DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`idtransferencia`)
) ENGINE=InnoDB AUTO_INCREMENT=9006 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock`
--

LOCK TABLES `transferencia_stock` WRITE;
/*!40000 ALTER TABLE `transferencia_stock` DISABLE KEYS */;
INSERT INTO `transferencia_stock` VALUES (9001,1,2,'2026-02-25 14:00:00','en_transito','Reposicion preventiva de filtros',1,NULL,NULL,NULL),(9002,1,2,'2026-03-12 09:20:00','recibido','Transferencia completa para mostrador',1,1,NULL,'2026-03-13 08:10:00'),(9003,1,2,'2026-04-05 15:40:00','recibido_parcial','Recepcion parcial por diferencia de conteo',1,1,NULL,'2026-04-06 10:00:00'),(9004,2,1,'2026-06-14 13:01:31','recibido_parcial','test',7,1,NULL,'2026-06-14 13:04:46'),(9005,1,2,'2026-06-14 13:04:46','en_transito',NULL,7,NULL,9004,'2026-06-14 13:04:46');
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
  `cantidad` decimal(12,4) DEFAULT NULL,
  `cantidad_recibida` decimal(12,4) DEFAULT NULL,
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
INSERT INTO `transferencia_stock_detalle` VALUES (9001,2,4.0000,0.0000),(9001,3,2.0000,0.0000),(9002,2,6.0000,6.0000),(9002,3,5.0000,5.0000),(9003,2,8.0000,5.0000),(9003,3,3.0000,3.0000),(9004,33,3.0000,2.0000),(9005,33,1.0000,NULL);
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
  `medida` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` int unsigned DEFAULT NULL,
  PRIMARY KEY (`idunidad_medida`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES (1,'Unidad',1),(2,'Litro',1),(3,'Par',1),(4,'Juego',1),(5,'Kit',1),(6,'Servicio',1),(7,'BALDE',1);
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
INSERT INTO `usuario_rol` VALUES (1,7),(7,12),(10,7),(11,11),(12,13);
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
  `usu_nombre` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_clave` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_estado` int unsigned DEFAULT NULL,
  `usu_intentos_fallidos` int unsigned NOT NULL DEFAULT '0',
  `usu_bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  `usu_cambiar_clave` tinyint unsigned NOT NULL DEFAULT '0',
  `usu_nick` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_apellido` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_telefono` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usu_ci` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  KEY `usuarios_FKIndex2` (`sucursalid`),
  CONSTRAINT `fk_sucursalesUsu` FOREIGN KEY (`sucursalid`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,1,'Administrador','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'admin','Del Sistema','admins@admin.com.py','0986203431','1234567'),(7,2,'User','$2y$10$XIq5J7iz7tIdhq0z6xaqwe51eZlBjKfcgRUo0FumDEugTYEe6efbe',1,0,0,0,'ucompra','Compra','ucompra@reducto.com.py','09862349732','1234566'),(8,1,'user','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'uservicio','Servicio','uservicio@reducto.com.py','0986234973','1234560'),(10,1,'Jorge','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'jdure','Dure','jdure@gmail.com','0985123654','5326548'),(11,2,'Angel','$2y$10$VQ/16VeuxoXk9SWafKK6ZeSwzC0yE5YSFnL0cyeg.SK2V6vzMH1d.',1,0,0,0,'adure','Dure','adure@reduc.com','0985123651','5456789'),(12,2,'Diego','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'ddure','Dure','ddure@admin.com','0985123654','6456789'),(13,1,'Rufino','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'rdure','Dure','rdure@admin.com','0985123456','2456987');
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
  `placa` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `anho` year DEFAULT NULL,
  `estado` int unsigned NOT NULL DEFAULT '1',
  `color` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transmision` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motor` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_vehiculo` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `uq_vehiculos_placa` (`placa`) /*!80000 INVISIBLE */,
  KEY `vehiculos_FKIndex1` (`id_modeloauto`),
  KEY `vehiculos_FKIndex3` (`id_cliente`),
  CONSTRAINT `fk_clientesVE` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_modeloAutoVe` FOREIGN KEY (`id_modeloauto`) REFERENCES `modelo_auto` (`id_modeloauto`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (17,11,1,'RGB513',2025,1,'negro','estandar','automatica','v6asddas-','Automovil','2026-05-26 22:13:49',NULL),(18,19,4,'AASZ012',NULL,1,'negro','',NULL,NULL,'','2026-05-29 16:13:19',NULL),(19,20,15,'ADJF4564',NULL,1,'blanco','','','','Camioneta','2026-05-31 10:56:29',NULL),(20,22,18,'DFH458',NULL,1,'NEGRO','','','','Camioneta','2026-05-31 10:58:26',NULL),(22,24,1,'TD59189',2026,1,'gris','test','manual','2.0','Automovil','2026-06-16 22:19:49',NULL),(23,25,1,'TD59294',2026,1,'gris','test','manual','decimal','Automovil','2026-06-16 22:21:34',NULL),(24,26,1,'TD32552',2026,1,'gris','test','manual','decimal','Automovil','2026-06-17 18:42:32',NULL),(25,27,1,'TD32665',2026,1,'gris','test','manual','decimal','Automovil','2026-06-17 18:44:25',NULL),(26,12,15,'ASD64',2012,1,'blanco','estandar','automatica','motor 2.0','SUV','2026-06-18 20:51:01',NULL);
/*!40000 ALTER TABLE `vehiculos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'bd_reduc_test'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-19 17:00:53
