<?php

namespace App\Model\Class;

use App\Entity\Departemant;
use App\Entity\Fournisor;
use App\Entity\Users;
use App\Model\Class\Session as ClassSession;
use App\Model\Interface\FunctionStrategyInterface;
use Exception;
use Symfony\Component\HttpFoundation\Session\Session;

class FunctionUsers implements FunctionStrategyInterface
{
    public function getData(String $url)
    {
        $ch = curl_init($url);
        $data = array();
        $payload = json_encode(array("user" => $data));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function checkData($data, $em)
    {
        foreach ($data as $key => $value) {
            $user = $em->getRepository(Users::class)->findOneBy(['name' => $value->name]);
            if ($user == null) {
                //if user not exist in database return 1
                $users = new Users();
                $this->addUsersLogin($users, $value, $em);
                Logger::log("Add user $value->name");
            } /*else {
                //if user exist in database return 0
                //Logger::log("this user already exicte $value->name" );
            }*/
        }
    }

    
    public function checkDataFourn($data, $em)
    {
        foreach ($data as $key => $value) {
            $fournisour = $em->getRepository(Fournisor::class)->findOneBy(['cliId' => $value->cliId]);
            if ($fournisour == null) {
                //if user not exist in database return 1
                $fournisour = new Fournisor();
                $fournisour->setCliId($value->cliId);
                $fournisour->setNome($value->name);
                $fournisour->setAdress($value->street);
                $fournisour->setNumber($value->number);
                $fournisour->setCity($value->city);
                $fournisour->setState($value->state);
                $fournisour->setDistrict($value->district);
                $fournisour->setPhone($value->phone);
                $fournisour->setEmail($value->email);
                $this->addEntity($fournisour,$em);
            }   else {/*
                $fournisour->setCliId($value->cliId);
                $fournisour->setNome($value->name);
                $fournisour->setAdress($value->street);
                $fournisour->setNumber($value->number);
                $fournisour->setCity($value->city);
                $fournisour->setState($value->state);
                $fournisour->setDistrict($value->district);
                $fournisour->setPhone($value->phone);
                $fournisour->setEmail($value->email);*/
            }
        }
    }


    public function addUsersLogin($entity, $data, $em)
    {
        $entity->setName($data->name);
        $entity->setEmail($data->email);
        $departemant = $em->getRepository(Departemant::class)->findOneBy(['name' => $data->departemant]);
        try {
            if ($departemant == null) {
                $departemant = new Departemant();
                $departemant->setName($data->departemant);
                $entity->setDepartemant($departemant);
                $em->persist($departemant);
                $em->persist($entity);
                $em->flush();
            } else {
                $entity->setDepartemant($departemant);
                $em->persist($entity);
                $em->flush();
            }
            Logger::log("the entity add with successful");
        } catch (Exception $e) {
            Logger::log("entity fail in add :  $e");
        }
    }

    public function addEntity($entity, $em)
    {
        try {
            $em->persist($entity);
            $em->flush();
            Logger::log("the entity add with successful");
        } catch (Exception $e) {
            Logger::log("entity fail in add :  $e");
        }
    }

    public function updateEntity($entity, $em)
    {
        try {
            $em->persist($entity);
            $em->flush();
            Logger::log("the entity update with successful");
        } catch (Exception $e) {
            Logger::log("entity fail in update : $e");
        }
    }

    public function login($username, $password, $em, $request): bool
    {
        try {
            $password = hash('whirlpool', $password);
            $url = 'http://10.100.1.253/a/connection.php?sys=20&user=' . $username . '&pass=' . $password . '';
            $user = $this->getData($url);
            $ses = new Sessions();
            if ($user != null) {
                $person = $em->getRepository(Users::class)->findOneBy(['name' => $user->name]);
                $person->setPassword($password);
                $person->setUsername($username);
                $person->setRole($user->role);
                $person->setLastConnection($this->getTimeNow());
                $em->persist($person);
                $em->flush();
                $ses->setSession($person, 'sessionUser', $request);
                Logger::log("User $username was login");
                return true;
            } else {
                Logger::log("User $username try to login");
                return false;
            }
        } catch (Exception $e) {
            Logger::log("User $username try to login with error: $e");
            return false;
        }
    }

    public function loginAdmin($username, $password, $em, $request): bool
    {
        try {
            $password = hash('whirlpool', $password);
            $url = 'http://10.100.1.253/a/connection.php?sys=20&user=' . $username . '&pass=' . $password . '';
            $user = $this->getData($url);
            $ses = new Sessions();
            if ($user != null) {
                $person = $em->getRepository(Users::class)->findOneBy(['username' => $username]);
                if ($person == null) {
                    Logger::log("User $username without permision as Admin");
                    return false;
                } else {
                    $ses->setSession($person, 'admin', $request);
                    Logger::log("User $username was login as Admin");
                    return true;
                }
            } else {
                Logger::log("User $username try to login as Admin");
                return false;
            }
        } catch (Exception $e) {
            Logger::log("User $username try to login as admin with error: $e");
            return false;
        }
    }

    public function getTimeNow()
    {
        date_default_timezone_set("America/Sao_Paulo");
        $datetime = new \DateTime();
        $datetime->format('Y-m-d H:i:s');
        return $datetime;
    }

    public function createToken($username): string
    {
        // Create token header as a JSON string
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // Create token payload as a JSON string
        $payload = json_encode(['user_id' => $username]);
        // Encode Header to Base64Url String
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        // Create JWT
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }
}
