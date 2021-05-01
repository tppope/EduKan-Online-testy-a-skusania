-- Najskor vytvor databazu s nazvom wt_skuskove_zadanie_databaza_testov a v nej spusti cely tento subor

START TRANSACTION;

CREATE TABLE `typy_otazok` (
  `id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `typ` tinytext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `typy_otazok` (`id`, `typ`) VALUES
(1, 'kratka_odpoved'),
(2, 'viacere_odpovede'),
(3, 'parovanie'),
(4, 'kreslenie'),
(5, 'matematicky_vyraz');



CREATE TABLE `zoznam_testov` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kto_vytvoril` int(11) NOT NULL,
  `nazov` text NOT NULL,
  `casovy_limit` tinyint(4) NOT NULL COMMENT 'v minutach',
  `aktivny` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE `zoznam_testov_otvorenych` (
  `kluc` int(11) NOT NULL AUTO_INCREMENT,
  `test_id` int(11) NOT NULL,
  PRIMARY KEY (`kluc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




COMMIT;