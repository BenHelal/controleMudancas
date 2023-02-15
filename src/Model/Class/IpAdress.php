<?php
namespace App\Model\Class;
/**
 * check the branch of git 
 */
class IpAdress 
{
    public function getIpAdress()
    {
        $branch = trim(shell_exec('git branch --show-current'));
        if($branch == 'local'){
            return "10.100.1.245";
        }elseif($branch == 'prod'){
            return "sistemas.serdia.com.br";
        }elseif($branch == 'test'){
            return "10.100.1.180";
        }
    }
}