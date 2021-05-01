<?php

// API_T = api/testy

class Hlasky__API_T {
	private static $hlasky__API_T = array(
		// GSC = generic sanity check (rovnake hlasky z celej API sluzby)
		"API_T__GSC_1" => array(
			"kod" => "API_T__GSC_1",
			"sprava" => "Neboli prijaté žiadne dáta."
		),

		"API_T__GSC_2" => array(
			"kod" => "API_T__GSC_2",
			"sprava" => "Nie je prihlásený žiaden učiteľ."
		),
		// END --- GSC = generic sanity check (rovnake hlasky z celej API sluzby)
		
		
		
		// NT = vytvaranie noveho testu, SC = sanity check
		"API_T__NT_SC_1" => array(
			"kod" => "API_T__NT_SC_1",
			"sprava" => "Dáta testu sú v nesprávnom formáte."
		),
		// END --- NT = vytvaranie noveho testu, SC = sanity check
		
		
		
		// NT = vytvaranie noveho testu, U = uspesna operacia
		"API_T__NT_U_1" => array(
			"kod" => "API_T__NT_U_1",
			"sprava" => "Nový test bol úspešne vytvorený.",
			"id_testu" => null // sem ide ID testu z db
		),
		// END --- NT = vytvaranie noveho testu, U = uspesna operacia
		
		
		
		// NT = vytvaranie noveho testu, C = chyba, neuspesna operacia
		"API_T__NT_C_1" => array(
			"kod" => "API_T__NT_C_1",
			"sprava" => "Nepodarilo sa vložiť dáta."
		)
		// END --- NT = vytvaranie noveho testu, C = chyba, neuspesna operacia
	);


	public static function get_hlaska($kod) {
		return self::$hlasky__API_T[$kod];
	}
}
?>