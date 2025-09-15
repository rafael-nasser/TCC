CREATE DATABASE  IF NOT EXISTS `projeto_rafaelnasser` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `projeto_rafaelnasser`;
-- MySQL dump 10.13  Distrib 8.0.42, for macos15 (x86_64)
--
-- Host: localhost    Database: projeto_rafaelnasser
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calibracao`
--

DROP TABLE IF EXISTS `calibracao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calibracao` (
  `idcalibracao` int NOT NULL AUTO_INCREMENT,
  `equipamento` int NOT NULL,
  `nomeprovedor` longtext,
  `acreditado` char(1) DEFAULT 'N' COMMENT 'N ou S',
  `relatorionumero` varchar(45) DEFAULT NULL,
  `datarelatorio` date DEFAULT NULL,
  `validaderelatorio` date DEFAULT NULL,
  `resumoanalise` longtext,
  `analisedogpt` longtext,
  `arquivo`  varchar(255),
  PRIMARY KEY (`idcalibracao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calibracao`
--

LOCK TABLES `calibracao` WRITE;
/*!40000 ALTER TABLE `calibracao` DISABLE KEYS */;
/*!40000 ALTER TABLE `calibracao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamento`
--

DROP TABLE IF EXISTS `equipamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamento` (
  `idequipamento` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(245) DEFAULT NULL,
  `tag` varchar(45) DEFAULT NULL,
  `fabricante` varchar(45) DEFAULT NULL,
  `modelo` varchar(45) DEFAULT NULL,
  `obs` longtext,
  `local` int DEFAULT '0',
  PRIMARY KEY (`idequipamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamento`
--

LOCK TABLES `equipamento` WRITE;
/*!40000 ALTER TABLE `equipamento` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamentocronograma`
--

DROP TABLE IF EXISTS `equipamentocronograma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamentocronograma` (
  `idequipamentocronograma` int NOT NULL AUTO_INCREMENT,
  `equipamento` int NOT NULL,
  `status` varchar(45) DEFAULT 'Previsto' COMMENT 'Previsto|Realizado|Cancelado',
  `dataprevisto` date DEFAULT NULL,
  `daterealizado` date DEFAULT NULL,
  `obs` longtext,
  `titulo` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`idequipamentocronograma`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentocronograma`
--

LOCK TABLES `equipamentocronograma` WRITE;
/*!40000 ALTER TABLE `equipamentocronograma` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipamentocronograma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `equipamentofaixa`
--

DROP TABLE IF EXISTS `equipamentofaixa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipamentofaixa` (
  `idequipamentofaixa` int NOT NULL AUTO_INCREMENT,
  `equipamento` int NOT NULL,
  `grandeza` varchar(45) DEFAULT NULL,
  `faixade` varchar(45) DEFAULT NULL,
  `faixaate` varchar(45) DEFAULT NULL,
  `criterio` varchar(45) DEFAULT NULL,
  `calibrarem` longtext,
  PRIMARY KEY (`idequipamentofaixa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipamentofaixa`
--

LOCK TABLES `equipamentofaixa` WRITE;
/*!40000 ALTER TABLE `equipamentofaixa` DISABLE KEYS */;
/*!40000 ALTER TABLE `equipamentofaixa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `locais`
--

DROP TABLE IF EXISTS `locais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `locais` (
  `idlocais` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(245) NOT NULL,
  PRIMARY KEY (`idlocais`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `locais`
--

LOCK TABLES `locais` WRITE;
/*!40000 ALTER TABLE `locais` DISABLE KEYS */;
/*!40000 ALTER TABLE `locais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `idusuario` int NOT NULL AUTO_INCREMENT,
  `usuario` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `senha` longtext,
  `nome` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--
INSERT INTO usuario (usuario , nome, email, senha) VALUES ('admin','Admin', 'admin@exemplo.com', '$2y$10$sR6n8/HvJG4aBASZ8luvN.hfDiUZXTzn5wIO7dRQOFuq/J26Ipjh6');



LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-13 21:05:23
