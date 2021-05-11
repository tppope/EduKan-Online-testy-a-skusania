<?php

require_once __DIR__."/tcpdf/TCPDF-main/tcpdf.php";
require_once __DIR__."/../../controllers/LoginController.php";

$controller = new LoginController();

session_start();

include "../../../../db-login.php";
include "../../../testy/api-backendova-implementacia/hlasky.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_sanityChecker.class.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_jsonParser.class.php";
include "../../../testy/api-backendova-implementacia/ApiTesty_sqlContainer.class.php";
include "../../../testy/api-backendova-implementacia/db-testov-setup.php";
include "../../../testy/api-frontend/ApiTesty_API_frontend_ucitel.class.php";


$kluc = "U1T1620690775"; ///$_SESSION['pisanyTestKluc'];
$ucitel_id = 1; ///$_SESSION['userId'];

$cely_test = ApiTesty_API_frontend_ucitel::nacitaj_existujuci_test($mysqli_api_testy, $kluc, $ucitel_id);


/// PDF
class MYPDF extends TCPDF {

    //Page header
    public function Header() {
        // Logo
        $this->Image('../../../../resources/pictures/educan/edukan.png',60,5,100,35);
        // set margins
        $this->SetMargins(PDF_MARGIN_LEFT, 45, PDF_MARGIN_RIGHT);
    }
    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Strana '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-10);
        $this->Cell(0, 12, 'Vytvorili: Tomáš Popík, Juraj Zozulák, Martin Smetanka, Katarína Stasová, Filip Poljak Škobla ', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

/// PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


foreach ($cely_test['data_testu']['zoznam_pisucich_studentov'] as $student) {
    $pdf->AddPage();
    $student_id = $student['student_id'];
    $info_about_student = $controller->getStudent($student_id);
    $datum_zaciatku_pisania = $student['datum_zaciatku_pisania'];
    $cas_zaciatku_pisania = $student['cas_zaciatku_pisania'];
    $odpovede_studenta = ApiTesty_API_frontend_ucitel::nacitaj_odpovede($mysqli_api_testy, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);

    $html = '
         <table>
            <tr>
                <th class="header-th name" align="center" style="font-size: xx-large;">'.$info_about_student['name']." ".$info_about_student['surname'].'</th>
            </tr>
            <tr class="header-tr">
                <th class="header-th" width="10%" align="center">Otázka</th>
                <th class="header-th" width="40%" align="center">Názov otázky</th>
                <th class="header-th" width="50%" align="center">Odpovede na otázku</th>
            </tr>
         ';
     foreach($cely_test['data_testu']['otazky'] as $key=>$otazky)
     {
         $cislo_otazky = $key;
         $nazov_otazky = $otazky['nazov'];
         $typ_otazky = $otazky['typ'];
         $html.='<tr>
                    <td align="center" style="font-weight: bold">'.$cislo_otazky.'</td>
                    <td align="center">'.$nazov_otazky.'</td>
         ';
         if($typ_otazky == 1 || $typ_otazky == 4 || $typ_otazky == 5){
             $odpoved = $odpovede_studenta['odpovede'][$typ_otazky]['zadana_odpoved'];
             if($odpoved == null){
                 $html.= '<td align="center">'."NULL".'</td>
                </tr>';
             }else{
             $html.= '<td align="center">'.$odpoved.'</td>
                </tr>';
             }
         }elseif ($typ_otazky == 2){
             $odpoved = $odpovede_studenta['odpovede'][$typ_otazky]['zadana_odpoved'];
             $full = "";
             foreach ($odpoved as $oneodpoved){
                 $full = $full + $oneodpoved;
             }
             if($full == null){
                 $html .= '<td align="center">' . "NULL" . '</td>
                </tr>';
             }else {
                 $html .= '<td align="center">' . $full . '</td>
                </tr>';
             }
         }
         else{
             $html.= '<td align="center">'."nic".'</td>
                </tr>';
         }
     }
    $html.= '</table>
            <style>
                table{
                 border-collapse: collapse;
                }
                th,td{
                border:1px solid black;
                }
                .header-th,.header-tr{
                background-color: rgb(23,162,184);
                color: white;
                font-weight: bold;
                }
               
            </style>
          ';
    $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
    $pdf->Write($h=0, "", $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
}


/// OUTPUT
$pdf->Output();



