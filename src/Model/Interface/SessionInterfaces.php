<?php
namespace App\Model\Interface;

use Symfony\Component\HttpFoundation\Request;

Interface SessionInterfaces {
    public function checkSession($session): bool; 
    public function checkSessionAdmin($session): bool;
    public function setSession($user, $typeSession,Request $request);
    
}