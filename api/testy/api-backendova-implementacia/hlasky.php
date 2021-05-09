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
			"sprava" => "Nemáte povolenie vykonať túto operáciu."
		),

		"API_T__GSC_3" => array(
			"kod" => "API_T__GSC_3",
			"sprava" => "Dáta požiadavky sú v nesprávnom formáte."
		),
		// END --- GSC = generic sanity check (rovnake hlasky z celej API sluzby)



		// LT = nacitanie (load) existujuceho testu, U = uspesna operacia
		"API_T__LT_U_1" => array(
			"kod" => "API_T__LT_U_1",
			"sprava" => "Test bol úspešne načítaný.",
			"data_testu" => null // data testu, popis pozri v "002 nacitanie testu response.json"
		),
		// END --- LT = nacitanie (load) existujuceho testu, U = uspesna operacia



		// LT = nacitanie (load) existujuceho testu, C = chyba, neuspesna operacia
		"API_T__LT_C_1" => array(
			"kod" => "API_T__LT_C_1",
			"sprava" => "Test s týmto kľúčom buď neexistuje, alebo ho vytvoril iný učiteľ."
		),

		"API_T__LT_C_2" => array(
			"kod" => "API_T__LT_C_2",
			"sprava" => "Test s týmto kľúčom buď neexistuje, alebo máte nesprávny kľúč."
		),
		// END --- LT = nacitanie (load) existujuceho testu, C = chyba, neuspesna operacia



		// NT = vytvaranie noveho testu, U = uspesna operacia
		"API_T__NT_U_1" => array(
			"kod" => "API_T__NT_U_1",
			"sprava" => "Nový test bol úspešne vytvorený.",
			"kluc_testu" => null // sem ide kluc testu z db
		),
		// END --- NT = vytvaranie noveho testu, U = uspesna operacia



		// NT = vytvaranie noveho testu, C = chyba, neuspesna operacia
		"API_T__NT_C_1" => array(
			"kod" => "API_T__NT_C_1",
			"sprava" => "Nepodarilo sa vložiť dáta."
		),
		// END --- NT = vytvaranie noveho testu, C = chyba, neuspesna operacia



		// PT = praca s testami z pohladu ucitela, U = uspesna operacia
		"API_T__PT_U_1" => array(
			"kod" => "API_T__PT_U_1",
			"sprava" => "Zoznam testov bol úspešne načítaný.",
			"zoznam_testov" => array() // sem ide zoznam testov z db
		),

		"API_T__PT_U_2" => array(
			"kod" => "API_T__PT_U_2",
			"sprava" => "Test bol úspešne aktivovaný."
		),

		"API_T__PT_U_3" => array(
			"kod" => "API_T__PT_U_3",
			"sprava" => "Test bol úspešne deaktivovaný."
		),
		// END --- PT = praca s testami z pohladu ucitela, U = uspesna operacia



		// PT = praca s testami z pohladu ucitela, GC = genericka chybova hlaska, neuspesna operacia
		"API_T__PT_GC" => array(
			"kod" => "API_T__PT_GC",
			"sprava" => "Požadovaná operácia s testom nebola úspešná."
		),
		// END --- PT = praca s testami z pohladu ucitela, GC = genericka chybova hlaska, neuspesna operacia



		// VT = vypracovavanie testu studentom, U = uspesna operacia
		"API_T__VT_U_1" => array(
			"kod" => "API_T__VT_U_1",
			"sprava" => "Úspešne ste začali písať test. Veľa šťastia.",
			"zostavajuci_cas" => null // sem ide zostavajuci cas z db
		),

		"API_T__VT_U_2" => array(
			"kod" => "API_T__VT_U_2",
			"sprava" => "Pokračujete v písaní tohto testu.",
			"zostavajuci_cas" => null, // sem ide zostavajuci cas z db
			"odoslane_odpovede" => array() // sem ide zoznam uz odoslanych odpovedi z db
		),

		"API_T__VT_U_3" => array(
			"kod" => "API_T__VT_U_3",
			"sprava" => "Odpoveď bola úspešne uložená."
		),
		// END --- VT = vypracovavanie testu studentom, U = uspesna operacia



		// VT = vypracovavanie testu studentom, C = chyba, neuspesna operacia
		"API_T__VT_C_1" => array(
			"kod" => "API_T__VT_C_1",
			"sprava" => "Tento test buď neexistuje alebo nemáte správny kľúč, preto ho nemôžete začať písať."
		),

		"API_T__VT_C_2" => array(
			"kod" => "API_T__VT_C_2",
			"sprava" => "Nemáte rozpísaný žiaden test."
		),

		"API_T__VT_C_3" => array(
			"kod" => "API_T__VT_C_3",
			"sprava" => "Odpoveď sa nepodarilo uložiť."
		)
		// END --- VT = vypracovavanie testu studentom, C = chyba, neuspesna operacia
	);


	public static function get_hlaska($kod) {
		return self::$hlasky__API_T[$kod];
	}
}
?>