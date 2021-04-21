<?php

// STATICKY KONTAJNER NA METODY, KTORE URCITYM SPOSOBOM SPRACUVAJU JSON DATA A ZAPISUJU ICH DO UZ PREDPRIPRAVENYCH PREMENNYCH PRIJATYCH ODKAZOM, RESP. SPRACUVAJU PRIJATE NON-JSON DATA A VYTVARAJU Z NICH JSON DATA


class ApiTesty_jsonParser {

	// Spracuje json data na vytvorenie SQL multi-query, ktora vklada novy test do DB, pozri metodu ApiTesty_sqlContainer::get_sql_vytvor_novy_test().
	public static function spracuj_json_novy_test(&$mysqli, $prijate_data, &$bool_test_aktivny, &$insert_vsetky_otazky, &$insert_otazky_typy, &$insert_pary) {
		$bool_test_aktivny = $prijate_data["aktivny"] ? 1 : 0;
		
		foreach ($prijate_data["otazky"] as $otazka_id => $otazka) {
			$vie_pocet_spravnych = 0;
			if ($otazka["typ"] == 2 && $otazka["vie_student_pocet_spravnych"] == 1) {
				$vie_pocet_spravnych = 1;
			}
			
			$otazka_id = $mysqli->escape_string($otazka_id);


			$insert_vsetky_otazky[] = "(" .
				$otazka_id . ", \'" .
				$mysqli->escape_string($otazka["nazov"]) . "\', " .
				$mysqli->escape_string($otazka["typ"]) . ", " .
				$vie_pocet_spravnych .
			")";
			
			switch ($otazka["typ"]) {
				case 1:
					foreach ($otazka["spravne_odpovede"] as $spravna_odpoved) {
						$insert_otazky_typy[1][] = "(" .
							$otazka_id . ", \'" .
							$mysqli->escape_string($spravna_odpoved) .
						"\')";
					}
				break;

				case 2:
					foreach ($otazka["odpovede"] as $odpoved) {
						$spravna = $odpoved["je_spravna"] ? 1 : 0;
						$insert_otazky_typy[2][] = "(" .
							$otazka_id . ", \'" .
							$mysqli->escape_string($odpoved["text"]) . "\', " .
							$spravna .
						")";
					}
				break;

				case 3:
					foreach ($otazka["odpovede_lave"] as $oid => $odpoved) {
						$insert_otazky_typy[3][] = "(" .
							$otazka_id . ", " .
							$mysqli->escape_string($oid) . ", \'" .
							$mysqli->escape_string($odpoved) . "\', \'L\')";
					}

					foreach ($otazka["odpovede_prave"] as $oid => $odpoved) {
						$insert_otazky_typy[3][] = "(" .
							$otazka_id . ", " .
							$mysqli->escape_string($oid) . ", \'" .
							$mysqli->escape_string($odpoved) . "\', \'P\')";
					}

					foreach ($otazka["pary"] as $par) {
						$insert_pary[] = "(" .
							$otazka_id . ", " .
							$mysqli->escape_string($par["lava"]) . ", " .
							$mysqli->escape_string($par["prava"]) .
						")";
					}
				break;
			}
		}
	}
}
?>