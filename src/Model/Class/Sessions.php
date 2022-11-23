<?php

namespace App\Model\Class;

use App\Model\Interface\SessionInterfaces;
use Symfony\Component\HttpFoundation\Request;

class Sessions implements SessionInterfaces{

    function checkSession($session):bool
    {
        if($session->get('token_jwt_') == null){
            return false;
        }else{
            return true;
        }
    }

    function checkSessionAdmin($session):bool
    {
        if($session->get('token_admin') == null){
            return false;
        }else{
            return true;
        }
    }

    public function setSession($person, $typeSession,Request $request)
    {
        $fu = new FunctionUsers();
        if ($typeSession == 'admin') {
            $session = $request->getSession();
            $session->set('token_admin',  $fu->createToken($person->getUsername()));
            $session->set('name_admin', $person->getName());
            $request->setSession($session);
            return;
        } elseif ($typeSession == 'sessionUser') {
            $session =  $request->getSession();
            $session->set('token_jwt',  $fu->createToken($person->getUsername()));
            $session->set('name', $person->getName());
            $request->setSession($session);
            return;
        }
    }
}