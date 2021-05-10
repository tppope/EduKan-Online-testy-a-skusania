<?php


// STATICKY KONTAJNER NA METODY, KTORE TESTUJU KONZISTENCIU PRIJATYCH DAT


class ApiTesty_sanityChecker {
	
	// Over, ci vobec boli prijate nejake data.
	public static function generic_check__prijate_data($data) {
		if ( $data == "" || is_array($data) && empty($data) ) {
			echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_1") );
			return false;
		}
		return true;
	}

	// Over, ci je prihlaseny ucitel (pouzivane tam, kde ma byt prihlaseny vylucne ucitel).
	public static function generic_check__prihlaseny_ucitel() {
		if ( isset($_SESSION["userId"]) ) {
			return true;
		}

		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_2") );
		return false;
	}

	// Over, ci je prihlaseny student (pouzivane tam, kde ma byt prihlaseny vylucne student).
	public static function generic_check__prihlaseny_student() {
		if ( isset($_SESSION["studentId"]) ) {
			return true;
		}

		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_2") );
		return false;
	}

	// Over, ci je niekto prihlaseny (pouzivane tam, kde ma byt prihlaseny bud ucitel alebo student).
	public static function generic_check__prihlaseny_ucitel_alebo_student() {
		if ( isset($_SESSION["userId"]) || isset($_SESSION["studentId"]) ) {
			return true;
		}

		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_2") );
		return false;
	}




	public static function nacitaj_test_ucitel() {
		return isset($_SESSION["userId"]) && isset( $_SESSION["pisanyTestKluc"] );
	}
	
	public static function nacitaj_test_student() {
		return isset($_SESSION["studentId"]) && isset( $_SESSION["pisanyTestKluc"] );
	}




	public static function praca_s_testami_ucitel__nacitaj_vsetky_testy($data) {
		return isset( $data["akcia"] ) && $data["akcia"] == "zoznam-testov";
	}

	public static function praca_s_testami_ucitel__aktivuj_test($data) {
		return
			isset( $data["akcia"] ) && $data["akcia"] == "aktivuj-test" &&
			isset( $data["kluc"] );
	}

	public static function praca_s_testami_ucitel__deaktivuj_test($data) {
		return
			isset( $data["akcia"] ) && $data["akcia"] == "deaktivuj-test" &&
			isset( $data["kluc"] );
	}
	
	public static function praca_s_testami_ucitel__nacitaj_vysledky_testu($data) {
		return
			isset( $data["akcia"] ) && $data["akcia"] == "nacitaj-vysledky" &&
			isset( $_SESSION["pisanyTestKluc"] ) && isset( $_SESSION["studentId"] ) &&
			isset( $_SESSION["datumZaciatkuPisania"] ) && isset( $_SESSION["casZaciatkuPisania"] );
	}



	// Skontroluje, ci data novovytvaraneho testu su v poriadku a spravnom formate.
	public static function novy_test($data) {
		$spravny_format_dat =
			isset( $data["nazov"] ) &&
			isset( $data["casovy_limit"] ) &&
			isset( $data["otazky"] );

		if (!$spravny_format_dat) return false;


		// over jednotlive otazky, ci su v spravnom formate
		foreach ($data["otazky"] as $otazka) {
			$check =
				isset( $otazka["nazov"] ) &&
				isset( $otazka["typ"] );
			
			if (!$check) return false;

			switch ( $otazka["typ"] ) {
				case 1:
					$check_otazky = isset( $otazka["spravne_odpovede"] );
					
					if (!$check_otazky) return false;
				break;

				case 2:
					$check_otazky =
						isset( $otazka["odpovede"] ) &&
						isset( $otazka["vie_student_pocet_spravnych"] );

					if (!$check_otazky) return false;
					
					foreach ($otazka["odpovede"] as $odpoved) {
						$check_odpovede =
						isset( $odpoved["text"] ) &&
						isset( $odpoved["je_spravna"] );

						if (!$check_odpovede) return false;
					}
				break;

				case 3:
					$check_otazky =
						isset( $otazka["odpovede_lave"] ) &&
						isset( $otazka["odpovede_prave"] ) &&
						isset( $otazka["pary"] ) &&
						count( $otazka["odpovede_lave"] ) > 0 &&
						count( $otazka["odpovede_prave"] ) > 0 &&
						count( $otazka["pary"] ) > 0;
					
					if (!$check_otazky) return false;
					
					foreach ($otazka["pary"] as $par) {
						$check_paru =
							isset( $par["lava"] ) &&
							isset( $par["prava"] );

						if (!$check_paru) return false;
					}
				break;

				case 4:
				case 5:
					return true; // otazky typu 4 a 5 maju iba nazov a typ otazky, co uz bolo overene pred tymto switchom
				break;

				default: return false; // iny typ nie je pripustny
			}
		}

		// test je v poriadku
		return true;
	}



	public static function vypracovanie_testu__zacni_pisat($data) {
		return
			isset( $data["akcia"] ) && $data["akcia"] == "zacat-pisat" &&
			isset( $data["kluc"] );
	}


	public static function vypracovanie_testu__ukladanie_odpovede($data) {
		return
			isset( $data["akcia"] ) && $data["akcia"] == "odoslat-odpoved" &&
			isset( $data["otazka_id"] ) &&
			isset( $data["typ_odpovede"] ) &&
			(
				isset( $data["odpoved"] ) ||
				isset( $data["volba_odpovede"] ) && ( $data["volba_odpovede"] == "neexistuje" || $data["volba_odpovede"] == "zmazat" )
			);
	}

	public static function vypracovanie_testu__uloz_odpoved__typ_1($data) {
		return $data["typ_odpovede"] == "textova";
	}

	public static function vypracovanie_testu__uloz_odpoved__typ_2($data) {
		return
			$data["typ_odpovede"] == "vyberova" && (
				isset( $data["volba_odpovede"] ) ||
				isset( $data["odpoved"] ) && is_array($data["odpoved"])
			);
	}

	public static function vypracovanie_testu__uloz_odpoved__typ_3($data) {
		$prvotna_kontrola = 
			$data["typ_odpovede"] == "parovacia" && (
				isset( $data["volba_odpovede"] ) ||
				isset( $data["odpoved"] ) && is_array($data["odpoved"])
			);

		if (!$prvotna_kontrola) return false;
		
		// skontroluj, ze ak je zadana odpoved, vsetky hodnoty su pary v spravnom tvare
		if ( isset($data["odpoved"]) ) {
			foreach ($data["odpoved"] as $odpoved) {
				if (
					!isset($odpoved["lava"]) || !isset($odpoved["prava"]) ||
					!is_int($odpoved["lava"]) || !is_int($odpoved["prava"])
				) {
					return false; // par nie je v spravnom tvare
				}
			}
		}

		return true;
	}




	public static function vypracovanie_testu__odovzdaj_test($data) {
		return isset( $data["akcia"] ) && $data["akcia"] == "odovzdat-test";
	}
}
?>
