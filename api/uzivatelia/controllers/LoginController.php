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
            session_start();
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
        session_start();
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
}
