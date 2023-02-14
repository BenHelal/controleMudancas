<?php
namespace App\Model\Interface;

interface FunctionStrategyInterface {
    // add user 
    public function addUsersLogin($users, $data, $em);
    public function addEntity($entity, $em);
    public function updateEntity($entity, $em);
    public function login($username, $password, $em, $request):bool;
    public function getTimeNow();
    public function createToken($username):string;
}