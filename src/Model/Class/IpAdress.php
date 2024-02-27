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
        if($branch == 'local' | $branch == 'local2'| $branch == 'local4' | $branch == 'local5'){
            return "10.100.1.124";
        }elseif($branch == 'prod'| $branch == 'prod1' | $branch == 'prod2'){
            return "sistemas.serdia.com.br";

        }elseif($branch == 'test9' | $branch == 'test2'  | $branch == 'test4' | $branch == 'test5'| $branch == 'test6'){
            return "10.100.1.180";
        }else{
            //return "sistemas.serdia.com.br";    
            return "10.100.1.124";
            //return "10.100.1.180";
        }
    }
}