<?php
require_once __DIR__ . "/DatabaseController.php";

class MathCanvasController extends DatabaseController
{
    public function saveAnswer($otazka_id,$odpoved): array
    {
        try {
            $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO odpovede_studentov_typ_1_4_5 (kluc_testu, otazka_id, student_id, datum_zaciatku_pisania, cas_zaciatku_pisania, zadana_odpoved)
                                                                        VALUES (:klucTestu, :otazkaId, :studentId, :datumZaciatkuPisania, :casZaciatkuPisania, :odpoved)
                                                                        ON DUPLICATE KEY UPDATE kluc_testu = :klucTestu, otazka_id = :otazkaId, zadana_odpoved = :odpoved, vyhodnotenie = 2");
            $statement->bindValue(":klucTestu",$_SESSION["pisanyTestKluc"], PDO::PARAM_STR);
            $statement->bindValue(":otazkaId",$otazka_id, PDO::PARAM_INT);
            $statement->bindValue(":studentId",$_SESSION["studentId"], PDO::PARAM_INT);
            $statement->bindValue(":datumZaciatkuPisania",$_SESSION["testDatumZaciatkuPisania"], PDO::PARAM_STR);
            $statement->bindValue(":casZaciatkuPisania", $_SESSION["testCasZaciatkuPisania"], PDO::PARAM_STR);
            $statement->bindValue(":odpoved",$odpoved, PDO::PARAM_STR);
            $statement->execute();
            return array(
                "error" => false,
                "status" => "success",
            );
        }
        catch (Exception $exception){
            return array(
                "error" => true,
                "status" => "failed",
                "errorMessage"=>$exception->getMessage()
            );
        }

    }
}
