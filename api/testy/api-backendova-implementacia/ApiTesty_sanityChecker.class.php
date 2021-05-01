<?php


// STATICKY KONTAJNER NA METODY, KTORE TESTUJU KONZISTENCIU PRIJATYCH DAT


class ApiTesty_sanityChecker {

	// Skontroluje data na vytvorenie noveho testu.
	public static function novy_test($data) {
		$spravny_format_dat =
			isset( $data["nazov"] ) &&
			isset( $data["casovy_limit"] ) &&
			isset( $data["aktivny"] ) &&
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

				default: return false; // iny typ nie je pripustny
			}
		}

		// test je v poriadku
		return true;
	}
}
?>