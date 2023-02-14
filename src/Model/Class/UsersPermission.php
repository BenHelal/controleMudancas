<?php

namespace App\Model\Class;

use App\Entity\Person;
use App\Entity\Users;
use App\Model\Interface\UsersPermissionInterface;

class UsersPermission implements UsersPermissionInterface 
{
    function checkPermission(Person $user):String
    {
        if($user->getPermission() === 1){
            return 'ler' ;
        }elseif($user->getPermission() == 2){
            return 'crio';
        }elseif($user->getPermission() == 3){
            return 'Atualizar';
        }elseif($user->getPermission() == null){
            return 'sem permissão';
        }elseif($user->getPermission() == 4){
            return 'toda permissão';
        }
    }

    public function getPermission($username, $em): int
    {
        $person = $em->getRepository(Users::class)->findOneBy(['username' => $username]);
        if($person->getPermission() == null){
            return 0;
        }else{
            return $person->getPermission();
        }
    }

    public function getPermissionByUser($user): int
    {
        if($user->getPermission() == null){
            return 0;
        }else{
            return $user->getPermission();
        }
    }
}