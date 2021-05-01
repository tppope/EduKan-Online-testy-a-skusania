-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: So 01.Máj 2021, 08:50
-- Verzia serveru: 8.0.23-0ubuntu0.20.04.1
-- Verzia PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `wt_skuskove_zadanie_databaza_pouzivatelov`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `ucitel`
--

CREATE TABLE `ucitel` (
  `id` int UNSIGNED NOT NULL,
  `meno` varchar(256) COLLATE utf8mb4_slovak_ci NOT NULL,
  `priezvisko` varchar(256) COLLATE utf8mb4_slovak_ci NOT NULL,
  `email` varchar(256) COLLATE utf8mb4_slovak_ci NOT NULL,
  `heslo` varchar(512) COLLATE utf8mb4_slovak_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_slovak_ci;

--
-- Sťahujem dáta pre tabuľku `ucitel`
--

INSERT INTO `ucitel` (`id`, `meno`, `priezvisko`, `email`, `heslo`) VALUES
(1, 'Tomáš', 'Popík', 'tpopik1999@gmail.com', '$2y$10$RujqG4fwjcVfqV0cm9FiM.xABmrFyA3.CabrHRKVobcrcKE.Lnitu'),
(15, 'Katarína', 'Stasová', 'stasovakatka6688@gmail.com', '$2y$10$EDMejo8WjZGq1.39U.TUpuGH1eqVErFKYrCmeo5Bkc1TJSqgnc3Ua'),
(17, 'Martin', 'Smetanka', 'martin.smetanka@gmail.com', '$2y$10$CtRR0N2hggwhncrrbie5TORT1AHKX.qU8dHOjklL3T1pT1EosFRxa'),
(18, 'Filip', 'Poljak Škobla', 'skoblafilip@gmail.com', '$2y$10$cRuiA6raq9zH8.CT7rM1mOQdgzoDvMjLNKwbul4YL6l/5zZ1cOL9K'),
(19, 'Juraj', 'Zozuľak', 'xzozulak@stuba.sk', '$2y$10$YTedAjDS34WfbFXuIgP6YePd/2Sk.en9VlYgI5KVAHyqEfIt5Og5u'),
(20, 'Filip', 'Poljak Škobla', 'filipkoskobla@gmail.com', '$2y$10$E/J4FbxwNeeDOmmJkO/bTum/I2Cuz.RJvSbh9Y3a7f78U/umRNQFi'),
(22, 'Filip', 'Poljak Škobla', 'xpoljakskobla@stuba.sk', '$2y$10$hJhX/jKQD9CgrTj3zNBE2.g8ALdPOcNBBjwAU1UvniVrOOzDjwx5O'),
(23, 'Filip', 'Poljak Škobla', 'arthas11111@gmail.com', '$2y$10$Yb2W7N75mFvS0Vz7x/FQP.JnGZwuOZJEFZ0w.R0D1Ey2GEwl7YzPe'),
(24, 'Filip', 'Poljak Škobla', 'flopisvk@gmail.com', '$2y$10$kXZ9gAcW0inbmwFtHkwa2.VG39tkZwBbHYPpC9AKR5vbCBHI7wZly');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `ucitel`
--
ALTER TABLE `ucitel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `ucitel`
--
ALTER TABLE `ucitel`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
