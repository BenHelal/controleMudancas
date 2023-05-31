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
        if($branch == 'local' | $branch == 'local2'){
            return "10.100.1.245";
        }elseif($branch == 'prod'| $branch == 'prod1'){
            return "sistemas.serdia.com.br";

        }elseif($branch == 'test' | $branch == 'test2'  | $branch == 'test4' | $branch == 'test5'){
            return "10.100.1.180";
        }else{
            return "sistemas.serdia.com.br";
        }
    }
}