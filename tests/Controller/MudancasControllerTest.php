<?php

namespace App\Tests\Controller;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LoginTest extends TestCase
{
    /**
     * @Route('/', name: 'log_employer')
     */
    public function login(ManagerRegistry $doctrine, Request $request, HttpClientInterface $client): Response
    {
        //get Session 
        $session = $doctrine->getManager()->getRepository('App\Entity\Session')->findOneBy(['client' => $client]);
        
        //validate session
        $session->validate();
        
        //return response
        return new Response($session);
    }
}

class MyTest extends WebTestCase
{
    public function testGetRequest()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}