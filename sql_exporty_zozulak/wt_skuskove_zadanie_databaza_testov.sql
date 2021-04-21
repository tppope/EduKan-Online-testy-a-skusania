-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: St 21.Apr 2021, 21:45
-- Verzia serveru: 10.4.17-MariaDB
-- Verzia PHP: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `wt_skuskove_zadanie_databaza_testov`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `typy_otazok`
--

CREATE TABLE `typy_otazok` (
  `id` tinyint(4) NOT NULL,
  `typ` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Sťahujem dáta pre tabuľku `typy_otazok`
--

INSERT INTO `typy_otazok` (`id`, `typ`) VALUES
(1, 'kratka_odpoved'),
(2, 'viacere_odpovede'),
(3, 'parovanie'),
(4, 'kreslenie'),
(5, 'matematicky_vyraz');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `zoznam_testov`
--

CREATE TABLE `zoznam_testov` (
  `id` int(11) NOT NULL,
  `kto_vytvoril` int(11) NOT NULL,
  `nazov` text NOT NULL,
  `casovy_limit` tinyint(4) NOT NULL COMMENT 'v minutach',
  `aktivny` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `typy_otazok`
--
ALTER TABLE `typy_otazok`
  ADD PRIMARY KEY (`id`);

--
-- Indexy pre tabuľku `zoznam_testov`
--
ALTER TABLE `zoznam_testov`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `typy_otazok`
--
ALTER TABLE `typy_otazok`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `zoznam_testov`
--
ALTER TABLE `zoznam_testov`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
