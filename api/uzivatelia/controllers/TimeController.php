<?php
require_once __DIR__ . "/DatabaseController.php";

class TimeController extends DatabaseController
{
    public function getTimeInSeconds(){
        if (!isset($_SESSION["pisanyTestKluc"]) || !isset($_SESSION["studentId"]))
            return array();

        $key = $_SESSION["pisanyTestKluc"];
        $studentId = $_SESSION["studentId"];
        $statement = $this->mysqlDatabase->prepareStatement("SELECT zoznam_pisucich_studentov.datum_zaciatku_pisania, zoznam_pisucich_studentov.cas_zaciatku_pisania, zoznam_pisucich_studentov.zostavajuci_cas
                                                                    FROM zoznam_pisucich_studentov
                                                                    WHERE kluc_testu = :key AND student_id = :studentId");

        try {

            $statement->bindValue(':key', $key, PDO::PARAM_STR);
            $statement->bindValue(':studentId', $studentId, PDO::PARAM_STR);
            $statement->execute();
            $dateTime = $statement->fetch(PDO::FETCH_NUM);

            $seconds = $this->getTimeSeconds($dateTime[0],$dateTime[1], $dateTime[2]);
            if ($seconds <= 0)
                return array(
                    "error"=>false,
                    "status"=>"success",
                    "time"=>"end"
                );
            return array(
                "error"=>false,
                "status"=>"success",
                "time"=>date("i:s",$seconds)
            );
        }
        catch (Exception $e){
            return array(
                "error"=>true,
                "status"=>"failed",
                "message"=>$e->getMessage()
            );
        }
    }

    private function getTimeSeconds($datum,$cas, $zostavajuciCas){
        return $zostavajuciCas -(time() - strtotime($datum." ".$cas));
    }

}
