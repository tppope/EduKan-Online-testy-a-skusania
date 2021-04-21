<?php
// API ENDPOINT NA VYTVORENIE NOVEHO TESTU

include "../../db-login.php";


$prijate_data = json_decode("{\"nazov\":\"Ukážkový test č. 1\",\"casovy_limit\":60,\"aktivny\":true,\"otazky\":{\"1\":{\"nazov\":\"Ako sa volá fakulta, na ktorej študujú členovia realizačného tímu tohto zadania? Uveďťe skratku alebo plný názov.\",\"typ\":1,\"spravne_odpovede\":[\"FEI\",\"Fakulta elektrotechniky a informatiky\"]},\"2\":{\"nazov\":\"Ktoré štáty susedia so Slovenskou republikou?\",\"typ\":2,\"odpovede\":[{\"text\":\"Litva\",\"je_spravna\":false},{\"text\":\"Maďarsko\",\"je_spravna\":true},{\"text\":\"Ukrajina\",\"je_spravna\":true},{\"text\":\"Nový Zéland\",\"je_spravna\":false}],\"vie_student_pocet_spravnych\":false},\"3\":{\"nazov\":\"Vyberte všetky slovesá\",\"typ\":2,\"odpovede\":[{\"text\":\"spať\",\"je_spravna\":true},{\"text\":\"jesť\",\"je_spravna\":true},{\"text\":\"vňať\",\"je_spravna\":false},{\"text\":\"inovať\",\"je_spravna\":false}],\"vie_student_pocet_spravnych\":true},\"4\":{\"nazov\":\"Vytvorte správne páry\",\"typ\":3,\"odpovede_lave\":{\"1\":\"červený\",\"2\":\"ostrý\",\"3\":\"zelená\",\"4\":\"šľachetné\"},\"odpovede_prave\":{\"1\":\"tráva\",\"2\":\"srdce\",\"3\":\"mak\",\"4\":\"nôž\"},\"pary\":[{\"lava\":1,\"prava\":3},{\"lava\":2,\"prava\":4},{\"lava\":3,\"prava\":1},{\"lava\":4,\"prava\":2}]},\"5\":{\"nazov\":\"Vytvorte správne páry\",\"typ\":3,\"odpovede_lave\":{\"1\":\"Slovensko\",\"2\":\"Grécko\",\"3\":\"USA\",\"4\":\"Čína\",\"5\":\"Brazília\",\"6\":\"Sudán\"},\"odpovede_prave\":{\"1\":\"Severná Amerika\",\"2\":\"Ázia\",\"3\":\"Európa\",\"4\":\"Antarktída\"},\"pary\":[{\"lava\":1,\"prava\":3},{\"lava\":2,\"prava\":3},{\"lava\":3,\"prava\":1},{\"lava\":4,\"prava\":2}]}}}", true);


//print_r($prijate_data);



// id ucitela, ktory vytvoril test, je docasne 0 (neskor nahradit skutocnym id)
$ucitel_id = 0;


$mysqli_api_testy = new mysqli($db_host, $db_user, $db_password, "wt_skuskove_zadanie_databaza_testov");
$mysqli_api_testy->set_charset("UTF-8");


// transakcia so zamknutymi tabulkami
//$mysqli_api_testy->autocommit(false);

$sql_multi = "
	LOCK TABLES zoznam_testov READ;
	
	
";

/*$mysqli_api_testy->multi_query($sql_multi);
$mysqli_api_testy->commit();
$mysqli_api_testy->multi_query("UNLOCK TABLES;");
// koniec transakcie


/*$mysqli_api_testy->multi_query(
	"INSERT INTO test_idtestu_otazky_kratka_odpoved VALUES (15, 'test');"
);*/



/*
SET autocommit=0;
LOCK TABLES t1 WRITE, t2 READ, ...;
... do something with tables t1 and t2 here ...
COMMIT;
UNLOCK TABLES;

*/

$a = "
SET autocommit=0;


LOCK TABLES zoznam_testov WRITE;
INSERT INTO zoznam_testov (kto_vytvoril, nazov, casovy_limit, aktivny) VALUES
	(" . $ucitel_id . ", '" . $prijate_data["nazov"] . "', " . $prijate_data["casovy_limit"] . ", " . $prijate_data["aktivny"] . ");

SET @novy_test_id = 1;
SELECT MAX(id) INTO @novy_test_id FROM zoznam_testov;

COMMIT;
UNLOCK TABLES;


START TRANSACTION;	


SET @tabulka_celkova = CONCAT('test_', @novy_test_id, '_otazky');
SET @create_celkova = CONCAT(
	'CREATE TABLE ', @tabulka_celkova, '(
		id int(11) NOT NULL PRIMARY KEY,
		nazov text NOT NULL,
		typ tinyint(1) NOT NULL DEFAULT 1,
		znamy_pocet_spravnych tinyint(1) NOT NULL DEFAULT 0 COMMENT \'ci student vie pocet spravnych odpovedi, relevantne iba pre otazky s viacerymi odpovedami\',
		FOREIGN KEY (typ) REFERENCES typy_otazok(id) ON DELETE CASCADE ON UPDATE CASCADE
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
);



SET @tabulka_otazok_typ_1 = CONCAT('test_', @novy_test_id, '_otazky_kratka_odpoved');
SET @tabulka_otazok_typ_2 = CONCAT('test_', @novy_test_id, '_otazky_viacere_odpovede');
SET @tabulka_otazok_typ_3 = CONCAT('test_', @novy_test_id, '_otazky_parovanie');

SET @create_typ_1 = CONCAT(
	'CREATE TABLE ', @tabulka_otazok_typ_1, '(
		otazka_id int(11) NOT NULL PRIMARY KEY,
		spravna_odpoved text NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
);

SET @create_typ_2 = CONCAT(
	'CREATE TABLE ', @tabulka_otazok_typ_2, '(
		otazka_id int(11) NOT NULL PRIMARY KEY,
		odpoved text NOT NULL,
		je_spravna tinyint(1) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
);

SET @create_typ_3 = CONCAT(
	'CREATE TABLE ', @tabulka_otazok_typ_3, '(
		otazka_id int(11) NOT NULL PRIMARY KEY,
		odpoved text NOT NULL,
		par tinyint(4) NOT NULL,
		strana varchar(1) NOT NULL COMMENT \'\"L\" alebo \"P\"\',
		pocet_vyskytov tinyint(4) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
);



PREPARE t_celkova FROM @create_celkova;
PREPARE t_otazok_typ_1 FROM @create_typ_1;
PREPARE t_otazok_typ_2 FROM @create_typ_2;
PREPARE t_otazok_typ_3 FROM @create_typ_3;


EXECUTE t_celkova;
EXECUTE t_otazok_typ_1;
EXECUTE t_otazok_typ_2;
EXECUTE t_otazok_typ_3;


COMMIT;
";

echo $a;
?>