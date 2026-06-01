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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ajuste_inventario`
--

LOCK TABLES `ajuste_inventario` WRITE;
/*!40000 ALTER TABLE `ajuste_inventario` DISABLE KEYS */;
INSERT INTO `ajuste_inventario` VALUES (16,1,1,3,'2026-05-26','General','INV general','2026-05-26',1),(17,2,7,3,'2026-05-26','Producto','test','2026-05-26',7),(18,1,1,3,'2026-05-29','Producto','alta producto amortiguador','2026-05-29',1);
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
INSERT INTO `ajuste_inventario_detalle` VALUES (2,16,0,0,0,0),(3,16,0,0,0,0),(4,16,0,0,0,0),(5,16,0,0,0,0),(6,16,0,0,0,0),(7,16,0,0,0,0),(8,16,0,0,0,0),(9,16,0,0,0,0),(10,16,0,0,0,0),(11,16,0,0,0,0),(12,16,0,0,0,0),(18,16,0,0,0,0),(19,16,0,0,0,0),(20,16,0,0,0,0),(21,16,0,0,0,0),(22,16,0,0,0,0),(23,16,0,0,0,0),(24,16,0,0,0,0),(25,16,0,0,0,0),(26,16,0,10,0,10),(27,16,0,0,0,0),(28,16,0,0,0,0),(29,16,0,0,0,0),(30,16,0,0,0,0),(31,16,0,10,0,10),(32,16,0,0,0,0),(32,17,0,10,0,10),(33,16,0,10,0,10),(34,18,0,10,0,10);
/*!40000 ALTER TABLE `ajuste_inventario_detalle` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulo_proveedor`
--

LOCK TABLES `articulo_proveedor` WRITE;
/*!40000 ALTER TABLE `articulo_proveedor` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulos`
--

LOCK TABLES `articulos` WRITE;
/*!40000 ALTER TABLE `articulos` DISABLE KEYS */;
INSERT INTO `articulos` VALUES (2,2,1,2,2,'Filtro de Aceite Bosch',45000,'FILBOS01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(3,2,1,2,2,'Filtro de Aire Toyota',60000,'FILTOY02',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(4,3,4,2,8,'Pastillas de Freno Delanteras',120000,'FREN001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(5,3,1,2,8,'Disco de Freno Ventilado',250000,'DISC001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(6,4,3,2,7,'Amortiguadores Delanteros (Par)',350000,'AMORT01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(7,5,5,2,3,'Kit de Distribución',450000,'KITDIST01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(8,5,1,2,3,'Bujía NGK',25000,'BUJNGK01',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(9,6,1,2,8,'Batería 12V 60Ah',550000,'BAT001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(10,7,5,2,7,'Kit de Embrague',650000,'EMB001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(11,8,1,2,4,'Cubierta 185/65R14 Michelin',400000,'NEU001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(12,9,2,2,10,'Refrigerante 1L',90000,'REF001',1,'2026-03-18 21:05:32','2026-03-18 21:05:32','producto'),(13,10,6,3,NULL,'Cambio de Filtro',50000,'SERV001',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(14,10,6,3,NULL,'Alineación y Balanceo',100000,'SERV002',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(15,10,6,3,NULL,'Diagnóstico Computarizado',70000,'SERV003',1,'2026-03-18 21:14:17','2026-03-18 21:14:17','servicio'),(17,7,1,1,15,'Mantenimiento vehiculos pequeños',150000,'123456',1,'2026-04-12 16:47:36','2026-04-12 16:47:36','servicio'),(18,1,1,1,NULL,'Desengrasante de motor',0,'INS-001',1,NULL,'2026-04-20 21:43:48','insumo'),(19,1,1,1,NULL,'Limpiador de frenos spray',0,'INS-002',1,NULL,'2026-04-20 21:43:48','insumo'),(20,1,1,1,NULL,'Limpiador de inyectores',0,'INS-003',1,NULL,'2026-04-20 21:43:48','insumo'),(21,1,1,1,NULL,'Lubricante WD-40',0,'INS-004',1,NULL,'2026-04-20 21:43:48','insumo'),(22,1,1,1,NULL,'Grasa multiuso',0,'INS-005',1,NULL,'2026-04-20 21:43:48','insumo'),(23,1,1,1,NULL,'Silicona RTV alta temperatura',0,'INS-006',1,NULL,'2026-04-20 21:43:48','insumo'),(24,1,1,1,NULL,'Sellador de roscas',0,'INS-007',1,NULL,'2026-04-20 21:43:48','insumo'),(25,1,1,1,NULL,'Refrigerante',0,'INS-008',1,NULL,'2026-04-20 21:43:48','insumo'),(26,1,1,1,NULL,'Líquido de frenos',0,'INS-009',1,NULL,'2026-04-20 21:43:48','insumo'),(27,1,1,1,NULL,'Trapo industrial',0,'INS-010',1,NULL,'2026-04-20 21:43:48','insumo'),(28,1,1,1,14,'Guantes descartables',0,'12345678',1,'2026-04-26 10:23:36','2026-04-20 21:43:48','producto'),(29,1,1,1,2,'Cinta aislante',80000,'012',1,'2026-04-28 21:09:10','2026-04-20 21:43:48','producto'),(30,6,1,2,4,'testtest',2000,'98764531',1,'2026-04-28 22:03:39','2026-04-26 11:57:00','producto'),(31,2,1,1,5,'test1',35000,'12345600',1,'2026-04-28 22:00:54','2026-04-28 22:00:54','producto'),(32,5,1,1,2,'test',2000,'12365400',1,'2026-04-28 22:01:43','2026-04-28 22:01:43','producto'),(33,3,1,2,13,'Aceite 30 50w 1L',35000,'123450015',1,'2026-04-30 12:54:59','2026-04-30 12:54:59','producto'),(34,4,1,2,16,'AMORTIGUADOR DEL LH TOYOTA',280000,'1267896',1,'2026-05-29 16:28:29','2026-05-29 16:28:29','producto'),(35,10,6,2,NULL,'Cambio de amortiguadores',150000,'1',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio'),(36,10,6,2,NULL,'Cambio de Aceite',50000,'2',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio'),(37,10,6,2,NULL,'Inspección general de motor',80000,'3',1,'2026-05-29 19:47:52','2026-05-29 19:47:52','servicio');
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,1,'1234567','Juan','González','Barrio San Vicente','0981123456','3','Soltero',1,'CI','juan.gonzalez@gmail.com'),(2,1,'2345678','María','López','Av. Eusebio Ayala','0982234567','1','Soltero',1,'CI','maria.lopez@gmail.com'),(3,182,'3216478','Carloss','Ramírezv','Barrio Obrero','0983345677','5','Soltero',1,'CI','carlos.ramirez@gmail.com'),(4,3,'4567890','Hanaa','Martínez','Avda. Mariscal López','0984456788','3','Soltero',1,'CI','ana.martinez@gmail.com'),(5,1,'5678901','Luis','Fernández','Barrio Trinidad','0985567890','7','Soltero',1,'CI','luis.fernandez@gmail.com'),(6,1,'6789012','Sofía','Benítez','Av. Artigas','0986678901','4','Soltero',1,'CI','sofia.benitez@gmail.com'),(7,1,'7890123','Miguel','Duarte','Barrio Sajonia','0987789012','9','Soltero',1,'CI','miguel.duarte@gmail.com'),(8,1,'8901234','Laura','Giménez','Av. Boggiani','0988890123','6','Soltero',1,'CI','laura.gimenez@gmail.com'),(9,1,'9012345','Pedro','Vera','Barrio Lambaré','0989901234','8','Soltero',0,'CI','pedro.vera@gmail.com'),(10,1,'1122334','Carolina','Rojas','Av. Fernando de la Mora','0981012345','0','Soltero',1,'PASAPORTE','carolina.rojas@gmail.com'),(11,2,'4964127','Juan Angel','Figueredo Martinez','Avda Cerro Patiño','0986203431','1','Soltero',1,'CI','juanmartinez076@gmail.com'),(12,166,'1299450','Gricelda','Martinez','Itaugua km 31 - Avda Cerro patiño','0985518660','','Soltero',0,'CI','Griceldamar@gmail.com'),(13,79,'1236487','asdas','asdas','asdasd','64789','','Viudo',1,'CI','asdas@admin.com'),(14,10,'1236452','tses','adsd','dsfsdfsd','03215987','','Soltero',1,'CI','testasd@admin.com'),(15,24,'80016096','ertca','asdsdasd','asdasd','asdsad','7','Casado',1,'RUC','asdasd@admin.com'),(16,10,'9745612','testse','asdasdas','sdasd','4568971','5','Divorciado',1,'CI','asdasd@admin.com'),(17,22,'6547892','jesus','mendieta','asdasd','123654987','','',1,'CI','jsd@gmail.com'),(18,219,'80016095','retail sa',NULL,'','','7','',1,'RUC',''),(19,1,'4799780','Mauricio','Montiel','No informado','','','',1,'CI',''),(20,162,'80016094','Retail S.A.','','','','8','',1,'RUC',''),(21,175,'1254865','JOSE','PEREZ','','','','Soltero',1,'CI',''),(22,178,'654856','JAUN','LOVEZNO','','','','',1,'CI','');
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra_cabecera`
--

LOCK TABLES `compra_cabecera` WRITE;
/*!40000 ALTER TABLE `compra_cabecera` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_a_pagar`
--

LOCK TABLES `cuentas_a_pagar` WRITE;
/*!40000 ALTER TABLE `cuentas_a_pagar` DISABLE KEYS */;
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
INSERT INTO `descuento_cliente` VALUES (11,4),(19,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descuentos`
--

LOCK TABLES `descuentos` WRITE;
/*!40000 ALTER TABLE `descuentos` DISABLE KEYS */;
INSERT INTO `descuentos` VALUES (4,NULL,1,NULL,'Descuento por Apertura','Promociones por apertura','PORCENTAJE',10.00,'TOTAL','2026-05-30','2026-05-31',1,1,'2026-05-30 19:43:56',NULL);
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
  `cantidad_repuesto` decimal(12,2) DEFAULT '1.00',
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_detalle`
--

LOCK TABLES `diagnostico_detalle` WRITE;
/*!40000 ALTER TABLE `diagnostico_detalle` DISABLE KEYS */;
INSERT INTO `diagnostico_detalle` VALUES (15,15,NULL,NULL,1.00,'faltante de aceite','media','TALLER'),(16,15,NULL,NULL,1.00,'faltante de fluido de direccion','leve','TALLER'),(17,15,NULL,NULL,1.00,'liquido de frenos espeso','grave','TALLER'),(18,16,NULL,NULL,1.00,'aceite espeso','leve','TALLER'),(19,17,NULL,NULL,1.00,'Exceso de aceite','media','TALLER'),(20,18,NULL,NULL,1.00,'faltante de liquido de transmision','media','TALLER'),(21,19,NULL,NULL,1.00,'asdsa','media','TALLER'),(22,20,NULL,NULL,1.00,'problema en amortiguador izquierdo','leve','TALLER'),(23,21,NULL,NULL,1.00,'cambio de filtro','media','TALLER'),(24,23,36,33,4.00,'cambio de aceite preventivo','media','TALLER'),(25,23,13,2,1.00,'Reemplazo preventivo junto con cambio de aceite','media','TALLER'),(26,23,37,NULL,0.00,'Verificar fugas, correas, mangueras, niveles y ruidos anormales','leve','NINGUNO'),(27,24,13,2,1.00,'cambio de repuesto por desperfecto de fabrica en uso','media','TALLER');
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `diagnostico_servicio`
--

LOCK TABLES `diagnostico_servicio` WRITE;
/*!40000 ALTER TABLE `diagnostico_servicio` DISABLE KEYS */;
INSERT INTO `diagnostico_servicio` VALUES (15,1,12,1,1,'2026-05-26 22:26:08',NULL,NULL,2,'cambio de aceite por faltante y en estado espeso','2026-05-27 01:26:08','2026-05-27 01:39:03',1,1,0),(16,1,13,1,1,'2026-05-27 20:08:27',NULL,NULL,2,'cambio de aceite sin problemas extras','2026-05-27 23:08:27','2026-05-27 23:09:35',1,1,0),(17,1,14,3,1,'2026-05-27 21:37:22',NULL,NULL,3,'test','2026-05-28 00:37:22','2026-05-28 00:37:26',1,1,0),(18,1,15,1,1,'2026-05-28 20:11:17',NULL,NULL,2,'falta de liquido de transimision','2026-05-28 23:11:17','2026-05-28 23:12:08',1,1,0),(19,1,16,2,1,'2026-05-28 21:49:32',NULL,NULL,3,'este','2026-05-29 00:49:32','2026-05-30 01:02:56',1,1,0),(20,1,17,1,1,'2026-05-29 16:23:58',NULL,NULL,2,'segun lo verificado se encuentra un efecto rebote continuo en la parte frontal de vehiculo a parte de fugas de aceite en el cuello de uno de los amortiguadores lado izquierdo conductor','2026-05-29 19:23:58','2026-05-29 23:02:34',1,1,0),(21,1,18,1,1,'2026-05-30 09:18:52',NULL,NULL,2,'','2026-05-30 12:18:52','2026-05-30 12:21:10',1,1,0),(23,1,19,1,1,'2026-05-30 18:30:02',NULL,NULL,2,'test','2026-05-30 21:30:02','2026-05-30 22:45:31',1,1,0),(24,1,20,1,1,'2026-05-30 20:31:03',NULL,NULL,3,'test','2026-05-30 23:31:03','2026-05-30 23:31:05',1,1,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `libro_compra`
--

LOCK TABLES `libro_compra` WRITE;
/*!40000 ALTER TABLE `libro_compra` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

LOCK TABLES `marcas` WRITE;
/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (1,'Toyota',1),(2,'Bosch',1),(3,'NGK',1),(4,'Michelin',1),(5,'Castrol',1),(6,'Mobil',1),(7,'SKF',1),(8,'Valeo',1),(9,'Pirelli',1),(10,'Shell',1),(11,'Nissan',1),(12,'Chevrolet',1),(13,'Kia',1),(14,'Hyundai',1),(15,'Volkswagen',1),(16,'KYB',1),(17,'Varios',1);
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
  `MovStockReferencia` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MovStockId`),
  KEY `sucmovimientostock_FKIndex1` (`id_sucursal`),
  KEY `fk_mov_articulo` (`MovStockArticuloId`),
  CONSTRAINT `fk_mov_articulo` FOREIGN KEY (`MovStockArticuloId`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_sucursalesSucmo` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientostock`
--

LOCK TABLES `movimientostock` WRITE;
/*!40000 ALTER TABLE `movimientostock` DISABLE KEYS */;
INSERT INTO `movimientostock` VALUES (48,1,'AJUSTE_INV',26,10.0000,0.00,0.00,'2026-05-26 21:31:27',NULL,NULL,1,1,'AJUSTE #16'),(49,1,'AJUSTE_INV',31,10.0000,0.00,0.00,'2026-05-26 21:31:27',NULL,NULL,1,1,'AJUSTE #16'),(50,1,'AJUSTE_INV',33,10.0000,0.00,0.00,'2026-05-26 21:31:27',NULL,NULL,1,1,'AJUSTE #16'),(51,2,'AJUSTE_INV',32,10.0000,0.00,0.00,'2026-05-26 22:26:56',NULL,NULL,7,1,'AJUSTE #17'),(52,2,'TRANSFERENCIA_SALIDA',32,2.0000,0.00,0.00,'2026-05-26 23:29:22','002-002-0000001',NULL,7,-1,'TRANSFERENCIA #7'),(53,1,'REG. SERVICIO',33,4.0000,35000.00,0.00,'2026-05-27 21:32:53',NULL,NULL,1,-1,'REG_SERV #19'),(54,1,'REG. SERVICIO',33,1.0000,35000.00,0.00,'2026-05-28 20:15:17',NULL,NULL,1,-1,'REG_SERV #22'),(55,1,'AJUSTE_INV',34,10.0000,0.00,0.00,'2026-05-29 15:29:15',NULL,NULL,1,1,'AJUSTE #18'),(56,1,'REG. SERVICIO',34,2.0000,280000.00,0.00,'2026-05-29 21:12:30',NULL,NULL,1,-1,'REG_SERV #24'),(57,1,'REG. SERVICIO',21,1.0000,0.00,0.00,'2026-05-29 21:12:30',NULL,NULL,1,-1,'REG_SERV #24'),(58,1,'REG. SERVICIO',33,1.0000,35000.00,0.00,'2026-05-30 09:25:48',NULL,NULL,1,-1,'REG_SERV #25'),(59,1,'REG. SERVICIO',21,1.0000,0.00,0.00,'2026-05-30 09:25:48',NULL,NULL,1,-1,'REG_SERV #25'),(60,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 14:15:17',NULL,NULL,1,-1,'SAL_INS #1'),(61,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 14:15:41',NULL,NULL,1,-1,'SAL_INS #2'),(62,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 14:34:44',NULL,NULL,1,-1,'SAL_INS #3'),(63,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:09:48',NULL,NULL,1,1,'ANUL_SAL_INS #3'),(64,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:10:32',NULL,NULL,1,-1,'SAL_INS #4'),(65,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:10:55',NULL,NULL,1,1,'ANUL_SAL_INS #4'),(66,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:17:26',NULL,NULL,1,-1,'SAL_INS #5'),(67,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:18:25',NULL,NULL,1,1,'ANUL_SAL_INS #5'),(68,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:32:40',NULL,NULL,1,-1,'SAL_INS #6'),(69,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:33:12',NULL,NULL,1,-1,'SAL_INS #7'),(70,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:33:25',NULL,NULL,1,1,'ANUL_SAL_INS #7'),(71,1,'SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:36:46',NULL,NULL,1,-1,'SAL_INS #8'),(72,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:37:12',NULL,NULL,1,1,'ANUL_SAL_INS #8'),(73,1,'ANUL SALIDA INSUMO',21,1.0000,0.00,0.00,'2026-05-30 15:38:06',NULL,NULL,1,1,'ANUL_SAL_INS #6'),(74,1,'REG. SERVICIO',33,4.0000,35000.00,0.00,'2026-05-30 19:48:08',NULL,NULL,1,-1,'REG_SERV #26'),(75,1,'REG. SERVICIO',2,1.0000,45000.00,0.00,'2026-05-30 19:48:08',NULL,NULL,1,-1,'REG_SERV #26'),(76,1,'REG. SERVICIO',2,1.0000,0.00,0.00,'2026-05-30 23:51:37',NULL,NULL,1,-1,'REG_SERV #27');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_compra`
--

LOCK TABLES `nota_compra` WRITE;
/*!40000 ALTER TABLE `nota_compra` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nota_remision`
--

LOCK TABLES `nota_remision` WRITE;
/*!40000 ALTER TABLE `nota_remision` DISABLE KEYS */;
INSERT INTO `nota_remision` VALUES (7,NULL,2,7,'2026-05-26 23:29:22','002-002-0000001','jose campos','2342344','0986234945','eleuterio','','toyota','fun cargo','asd234','2026-05-26','2026-05-26','etst',NULL,NULL,NULL,'transferencia',7);
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
INSERT INTO `nota_remision_detalle` VALUES (32,7,2.00,0.00,0.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_compra`
--

LOCK TABLES `orden_compra` WRITE;
/*!40000 ALTER TABLE `orden_compra` DISABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo`
--

LOCK TABLES `orden_trabajo` WRITE;
/*!40000 ALTER TABLE `orden_trabajo` DISABLE KEYS */;
INSERT INTO `orden_trabajo` VALUES (19,1,6,10,1,11,17,1,'2026-05-26 22:59:20','2026-05-27 21:32:53',2,'','2026-05-28 00:32:53',1,'2026-05-26 22:59:20','NORMAL',NULL),(20,1,9,NULL,1,11,17,1,'2026-05-27 21:37:26','2026-05-29 14:13:39',2,'realizar validacion de cantidad de aceite','2026-05-29 17:13:39',1,'2026-05-27 21:37:26','RECLAMO',7),(21,2,1,13,1,11,17,1,'2026-05-28 20:14:17','2026-05-28 20:15:17',2,'cambio simple','2026-05-28 23:15:17',1,'2026-05-28 20:14:17','NORMAL',NULL),(22,NULL,NULL,NULL,1,11,17,1,'2026-05-28 21:49:38',NULL,3,NULL,'2026-05-29 00:49:38',NULL,'2026-05-28 21:49:38','RECLAMO',8),(23,2,1,14,1,19,18,1,'2026-05-29 20:38:48','2026-05-29 21:12:30',2,'realizar las pruebas correspondientes una vez finalizado el cambio','2026-05-30 00:12:30',1,'2026-05-29 20:38:48','NORMAL',NULL),(24,2,6,12,1,11,17,1,'2026-05-30 09:22:06','2026-05-30 09:25:48',2,'cambio de filtro','2026-05-30 12:25:48',1,'2026-05-30 09:22:06','NORMAL',NULL),(25,1,6,17,1,19,18,1,'2026-05-30 19:47:10','2026-05-30 19:48:08',2,'','2026-05-30 22:48:08',1,'2026-05-30 19:47:10','NORMAL',NULL),(26,1,7,NULL,1,19,18,1,'2026-05-30 20:31:05','2026-05-30 23:51:37',2,'test','2026-05-31 02:51:37',1,'2026-05-30 20:31:05','RECLAMO',11);
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
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orden_trabajo_detalle`
--

LOCK TABLES `orden_trabajo_detalle` WRITE;
/*!40000 ALTER TABLE `orden_trabajo_detalle` DISABLE KEYS */;
INSERT INTO `orden_trabajo_detalle` VALUES (42,4,35000.00,140000.00,19,33),(43,1,150000.00,150000.00,19,17),(45,1,0.00,0.00,20,17),(46,1,150000.00,150000.00,21,17),(47,1,35000.00,35000.00,21,33),(49,2,280000.00,560000.00,23,34),(50,1,150000.00,150000.00,23,35),(52,1,80000.00,80000.00,24,13),(53,1,35000.00,35000.00,24,33),(54,1,50000.00,50000.00,25,36),(55,4,35000.00,140000.00,25,33),(56,1,50000.00,50000.00,25,13),(57,1,45000.00,45000.00,25,2),(58,1,80000.00,80000.00,25,37),(61,1,0.00,0.00,26,2),(62,1,0.00,0.00,26,13);
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido_cabecera`
--

LOCK TABLES `pedido_cabecera` WRITE;
/*!40000 ALTER TABLE `pedido_cabecera` DISABLE KEYS */;
INSERT INTO `pedido_cabecera` VALUES (13,1,1,'2026-05-26 23:07:28',1,NULL,NULL);
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
  `stock_actual` int unsigned DEFAULT NULL,
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
INSERT INTO `pedido_detalle` VALUES (13,2,13,0),(13,3,15,0);
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_compra`
--

LOCK TABLES `presupuesto_compra` WRITE;
/*!40000 ALTER TABLE `presupuesto_compra` DISABLE KEYS */;
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
INSERT INTO `presupuesto_descuento` VALUES (17,4,1,'PORCENTAJE',10.00,24550.00,'Descuento por Apertura','2026-05-30 19:45:31');
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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_detalleservicio`
--

LOCK TABLES `presupuesto_detalleservicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_detalleservicio` DISABLE KEYS */;
INSERT INTO `presupuesto_detalleservicio` VALUES (14,10,33,NULL,4.00,35000.00,140000.00),(15,10,17,NULL,1.00,150000.00,150000.00),(16,11,13,NULL,1.00,80000.00,80000.00),(17,11,33,NULL,1.00,35000.00,35000.00),(18,12,13,NULL,1.00,80000.00,80000.00),(19,12,33,NULL,1.00,35000.00,35000.00),(20,13,17,NULL,1.00,150000.00,150000.00),(21,13,33,NULL,1.00,35000.00,35000.00),(22,14,34,NULL,2.00,280000.00,560000.00),(23,14,35,NULL,1.00,150000.00,150000.00),(24,15,13,NULL,1.00,80000.00,80000.00),(25,15,2,NULL,1.00,45000.00,45000.00),(26,16,36,NULL,1.00,50000.00,50000.00),(27,16,33,NULL,4.00,17500.00,70000.00),(28,16,13,NULL,1.00,50000.00,50000.00),(29,16,2,NULL,1.00,45000.00,45000.00),(30,16,37,NULL,1.00,80000.00,80000.00),(31,17,36,NULL,1.00,50000.00,50000.00),(32,17,33,NULL,4.00,35000.00,140000.00),(33,17,13,NULL,1.00,50000.00,50000.00),(34,17,2,NULL,1.00,45000.00,45000.00),(35,17,37,NULL,1.00,80000.00,80000.00);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_promocion`
--

LOCK TABLES `presupuesto_promocion` WRITE;
/*!40000 ALTER TABLE `presupuesto_promocion` DISABLE KEYS */;
INSERT INTO `presupuesto_promocion` VALUES (1,17,31,5,36,1.00,15000.00,15000.00,'2026-05-30 19:45:31'),(2,17,32,3,33,4.00,17500.00,70000.00,'2026-05-30 19:45:31'),(3,17,33,5,13,1.00,15000.00,15000.00,'2026-05-30 19:45:31'),(4,17,34,4,2,1.00,4500.00,4500.00,'2026-05-30 19:45:31'),(5,17,35,5,37,1.00,15000.00,15000.00,'2026-05-30 19:45:31');
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_servicio`
--

LOCK TABLES `presupuesto_servicio` WRITE;
/*!40000 ALTER TABLE `presupuesto_servicio` DISABLE KEYS */;
INSERT INTO `presupuesto_servicio` VALUES (10,15,1,1,11,17,'2026-05-26',3,'2026-05-31',290000.00,0.00,290000.00,'DIAGNOSTICO',NULL),(11,NULL,1,1,11,17,'2026-05-27',5,'2026-05-31',115000.00,0.00,115000.00,'PRELIMINAR',NULL),(12,16,1,1,11,17,'2026-05-27',3,'2026-05-31',115000.00,0.00,115000.00,'DIAGNOSTICO',11),(13,18,1,1,11,17,'2026-05-28',3,'2026-06-06',185000.00,0.00,185000.00,'DIAGNOSTICO',NULL),(14,20,1,1,19,18,'2026-05-29',3,'2026-06-06',710000.00,0.00,710000.00,'DIAGNOSTICO',NULL),(15,21,1,1,11,17,'2026-05-30',2,'2026-05-30',125000.00,0.00,125000.00,'DIAGNOSTICO',NULL),(16,23,1,1,19,18,'2026-05-30',0,'2026-05-30',295000.00,0.00,295000.00,'DIAGNOSTICO',NULL),(17,23,1,1,19,18,'2026-05-30',3,'2026-05-31',365000.00,24550.00,220950.00,'DIAGNOSTICO',NULL);
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
INSERT INTO `promocion_producto` VALUES (2,4),(13,5),(33,3),(36,5),(37,5);
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promociones`
--

LOCK TABLES `promociones` WRITE;
/*!40000 ALTER TABLE `promociones` DISABLE KEYS */;
INSERT INTO `promociones` VALUES (3,NULL,1,NULL,'Aceites','Promo Aceites','PORCENTAJE',50.00,'2026-05-01','2026-05-31','2026-05-30 18:56:27',NULL,1),(4,NULL,1,NULL,'Filtros','Filtros off','PORCENTAJE',10.00,'2026-05-01','2026-05-31','2026-05-30 19:43:05',NULL,1),(5,NULL,1,NULL,'promo Servicios','promo Servicios','MONTO_FIJO',15000.00,'2026-05-01','2026-05-31','2026-05-30 19:44:46',NULL,1);
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
INSERT INTO `recepcion_fotos` VALUES (2,18,'uploads/recepciones/1780143317_0_2026-Formula1-Red-Bull-Racing-RB22-001-2160.jpg',NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recepcion_servicio`
--

LOCK TABLES `recepcion_servicio` WRITE;
/*!40000 ALTER TABLE `recepcion_servicio` DISABLE KEYS */;
INSERT INTO `recepcion_servicio` VALUES (12,1,17,11,'2026-05-26 22:15:15','2026-05-27 21:32:53','150000','1/2','sin_danos','','mantenimiento','motor','normal','llave,rueda_auxilio,baliza','Mantenimiento, cambio de aceite',3,'2026-05-26 22:15:15','2026-05-27 21:32:53',1,'NORMAL',NULL),(13,1,17,11,'2026-05-27 20:07:02','2026-05-30 09:25:48','50000','1/4','rayones','','mantenimiento','motor','normal','llave,llave_repuesto,herramientas','mantenimiento de motor - cambio de aceite',3,'2026-05-27 20:07:02','2026-05-30 09:25:48',1,'NORMAL',NULL),(14,1,17,11,'2026-05-27 21:35:09','2026-05-29 14:13:39','55000','vacio','rayones','','garantia','motor','normal','llave,herramientas','verificacion por reclamo',3,'2026-05-27 21:35:09','2026-05-29 14:13:39',1,'RECLAMO',7),(15,1,17,11,'2026-05-28 20:10:34','2026-05-28 20:15:17','44545','1/4','rayones','','diagnostico','transmision','normal','llave,llave_repuesto,herramientas','verificacion de transmision',3,'2026-05-28 20:10:34','2026-05-28 20:15:17',1,'NORMAL',NULL),(16,1,17,11,'2026-05-28 21:49:04',NULL,'155000','1/4','rayones','asd','diagnostico','motor','normal','llave','asd',2,'2026-05-28 21:49:04','2026-05-28 21:49:32',1,'RECLAMO',8),(17,1,18,19,'2026-05-29 16:14:05','2026-05-29 21:12:30','80000','1/2','sin_danos','','mantenimiento','suspension','normal','llave,herramientas,rueda_auxilio','verificacion de amortiguadores',3,'2026-05-29 16:14:05','2026-05-29 21:12:30',1,'NORMAL',NULL),(18,1,17,11,'2026-05-30 09:15:17',NULL,'85000','1/2','rayones','ra','diagnostico','transmision','normal','llave,herramientas,rueda_auxilio','el cliente solicita verificacion de transmision',2,'2026-05-30 09:15:17','2026-05-30 09:18:52',1,'NORMAL',NULL),(19,1,18,19,'2026-05-30 18:20:45','2026-05-30 19:48:08','90000','1/2','sin_danos','','mantenimiento','motor','normal','llave,llave_repuesto,herramientas,rueda_auxilio','mantenimiento preventivo de motor',3,'2026-05-30 18:20:45','2026-05-30 19:48:08',1,'NORMAL',NULL),(20,1,18,19,'2026-05-30 20:12:59','2026-05-30 23:51:37','90100','1/4','sin_danos','','garantia','motor','normal','llave,llave_repuesto,rueda_auxilio','test reclamo',3,'2026-05-30 20:12:59','2026-05-30 23:51:37',1,'RECLAMO',11);
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio`
--

LOCK TABLES `reclamo_servicio` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio` DISABLE KEYS */;
INSERT INTO `reclamo_servicio` VALUES (7,19,1,11,17,'2026-05-27 21:34:30','ruidos luego del cambio de aceite en el motor',3,1,'2026-05-29 14:13:39','Servicio registrado',NULL,'SERVICIO','CLIENTE',2,1),(8,22,1,11,17,'2026-05-28 21:48:41','test',2,1,NULL,NULL,NULL,'REPUESTO','CLIENTE',2,1),(9,24,1,19,18,'2026-05-29 21:22:41','el cliente reclama que posterior al dia del retiro del vehiculo presento un efecto rebote muy prologando durante el uso',1,1,NULL,NULL,NULL,'REPUESTO','CLIENTE',2,1),(10,25,1,11,17,'2026-05-30 09:28:39','inconveniente reportado filtro con problemas',1,1,NULL,NULL,NULL,'SERVICIO','CLIENTE',2,1),(11,26,1,19,18,'2026-05-30 20:11:55','incidente reportado por el cliente posterior a 3 dias del servicio',3,1,'2026-05-30 23:51:37','Servicio registrado',NULL,'GENERAL','CLIENTE',2,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reclamo_servicio_detalle`
--

LOCK TABLES `reclamo_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `reclamo_servicio_detalle` DISABLE KEYS */;
INSERT INTO `reclamo_servicio_detalle` VALUES (1,11,58,'filtro con problemas',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio`
--

LOCK TABLES `registro_servicio` WRITE;
/*!40000 ALTER TABLE `registro_servicio` DISABLE KEYS */;
INSERT INTO `registro_servicio` VALUES (19,19,17,11,1,'2026-05-27','2026-05-27 21:32:53',NULL,1,3,'finalizado'),(20,20,17,11,1,'2026-05-27','2026-05-27 21:38:49',NULL,1,0,'se valido que la cantidad de aceite sea la correcta en el vehiculo'),(21,20,17,11,1,'2026-05-28','2026-05-28 19:44:28',NULL,1,0,'test'),(22,21,17,11,1,'2026-05-28','2026-05-28 20:15:17',NULL,1,3,'test'),(23,20,17,11,1,'2026-05-29','2026-05-29 14:13:39',NULL,1,1,'sads'),(24,23,18,19,1,'2026-05-29','2026-05-29 21:12:30',80004,1,3,'se realizo test de verificacion, por eso el aumento en el kilometraje '),(25,24,17,11,1,'2026-05-30','2026-05-30 09:25:48',80002,1,3,'se realizo validacion post salida'),(26,25,18,19,1,'2026-05-30','2026-05-30 19:48:08',90005,1,3,'finalizado el trabajo'),(27,26,18,19,1,'2026-05-30','2026-05-30 23:51:37',91005,1,1,'fhhfhhjgj');
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
  `origen` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `idregistro_servicio` int unsigned NOT NULL,
  `id_articulo` int unsigned NOT NULL,
  PRIMARY KEY (`id_registro_servicio_detalle`),
  KEY `fk_registro_servicio_detalle_registro_servicio1_idx` (`idregistro_servicio`),
  KEY `fk_registro_servicio_detalle_articulos1_idx` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_articulos1` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_registro_servicio_detalle_registro_servicio1` FOREIGN KEY (`idregistro_servicio`) REFERENCES `registro_servicio` (`idregistro_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `registro_servicio_detalle`
--

LOCK TABLES `registro_servicio_detalle` WRITE;
/*!40000 ALTER TABLE `registro_servicio_detalle` DISABLE KEYS */;
INSERT INTO `registro_servicio_detalle` VALUES (38,4.00,35000.00,140000.00,'OT',19,33),(39,1.00,150000.00,150000.00,'OT',19,17),(41,1.00,0.00,0.00,'OT',20,17),(42,1.00,0.00,0.00,'OT',21,17),(43,1.00,150000.00,150000.00,'OT',22,17),(44,1.00,35000.00,35000.00,'OT',22,33),(46,1.00,0.00,0.00,'OT',23,17),(47,2.00,280000.00,560000.00,'OT',24,34),(48,1.00,150000.00,150000.00,'OT',24,35),(50,1.00,0.00,0.00,'INSUMO',24,21),(51,1.00,80000.00,80000.00,'OT',25,13),(52,1.00,35000.00,35000.00,'OT',25,33),(54,1.00,0.00,0.00,'INSUMO',25,21),(55,1.00,50000.00,50000.00,'OT',26,36),(56,4.00,35000.00,140000.00,'OT',26,33),(57,1.00,50000.00,50000.00,'OT',26,13),(58,1.00,45000.00,45000.00,'OT',26,2),(59,1.00,80000.00,80000.00,'OT',26,37),(62,1.00,0.00,0.00,'OT',27,2),(63,1.00,0.00,0.00,'OT',27,13);
/*!40000 ALTER TABLE `registro_servicio_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regla_comercial`
--

DROP TABLE IF EXISTS `regla_comercial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regla_comercial` (
  `id_regla` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `id_sucursal` int unsigned DEFAULT NULL,
  `prioridad` int NOT NULL DEFAULT '0',
  `modo_competencia` varchar(30) NOT NULL DEFAULT 'COMPITE_MISMO_ALCANCE',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_usuario_crea` int unsigned NOT NULL,
  `id_usuario_modifica` int unsigned DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_regla`),
  KEY `idx_regla_comercial_filtros` (`estado`,`fecha_inicio`,`fecha_fin`,`id_sucursal`),
  KEY `fk_regla_comercial_sucursal_idx` (`id_sucursal`),
  KEY `fk_regla_comercial_usuario_crea_idx` (`id_usuario_crea`),
  KEY `fk_regla_comercial_usuario_modifica_idx` (`id_usuario_modifica`),
  CONSTRAINT `fk_regla_comercial_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_regla_comercial_usuario_crea` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_regla_comercial_usuario_modifica` FOREIGN KEY (`id_usuario_modifica`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regla_comercial`
--

LOCK TABLES `regla_comercial` WRITE;
/*!40000 ALTER TABLE `regla_comercial` DISABLE KEYS */;
/*!40000 ALTER TABLE `regla_comercial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regla_comercial_condicion`
--

DROP TABLE IF EXISTS `regla_comercial_condicion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regla_comercial_condicion` (
  `id_condicion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_regla` int unsigned NOT NULL,
  `tipo_condicion` varchar(30) NOT NULL,
  `operador` varchar(20) NOT NULL DEFAULT '=',
  `valor_ref` int unsigned DEFAULT NULL,
  `valor_texto` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id_condicion`),
  KEY `idx_regla_condicion_regla` (`id_regla`),
  KEY `idx_regla_condicion_tipo` (`tipo_condicion`,`valor_ref`),
  CONSTRAINT `fk_regla_condicion_regla` FOREIGN KEY (`id_regla`) REFERENCES `regla_comercial` (`id_regla`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regla_comercial_condicion`
--

LOCK TABLES `regla_comercial_condicion` WRITE;
/*!40000 ALTER TABLE `regla_comercial_condicion` DISABLE KEYS */;
/*!40000 ALTER TABLE `regla_comercial_condicion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `regla_comercial_descuento`
--

DROP TABLE IF EXISTS `regla_comercial_descuento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `regla_comercial_descuento` (
  `id_regla_descuento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_regla` int unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `valor` decimal(12,2) NOT NULL,
  `cantidad_requerida` decimal(12,2) DEFAULT NULL,
  `cantidad_cobrada` decimal(12,2) DEFAULT NULL,
  `aplica_a` varchar(20) NOT NULL DEFAULT 'TOTAL',
  `alcance_tipo` varchar(30) DEFAULT NULL,
  `alcance_ref` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id_regla_descuento`),
  KEY `idx_regla_descuento_regla` (`id_regla`),
  CONSTRAINT `fk_regla_descuento_regla` FOREIGN KEY (`id_regla`) REFERENCES `regla_comercial` (`id_regla`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `regla_comercial_descuento`
--

LOCK TABLES `regla_comercial_descuento` WRITE;
/*!40000 ALTER TABLE `regla_comercial_descuento` DISABLE KEYS */;
/*!40000 ALTER TABLE `regla_comercial_descuento` ENABLE KEYS */;
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
INSERT INTO `rol_permiso` VALUES (1,1),(1,2),(1,7),(1,9),(1,11),(1,62),(1,171),(1,199),(7,1),(7,2),(7,3),(7,4),(7,5),(7,6),(7,7),(7,8),(7,9),(7,10),(7,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17),(7,18),(7,19),(7,20),(7,21),(7,22),(7,23),(7,24),(7,25),(7,26),(7,27),(7,47),(7,48),(7,49),(7,50),(7,51),(7,52),(7,53),(7,54),(7,55),(7,56),(7,57),(7,58),(7,59),(7,60),(7,61),(7,62),(7,63),(7,64),(7,71),(7,120),(7,121),(7,122),(7,123),(7,124),(7,125),(7,126),(7,127),(7,128),(7,129),(7,130),(7,131),(7,160),(7,161),(7,162),(7,164),(7,165),(7,166),(7,167),(7,168),(7,169),(7,170),(7,171),(7,172),(7,173),(7,175),(7,176),(7,177),(7,178),(7,179),(7,180),(7,181),(7,182),(7,183),(7,184),(7,185),(7,186),(7,187),(7,188),(7,189),(7,190),(7,191),(7,192),(7,193),(7,194),(7,195),(7,196),(7,197),(7,198),(7,199),(7,200),(7,201),(7,202),(7,203),(7,204),(7,205),(7,206),(7,207),(7,208),(7,209),(7,210),(7,211),(7,215),(7,216),(7,217),(7,218),(7,219),(7,220),(7,221),(7,222),(7,223),(7,224),(7,225),(7,226),(7,227),(7,228),(7,229),(7,230),(7,231),(7,232),(7,233),(7,234),(7,235),(7,236),(7,237),(7,238),(7,239),(7,240),(7,241),(8,1),(8,2),(8,3),(8,4),(8,5),(8,6),(8,7),(8,8),(8,9),(8,10),(8,11),(8,12),(8,13),(8,14),(8,15),(8,16),(8,17),(8,18),(8,19),(8,20),(8,21),(8,22),(8,23),(8,24),(8,25),(8,26),(8,27),(8,47),(8,48),(8,49),(8,50),(8,51),(8,52),(8,53),(8,54),(8,55),(8,56),(8,57),(8,58),(8,59),(8,60),(8,61),(8,62),(8,63),(8,64),(8,71),(8,120),(8,121),(8,122),(8,123),(8,124),(8,125),(8,126),(8,127),(8,128),(8,129),(8,130),(8,131),(8,160),(8,161),(8,162),(9,1),(9,2),(9,3),(9,9),(9,11),(9,22),(9,23),(9,25),(9,26),(10,1),(10,2),(10,3),(10,4),(10,5),(10,6),(10,7),(10,8),(10,9),(10,10),(10,11),(10,14),(10,22),(10,23),(10,24),(10,25),(10,26),(10,27),(10,57),(10,60),(10,61),(10,62),(10,63),(10,64),(10,165),(10,166),(10,167),(10,179),(10,180),(10,181),(10,182),(10,183),(10,184),(10,195),(10,196),(10,197),(10,198),(10,199),(10,201),(10,202),(11,5),(11,6),(11,61),(11,64),(12,47),(12,50),(12,51),(12,52),(12,54),(12,120),(12,121),(12,126),(12,127),(12,128),(12,129),(12,130),(12,131),(12,168),(12,169),(12,193),(13,14),(13,20),(13,21),(13,47),(13,48),(13,49),(13,50),(13,51),(13,52),(13,53),(13,58),(13,120),(13,121),(13,122),(13,123),(13,124),(13,125),(13,126),(13,127),(13,128),(13,129),(13,130),(13,131),(13,160),(13,161),(13,162),(13,164),(13,168),(13,169),(13,170),(13,176),(13,177),(13,178),(13,188),(13,189),(13,190),(13,191),(13,192),(13,193),(13,194),(14,2),(14,11),(14,12),(14,16),(14,18),(14,20),(14,22),(14,25),(14,50),(14,51),(14,54),(14,56),(14,57),(14,58),(14,59);
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
  `idsalida_consumible` int NOT NULL AUTO_INCREMENT,
  `id_sucursal` int NOT NULL,
  `id_usuario` int NOT NULL,
  `id_tecnico` int DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observacion` text,
  `estado` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`idsalida_consumible`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salida_insumo`
--

LOCK TABLES `salida_insumo` WRITE;
/*!40000 ALTER TABLE `salida_insumo` DISABLE KEYS */;
INSERT INTO `salida_insumo` VALUES (1,1,1,NULL,'2026-05-30 00:00:00','test',1),(2,1,1,NULL,'2026-05-30 00:00:00','',1),(3,1,1,2,'2026-05-30 00:00:00','para uso en el taller',0),(4,1,1,1,'2026-05-30 00:00:00','test',0),(5,1,1,1,'2026-05-30 00:00:00','test',0),(6,1,1,1,'2026-05-30 00:00:00','',0),(7,1,1,1,'2026-05-30 00:00:00','',0),(8,1,1,1,'2026-05-30 15:36:46','etes',0);
/*!40000 ALTER TABLE `salida_insumo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salida_insumo_detalle`
--

DROP TABLE IF EXISTS `salida_insumo_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salida_insumo_detalle` (
  `idsalida_consumible_detalle` int NOT NULL AUTO_INCREMENT,
  `idsalida_consumible` int NOT NULL,
  `id_articulo` int NOT NULL,
  `cantidad` decimal(12,3) NOT NULL,
  PRIMARY KEY (`idsalida_consumible_detalle`),
  KEY `idsalida_consumible` (`idsalida_consumible`),
  CONSTRAINT `salida_insumo_detalle_ibfk_1` FOREIGN KEY (`idsalida_consumible`) REFERENCES `salida_insumo` (`idsalida_consumible`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salida_insumo_detalle`
--

LOCK TABLES `salida_insumo_detalle` WRITE;
/*!40000 ALTER TABLE `salida_insumo_detalle` DISABLE KEYS */;
INSERT INTO `salida_insumo_detalle` VALUES (1,1,21,1.000),(2,2,21,1.000),(3,3,21,1.000),(4,4,21,1.000),(5,5,21,1.000),(6,6,21,1.000),(7,7,21,1.000),(8,8,21,1.000);
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
INSERT INTO `stock` VALUES (2,1,200,15,13.0000,'2026-05-30 23:51:37',1,76),(3,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(4,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(5,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(6,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(7,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(8,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(9,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(10,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(11,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(12,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(18,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(19,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(20,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(21,1,200,15,11.0000,'2026-05-30 15:38:06',1,73),(22,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(23,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(24,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(25,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(26,1,200,15,10.0000,'2026-05-26 21:31:27',1,16),(27,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(28,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(29,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(30,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(31,1,200,15,10.0000,'2026-05-26 21:31:27',1,16),(32,1,200,15,0.0000,'2026-05-26 21:31:27',1,16),(32,2,200,15,8.0000,'2026-05-26 22:26:56',7,17),(33,1,200,15,0.0000,'2026-05-30 19:48:08',1,74),(34,1,200,15,8.0000,'2026-05-29 21:12:30',1,56);
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
INSERT INTO `sucursal_documento` VALUES (1,NULL,1,1,'remision','001','002',6,1),(2,NULL,1,2,'remision','002','002',1,1);
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transferencia_stock`
--

LOCK TABLES `transferencia_stock` WRITE;
/*!40000 ALTER TABLE `transferencia_stock` DISABLE KEYS */;
INSERT INTO `transferencia_stock` VALUES (7,2,1,'2026-05-26 23:29:22','en_transito','etst',7,NULL,NULL,NULL);
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
INSERT INTO `transferencia_stock_detalle` VALUES (7,32,2.00,NULL);
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
INSERT INTO `usuario_rol` VALUES (1,7),(7,13),(10,7),(11,11),(12,13);
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
INSERT INTO `usuarios` VALUES (1,1,'Administrador','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'admin','Del Sistema','admins@admin.com.py','0986203431','1234567'),(7,2,'User','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'ucompra','Compra','ucompra@reducto.com.py','09862349732','1234566'),(8,1,'user','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'uservicio','Servicio','uservicio@reducto.com.py','0986234973','1234560'),(10,1,'Jorge','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'jdure','Dure','jdure@gmail.com','0985123654','5326548'),(11,2,'Angel','$2y$10$VQ/16VeuxoXk9SWafKK6ZeSwzC0yE5YSFnL0cyeg.SK2V6vzMH1d.',1,0,0,0,'adure','Dure','adure@reduc.com','0985123651','5456789'),(12,2,'Diego','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'ddure','Dure','ddure@admin.com','0985123654','6456789'),(13,1,'Rufino','$2y$10$GneMLiQCeRhiJW4diFDyf.zjJG70X69B8vhZh36ay/rOHLYzcN3Am',1,0,0,0,'rdure','Dure','rdure@admin.com','0985123456','2456987');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehiculos`
--

LOCK TABLES `vehiculos` WRITE;
/*!40000 ALTER TABLE `vehiculos` DISABLE KEYS */;
INSERT INTO `vehiculos` VALUES (17,11,1,'RGB513',2025,1,'negro','estandar','automatica','v6asddas-','Automovil','2026-05-26 22:13:49',NULL),(18,19,4,'AASZ012',NULL,1,'negro','',NULL,NULL,'','2026-05-29 16:13:19',NULL),(19,20,15,'ADJF4564',NULL,1,'blanco','','','','Camioneta','2026-05-31 10:56:29',NULL),(20,22,18,'DFH458',NULL,1,'NEGRO','','','','Camioneta','2026-05-31 10:58:26',NULL);
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

-- Dump completed on 2026-05-31 19:56:49
