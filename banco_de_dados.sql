-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 13/10/2023 às 15:09
-- Versão do servidor: 10.5.19-MariaDB-cll-lve-log
-- Versão do PHP: 8.1.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `agendamentos` (
  `id` int(1) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `telefone` varchar(255) NOT NULL,
  `data_hora` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `servico` varchar(255) NOT NULL,
  `atendente` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `agendamentos`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE `admin_log` (
  `id` tinyint(4) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `admin_log` (`id`, `username`, `password`) VALUES
(1, 'admin', md5('admin'));


ALTER TABLE `admin_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `admin_log`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;


CREATE TABLE IF NOT EXISTS `servicos` (
    `id` int(1) NOT NULL AUTO_INCREMENT,
    `nome` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `horarios` (
    `id` int(1) NOT NULL AUTO_INCREMENT,
    `horario` varchar(255) NOT NULL,
    `disponivel` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE atendentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cargo VARCHAR(255) NOT NULL,
    disponivel TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE atendente_servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atendente_id INT,
    servico_id INT,
    FOREIGN KEY (atendente_id) REFERENCES atendentes(id),
    FOREIGN KEY (servico_id) REFERENCES servicos(id)
);