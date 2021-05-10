<?php
require_once __DIR__ . "/DatabaseController.php";


class LoginController extends DatabaseController
{
    public function performLogin($email, $password): array
    {
        try {
            $user = $this->getUser(trim($email));
            if ($user) {
                $response = $this->checkPasswords($password, $user["password"]);
                $this->sessionLogin($response, $user["id"]);
            } else
                $response = array(
                    "error" => false,
                    "status" => "success",
                    "emailVerify" => false,
                    "passwordVerify" => null,
                );
        } catch (Exception $exception) {
            $response = array(
                "error" => true,
                "status" => "failed",
                "emailVerify" => null,
                "passwordVerify" => null,
                "errorMessage"=>$exception->getMessage(),
            );
        }

        return $response;
    }

    private function sessionLogin($response, $id)
    {
        if ($response["passwordVerify"]) {
            $_SESSION["userId"] = $id;
        }
    }

    private function getUser($email)
    {
        $statement = $this->mysqlDatabase->prepareStatement("SELECT ucitel.id, ucitel.heslo AS password
                                                                    FROM ucitel
                                                                    WHERE ucitel.email = :email");
        try {
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }

    public function getLoggedInUser(): array
    {
        if (isset($_SESSION["userId"])) {
            $statement = $this->mysqlDatabase->prepareStatement("SELECT ucitel.id, ucitel.meno, ucitel.priezvisko, ucitel.email AS password
                                                                    FROM ucitel
                                                                    WHERE ucitel.id = :id");
            try {
                $statement->bindValue(':id', $_SESSION["userId"], PDO::PARAM_STR);
                $statement->execute();
                return array(
                    "error" => false,
                    "status" => "success",
                    "alreadyLogin" => true,
                    "user" => $statement->fetch(PDO::FETCH_ASSOC)
                );
            } catch (PDOException $PDOException) {
                return array(
                    "error" => true,
                    "status" => "failed",
                    "alreadyLogin" => null,
                    "user" => null,
                    "errorMessage"=>$PDOException->getMessage(),
                );
            }
        } else {
            return array(
                "error" => false,
                "status" => "success",
                "alreadyLogin" => false,
                "user" => null,
            );
        }
    }

    private function checkPasswords($password, $hashPassword): array
    {
        return array(
            "error" => false,
            "status" => "success",
            "emailVerify" => true,
            "passwordVerify" => password_verify($password, $hashPassword),
        );
    }

    public function performStudentLogin($testKey, $studentOwnId, $name, $surname): array
    {
        if ($this->checkTest($testKey)) {
            $studentId = $this->getStudentId($studentOwnId, $name, $surname);
            if ($studentId == false)
                $studentId = $this->insertStudent($studentOwnId, $name, $surname);
            $_SESSION["studentId"] = $studentId;
            return array(
                "error" => false,
                "status" => "success",
                "badTestKey" => false,
            );

        }
        else{
            return array(
                "error" => true,
                "status" => "failed",
                "badTestKey" => true,
            );
        }

    }

    private function getStudentId ($studentOwnId, $name, $surname){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT student.id
                                                                    FROM student
                                                                    WHERE student.meno = :meno AND student.priezvisko = :priezvisko AND student.student_own_id = :ownId");
        try {
            $statement->bindValue(':meno', $name, PDO::PARAM_STR);
            $statement->bindValue(':priezvisko', $surname, PDO::PARAM_STR);
            $statement->bindValue(':ownId', $studentOwnId, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchColumn();
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }

    private function insertStudent($studentOwnId, $name, $surname): string
    {
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO student (meno, priezvisko, student_own_id)
                                                                    VALUES (:name, :surname, :ownId)");
        $statement->bindValue(':name', trim($name), PDO::PARAM_STR);
        $statement->bindValue(':surname', trim($surname), PDO::PARAM_STR);
        $statement->bindValue(':ownId', trim($studentOwnId), PDO::PARAM_STR);
        $statement->execute();
        return $this->mysqlDatabase->getConnection()->lastInsertId();
    }

    private function checkTest($kluc){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT kluc_testu
                                                                    FROM zoznam_testov
                                                                    WHERE kluc_testu = :kluc AND aktivny = 1");
        try {
            $statement->bindValue(':kluc', $kluc, PDO::PARAM_STR);
            $statement->execute();
            if ($statement->fetchColumn())
                return true;
            else
                return false;
        } catch (PDOException $PDOException) {
            throw $PDOException;
        }
    }

    public function getStudent($studentId){
        $statement = $this->mysqlDatabase->prepareStatement("SELECT student.id as dbID, student.student_own_id as id, student.meno as name, student.priezvisko as surname
                                                                    FROM student
                                                                    WHERE student.id = :studentId");
        try {
            $statement->bindValue(':studentId', $studentId, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $PDOException) {
            return array(
                "error"=>true,
                "status"=>"failed",
                "message"=>$PDOException->getMessage()
            );
        }
    }

    public function sendLeaveTabAlert(){
        try{

            if (!isset($_SESSION["pisanyTestKluc"]) || !isset($_SESSION["studentId"]))
                throw new Exception("Nenastavené session");

            $key = $_SESSION["pisanyTestKluc"];
            $studentId = $_SESSION["studentId"];

            $statement = $this->mysqlDatabase->prepareStatement("UPDATE zoznam_pisucich_studentov
                                                                    SET zoznam_pisucich_studentov.pocet_tab_odideni = 1
                                                                    WHERE kluc_testu = :key AND student_id = :studentId");

            $statement->bindValue(':key', $key, PDO::PARAM_STR);
            $statement->bindValue(':studentId', $studentId, PDO::PARAM_INT);
            $statement->execute();
            return array(
                "status"=>"succes",
                "error"=>false,
            );
        }catch(PDOException $e) {
            return array(
                "status"=>"failed",
                "error"=>true,
                "errorMessage"=>$e->getMessage()
            );
        }
    }

    public function getLeftStudents(): array
    {
        if (!isset($_SESSION["pisanyTestKluc"]))
            return array();

        $key = $_SESSION["pisanyTestKluc"];
        $statement = $this->mysqlDatabase->prepareStatement("SELECT zoznam_pisucich_studentov.student_id
                                                                    FROM zoznam_pisucich_studentov
                                                                    WHERE kluc_testu = :key AND pocet_tab_odideni = 1");

        try {
            $statement->bindValue(':key', $key, PDO::PARAM_STR);
            $statement->execute();
            $students = $statement->fetchAll(PDO::FETCH_COLUMN);

            if (empty($students))
                return array();
            $this->setLeftStudentToFalse($key);

            return $this->getStudents($students);
        }
        catch (Exception $e){
            return array(
                "error"=>true,
                "status"=>"failed",
                "message"=>$e->getMessage()
            );
        }
    }

    private function getStudents($students): array
    {
        $studentsNames = array();
        foreach ($students as $studentId){
            array_push($studentsNames,$this->getStudent($studentId));
        }
        return $studentsNames;
    }


    private function setLeftStudentToFalse($key){
        $statement = $this->mysqlDatabase->prepareStatement("UPDATE zoznam_pisucich_studentov
                                                                    SET zoznam_pisucich_studentov.pocet_tab_odideni = 0
                                                                    WHERE kluc_testu = :key");
        try{
            $statement->bindValue(':key', $key, PDO::PARAM_STR);
            $statement->execute();
        }catch(PDOException $e) {
            throw $e;
        }
    }




}
