<?php
// API ENDPOINT NA PRACU S TESTAMI Z POHLADU UCITELA

$surove_prijate_data = file_get_contents('php://input');

include "api-endpoint-include.php";
include "api-frontend/ApiTesty_API_frontend_student.class.php";


$generic_sanity_check =
    ApiTesty_sanityChecker::generic_check__prijate_data($surove_prijate_data) &&
    ApiTesty_sanityChecker::generic_check__prihlaseny_student();// na tejto API musi byt prihlaseny vylucne student



if ($generic_sanity_check) {
	$prijate_data = json_decode($surove_prijate_data, true);


	// sanity checker v tychto vetvach rovno kontroluje, ktora poziadavka sa ma vykonat, preto je to rozvetvene cez if elseif

	if (ApiTesty_sanityChecker::vypracovanie_testu__zacni_pisat($prijate_data)) {
        $vystup = ApiTesty_API_frontend_student::zacni_pisat($mysqli_api_testy, $prijate_data["kluc"], $_SESSION["studentId"]);
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
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved(
                    "1_4_5", $mysqli_api_testy, $kluc, $student_id, $otazka_id, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }

            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje
                $odpoved = isset($prijate_data["odpoved"]) ? $prijate_data["odpoved"] : "NULL";
                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_1_4_5(
                    $mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }
        }

        elseif (ApiTesty_sanityChecker::vypracovanie_testu__uloz_odpoved__typ_2($prijate_data)) {
            if ( isset($prijate_data["volba_odpovede"]) && $prijate_data["volba_odpovede"] == "zmazat" ) { // student chce odpoved na tuto otazku zmazat
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved(
                    "2", $mysqli_api_testy, $kluc, $student_id, $otazka_id, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }
            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje (pozor, tu to je vzdy ako array odpovedi)
                $odpoved = isset($prijate_data["odpoved"]) ? $prijate_data["odpoved"] : array("NULL");
                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_2(
                    $mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }
        }

        elseif (ApiTesty_sanityChecker::vypracovanie_testu__uloz_odpoved__typ_3($prijate_data)) {
            if ( isset($prijate_data["volba_odpovede"]) && $prijate_data["volba_odpovede"] == "zmazat" ) { // student chce odpoved na tuto otazku zmazat
                $pokus = ApiTesty_sqlContainer::zmaz_odpoved(
                    "3", $mysqli_api_testy, $kluc, $student_id, $otazka_id, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }
            else {
                // odpoved sa bud zapisuje, prepisuje alebo nastavuje, ze podla studenta neexistuje (pozor, tu to je vzdy ako array parov lava-prava strana)
                $odpoved = array( // ak sa nastavuje, ze neexistuje, je tu jeden par
                    array("lava" => "NULL", "prava" => "NULL")
                );

                if ( isset($prijate_data["odpoved"]) ) {
                    $odpoved = $prijate_data["odpoved"];
                }

                $pokus = ApiTesty_sqlContainer::uloz_odpoved__typ_3(
                    $mysqli_api_testy, $kluc, $student_id, $otazka_id, $odpoved, $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
                );
            }
        }

        if ($pokus) echo json_encode( Hlasky__API_T::get_hlaska("API_T__VT_U_3") );
        else echo json_encode( Hlasky__API_T::get_hlaska("API_T__VT_C_3") );
    }

    elseif (ApiTesty_sanityChecker::vypracovanie_testu__odovzdaj_test($prijate_data)) {
        $vystup = ApiTesty_API_frontend_student::odovzdaj_test(
            $mysqli_api_testy, $_SESSION["pisanyTestKluc"], $_SESSION["studentId"],
            $_SESSION["testDatumZaciatkuPisania"], $_SESSION["testCasZaciatkuPisania"]
        );
		echo json_encode($vystup);
    }

	else {
		echo json_encode( Hlasky__API_T::get_hlaska("API_T__GSC_3") ); // nespravny format dat
	}
}
?>