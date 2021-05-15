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


$kluc = $_SESSION['pisanyTestKluc'];
$ucitel_id = $_SESSION['userId'];

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
        $this->SetFont('Dejavu Sans', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Strana '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->SetY(-10);
        $this->Cell(0, 12, 'Vytvorili: Tomáš Popík, Juraj Zozuľák, Martin Smetanka, Katarína Stasová, Filip Poljak Škobla ', 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}



/// PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetFont('Dejavu Sans','',12);



$pdf->AddPage("","","",true);
$htmlTestName = '<header>
            <h1>Názov testu: '.$cely_test['data_testu']['nazov'].'</h1>
         </header>
         <style>
            h1{
                color: rgb(23,162,184);
            }
         </style>
        ';
$pdf->writeHTMLCell(0, 0, '', '', $htmlTestName, 0, 1, 0, true, 'C', true);






foreach ($cely_test['data_testu']['zoznam_pisucich_studentov'] as $student) {

    $student_id = $student['student_id'];
    $info_about_student = $controller->getStudent($student_id);
    $datum_zaciatku_pisania = $student['datum_zaciatku_pisania'];
    $cas_zaciatku_pisania = $student['cas_zaciatku_pisania'];
    $odpovede_studenta = ApiTesty_API_frontend_ucitel::nacitaj_odpovede($mysqli_api_testy, $kluc, $student_id, $datum_zaciatku_pisania, $cas_zaciatku_pisania);

    $htmlStudentName= '<div>
                <header>
                    <h2>'.$info_about_student['id'].' - '.$info_about_student['name'].' '.$info_about_student['surname'].'</h2>
                </header>
             ';

     foreach($cely_test['data_testu']['otazky'] as $key=>$otazky)
     {
         $cislo_otazky = $key;
         $nazov_otazky = $otazky['nazov'];
         $typ_otazky = $otazky['typ'];

         $classOtazka = "";
         if($odpovede_studenta['vyhodnotenieCeleho'][$key] === 0){
             $classOtazka= "nespravnaotazka";
         }elseif($odpovede_studenta['vyhodnotenieCeleho'][$key] === 1){
             $classOtazka= "spravnaotazka";
         }else{
             $classOtazka = "nezodpovedanaotazka";
         }

         $htmlStudentName.='<div class="'.$classOtazka.'">
                    <h3>'." ".$cislo_otazky.'. '.$nazov_otazky.'</h3>
                
            ';
         if($typ_otazky == 1){
             if (empty($odpovede_studenta['odpovede'][$key]['zadana_odpoved']))
                 $htmlStudentName.='<div align="center">Nebolo zodpovedané</div>';
             else
                 $htmlStudentName.='<div align="center">'.$odpovede_studenta['odpovede'][$key]['zadana_odpoved'].'</div>';

             $spravne_odpovede_pre_1 = "";
             foreach ($otazky['spravne_odpovede'] as $odpoved){
                 $spravne_odpovede_pre_1.=$odpoved.", ";
             }
             $htmlStudentName.='<div style="color:green"> Možné správne odpovede: '.$spravne_odpovede_pre_1.'</div>
             ';
         }
         if($typ_otazky == 2){
             $spravne_zadane_odpovede_2 = "";
             foreach ($odpovede_studenta['odpovede'][$key] as $odpovedstudenta){
                 $spravne_zadane_odpovede_2.=$odpovedstudenta['zadana_odpoved'].", ";
             }
             if (empty($spravne_zadane_odpovede_2))
                 $htmlStudentName.='<div align="center">Nebolo zodpovedané</div>';
             else
                 $htmlStudentName.='<div align="center">'.$spravne_zadane_odpovede_2.'</div>';

             $spravne_odpovede_pre_2 = "";
             $nespravne_odpovede_pre_2 = "";
             foreach ($otazky['odpovede'] as $odpoved){
                 if($odpoved['je_spravna']){
                     $spravne_odpovede_pre_2.=$odpoved['text'].", ";
                 }else{
                     $nespravne_odpovede_pre_2.=$odpoved['text'].", ";
                 }
             }
             $htmlStudentName.='<div style="color:green"> Možné správne odpovede: '.$spravne_odpovede_pre_2.'</div>
             ';
             $htmlStudentName.='<div style="color:red"> Nesprávne odpovede: '.$nespravne_odpovede_pre_2.'</div>
             ';
         }
         if($typ_otazky == 3){
             $spravne_zadane_odpovede_3 = "";
            foreach ($odpovede_studenta['odpovede'][$key] as $odpovedstudenta){
                $spravne_zadane_odpovede_3.="L".$odpovedstudenta['par_lava_strana']."---"."P".$odpovedstudenta['par_prava_strana']."<br>";
            }
            if (empty($spravne_zadane_odpovede_3))
                $htmlStudentName.='<div align="center">Nebolo zodpovedané</div>';
            else
                $htmlStudentName.='<div align="center">'.$spravne_zadane_odpovede_3.'</div>';


            $pary = "";
            foreach ($otazky['pary'] as $par){
                $pary.="   L".$par['lava'].":".$otazky['odpovede_lave'][$par['lava']]."---"."P".$par['prava'].":".$otazky['odpovede_prave'][$par['prava']]."<br>";
            }
             $htmlStudentName.='<div style="color:green"> Možné správne odpovede: <br> '.$pary.'</div>
             ';
         }
         if($typ_otazky == 4){
             $spravne_zadane_odpovede_4 = $odpovede_studenta['odpovede'][$key]['zadana_odpoved'];
             if ($spravne_zadane_odpovede_4) {
                 if (str_contains($spravne_zadane_odpovede_4, "inFiles-")) {
                     $file_type = (explode("-", $spravne_zadane_odpovede_4))[1];
                     $path = $key . "_" . $kluc . "_" . $student_id . "_" . $datum_zaciatku_pisania . "_" . $cas_zaciatku_pisania . "." . $file_type;
                     $htmlStudentName .= '<div align="center"><img class="img-content" src="../../testy/uploadedImages/' . $path . '" alt="Math_obrazok" />  </div>';
                 } else {
                     $img = $odpovede_studenta['odpovede'][$key]['zadana_odpoved'];
                     $img = str_replace('data:image/png;base64,', '', $img);
                     $data = base64_decode($img);
                     $file = 'images/' . $key . "_" . $student_id . "_" . $kluc . '.png';
                     $success = file_put_contents($file, $data);
                     //$htmlStudentName.= '<div align="center"><img src="images/'.$key."_".$student_id.".png".'" alt="Math_obrazok" height="300" width="300" />  </div>';
                     $htmlStudentName .= '<div align="center"><img src="images/' . $key . '_' . $student_id . '_' . $kluc . '.png" height="500" width="500" alt="Math_obrazok"/>  </div>';
                     //$htmlStudentName.= '<div align="center"><img src="'.$odpovede_studenta['odpovede'][$key]['zadana_odpoved'].'" alt="Math_obrazok" height="300" width="300" />  </div>';
                 }
             }
             else{
                 $htmlStudentName .= '<div align="center">Nebolo zodpovedané</div>';
             }
         }
         if($typ_otazky == 5){
             $spravne_zadane_odpovede_5 = $odpovede_studenta['odpovede'][$key]['zadana_odpoved'];
             if($spravne_zadane_odpovede_5) {
                 if (str_contains($spravne_zadane_odpovede_5, "inFiles-")) {
                     $file_type = (explode("-", $spravne_zadane_odpovede_5))[1];
                     $path = $key . "_" . $kluc . "_" . $student_id . "_" . $datum_zaciatku_pisania . "_" . $cas_zaciatku_pisania . "." . $file_type;
                     $htmlStudentName .= '<div align="center"><img class="img-content" src="../../testy/uploadedImages/' . $path . '" alt="Math_obrazok" />  </div>';

                 } else {
                     $img = file_get_contents("https://latex.codecogs.com/gif.latex?".$spravne_zadane_odpovede_5);
                     $path = "images/latex_".$key . "_" . $kluc . "_" . $student_id . "_" . $datum_zaciatku_pisania . "_" . $cas_zaciatku_pisania . ".gif";
                     file_put_contents($path,$img);
                     if (getimagesize($path)===false){
                         $htmlStudentName.='<div align="center">Zadaná odpoveď nie je platný Latex výraz</div>';
                     }
                     else{
                         $htmlStudentName .= '<div class="latex" align="center"><img src="'.$path.'"alt="Math_obrazok" /></div>';
                     }
                     $htmlStudentName.='<div align="center"><strong>RAW formát: </strong>'.$spravne_zadane_odpovede_5.'</div>';


                 }
             }
             else{
                 $htmlStudentName .= '<div align="center">Nebolo zodpovedané</div>';
             }
         }
         $htmlStudentName.='</div>
         ';
     }
    $htmlStudentName.='</div>
         ';


    $htmlStudentName.= '   
            <style>
               .nezodpovedanaotazka{
                    border: 1.5px solid grey;
                    border-radius: 10px; 
               }
               .spravnaotazka{
                    border: 1.5px solid green;
                    border-radius: 10px; 
               }
               .nespravnaotazka{
                    border: 1.5px solid red;
                    border-radius: 10px; 
               }
               .img-content{
                    width: 500px;
               }
               h2{
                    
               }
               h3{
                    margin-left: 10px;
               }
              
            </style>
              
          ';
    ///$pdf->writeHTMLCell(0, 0, '', '', $htmlStudentName, 0, 1, 0, true, 'L', true);
    ///$pdf->Write($h=0, "", $link='', $fill=0, $align='C', $ln=true, $stretch=0, $firstline=false, $firstblock=false, $maxh=0);
    $pdf->writeHTML($htmlStudentName, true, false, true, false, '');
}

/// OUTPUT
ob_end_clean();
$pdf->Output("$kluc.pdf" );



