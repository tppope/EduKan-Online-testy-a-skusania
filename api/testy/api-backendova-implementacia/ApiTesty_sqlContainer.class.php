<?php

// STATICKY KONTAJNER NA TEXTOVE SQL QUERIES, RESP. MULTI-QUERIES

class ApiTesty_sqlContainer {

	// Vrati multiquery, ktora vytvori novy test a zapise vsetky jeho otazky do databazy podla prijatych dat.
	public static function get_sql_vytvor_novy_test($ucitel_id, $nazov_testu, $casovy_limit, $bool_test_aktivny, $insert_vsetky_otazky, $insert_otazky_typy, $insert_pary) {
		$sql = "
		SET autocommit = 0;
		
		
		LOCK TABLES zoznam_testov WRITE;
		INSERT INTO zoznam_testov (kto_vytvoril, nazov, casovy_limit, aktivny) VALUES($ucitel_id, '$nazov_testu', $casovy_limit, $bool_test_aktivny);

		SET @novy_test_id = 1;
		SELECT MAX(id) INTO @novy_test_id FROM zoznam_testov;
		
		SELECT @novy_test_id AS novy_test_id;

		COMMIT;
		UNLOCK TABLES;		



		START TRANSACTION;


		SET @tabulka_celkova = CONCAT('test_', @novy_test_id, '_otazky');
		SET @create_celkova = CONCAT(
			'CREATE TABLE ', @tabulka_celkova, '(
				id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				nazov text NOT NULL,
				typ tinyint(1) NOT NULL DEFAULT 1,
				znamy_pocet_spravnych tinyint(1) NOT NULL DEFAULT 0 COMMENT \'ci student vie pocet spravnych odpovedi, relevantne iba pre otazky s viacerymi odpovedami\',
				FOREIGN KEY (typ) REFERENCES typy_otazok(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
		);



		SET @tabulka_otazok_typ_1 = CONCAT('test_', @novy_test_id, '_otazky_typ_1');
		SET @tabulka_otazok_typ_2 = CONCAT('test_', @novy_test_id, '_otazky_typ_2');
		SET @tabulka_otazok_typ_3 = CONCAT('test_', @novy_test_id, '_otazky_typ_3');
		SET @tabulka_otazok_typ_3_pary = CONCAT('test_', @novy_test_id, '_otazky_typ_3_pary');

		SET @create_typ_1 = CONCAT(
			'CREATE TABLE ', @tabulka_otazok_typ_1, '(
				otazka_id int(11) NOT NULL,
				spravna_odpoved text NOT NULL,

				INDEX (otazka_id),
				FOREIGN KEY (otazka_id) REFERENCES ', @tabulka_celkova, ' (id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
		);

		SET @create_typ_2 = CONCAT(
			'CREATE TABLE ', @tabulka_otazok_typ_2, '(
				otazka_id int(11) NOT NULL,
				odpoved text NOT NULL,
				je_spravna tinyint(1) NOT NULL,

				INDEX (otazka_id),
				FOREIGN KEY (otazka_id) REFERENCES ', @tabulka_celkova, ' (id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
		);

		SET @create_typ_3 = CONCAT(
			'CREATE TABLE ', @tabulka_otazok_typ_3, '(
				otazka_id int(11) NOT NULL,
				odpoved_id int(11) NOT NULL,
				odpoved text NOT NULL,
				strana varchar(1) NOT NULL COMMENT \'\"L\" alebo \"P\"\',

				INDEX (otazka_id),
				FOREIGN KEY (otazka_id) REFERENCES ', @tabulka_celkova, ' (id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
		);

		SET @create_typ_3_pary = CONCAT(
			'CREATE TABLE ', @tabulka_otazok_typ_3_pary, '(
				otazka_id int(11) NOT NULL,
				odpoved_lava int(11) NOT NULL,
				odpoved_prava int(11) NOT NULL,
				par_id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				
				INDEX (otazka_id),
				FOREIGN KEY (otazka_id) REFERENCES ', @tabulka_celkova, ' (id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;'
		);



		PREPARE t_celkova FROM @create_celkova;
		PREPARE t_otazok_typ_1 FROM @create_typ_1;
		PREPARE t_otazok_typ_2 FROM @create_typ_2;
		PREPARE t_otazok_typ_3 FROM @create_typ_3;
		PREPARE t_otazok_typ_3_pary FROM @create_typ_3_pary;


		EXECUTE t_celkova;
		EXECUTE t_otazok_typ_1;
		EXECUTE t_otazok_typ_2;
		EXECUTE t_otazok_typ_3;
		EXECUTE t_otazok_typ_3_pary;



		COMMIT;



		START TRANSACTION;

		SET @insert_celkova = CONCAT(
			'INSERT INTO ', @tabulka_celkova, ' (id, nazov, typ, znamy_pocet_spravnych) VALUES " . implode(", ", $insert_vsetky_otazky) . ";'
		);

		PREPARE i_celkova FROM @insert_celkova;
		EXECUTE i_celkova;

		";


		if ( !empty($insert_otazky_typy[1]) ) {
			$sql .= "
			SET @insert_typ_1 = CONCAT(
				'INSERT INTO ', @tabulka_otazok_typ_1, ' (otazka_id, spravna_odpoved) VALUES " . implode(", ", $insert_otazky_typy[1]) . ";'
			);

			PREPARE i_typ_1 FROM @insert_typ_1;
			EXECUTE i_typ_1;
			";
		}


		if ( !empty($insert_otazky_typy[2]) ) {
			$sql .= "
			SET @insert_typ_2 = CONCAT(
				'INSERT INTO ', @tabulka_otazok_typ_2, ' (otazka_id, odpoved, je_spravna) VALUES " . implode(", ", $insert_otazky_typy[2]) . ";'
			);

			PREPARE i_typ_2 FROM @insert_typ_2;
			EXECUTE i_typ_2;
			";
		}


		if ( !empty($insert_otazky_typy[3]) ) {
			$sql .= "
			SET @insert_typ_3 = CONCAT(
				'INSERT INTO ', @tabulka_otazok_typ_3, ' (otazka_id, odpoved_id, odpoved, strana) VALUES " . implode(", ", $insert_otazky_typy[3]) . ";'
			);
			
			SET @insert_typ_3_pary = CONCAT(
				'INSERT INTO ', @tabulka_otazok_typ_3_pary, ' (otazka_id, odpoved_lava, odpoved_prava) VALUES " . implode(", ", $insert_pary) . ";'
			);

			PREPARE i_typ_3 FROM @insert_typ_3;
			PREPARE i_typ_3_pary FROM @insert_typ_3_pary;
			
			EXECUTE i_typ_3;
			EXECUTE i_typ_3_pary;
			";
		}





		$sql .= " COMMIT;

		START TRANSACTION;


		SET @c1 = 0;
		SET @c2 = 0;
		SET @c3 = 0;
		SET @pocet_skutocny = 0;


		SET @select_1 = CONCAT('SELECT COUNT(DISTINCT otazka_id) INTO @c1 FROM test_', @novy_test_id, '_otazky_typ_1;');
		SET @select_2 = CONCAT('SELECT COUNT(DISTINCT otazka_id) INTO @c2 FROM test_', @novy_test_id, '_otazky_typ_2;');
		SET @select_3 = CONCAT('SELECT COUNT(DISTINCT otazka_id) INTO @c3 FROM test_', @novy_test_id, '_otazky_typ_3;');
		SET @select_pocet_skutocny = CONCAT('SELECT COUNT(id) INTO @pocet_skutocny FROM test_', @novy_test_id, '_otazky;');

		PREPARE s1 FROM @select_1;
		PREPARE s2 FROM @select_2;
		PREPARE s3 FROM @select_3;
		PREPARE s_pocet_skutocny FROM @select_pocet_skutocny;

		EXECUTE s1;
		EXECUTE s2;
		EXECUTE s3;
		EXECUTE s_pocet_skutocny;


		SET @pocet_zisteny = @c1 + @c2 + @c3;


		SELECT ( @pocet_zisteny = @pocet_skutocny AND @pocet_skutocny != 0 ) AS check_result;

		COMMIT;
		";
		
		return $sql;
	}
	
	
	// Vrati multiquery, ktora zmaze test s tymto id.
	public static function get_sql_zmaz_test($id) {
		$sql = "
		SET autocommit = 0;

		DROP TABLE IF EXISTS
			test_" . $id . "_otazky_typ_1,
			test_" . $id . "_otazky_typ_2,
			test_" . $id . "_otazky_typ_3,
			test_" . $id . "_otazky_typ_3_pary,
			test_" . $id . "_otazky
		CASCADE;

		DELETE FROM zoznam_testov WHERE id = " . $id . ";
		COMMIT;
		";
		
		return $sql;
	}
}
?>