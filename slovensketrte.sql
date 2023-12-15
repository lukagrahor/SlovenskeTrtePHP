-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gostitelj: 127.0.0.1
-- Čas nastanka: 15. dec 2023 ob 19.32
-- Različica strežnika: 10.4.28-MariaDB
-- Različica PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Zbirka podatkov: `slovensketrte`
--

-- --------------------------------------------------------

--
-- Struktura tabele `pridelek`
--

CREATE TABLE `pridelek` (
  `ID` int(5) NOT NULL,
  `vrsta` varchar(35) NOT NULL,
  `kolicina_pridelka` int(10) NOT NULL,
  `kolicina_prodanega_pridelka` int(10) NOT NULL,
  `cena` int(10) NOT NULL,
  `zemljisce_ID` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `pridelek`
--

INSERT INTO `pridelek` (`ID`, `vrsta`, `kolicina_pridelka`, `kolicina_prodanega_pridelka`, `cena`, `zemljisce_ID`) VALUES
(3, 'refosk', 450, 250, 7, 4),
(4, 'teran', 500, 400, 4, 5),
(6, 'Teran', 400, 300, 5, 9),
(7, 'rijoha', 500, 50, 3, 10),
(8, 'Beli pinot', 120, 90, 5, 11),
(9, 'Rose', 50, 30, 40, 10);

-- --------------------------------------------------------

--
-- Struktura tabele `vinogradnik`
--

CREATE TABLE `vinogradnik` (
  `ID` int(5) NOT NULL,
  `ime` varchar(35) NOT NULL,
  `priimek` varchar(35) NOT NULL,
  `naslov` varchar(50) NOT NULL,
  `telefon` int(15) NOT NULL,
  `e_posta` varchar(50) NOT NULL,
  `uporabnisko_ime` varchar(50) NOT NULL,
  `geslo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `vinogradnik`
--

INSERT INTO `vinogradnik` (`ID`, `ime`, `priimek`, `naslov`, `telefon`, `e_posta`, `uporabnisko_ime`, `geslo`) VALUES
(4, 'Peter', 'Novak', 'Novakovo 125', 31265789, 'janezn125@gmail.com', 'janezn125', 'hojladrija'),
(6, 'qwasd', 'asdads', 'asdasd', 31213431, 'ahah@gmail.com', 'janezn1', 'sadasd'),
(7, 'Frane', 'Franov', 'Fransko3', 68452312, 'frane@gmail.com', 'frane123', 'frane123'),
(9, 'Luka', 'Grahot', 'Zgornji Zemon 22', 68265659, 'lgrahot@gmail.com', 'lgrahot', 'lgrahot123'),
(11, 'Ti', 'Lenoba', 'Luknja3', 123321123, 'tilen@gmail.com', 'tilen25', 'tilen12345');

-- --------------------------------------------------------

--
-- Struktura tabele `zemljisce`
--

CREATE TABLE `zemljisce` (
  `ID` int(5) NOT NULL,
  `velikost` int(10) NOT NULL,
  `kolicina_trt` int(11) NOT NULL,
  `vinogradnik_ID` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Odloži podatke za tabelo `zemljisce`
--

INSERT INTO `zemljisce` (`ID`, `velikost`, `kolicina_trt`, `vinogradnik_ID`) VALUES
(2, 30, 50, 4),
(3, 100, 40, 4),
(4, 90, 80, 7),
(5, 300, 200, 7),
(6, 700, 500, 4),
(9, 250, 200, 9),
(10, 200, 100, 9),
(11, 800, 120, 11);

--
-- Indeksi zavrženih tabel
--

--
-- Indeksi tabele `pridelek`
--
ALTER TABLE `pridelek`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `zemljisce_FK` (`zemljisce_ID`);

--
-- Indeksi tabele `vinogradnik`
--
ALTER TABLE `vinogradnik`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `uporabnisko_ime` (`uporabnisko_ime`);

--
-- Indeksi tabele `zemljisce`
--
ALTER TABLE `zemljisce`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `vinogradnik_FK` (`vinogradnik_ID`);

--
-- AUTO_INCREMENT zavrženih tabel
--

--
-- AUTO_INCREMENT tabele `pridelek`
--
ALTER TABLE `pridelek`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT tabele `vinogradnik`
--
ALTER TABLE `vinogradnik`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT tabele `zemljisce`
--
ALTER TABLE `zemljisce`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Omejitve tabel za povzetek stanja
--

--
-- Omejitve za tabelo `pridelek`
--
ALTER TABLE `pridelek`
  ADD CONSTRAINT `zemljisce_FK` FOREIGN KEY (`zemljisce_ID`) REFERENCES `zemljisce` (`ID`);

--
-- Omejitve za tabelo `zemljisce`
--
ALTER TABLE `zemljisce`
  ADD CONSTRAINT `vinogradnik_FK` FOREIGN KEY (`vinogradnik_ID`) REFERENCES `vinogradnik` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
