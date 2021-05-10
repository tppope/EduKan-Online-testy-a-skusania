
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
	student_id int NOT NULL,
	zostavajuci_cas int NOT NULL COMMENT 'v sekundach',
	datum_zaciatku_pisania date NOT NULL,
	cas_zaciatku_pisania time NOT NULL,
	datum_konca_pisania date DEFAULT NULL,
	cas_konca_pisania time DEFAULT NULL,
    pocet_tab_odideni tinyint UNSIGNED NOT NULL DEFAULT '0',

	PRIMARY KEY (student_id, datum_zaciatku_pisania, cas_zaciatku_pisania),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




CREATE TABLE odpovede_studentov_typ_1_4_5 (
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	student_id int NOT NULL,
	datum_zaciatku_pisania date NOT NULL,
	cas_zaciatku_pisania time NOT NULL,
	zadana_odpoved text DEFAULT NULL COMMENT 'ak hrac potvrdi, ze otazka nema spravnu odpoved, sem sa ulozi null',
	vyhodnotenie tinyint(1) NOT NULL DEFAULT 2 COMMENT '0 znamena, ze odpoved bola vyhodnotena ako nespravna, 1, ze ako spravna, a 2 ze este nebola vyhodnotena',

	PRIMARY KEY (student_id, datum_zaciatku_pisania, cas_zaciatku_pisania),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) REFERENCES zoznam_pisucich_studentov(student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE odpovede_studentov_typ_2 (
	unique_id int NOT NULL AUTO_INCREMENT,
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	student_id int NOT NULL,
	datum_zaciatku_pisania date NOT NULL,
	cas_zaciatku_pisania time NOT NULL,
	zadana_odpoved text DEFAULT NULL COMMENT 'ak hrac potvrdi, ze otazka nema spravnu odpoved, sem sa ulozi null',
	vyhodnotenie tinyint(1) NOT NULL DEFAULT 2 COMMENT '0 znamena, ze odpoved bola vyhodnotena ako nespravna, 1, ze ako spravna, a 2 ze este nebola vyhodnotena',

	PRIMARY KEY (unique_id),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) REFERENCES zoznam_pisucich_studentov(student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE odpovede_studentov_typ_3 (
	unique_id int NOT NULL AUTO_INCREMENT,
	kluc_testu varchar(40) NOT NULL,
	otazka_id tinyint NOT NULL,
	student_id int NOT NULL,
	datum_zaciatku_pisania date NOT NULL,
	cas_zaciatku_pisania time NOT NULL,
	par_lava_strana tinyint DEFAULT NULL COMMENT 'ak hrac potvrdi, ze otazka nema spravnu odpoved, sem sa ulozi null',
	par_prava_strana tinyint DEFAULT NULL COMMENT 'a sem tiez',
	vyhodnotenie tinyint(1) NOT NULL DEFAULT 2 COMMENT '0 znamena, ze odpoved bola vyhodnotena ako nespravna, 1, ze ako spravna, a 2 ze este nebola vyhodnotena',

	PRIMARY KEY (unique_id),
	FOREIGN KEY (kluc_testu) REFERENCES zoznam_testov(kluc_testu) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) REFERENCES zoznam_pisucich_studentov(student_id, datum_zaciatku_pisania, cas_zaciatku_pisania) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


COMMIT;
