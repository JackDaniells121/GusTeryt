-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Czas generowania: 26 Sie 2020, 18:25
-- Wersja serwera: 8.0.19
-- Wersja PHP: 7.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `teryt`
--
CREATE DATABASE IF NOT EXISTS `teryt` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `teryt`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `TerytDistrict`
--

CREATE TABLE `TerytDistrict` (
  `Id` varchar(13) NOT NULL,
  `TerytId` varchar(4) NOT NULL,
  `Name` varchar(333) NOT NULL,
  `Population` int DEFAULT NULL,
  `DateCreated` date NOT NULL,
  `DateModified` date DEFAULT NULL,
  `DateValidated` date DEFAULT NULL,
  `Geometry` geometry DEFAULT NULL,
  `TerytRegion` varchar(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `TerytMunicipal`
--

CREATE TABLE `TerytMunicipal` (
  `Id` varchar(13) NOT NULL,
  `TerytId` varchar(6) NOT NULL,
  `Name` varchar(333) NOT NULL,
  `Population` int DEFAULT NULL,
  `Geometry` geometry DEFAULT NULL,
  `DateCreated` date NOT NULL,
  `DateModified` date DEFAULT NULL,
  `DateValidated` date DEFAULT NULL,
  `TerytDistrict` varchar(13) NOT NULL,
  `Type` varchar(33) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `TerytRegion`
--

CREATE TABLE `TerytRegion` (
  `Id` varchar(13) NOT NULL,
  `TerytId` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Name` varchar(333) NOT NULL,
  `Geometry` geometry DEFAULT NULL,
  `Population` int DEFAULT NULL,
  `DateCreated` date NOT NULL,
  `DateModified` date DEFAULT NULL,
  `DateVerified` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `TerytDistrict`
--
ALTER TABLE `TerytDistrict`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `TerytMunicipal`
--
ALTER TABLE `TerytMunicipal`
  ADD PRIMARY KEY (`Id`);

--
-- Indeksy dla tabeli `TerytRegion`
--
ALTER TABLE `TerytRegion`
  ADD PRIMARY KEY (`Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
