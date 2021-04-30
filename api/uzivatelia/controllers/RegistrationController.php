<?php
require_once __DIR__ . "/DatabaseController.php";

class RegistrationController extends DatabaseController
{
    public function performRegistration($name,$surname,$email, $password): array
    {
        $hashPassword = password_hash(trim($password),PASSWORD_DEFAULT);
        $statement = $this->mysqlDatabase->prepareStatement("INSERT INTO ucitel (meno, priezvisko, email, heslo)
                                                                    VALUES (:name, :surname, :email, :password)");
        $statement->bindValue(':name', trim($name), PDO::PARAM_STR);
        $statement->bindValue(':surname', trim($surname), PDO::PARAM_STR);
        $statement->bindValue(':email', trim($email), PDO::PARAM_STR);
        $statement->bindValue(':password', $hashPassword, PDO::PARAM_STR);
        try {
            $statement->execute();
            return array(
                "error"=>false,
                "status"=>"success",
                "lastInsertId"=>$this->mysqlDatabase->getConnection()->lastInsertId(),
                "errorCode"=>null,
                );
        }
        catch (PDOException $PDOException){
            return array(
                "error"=>true,
                "status"=>"failed",
                "lastInsertId"=>-1,
                "errorCode"=>$PDOException->errorInfo[1],
                "errorMessage"=>$PDOException->getMessage(),
            );
        }
    }
}
