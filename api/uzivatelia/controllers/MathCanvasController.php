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

    public function markAsRight($right, $otazkaId){
        $vyhodnotenie = 0;
        if ($right)
            $vyhodnotenie = 1;

        try {
            $statement = $this->mysqlDatabase->prepareStatement("UPDATE odpovede_studentov_typ_1_4_5
                                                                        SET vyhodnotenie = :vyhodnotenie
                                                                        WHERE kluc_testu = :klucTestu AND otazka_id = :otazkaId AND datum_zaciatku_pisania = :datumZaciatkuPisania AND cas_zaciatku_pisania = :casZaciatkuPisania AND student_id = :studentId");
            $statement->bindValue(":klucTestu",$_SESSION["pisanyTestKluc"], PDO::PARAM_STR);
            $statement->bindValue(":otazkaId",$otazkaId, PDO::PARAM_INT);
            $statement->bindValue(":studentId",$_SESSION["studentId"], PDO::PARAM_INT);
            $statement->bindValue(":datumZaciatkuPisania",$_SESSION["datumZaciatkuPisania"], PDO::PARAM_STR);
            $statement->bindValue(":casZaciatkuPisania", $_SESSION["casZaciatkuPisania"], PDO::PARAM_STR);
            $statement->bindValue(":vyhodnotenie", $vyhodnotenie, PDO::PARAM_INT);
            $statement->execute();

            $statement2 = $this->mysqlDatabase->prepareStatement("UPDATE vyhodnotenie_testov_studentov
                                                                        SET vyhodnotenie = :vyhodnotenie
                                                                        WHERE kluc_testu = :klucTestu AND otazka_id = :otazkaId AND datum_zaciatku_pisania = :datumZaciatkuPisania AND cas_zaciatku_pisania = :casZaciatkuPisania AND student_id = :studentId");
            $statement2->bindValue(":klucTestu",$_SESSION["pisanyTestKluc"], PDO::PARAM_STR);
            $statement2->bindValue(":otazkaId",$otazkaId, PDO::PARAM_INT);
            $statement2->bindValue(":studentId",$_SESSION["studentId"], PDO::PARAM_INT);
            $statement2->bindValue(":datumZaciatkuPisania",$_SESSION["datumZaciatkuPisania"], PDO::PARAM_STR);
            $statement2->bindValue(":casZaciatkuPisania", $_SESSION["casZaciatkuPisania"], PDO::PARAM_STR);
            $statement2->bindValue(":vyhodnotenie", $vyhodnotenie, PDO::PARAM_INT);
            $statement2->execute();

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
