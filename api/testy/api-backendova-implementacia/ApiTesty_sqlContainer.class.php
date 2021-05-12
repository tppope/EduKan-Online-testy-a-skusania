<?php

// STATICKY KONTAJNER NA TEXTOVE SQL QUERIES, RESP. MULTI-QUERIES

class ApiTesty_sqlContainer {

	public static function vytvor_novy_test(&$mysqli, $ucitel_id, $data) {
		$sql_novy_test = "INSERT INTO zoznam_testov(kluc_testu, kto_vytvoril, nazov, casovy_limit) VALUES(?, ?, ?, ?)";
		$stmt_novy_test = $mysqli->prepare($sql_novy_test);
		if (!$stmt_novy_test) return false;
		
		$sql_zoznam_otazok = "INSERT INTO zoznam_testov_otazky(kluc_testu, otazka_id, nazov, typ, znamy_pocet_spravnych) VALUES(?, ?, ?, ?, ?)";
		$stmt_zoznam_otazok = $mysqli->prepare($sql_zoznam_otazok);
		if (!$stmt_zoznam_otazok) return false;
		
		$sql_zoznam_otazok_typy_1_2 = "INSERT INTO zoznam_testov_otazky_typy_1_2(kluc_testu, otazka_id, odpoved, je_spravna) VALUES(?, ?, ?, ?)";
		$stmt_zoznam_otazok_typy_1_2 = $mysqli->prepare($sql_zoznam_otazok_typy_1_2);
		if (!$stmt_zoznam_otazok_typy_1_2) return false;

		$sql_zoznam_otazok_typ_3 =
			"INSERT INTO zoznam_testov_otazky_typ_3(kluc_testu, otazka_id, odpoved_id, odpoved, strana, sparovana_odpoved_id)
			VALUES(?, ?, ?, ?, ?, ?)";
		$stmt_zoznam_otazok_typ_3 = $mysqli->prepare($sql_zoznam_otazok_typ_3);
		if (!$stmt_zoznam_otazok_typ_3) return false;



		$mysqli->autocommit(false);
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);


		
		$stmt_novy_test->bind_param("sisi", $data["kluc"], $ucitel_id, $data["nazov"], $data["casovy_limit"]);
		$exec1 = $stmt_novy_test->execute();
		if (!$exec1) {
			$mysqli->rollback();
			return false;
		}


		foreach ($data["zoznam_otazok"] as $otazka) {
			$stmt_zoznam_otazok->bind_param("sisii", $data["kluc"], $otazka["otazka_id"], $otazka["nazov"], $otazka["typ"], $otazka["znamy_pocet_spravnych"]);
			$exec2 = $stmt_zoznam_otazok->execute();
			if (!$exec2) {
				$mysqli->rollback();
				return false;
			}
		}


		foreach ($data["zoznam_otazok_typy_1_2"] as $otazka) {
			$stmt_zoznam_otazok_typy_1_2->bind_param("sisi", $data["kluc"], $otazka["otazka_id"], $otazka["odpoved"], $otazka["je_spravna"]);
			$exec3 = $stmt_zoznam_otazok_typy_1_2->execute();
			if (!$exec3) {
				$mysqli->rollback();
				return false;
			}
		}


		foreach ($data["zoznam_otazok_typ_3"] as $otazka_id => $otazka) {
			foreach ($otazka["lave"] as $odpoved) {
				$stmt_zoznam_otazok_typ_3->bind_param(
					"siissi", $data["kluc"], $otazka_id,
					$odpoved["odpoved_id"], $odpoved["odpoved"], $odpoved["strana"], $odpoved["sparovana_odpoved_id"]
				);
				$exec4a = $stmt_zoznam_otazok_typ_3->execute();

				if (!$exec4a) {
					$mysqli->rollback();
					return false;
				}
			}

			foreach ($otazka["prave"] as $odpoved) {
				$stmt_zoznam_otazok_typ_3->bind_param(
					"siissi", $data["kluc"], $otazka_id,
					$odpoved["odpoved_id"], $odpoved["odpoved"], $odpoved["strana"], $odpoved["sparovana_odpoved_id"]
				);
				$exec4b = $stmt_zoznam_otazok_typ_3->execute();

				if (!$exec4b) {
					$mysqli->rollback();
					return false;
				}
			}
		}


		return $mysqli->commit();
	}


	public static function nastav_aktivnost_testu(&$mysqli, $kluc, $ucitel_id, $aktivny) {
		$sql = "UPDATE zoznam_testov SET aktivny = ? WHERE kluc_testu = ? AND kto_vytvoril = ?";
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("isi", $aktivny, $kluc, $ucitel_id);
		$stmt->execute();

		return $mysqli->affected_rows;
	}
	
	
	
	
	
	// Nacita test (bez otazok) pre daneho studenta.
	public static function get_result_test_pre_studenta(&$mysqli, $kluc) {
		$sql = "SELECT kluc_testu, nazov, casovy_limit FROM zoznam_testov WHERE kluc_testu = ? AND aktivny = 1";
		$stmt = $mysqli->prepare($sql);

		$stmt->bind_param("s", $kluc);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->fetch_assoc(); // vzdy max. jeden riadok
	}

	// Nacita test (bez otazok) pre daneho ucitela.
	public static function get_result_test_pre_ucitela(&$mysqli, $kluc, $ucitel_id) {
		$sql = "SELECT * FROM zoznam_testov WHERE kluc_testu = ? AND kto_vytvoril = ?";
		$stmt = $mysqli->prepare($sql);

		$stmt->bind_param("si", $kluc, $ucitel_id);
		$stmt->execute();
		$result = $stmt->get_result();

		return $result->fetch_assoc(); // vzdy max. jeden riadok
	}
	

	// Nacita testove otazky (input parameter je vzdy bezpecny, nikdy nepochadza z uzivatelskeho vstupu).
	public static function get_result_testove_otazky(&$mysqli, $kluc, $s_odpovedami = false) {
		$sql_array = array(
			"vsetky_otazky" => "SELECT otazka_id, nazov, typ, znamy_pocet_spravnych FROM zoznam_testov_otazky WHERE kluc_testu = '{$kluc}'",
			"typy_1_2" => "SELECT otazka_id, odpoved, je_spravna FROM zoznam_testov_otazky_typy_1_2 WHERE kluc_testu = '{$kluc}'",
			"typ_3_lave" => "SELECT otazka_id, odpoved_id, odpoved, sparovana_odpoved_id FROM zoznam_testov_otazky_typ_3 WHERE kluc_testu = '{$kluc}' AND strana = 'L'",
			"typ_3_prave" => "SELECT otazka_id, odpoved_id, odpoved, sparovana_odpoved_id FROM zoznam_testov_otazky_typ_3 WHERE kluc_testu = '{$kluc}' AND strana = 'P'"
		);

		$fetch_all_array = array();
		$vyskladana_odpoved = array();
		
		foreach ($sql_array as $co => $sql) {
			$stmt = $mysqli->prepare($sql);
			if (!$stmt) return $vyskladana_odpoved;

			$exec = $stmt->execute();
			if (!$exec) return $vyskladana_odpoved;

			$fetch_all_array[$co] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
		}



		foreach ($fetch_all_array["vsetky_otazky"] as $otazka) {
			$jedna_otazka = array(
				"nazov" => $otazka["nazov"],
				"typ" => $otazka["typ"]
			);
			
			
			switch ($otazka["typ"]) {
				case 2:
					$jedna_otazka["odpovede"] = array();

					if ($otazka["znamy_pocet_spravnych"] == 1) $jedna_otazka["vie_student_pocet_spravnych"] = true;
					else $jedna_otazka["vie_student_pocet_spravnych"] = false;


					if ($otazka["znamy_pocet_spravnych"] == 1 || $s_odpovedami) {
						// ak student vie pocet spravnych, resp. nacitavas test ucitelovi, zobraz mu pocet spravnych odpovedi
						$jedna_otazka["pocet_spravnych"] = 0;
					}
				break;

				case 3:
					$jedna_otazka["odpovede_lave"] = array();
					$jedna_otazka["odpovede_prave"] = array();

					$zatial_nesparovane[ $otazka["otazka_id"] ] = array( // pre ucitela vytvor parovanie (toto je pomocna array)
						"lave" => array(),
						"prave" => array()
					);
				break;
			}
			
			
			if ($s_odpovedami) { // toto dostava iba ucitel, student nie
				if ( $otazka["typ"] == 1 ) $jedna_otazka["spravne_odpovede"] = array();
				elseif ( $otazka["typ"] == 3 ) {
					$jedna_otazka["pary"] = array();
				}
			}
			
			$vyskladana_odpoved[ $otazka["otazka_id"] ] = $jedna_otazka;
		}
		



		// lave a prave odpovede na parovacie otazky idu vzdy, aj studentovi
		foreach ($fetch_all_array["typ_3_lave"] as $otazka) {
			$vyskladana_odpoved[ $otazka["otazka_id"] ]["odpovede_lave"][ $otazka["odpoved_id"] ] = $otazka["odpoved"];
		}

		foreach ($fetch_all_array["typ_3_prave"] as $otazka) {
			$vyskladana_odpoved[ $otazka["otazka_id"] ]["odpovede_prave"][ $otazka["odpoved_id"] ] = $otazka["odpoved"];
		}

		

		if ($s_odpovedami) {
			foreach ($fetch_all_array["typy_1_2"] as $otazka) {
				if ( $vyskladana_odpoved[ $otazka["otazka_id"] ]["typ"] == 1 ) {
					$vyskladana_odpoved[ $otazka["otazka_id"] ]["spravne_odpovede"][] = $otazka["odpoved"];
				}

				// zoznam moznych odpovedi s pravdivostnymi hodnotami ide iba ucitelovi
				else if ( $vyskladana_odpoved[ $otazka["otazka_id"] ]["typ"] == 2 ) {
					$array = array(
						"text" => $otazka["odpoved"]
					);
					if ($otazka["je_spravna"] == 1) {
						$array["je_spravna"] = true; // kvoli JSON

						// ucitel vzdy vie pocet spravnych odpovedi
						$vyskladana_odpoved[ $otazka["otazka_id"] ]["pocet_spravnych"]++;
					}
					else $array["je_spravna"] = false;
					
					$vyskladana_odpoved[ $otazka["otazka_id"] ]["odpovede"][] = $array;
				}
			}


			// vytvor pary pre otazky z lavej strany (vsetky otazky, ktore maju par, su sparovane z lavej strany)
			foreach ($fetch_all_array["typ_3_lave"] as $otazka) {
				if ($otazka["sparovana_odpoved_id"] != 0) { // otazka ma pravy par
					$vyskladana_odpoved[ $otazka["otazka_id"] ]["pary"][] = array(
						"lava" => $otazka["odpoved_id"],
						"prava" => $otazka["sparovana_odpoved_id"]
					);
				}
			}
		}
		else { // zoznam moznych odpovedi bez pravdivostnych hodnot ide iba studentovi
			foreach ($fetch_all_array["typy_1_2"] as $otazka) {
				if ( $vyskladana_odpoved[ $otazka["otazka_id"] ]["typ"] == 2 ) {
					$vyskladana_odpoved[ $otazka["otazka_id"] ]["odpovede"][] = $otazka["odpoved"];

					// ak student vie, kolko ma spravnych odpovedi, zrataj ich
					if (
						$vyskladana_odpoved[ $otazka["otazka_id"] ]["vie_student_pocet_spravnych"] &&
						$otazka["je_spravna"]
					) {
						$vyskladana_odpoved[ $otazka["otazka_id"] ]["pocet_spravnych"]++;
					}
				}
			}
		}


		return $vyskladana_odpoved;
	}



	// Nacita zoznam vsetkych testov (bez otazok), ktore vytvoril dany ucitel.
	public static function get_result_vsetky_testy_ucitela(&$mysqli, $ucitel_id) {
		$sql = "SELECT kluc_testu, nazov, casovy_limit, aktivny FROM zoznam_testov WHERE kto_vytvoril = ?";
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$sql2 = "SELECT COUNT(student_id) AS pocet_studentov FROM zoznam_pisucich_studentov WHERE kluc_testu = ?";
		$stmt2 = $mysqli->prepare($sql2);
		if (!$stmt2) return false;

		$sql3 = "SELECT COUNT(otazka_id) AS pocet_otazok FROM zoznam_testov_otazky WHERE kluc_testu = ?";
		$stmt3 = $mysqli->prepare($sql3);
		if (!$stmt3) return false;


		$stmt->bind_param("i", $ucitel_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$return = array();
		while ( $row = $result->fetch_assoc() ) {
			$array = array(
				"kluc" => $row["kluc_testu"],
				"nazov" => $row["nazov"],
				"casovy_limit" => $row["casovy_limit"],
				"aktivny" => $row["aktivny"]
			);


			$stmt3->bind_param("s", $row["kluc_testu"]);
			$stmt3->execute();
			$result3 = $stmt3->get_result();

			$row3 = $result3->fetch_assoc();
			$array["pocet_otazok"] = $row3["pocet_otazok"];



			// pocet pisucich studentov
            $stmt2->bind_param("s", $row["kluc_testu"]);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            $row2 = $result2->fetch_assoc();
            $array["pocet_pisucich_studentov"] = $row2["pocet_studentov"];


			$return[] = $array;
		}
		
		return $return;
	}


	// Nacita zoznam vsetkych odpovedi studenta na danom teste podla typu otazky.
	private static function get_result_zoznam_odpovedi_podla_typu(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$sql = array(
			"1_4_5" => "SELECT otazka_id, zadana_odpoved, vyhodnotenie FROM odpovede_studentov_typ_1_4_5
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?",

			"2" => "SELECT otazka_id, zadana_odpoved, vyhodnotenie FROM odpovede_studentov_typ_2
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?",

			"3" => "SELECT otazka_id, par_lava_strana, par_prava_strana, vyhodnotenie FROM odpovede_studentov_typ_3
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?"
		);


		$stmt = array();
		$result = array();

		foreach ($sql as $kod => $query) {
			$stmt[$kod] = $mysqli->prepare($query);
			if (!$stmt[$kod]) return false;
		}

		foreach ($stmt as $kod => $stmt_obj) {
			$stmt_obj->bind_param("siss", $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
			$exec = $stmt_obj->execute();
			if (!$exec) return false;
			$result[$kod] = $stmt_obj->get_result();
		}


		$odpovede = array(
			"1_4_5" => array(),
			"2" => array(),
			"3" => array()
		);
		
		while ( $row = $result["1_4_5"]->fetch_assoc() ) { // tu je iba jedna mozna odpoved ku kazdej otazke
			$odpovede["1_4_5"][ $row["otazka_id"] ] = array(
				"zadana_odpoved" => $row["zadana_odpoved"],
				"vyhodnotenie" => $row["vyhodnotenie"]
			);
		}

		while ( $row = $result["2"]->fetch_assoc() ) { // viac moznosti ku kazdej odpovedi
			if ( !isset($odpovede["2"][ $row["otazka_id"] ]) ) {
				$odpovede["2"][ $row["otazka_id"] ] = array();
			}
			
			$odpovede["2"][ $row["otazka_id"] ][] = array(
				"zadana_odpoved" => $row["zadana_odpoved"],
				"vyhodnotenie" => $row["vyhodnotenie"]
			);
		}

		while ( $row = $result["3"]->fetch_assoc() ) { // viac moznosti ku kazdej odpovedi, pary
			if ( !isset($odpovede["3"][ $row["otazka_id"] ]) ) {
				$odpovede["3"][ $row["otazka_id"] ] = array();
			}
			
			$odpovede["3"][ $row["otazka_id"] ][] = array(
				"par_lava_strana" => $row["par_lava_strana"],
				"par_prava_strana" => $row["par_prava_strana"],
				"vyhodnotenie" => $row["vyhodnotenie"]
			);
		}

		return $odpovede;
	}




	// Nacita zoznam vsetkych odpovedi studenta na danom teste
	public static function get_result_zoznam_odpovedi(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$odpovede = self::get_result_zoznam_odpovedi_podla_typu($mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
		
		$spolu = array();
		foreach ($odpovede["1_4_5"] as $otazka_id => $odpoved) {
			$spolu[$otazka_id] = $odpoved;
		}
		foreach ($odpovede["2"] as $otazka_id => $odpoved) {
			$spolu[$otazka_id] = $odpoved;
		}
		foreach ($odpovede["3"] as $otazka_id => $odpoved) {
			$spolu[$otazka_id] = $odpoved;
		}

		ksort($spolu);
		return $spolu;
	}



	public static function zacni_pisat_test(&$mysqli, $kluc, $student_id) {
		// over, ci student tento test uz nepise
		$sql = "SELECT zostavajuci_cas, datum_zaciatku_pisania, cas_zaciatku_pisania FROM zoznam_pisucich_studentov WHERE kluc_testu = ? AND student_id = ?";
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("si", $kluc, $student_id);
		$exec = $stmt->execute();
		if (!$exec) return false;

		$result = $stmt->get_result();

		$row = $result->fetch_assoc();
		if ($row != null) {
			// student tento test uz pise, vrat zostavajuci pocet minut a info o tom, ze tento test uz je rozpisany
			return array(
				"udalost" => "rozpisany-test",
				"zostavajuci_cas" => $row["zostavajuci_cas"],
				"datum_zaciatku_pisania" => $row["datum_zaciatku_pisania"],
				"cas_zaciatku_pisania" => $row["cas_zaciatku_pisania"]
			);
		}


		// ak student este tento test nema rozpisany, pokus sa ulozit mu, ze ho zacal pisat
		$sql = "SELECT casovy_limit FROM zoznam_testov WHERE aktivny = 1 AND kluc_testu = ?";
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("s", $kluc);
		$exec = $stmt->execute();
		if (!$exec) return false;

		$result = $stmt->get_result();

		$row = $result->fetch_assoc();
		if ($row == null) return false; // neexistuje takyto aktivny test


		// zapis studenta do zoznamu pisucich studentov a nastav mu cas v sekundach
		$zostavajuci_cas = $row["casovy_limit"] * 60;
		$cas_akt = time();
		$datum = date("Y-m-d", $cas_akt);
		$cas = date("H:i:s", $cas_akt);



		$sql2 = "INSERT INTO zoznam_pisucich_studentov(kluc_testu, student_id, zostavajuci_cas, datum_zaciatku_pisania, cas_zaciatku_pisania) VALUES(?, ?, ?, ?, ?)";
		$stmt2 = $mysqli->prepare($sql2);
		if (!$stmt2) return false;

		$sql3 = "INSERT INTO vyhodnotenie_testov_studentov(kluc_testu, student_id, datum_zaciatku_pisania, cas_zaciatku_pisania, otazka_id) VALUES(?, ?, ?, ?, ?)";
		$stmt3 = $mysqli->prepare($sql3);
		if (!$stmt3) return false;


		$sql4 = "SELECT MAX(otazka_id) AS pocet_otazok FROM zoznam_testov_otazky WHERE kluc_testu = ?";
		$stmt4 = $mysqli->prepare($sql4);
		if (!$stmt4) return false;

		$stmt4->bind_param("s", $kluc);
		$exec4 = $stmt4->execute();
		if (!$exec4) return false;
		$pocet_otazok_testu = $stmt4->get_result()->fetch_assoc()["pocet_otazok"];


		$mysqli->autocommit(false);
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

		$stmt2->bind_param("siiss", $kluc, $student_id, $zostavajuci_cas, $datum, $cas);
		$exec2 = $stmt2->execute();
		if (!$exec2) {
			$mysqli->rollback();
			return false;
		}

		// zaroven studenta zapis do zoznamu odpovedi testu s tym, ze jeho odpovede zatial neboli zadane
		for ($a = 1; $a <= $pocet_otazok_testu; $a++) { // otazky su cislovane od 1
			$stmt3->bind_param("sissi", $kluc, $student_id, $datum, $cas, $a);
			$exec3 = $stmt3->execute();
			if (!$exec3) {
				$mysqli->rollback();
				return false;
			}
		}

		$mysqli->commit();


		return array(
			"udalost" => "student-zacal-pisat-teraz",
			"zostavajuci_cas" => $zostavajuci_cas,
			"datum_zaciatku_pisania" => $datum,
			"cas_zaciatku_pisania" => $cas
		);
	}



	// Ulozi odpoved, ktora je na kazdu otazku unikatna (otazky typu 1, 4 a 5).
	public static function uloz_odpoved__typ_1_4_5($mysqli, $kluc, $student_id, $otazka_id, $odpoved_hodnota, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$sql = "INSERT INTO
			odpovede_studentov_typ_1_4_5(kluc_testu, student_id, otazka_id, zadana_odpoved, datum_zaciatku_pisania, cas_zaciatku_pisania)
			VALUES(?, ?, ?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE zadana_odpoved = ?";
		
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("siissss", $kluc, $student_id, $otazka_id, $odpoved_hodnota, $datum_zaciatku_pisania, $cas_zaciatku_pisania, $odpoved_hodnota);
		$exec = $stmt->execute();
		if (!$exec) return false;

		return $mysqli->affected_rows;
	}


	// Ulozi odpoved na otazku typu 2 (viac moznosti), no predtym zmaze vsetky predosle odpovede.
	public static function uloz_odpoved__typ_2($mysqli, $kluc, $student_id, $otazka_id, $odpovede_array, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		self::zmaz_odpoved("2", $mysqli, $kluc, $student_id, $otazka_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);

		$sql = "INSERT INTO odpovede_studentov_typ_2(kluc_testu, student_id, otazka_id, zadana_odpoved, datum_zaciatku_pisania, cas_zaciatku_pisania)
				VALUES(?, ?, ?, ?, ?, ?)";

		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		// v transakcii sa pokus ulozit vsetky odpovede na tuto otazku
		$mysqli->autocommit(false);
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

		foreach ($odpovede_array as $odpoved) {
			$stmt->bind_param("siisss", $kluc, $student_id, $otazka_id, $odpoved, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
			$exec = $stmt->execute();
			if (!$exec) {
				$mysqli->rollback();
				return false;
			}
		}

		return $mysqli->commit();
	}

	public static function uloz_odpoved__typ_3($mysqli, $kluc, $student_id, $otazka_id, $odpovede_array, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		self::zmaz_odpoved("3", $mysqli, $kluc, $student_id, $otazka_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);

		$sql = "INSERT INTO odpovede_studentov_typ_3(kluc_testu, student_id, otazka_id, par_lava_strana, par_prava_strana, datum_zaciatku_pisania, cas_zaciatku_pisania)
		VALUES(?, ?, ?, ?, ?, ?, ?)";

		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		// v transakcii sa pokus ulozit vsetky odpovede na tuto otazku
		$mysqli->autocommit(false);
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

		foreach ($odpovede_array as $odpoved) {
			$stmt->bind_param("siiiiss", $kluc, $student_id, $otazka_id, $odpoved["lava"], $odpoved["prava"], $datum_zaciatku_pisania, $cas_zaciatku_pisania);
			$exec = $stmt->execute();
			if (!$exec) {
				$mysqli->rollback();
				return false;
			}
		}

		return $mysqli->commit();
	}


	// Zmaze vsetky odpovede studenta na tuto otazku.
	public static function zmaz_odpoved($typ, $mysqli, $kluc, $student_id, $otazka_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$tabulka = "odpovede_studentov_typ_" . $typ; // $typ je stale bezpecny, pochadza z kodu a je pevne zadany
		
		$sql = "DELETE FROM {$tabulka} WHERE
			kluc_testu = ? AND student_id = ? AND otazka_id = ? AND
			datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?";
		
		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("siiss", $kluc, $student_id, $otazka_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
		$exec = $stmt->execute();
		if (!$exec) return false;

		return $mysqli->affected_rows;
	}




	public static function get_zoznam_studentov_na_teste(&$mysqli, $kluc) {
		$sql = "SELECT student_id, zostavajuci_cas, datum_zaciatku_pisania, cas_zaciatku_pisania, datum_konca_pisania, cas_konca_pisania FROM zoznam_pisucich_studentov WHERE kluc_testu = ?";

		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return array();

		$stmt->bind_param("s", $kluc);
		$exec = $stmt->execute();
		if (!$exec) return array();

		$result = $stmt->get_result();
		if ($result == null) return array();
		
		return $result->fetch_all(MYSQLI_ASSOC);
	}



	public static function ukonci_pisanie_testu(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$cas_akt = time();
		$datum = date("Y-m-d", $cas_akt);
		$cas = date("H:i:s", $cas_akt);

		$sql = "UPDATE zoznam_pisucich_studentov
			SET datum_konca_pisania = ?, cas_konca_pisania = ?
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?";

		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return false;

		$stmt->bind_param("sssiss", $datum, $cas, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
		$exec = $stmt->execute();
		if (!$exec) return false;

		return self::automaticky_vyhodnot_odpovede_typy_1_2_3($mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
	}



	private static function automaticky_vyhodnot_odpovede_typy_1_2_3(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$odpovede_studenta = self::get_result_zoznam_odpovedi($mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
		$spravne_odpovede = self::get_result_testove_otazky($mysqli, $kluc, true);

		$sql = array(
			"1_4_5" => "UPDATE odpovede_studentov_typ_1_4_5 SET vyhodnotenie = ?
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ? AND otazka_id = ?",

			"2" => "UPDATE odpovede_studentov_typ_2 SET vyhodnotenie = ?
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ? AND zadana_odpoved = ?",

			"3" => "UPDATE odpovede_studentov_typ_3 SET vyhodnotenie = ?
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ? AND par_lava_strana = ? AND par_prava_strana = ?",

			"zoznam_otazok" => "SELECT otazka_id FROM zoznam_testov_otazky WHERE kluc_testu = ? AND typ < 4",

			"pocet_spravnych_typ_2" => "SELECT otazka_id, COUNT(je_spravna) as pocet_spravnych_2 FROM zoznam_testov_otazky_typy_1_2
			WHERE kluc_testu = ? AND je_spravna = 1
			GROUP BY otazka_id",

			"pocet_spravnych_typ_3" => "SELECT otazka_id, COUNT(sparovana_odpoved_id) as pocet_spravnych_3 FROM zoznam_testov_otazky_typ_3
			WHERE kluc_testu = ? AND strana = 'L' AND sparovana_odpoved_id > 0
			GROUP BY otazka_id",

			"vyhodnotenie" => "UPDATE vyhodnotenie_testov_studentov SET vyhodnotenie = ?
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ? AND otazka_id = ?"
		);


		$stmt = array();

		foreach ($sql as $kod => $query) {
			$stmt[$kod] = $mysqli->prepare($query);
			if (!$stmt[$kod]) return false;
		}

		$stmt["zoznam_otazok"]->bind_param("s", $kluc);
		if ( !$stmt["zoznam_otazok"]->execute() ) return false;

		$vysledky_obodovane = array();
		
		//$zoznam_automaticky_vyhodnotenych_otazok_testu = $stmt["zoznam_otazok"]->get_result()->fetch_assoc()["pocet_otazok"];
		$result_zoznam_otazok = $stmt["zoznam_otazok"]->get_result();
		while ( $row = $result_zoznam_otazok->fetch_assoc() ) {
			$vysledky_obodovane[ $row["otazka_id"] ] = -1; // default nevyhodnotene
		}

		// array poctu spravnych odpovedi studenta
		$pocet_spravnych_studenta = array();
		
		
		$mysqli->autocommit(false);
		$mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

		foreach ($spravne_odpovede as $otazka_id=>$otazka) {
			if ( isset($odpovede_studenta[$otazka_id]) ) {
				switch ($otazka["typ"]) {
					case 1:
						$zadana = $odpovede_studenta[$otazka_id]["zadana_odpoved"];
						$vyhodnotenie = in_array($zadana, $otazka["spravne_odpovede"]) ? 1 : 0;

						$vysledky_obodovane[$otazka_id] = $vyhodnotenie;

						$stmt["1_4_5"]->bind_param("isissi", $vyhodnotenie, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania, $otazka_id);
						$exec = $stmt["1_4_5"]->execute();
						if (!$exec) {
							$mysqli->rollback();
							return false;
						}
					break;

					case 2:
						foreach ( $odpovede_studenta[$otazka_id] as $id=>$odpoved_studenta ) {
							$vyhodnotenie = 0;

							if ( !isset($pocet_spravnych_studenta[$otazka_id]) ) {
								$pocet_spravnych_studenta[$otazka_id] = 0;
							}

							foreach ($otazka["odpovede"] as $odpoved) {
								if ($odpoved["text"] == $odpoved_studenta["zadana_odpoved"]) {
									if ( $odpoved["je_spravna"] ) {
										$vyhodnotenie = 1;
	
										if ($pocet_spravnych_studenta[$otazka_id] != -1) { // -1 znamena, ze za tuto otazku ma student nejake nespravne odpovede
											$pocet_spravnych_studenta[$otazka_id]++;
										}
									}
									else $pocet_spravnych_studenta[$otazka_id] = -1; // nespravna odpoved
								}
							}

							$stmt["2"]->bind_param(
								"isisss", $vyhodnotenie, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania, $odpoved_studenta["zadana_odpoved"]
							);
							$exec = $stmt["2"]->execute();
							if (!$exec) {
								$mysqli->rollback();
								return false;
							}
						}
					break;

					case 3:
						foreach ( $odpovede_studenta[$otazka_id] as $id=>$odpoved_studenta ) {
							$vyhodnotenie = 0;

							if ( !isset($pocet_spravnych_studenta[$otazka_id]) ) {
								$pocet_spravnych_studenta[$otazka_id] = 0;
							}

							foreach ($otazka["pary"] as $par) {
								if ($par["lava"] == $odpoved_studenta["par_lava_strana"] && $par["prava"] == $odpoved_studenta["par_prava_strana"]) {
									$vyhodnotenie = 1;

									if ($pocet_spravnych_studenta[$otazka_id] != -1) { // -1 znamena, ze za tuto otazku ma student nejake nespravne odpovede
										$pocet_spravnych_studenta[$otazka_id]++;
									}
									else $pocet_spravnych_studenta[$otazka_id] = -1; // nespravna odpoved
								}
							}

							$stmt["3"]->bind_param(
								"isissii", $vyhodnotenie, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania, $odpoved_studenta["par_lava_strana"], $odpoved_studenta["par_prava_strana"]
							);
							$exec = $stmt["3"]->execute();
							if (!$exec) {
								$mysqli->rollback();
								return false;
							}
						}
					break;
				}
			}
		}


		$stmt["pocet_spravnych_typ_2"]->bind_param("s", $kluc);
		$stmt["pocet_spravnych_typ_3"]->bind_param("s", $kluc);
		$exec_T2 = $stmt["pocet_spravnych_typ_2"]->execute();
		if (!$exec_T2) return false;

		$spravne_T2_r = $stmt["pocet_spravnych_typ_2"]->get_result();
		$spravne = array();
		while ($row = $spravne_T2_r->fetch_assoc()) {
			$spravne[ $row["otazka_id"] ] = $row["pocet_spravnych_2"];
		}


		$exec_T3 = $stmt["pocet_spravnych_typ_3"]->execute();
		if (!$exec_T3) return false;

		$spravne_T3_r = $stmt["pocet_spravnych_typ_3"]->get_result();
		while ($row = $spravne_T3_r->fetch_assoc()) {
			$spravne[ $row["otazka_id"] ] = $row["pocet_spravnych_3"];
		}

		// prejdi otazky typov 2 a 3 a spocitaj uplne spravne odpovede
		foreach ($pocet_spravnych_studenta as $otazka_id => $pocet) {
			if ($spravne[$otazka_id] == $pocet) {
				$vysledky_obodovane[$otazka_id] = 1;
			}
			else $vysledky_obodovane[$otazka_id] = 0;
		}


		// prejdi obodovane vysledky a tam, kde nebola odpoved, daj 0 a zapis vsetko do DB
		foreach ($vysledky_obodovane as $otazka_id => $body) {
			if ($body == -1) $body = 0;

			$stmt["vyhodnotenie"]->bind_param("isissi", $body, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania, $otazka_id);
			$exec = $stmt["vyhodnotenie"]->execute();
			if (!$exec) {
				$mysqli->rollback();
				return false;
			}
		}

		$mysqli->commit();


		return true;
	}




	public static function nacitaj_vyhodnotene_odpovede(&$mysqli, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania) {
		$sql = "SELECT otazka_id, vyhodnotenie FROM vyhodnotenie_testov_studentov
			WHERE kluc_testu = ? AND student_id = ? AND datum_zaciatku_pisania = ? AND cas_zaciatku_pisania = ?";

		$stmt = $mysqli->prepare($sql);
		if (!$stmt) return array();

		$stmt->bind_param("siss", $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);
		$exec = $stmt->execute();
		if (!$exec) return array();

		$result = $stmt->get_result();
		if ($result == null) return array();
		
		$body = array();

		foreach ( $result->fetch_all(MYSQLI_ASSOC) as $data ) {
			$body[ $data["otazka_id"] ] = $data["vyhodnotenie"];
		}

		return $body;
	}
}
?>
