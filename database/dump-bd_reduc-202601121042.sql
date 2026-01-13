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
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajuste_inventario_detalle`
--

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
-- Table structure for table `apercier_cajas`
--

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
-- Table structure for table `articulos`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bancos`
--

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
-- Table structure for table `cajas`
--

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
-- Table structure for table `cargos`
--

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
-- Table structure for table `categorias`
--

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
-- Table structure for table `ciudades`
--

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
-- Table structure for table `clientes`
--

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
-- Table structure for table `cobro_cheque`
--

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
-- Table structure for table `cobro_detalle`
--

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
-- Table structure for table `cobro_efectivo`
--

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
-- Table structure for table `cobro_tarjeta`
--

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
-- Table structure for table `cobros`
--

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
-- Table structure for table `colores`
--

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
-- Table structure for table `compra_cabecera`
--

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
  `total_compra` double DEFAULT NULL,
  `condicion` varchar(20) DEFAULT NULL,
  `compra_intervalo` varchar(20) DEFAULT NULL,
  `idOcompra` int(10) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `updatedby` int(10) DEFAULT NULL,
  PRIMARY KEY (`idcompra_cabecera`),
  KEY `compra_cabecera_FKIndex1` (`id_usuario`),
  KEY `compra_cabecera_FKIndex2` (`idproveedores`),
  KEY `idx_compra_sucursal` (`id_sucursal`),
  CONSTRAINT `compra_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `compra_cabecera_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`),
  CONSTRAINT `fk_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compra_detalle`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra_detalle` (
  `idcompra_cabecera` int(10) unsigned NOT NULL,
  `id_articulo` int(10) unsigned NOT NULL,
  `precio_unitario` double NOT NULL,
  `cantidad_recibida` bigint(20) DEFAULT NULL,
  `subtotal` double DEFAULT NULL,
  `ivaPro` double DEFAULT NULL,
  `tipo_iva` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idcompra_cabecera`,`id_articulo`),
  KEY `compra_cabecera_has_orden_compra_detalle_FKIndex1` (`idcompra_cabecera`),
  KEY `compra_detalle_FKIndex2` (`id_articulo`),
  CONSTRAINT `compra_detalle_ibfk_1` FOREIGN KEY (`idcompra_cabecera`) REFERENCES `compra_cabecera` (`idcompra_cabecera`),
  CONSTRAINT `compra_detalle_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuentas_a_pagar`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cuentas_cobrar`
--

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
-- Table structure for table `depositos`
--

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
-- Table structure for table `descuento_cliente`
--

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
-- Table structure for table `descuentos`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `detalle_fac`
--

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
-- Table structure for table `empleados`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `empresa`
--

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
-- Table structure for table `entidad_emisora`
--

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
-- Table structure for table `equipo_empleado`
--

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
-- Table structure for table `equipo_trabajo`
--

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
-- Table structure for table `equipo_trabajo_old`
--

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
-- Table structure for table `factura`
--

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
-- Table structure for table `factura_servicio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_servicio` (
  `idfactura` int(10) unsigned NOT NULL,
  KEY `registro_servicio_has_factura_FKIndex2` (`idfactura`),
  CONSTRAINT `factura_servicio_ibfk_2` FOREIGN KEY (`idfactura`) REFERENCES `factura` (`idfactura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `forma_cobro`
--

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
-- Table structure for table `libro_compra`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libro_venta`
--

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
-- Table structure for table `marcas`
--

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
-- Table structure for table `modelo_auto`
--

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
-- Table structure for table `nota_compra`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nota_compra` (
  `idnota_compra` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `idproveedor` bigint(20) unsigned DEFAULT NULL,
  `tipo` varchar(20) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nota_compra_detalle`
--

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
-- Table structure for table `nota_remision`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nota_remision_detalle`
--

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
-- Table structure for table `orden_compra`
--

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
  CONSTRAINT `fk_orden_compra_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `orden_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `orden_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_compra_detalle`
--

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
-- Table structure for table `orden_trabajo`
--

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
  KEY `fk_ot_recepcion` (`idrecepcion`),
  KEY `fk_ot_usuario` (`id_usuario`),
  KEY `fk_ot_equipo` (`idtrabajos`),
  KEY `fk_ot_tecnico` (`tecnico_responsable`),
  CONSTRAINT `fk_ot_equipo` FOREIGN KEY (`idtrabajos`) REFERENCES `equipo_trabajo` (`id_equipo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ot_presupuesto` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `fk_ot_recepcion` FOREIGN KEY (`idrecepcion`) REFERENCES `recepcion_servicio` (`idrecepcion`),
  CONSTRAINT `fk_ot_tecnico` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_ot_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `orden_trabajo_detalle`
--

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
  CONSTRAINT `fk_ot_detalle_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `fk_ot_detalle_ot` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ordencompra_compra`
--

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
-- Table structure for table `pedido_cabecera`
--

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
  CONSTRAINT `fk_pedido_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `pedido_cabecera_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pedido_detalle`
--

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
-- Table structure for table `permisos`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id_permiso` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clave` varchar(100) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_permiso`),
  UNIQUE KEY `clave` (`clave`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto_compra`
--

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
  CONSTRAINT `fk_presupuesto_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `presupuesto_compra_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `presupuesto_compra_ibfk_2` FOREIGN KEY (`idproveedores`) REFERENCES `proveedores` (`idproveedores`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto_descuento`
--

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
-- Table structure for table `presupuesto_detalle`
--

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
-- Table structure for table `presupuesto_detalleservicio`
--

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
  CONSTRAINT `presupuesto_detalleservicio_ibfk_1` FOREIGN KEY (`idpresupuesto_servicio`) REFERENCES `presupuesto_servicio` (`idpresupuesto_servicio`),
  CONSTRAINT `presupuesto_detalleservicio_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `presupuesto_promocion`
--

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
-- Table structure for table `presupuesto_servicio`
--

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
  CONSTRAINT `presupuesto_servicio_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `promocion_producto`
--

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
-- Table structure for table `promociones`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `proveedores`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recaudacion_deposito`
--

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
-- Table structure for table `recepcion_servicio`
--

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
  CONSTRAINT `fk_recepcion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_recepcion_sucursal` FOREIGN KEY (`id_sucursal`) REFERENCES `sucursales` (`id_sucursal`),
  CONSTRAINT `fk_recepcion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `fk_recepcion_vehiculo` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reclamo_servicio`
--

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
-- Table structure for table `reclamos_servicio`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reclamos_servicio` (
  `cantidad` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registro_servicio`
--

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
  CONSTRAINT `fk_rs_ot` FOREIGN KEY (`idorden_trabajo`) REFERENCES `orden_trabajo` (`idorden_trabajo`),
  CONSTRAINT `fk_rs_tecnico` FOREIGN KEY (`tecnico_responsable`) REFERENCES `empleados` (`idempleados`),
  CONSTRAINT `fk_rs_usuario` FOREIGN KEY (`usuario_registra`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registro_servicio_detalle`
--

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
  CONSTRAINT `fk_rs_detalle_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rol_permiso`
--

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
-- Table structure for table `roles`
--

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
-- Table structure for table `snc_compras`
--

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
-- Table structure for table `snc_compras_detalle`
--

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
-- Table structure for table `snc_compras_diferencia`
--

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
-- Table structure for table `stock`
--

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
-- Table structure for table `sucmovimientostock`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sucursal_documento`
--

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
-- Table structure for table `sucursal_timbrado`
--

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
-- Table structure for table `sucursales`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `timbrado`
--

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
-- Table structure for table `tipo_impuesto`
--

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
-- Table structure for table `transferencia_stock`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transferencia_stock_detalle`
--

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
-- Table structure for table `unidad_medida`
--

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
-- Table structure for table `usuario_rol`
--

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
-- Table structure for table `usuarios`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vehiculos`
--

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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

-- Dump completed on 2026-01-12 10:42:58
