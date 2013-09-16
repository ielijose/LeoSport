/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50051
Source Host           : localhost:3306
Source Database       : leosport

Target Server Type    : MYSQL
Target Server Version : 50051
File Encoding         : 65001

Date: 2013-09-15 22:21:47
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of clientes
-- ----------------------------
INSERT INTO `clientes` VALUES ('1', '21382657', 'Eli José Carrasquero', 'Santa Rita');
INSERT INTO `clientes` VALUES ('2', '11452007', 'Yuneira Mata', 'saasf asf');
INSERT INTO `clientes` VALUES ('3', '15123', 'Julmer Oliveros', 'Maracaibo');
INSERT INTO `clientes` VALUES ('4', '123', 'Dánielle', 'asf');

-- ----------------------------
-- Table structure for facturas
-- ----------------------------
DROP TABLE IF EXISTS `facturas`;
CREATE TABLE `facturas` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cliente_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of facturas
-- ----------------------------
INSERT INTO `facturas` VALUES ('1', '1', '2013-09-15 00:03:29');
INSERT INTO `facturas` VALUES ('2', '1', '2013-09-15 16:47:45');

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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES ('1', 'Balon', '10', '30');
INSERT INTO `productos` VALUES ('2', 'camisa', '2', '620');
INSERT INTO `productos` VALUES ('3', 'Short', '15', '80');
INSERT INTO `productos` VALUES ('4', 'Julmer', '3', '100');
INSERT INTO `productos` VALUES ('5', 'prueba', '3', '61651');
INSERT INTO `productos` VALUES ('6', 'prueba2', '1000', '2165');
INSERT INTO `productos` VALUES ('7', 'prueba 3', '165165', '151');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES ('1', 'ielijose', '2512368', 'on');
INSERT INTO `usuarios` VALUES ('2', 'anamar', '1234', 'on');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ventas
-- ----------------------------
