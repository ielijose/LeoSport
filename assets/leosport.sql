/*
Navicat MySQL Data Transfer

Source Server         : localhostz
Source Server Version : 50051
Source Host           : 127.0.0.1:3306
Source Database       : leosport

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2013-10-15 08:30:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for clientes
-- ----------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ci` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of clientes
-- ----------------------------
INSERT INTO `clientes` VALUES ('1', '21382657', 'Eli José Carrasquero', 'Calle la milagrosa Sector 19 de Abril, Santa Rita, Zulia');
INSERT INTO `clientes` VALUES ('2', '11452007', 'Yuneira Mata', 'saasf asf');
INSERT INTO `clientes` VALUES ('3', '15123', 'Julmer Oliveros', 'Maracaibo');
INSERT INTO `clientes` VALUES ('4', '123', 'Dánielle', 'asf');
INSERT INTO `clientes` VALUES ('5', '20333547', 'José David Casanova', 'Griseldita');
INSERT INTO `clientes` VALUES ('6', '19098084', 'Jorge Garces', 'Frente al liceo');
INSERT INTO `clientes` VALUES ('7', '123456789', 'prueba', 'asf');
INSERT INTO `clientes` VALUES ('8', '16023020', 'Javier Borjas', 'la pelona');
INSERT INTO `clientes` VALUES ('9', '16847868', 'Hostwerl Reyes', 'dñfgmnSDG');
INSERT INTO `clientes` VALUES ('10', '18682281', 'Janetzy Romero', 'Santa Rita\n');

-- ----------------------------
-- Table structure for facturas
-- ----------------------------
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cliente_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('venta','apartado') NOT NULL default 'venta',
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facturas
-- ----------------------------
INSERT INTO `facturas` VALUES ('1', '1', '1', 'apartado', '2013-10-05 15:25:27');
INSERT INTO `facturas` VALUES ('2', '1', '1', 'venta', '2013-09-18 23:55:21');
INSERT INTO `facturas` VALUES ('3', '6', '1', 'venta', '2013-09-23 21:40:00');
INSERT INTO `facturas` VALUES ('4', '2', '1', 'venta', '2013-09-25 22:17:09');
INSERT INTO `facturas` VALUES ('5', '2', '1', 'venta', '2013-09-25 22:19:18');
INSERT INTO `facturas` VALUES ('6', '2', '1', 'venta', '2013-09-25 22:19:59');
INSERT INTO `facturas` VALUES ('7', '2', '1', 'venta', '2013-09-25 22:20:49');
INSERT INTO `facturas` VALUES ('8', '2', '1', 'venta', '2013-09-25 22:20:53');
INSERT INTO `facturas` VALUES ('9', '2', '1', 'venta', '2013-09-25 22:20:54');
INSERT INTO `facturas` VALUES ('10', '2', '1', 'venta', '2013-09-25 22:20:59');
INSERT INTO `facturas` VALUES ('11', '2', '1', 'venta', '2013-09-25 22:21:00');
INSERT INTO `facturas` VALUES ('12', '2', '1', 'venta', '2013-09-25 22:21:32');
INSERT INTO `facturas` VALUES ('13', '2', '1', 'venta', '2013-09-25 22:22:08');
INSERT INTO `facturas` VALUES ('14', '1', '1', 'venta', '2013-09-27 20:44:03');
INSERT INTO `facturas` VALUES ('15', '2', '1', 'venta', '2013-09-28 12:22:58');
INSERT INTO `facturas` VALUES ('16', '1', '1', 'venta', '2013-09-29 17:36:35');
INSERT INTO `facturas` VALUES ('17', '8', '1', 'venta', '2013-09-30 19:12:48');
INSERT INTO `facturas` VALUES ('18', '9', '1', 'venta', '2013-10-04 20:57:40');
INSERT INTO `facturas` VALUES ('19', '10', '1', 'venta', '2013-10-12 17:44:42');
INSERT INTO `facturas` VALUES ('20', '2', '1', 'apartado', '2013-10-12 17:57:07');
INSERT INTO `facturas` VALUES ('21', '2', '1', 'venta', '2013-10-12 17:58:22');
INSERT INTO `facturas` VALUES ('22', '10', '1', 'venta', '2013-10-12 17:58:50');
INSERT INTO `facturas` VALUES ('23', '10', '1', 'apartado', '2013-10-12 18:00:21');

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `producto` varchar(255) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` float NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES ('1', 'Balon', '-1', '30');
INSERT INTO `productos` VALUES ('2', 'camisa', '0', '620');
INSERT INTO `productos` VALUES ('3', 'Short', '6', '80');
INSERT INTO `productos` VALUES ('4', 'Julmer', '0', '100');
INSERT INTO `productos` VALUES ('5', 'prueba', '1', '61651');
INSERT INTO `productos` VALUES ('6', 'prueba2', '999', '2165');
INSERT INTO `productos` VALUES ('7', 'prueba 3', '165164', '151');
INSERT INTO `productos` VALUES ('8', 'Jose David', '9', '500');
INSERT INTO `productos` VALUES ('9', 'Kit de entrenamiento', '2', '1500');
INSERT INTO `productos` VALUES ('10', 'Hoswer', '99', '20');

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('on','off') NOT NULL default 'on',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES ('1', 'ielijose', '2512368', 'on');
INSERT INTO `usuarios` VALUES ('2', 'anamar', '1234', 'on');
INSERT INTO `usuarios` VALUES ('3', 'julmer', '1234', 'on');

-- ----------------------------
-- Table structure for ventas
-- ----------------------------
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `factura_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` float NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ventas
-- ----------------------------
INSERT INTO `ventas` VALUES ('1', '1', '2', '2', '620');
INSERT INTO `ventas` VALUES ('2', '1', '4', '3', '100');
INSERT INTO `ventas` VALUES ('3', '2', '1', '1', '30');
INSERT INTO `ventas` VALUES ('4', '3', '9', '1', '1500');
INSERT INTO `ventas` VALUES ('5', '4', '1', '1', '30');
INSERT INTO `ventas` VALUES ('6', '5', '1', '1', '30');
INSERT INTO `ventas` VALUES ('7', '6', '1', '1', '30');
INSERT INTO `ventas` VALUES ('8', '7', '1', '1', '30');
INSERT INTO `ventas` VALUES ('9', '8', '1', '1', '30');
INSERT INTO `ventas` VALUES ('10', '9', '1', '1', '30');
INSERT INTO `ventas` VALUES ('11', '10', '1', '2', '30');
INSERT INTO `ventas` VALUES ('12', '11', '1', '2', '30');
INSERT INTO `ventas` VALUES ('13', '12', '3', '1', '80');
INSERT INTO `ventas` VALUES ('14', '13', '3', '1', '80');
INSERT INTO `ventas` VALUES ('15', '14', '3', '1', '80');
INSERT INTO `ventas` VALUES ('16', '14', '5', '1', '61651');
INSERT INTO `ventas` VALUES ('17', '14', '6', '1', '2165');
INSERT INTO `ventas` VALUES ('18', '14', '7', '1', '151');
INSERT INTO `ventas` VALUES ('19', '14', '8', '1', '500');
INSERT INTO `ventas` VALUES ('20', '14', '9', '1', '1500');
INSERT INTO `ventas` VALUES ('21', '15', '3', '1', '80');
INSERT INTO `ventas` VALUES ('22', '16', '3', '1', '80');
INSERT INTO `ventas` VALUES ('23', '17', '9', '1', '1500');
INSERT INTO `ventas` VALUES ('24', '18', '10', '1', '20');
INSERT INTO `ventas` VALUES ('25', '19', '3', '1', '80');
INSERT INTO `ventas` VALUES ('26', '20', '3', '1', '80');
INSERT INTO `ventas` VALUES ('27', '21', '3', '1', '80');
INSERT INTO `ventas` VALUES ('28', '22', '5', '1', '61651');
INSERT INTO `ventas` VALUES ('29', '23', '3', '1', '80');

-- ----------------------------
-- View structure for view_ventas
-- ----------------------------
DROP VIEW IF EXISTS `view_ventas`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost`  VIEW `view_ventas` AS SELECT v.id, v.factura_id, p.producto, v.cantidad, v.precio
FROM ventas v
JOIN productos p ON p.id = v.producto_id ;
