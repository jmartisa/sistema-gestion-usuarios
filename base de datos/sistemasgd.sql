SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `sistemasgd_v2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `sistemasgd_v2`;

-- 1. Roles (Ahora se crea primero para que las otras tablas puedan referenciarla)
CREATE TABLE `roles` (
  `idpermiso` int(11) NOT NULL AUTO_INCREMENT,
  `permiso` varchar(50) NOT NULL,
  PRIMARY KEY (`idpermiso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Usuario (Corregido: contraseña a 255, teléfonos a VARCHAR, y agrega id_rol)
CREATE TABLE `usuario` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `cel` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `perfil_usuario` varchar(50) NOT NULL,
  `departamento` varchar(50) DEFAULT NULL,
  `ciudad` varchar(50) DEFAULT NULL,
  `id_rol` int(11) NOT NULL, -- Nueva columna para la relación correcta
  PRIMARY KEY (`idusuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`idpermiso`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Categorización productos
CREATE TABLE `categorizacion_productos` (
  `idcate_prod` int(15) NOT NULL AUTO_INCREMENT,
  `estado` varchar(50) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  PRIMARY KEY (`idcate_prod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Producto (Corregido: nombre y longitud de valor)
CREATE TABLE `producto` (
  `Idproducto` int(15) NOT NULL AUTO_INCREMENT,
  `nombre_producto` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL, -- Mejor decimal para dinero
  `idcategoria` int(15) NOT NULL,
  PRIMARY KEY (`Idproducto`),
  KEY `idcategoria` (`idcategoria`),
  CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`idcategoria`) REFERENCES `categorizacion_productos` (`idcate_prod`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tipo proveedor (Corregido: tipo_proveedor ahora es VARCHAR)
CREATE TABLE `tipo_proveedor` (
  `idtipo_prov` int(15) NOT NULL AUTO_INCREMENT,
  `tipo_proveedor` varchar(50) NOT NULL,
  PRIMARY KEY (`idtipo_prov`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Gestión proveedor (Corregido: ahora referencia a producto y tipo_proveedor correctamente)
CREATE TABLE `gestion_proveedor` (
  `id_gestion` int(15) NOT NULL AUTO_INCREMENT, -- Nueva PK propia
  `Idproducto` int(15) NOT NULL,
  `idproveedor` int(15) NOT NULL,
  `sede` varchar(100) NOT NULL,
  `telprincipal` varchar(20) NOT NULL,
  PRIMARY KEY (`id_gestion`),
  KEY `Idproducto` (`Idproducto`),
  KEY `idproveedor` (`idproveedor`),
  CONSTRAINT `gestion_proveedor_ibfk_1` FOREIGN KEY (`Idproducto`) REFERENCES `producto` (`Idproducto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `gestion_proveedor_ibfk_2` FOREIGN KEY (`idproveedor`) REFERENCES `tipo_proveedor` (`idtipo_prov`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Clientes (Tabla independiente, sin FK invertida)
CREATE TABLE `clientes` (
  `idclientes` int(15) NOT NULL AUTO_INCREMENT,
  `responfiscal` varchar(100) NOT NULL,
  `matrimercantil` varchar(100) NOT NULL,
  `tipo_regimen` varchar(50) NOT NULL, -- Cambiado a VARCHAR para mayor claridad
  PRIMARY KEY (`idclientes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Gestor documentos (Corregido: documentos a 255, y relación lógica con clientes o proveedores)
CREATE TABLE `gestor_documentos` (
  `idanexosdoc` int(15) NOT NULL AUTO_INCREMENT,
  `idproveedor` int(15) DEFAULT NULL, -- Puede ser nulo si es de cliente
  `idcliente` int(15) DEFAULT NULL,   -- Puede ser nulo si es de proveedor
  `fechaingreso` date NOT NULL,
  `documentos` varchar(255) NOT NULL, -- Ruta o nombre del archivo
  PRIMARY KEY (`idanexosdoc`),
  KEY `idproveedor` (`idproveedor`),
  KEY `idcliente` (`idcliente`),
  CONSTRAINT `gestor_doc_ibfk_1` FOREIGN KEY (`idproveedor`) REFERENCES `tipo_proveedor` (`idtipo_prov`),
  CONSTRAINT `gestor_doc_ibfk_2` FOREIGN KEY (`idcliente`) REFERENCES `clientes` (`idclientes`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;