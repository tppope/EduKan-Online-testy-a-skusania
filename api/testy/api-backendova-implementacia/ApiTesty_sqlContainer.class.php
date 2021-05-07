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
		return $stmt->execute();
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

					if ($otazka["znamy_pocet_spravnych"] == 1) {
						$jedna_otazka["vie_student_pocet_spravnych"] = true; // kvoli JSON, aby bolo true a nie 1
						$jedna_otazka["pocet_spravnych"] = 0;
					}
					else $jedna_otazka["vie_student_pocet_spravnych"] = false;
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
					if ($otazka["je_spravna"] == 1) $array["je_spravna"] = true; // kvoli JSON
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



			if ( $row["aktivny"] ) { // na aktivnom teste nacitaj aj pocet pisucich studentov
				$stmt2->bind_param("s", $row["kluc_testu"]);
				$stmt2->execute();
				$result2 = $stmt2->get_result();

				$row2 = $result2->fetch_assoc();
				$array["pocet_pisucich_studentov"] = $row2["pocet_studentov"];
			}

			$return[] = $array;
		}
		
		return $return;
	}
}
?>