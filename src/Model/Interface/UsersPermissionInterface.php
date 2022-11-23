<?php
namespace App\Model\Interface;

use App\Entity\Person;
use App\Entity\Users;

interface UsersPermissionInterface {
    public function checkPermission(Person $user):String;
    public function getPermission($username,$em):int;
}