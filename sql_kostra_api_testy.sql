-- Najskor vytvor databazu s nazvom wt_skuskove_zadanie_databaza_testov a v nej spusti cely tento subor

START TRANSACTION;

CREATE TABLE typy_otazok (
	id tinyint NOT NULL AUTO_INCREMENT,
	typ tinytext NOT NULL,
	PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO typy_otazok (id, typ) VALUES
(1, 'kratka_odpoved'),
(2, 'viacere_odpovede'),
(3, 'parovanie'),
(4, 'kreslenie'),
(5, 'matematicky_vyraz');



CREATE TABLE zoznam_testov (
	kluc_testu varchar(40) NOT NULL,
	kto_vytvoril int NOT NULL,
	nazov text NOT NULL,
	casovy_limit tinyint NOT NULL COMMENT 'v minutach',
	aktivny tinyint(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (kluc_testu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE zoznam_testov_otazky (
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	nazov tinytext NOT NULL,
	typ tinyint(1) NOT NULL DEFAULT 1,
	znamy_pocet_spravnych tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ci student vie pocet spravnych odpovedi, relevantne iba pre otazky s viacerymi odpovedami',

	PRIMARY KEY (kluc_testu, otazka_id),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (typ) REFERENCES typy_otazok(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE zoznam_testov_otazky_typy_1_2 (
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	odpoved varchar(250) NOT NULL,
	je_spravna tinyint(1) NOT NULL DEFAULT 0,

	PRIMARY KEY (kluc_testu, otazka_id, odpoved),
	FOREIGN KEY (kluc_testu, otazka_id) REFERENCES zoznam_testov_otazky(kluc_testu, otazka_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE zoznam_testov_otazky_typ_3 (
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	odpoved_id tinyint NOT NULL,
	odpoved varchar(250) NOT NULL,
	strana varchar(1) NOT NULL COMMENT '\'L\' alebo \'P\'',
	sparovana_odpoved_id tinyint NOT NULL DEFAULT 0 COMMENT 'ak odpoved nema par, je tu hodnota 0',

	PRIMARY KEY (kluc_testu, otazka_id, odpoved),
	FOREIGN KEY (kluc_testu, otazka_id) REFERENCES zoznam_testov_otazky(kluc_testu, otazka_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE zoznam_pisucich_studentov (
	kluc_testu varchar(40) NOT NULL,
	student_id varchar(40) NOT NULL,
	zostavajuci_cas int NOT NULL COMMENT 'v sekundach',

	PRIMARY KEY (kluc_testu, student_id),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


COMMIT;