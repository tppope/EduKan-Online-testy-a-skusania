<?php

// STATICKY KONTAJNER NA METODY, KTORE URCITYM SPOSOBOM SPRACUVAJU JSON DATA A ZAPISUJU ICH DO UZ PREDPRIPRAVENYCH PREMENNYCH PRIJATYCH ODKAZOM, RESP. SPRACUVAJU PRIJATE NON-JSON DATA A VYTVARAJU Z NICH JSON DATA


class ApiTesty_jsonParser {

	private static function vypocitaj_kluc_testu($ucitel_id, $nazov_testu) {
		return "U" . $ucitel_id . "T" . time();
	}


	// Spracuje json data na vytvorenie noveho testu v DB.
	public static function spracuj_json_novy_test($ucitel_id, $json_data) {
		$kluc = self::vypocitaj_kluc_testu($ucitel_id, $json_data["nazov"]);

		$spracovane_data = array(
			"kluc" => $kluc,
			"nazov" => $json_data["nazov"],
			"casovy_limit" => $json_data["casovy_limit"],
			"zoznam_otazok" => array(),
			"zoznam_otazok_typy_1_2" => array(),
			"zoznam_otazok_typ_3" => array()
		);


		foreach ($json_data["otazky"] as $otazka_id => $otazka) {
			$vie_pocet_spravnych = 0;
			if ($otazka["typ"] == 2 && $otazka["vie_student_pocet_spravnych"] == 1) {
				$vie_pocet_spravnych = 1;
			}
			

			$spracovane_data["zoznam_otazok"][] = array(
				"otazka_id" => $otazka_id,
				"nazov" => $otazka["nazov"],
				"typ" => $otazka["typ"],
				"znamy_pocet_spravnych" => $vie_pocet_spravnych
			);


			switch ($otazka["typ"]) {
				case 1:
					foreach ($otazka["spravne_odpovede"] as $odpoved) {
						$spracovane_data["zoznam_otazok_typy_1_2"][] = array(
							"otazka_id" => $otazka_id,
							"odpoved" => $odpoved,
							"je_spravna" => 1 // vsetky ulozene odpovede otazok typu 1 su spravne
						);
					}
				break;

				case 2:
					foreach ($otazka["odpovede"] as $odpoved) {
						$je_spravna = $odpoved["je_spravna"] ? 1 : 0;
						$spracovane_data["zoznam_otazok_typy_1_2"][] = array(
							"otazka_id" => $otazka_id,
							"odpoved" => $odpoved["text"],
							"je_spravna" => $je_spravna
						);
					}
				break;

				case 3:
					$lave = array();
					$prave = array();

					foreach ($otazka["odpovede_lave"] as $oid => $odpoved) {
						$lave[$oid] = array(
							"odpoved_id" => $oid,
							"odpoved" => $odpoved,
							"strana" => "L",
							"sparovana_odpoved_id" => 0 // default tato odpoved nema par (nemusi mat par)
						);
					}

					foreach ($otazka["odpovede_prave"] as $oid => $odpoved) {
						$prave[$oid] = array(
							"odpoved_id" => $oid,
							"odpoved" => $odpoved,
							"strana" => "P",
							"sparovana_odpoved_id" => 0 // default tato odpoved nema par (nemusi mat par)
						);
					}


					foreach ($otazka["pary"] as $par) {
						$lave[ $par["lava"] ]["sparovana_odpoved_id"] = $par["prava"];
						$prave[ $par["prava"] ]["sparovana_odpoved_id"] = $par["lava"];
					}

					$spracovane_data["zoznam_otazok_typ_3"][$otazka_id] = array(
						"lave" => $lave,
						"prave" => $prave
					);
				break;
			}
		}
		
		
		return $spracovane_data;
	}
}
?>