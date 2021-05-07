<?php
// API ENDPOINT NA PRACU S TESTAMI Z POHLADU UCITELA

$surove_prijate_data = file_get_contents('php://input');

include "api-endpoint-include.php";
include "api-frontend/ApiTesty_API_frontend_student.class.php";

$_SESSION["pisanyTestKluc"] = "U1T1620377014";
if ($generic_sanity_check) { // na tejto API musi byt prihlaseny vylucne student
	$generic_sanity_check = ApiTesty_sanityChecker::generic_check__prihlaseny_student();
}

if ($generic_sanity_check) {
	$prijate_data = json_decode($surove_prijate_data, true);


	// sanity checker v tychto vetvach rovno kontroluje, ktora poziadavka sa ma vykonat, preto je to rozvetvene cez if elseif

	if (ApiTesty_sanityChecker::vypracovanie_testu__zacni_pisat($prijate_data)) {
        $vystup = ApiTesty_API_frontend_student::zacni_pisat($mysqli_api_testy, $prijate_data["kluc"], $_SESSION["studentId"]);
		
        if ($vystup["kod"] == "API_T__VT_U_1" || $vystup["kod"] == "API_T__VT_U_2") {
            // student test zacal pisat, resp. ho uz mal rozpisany, zapis do session kluc testu
            $_SESSION["pisanyTestKluc"] = $prijate_data["kluc"];
        }
        
        echo json_encode($vystup);
	}

    elseif ( !isset($_SESSION["pisanyTestKluc"]) ) { // student nepise ziaden test, na tejto API stranke pre neho nie su urcene ziadne odpovede
        echo json_encode( Hlasky__API_T::get_hlaska("API_T__VT_C_2") );
    }

    elseif (ApiTesty_sanityChecker::vypracovanie_testu__ukladanie_odpovede($prijate_data)) {
        // aky typ odpovede sa spracuva?

        $pokus = 0;
        $kluc = $_SESSION["pisanyTestKluc"];
        $student_id = $_SESSION["studentId"];
        $otazka_id = $prijate_data["otazka_id"];

        if (ApiTesty_sanityChecker::vypracovanie_testu__uloz_odpoved__typ_1($prijate_data)) {
            if ( isset($prijate_data["volba_odpovede"]) && $prijate_data["volba_odpovede"] == "zmazat" ) { // student chce odpoved na tuto otazku zmazat
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved("1_4_5", $mysqli_api_testy, $kluc, $student_id, $otazka_id);
            }

            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje
                $odpoved = isset($prijate_data["odpoved"]) ? $prijate_data["odpoved"] : "NULL";
                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_1_4_5($mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved);
            }
        }

        elseif (ApiTesty_sanityChecker::vypracovanie_testu__uloz_odpoved__typ_2($prijate_data)) {
            if ( isset($prijate_data["volba_odpovede"]) && $prijate_data["volba_odpovede"] == "zmazat" ) { // student chce odpoved na tuto otazku zmazat
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved("2", $mysqli_api_testy, $kluc, $student_id, $otazka_id);
            }
            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje (pozor, tu to je vzdy ako array odpovedi)
                $odpoved = isset($prijate_data["odpoved"]) ? $prijate_data["odpoved"] : array("NULL");
                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_2($mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved);
            }
        }

        elseif (ApiTesty_sanityChecker::vypracovanie_testu__uloz_odpoved__typ_3($prijate_data)) {
            if ( isset($prijate_data["volba_odpovede"]) && $prijate_data["volba_odpovede"] == "zmazat" ) { // student chce odpoved na tuto otazku zmazat
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved("3", $mysqli_api_testy, $kluc, $student_id, $otazka_id);
            }
            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje (pozor, tu to je vzdy ako array parov lava-prava strana)
                $odpoved = array( // ak sa nastavuje, ze neexistuje, je tu jeden par
                    array("lava" => "NULL", "prava" => "NULL")
                );

                if ( isset($prijate_data["odpoved"]) ) {
                    $odpoved = $prijate_data["odpoved"];
                }

                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_3($mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved);
            }
        }

        if ($pokus) echo json_encode( Hlasky__API_T::get_hlaska("API_T__VT_U_3") );
        else echo json_encode( Hlasky__API_T::get_hlaska("API_T__VT_C_3") );
    }

	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_3") ); // nespravny format dat
	}
}
?>