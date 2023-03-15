<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Entity\Mudancas;
use App\Entity\Person;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/adminmud')]
class LogClientController extends AbstractController
{
    #[Route('/log/client/{id}', name: 'app_log_client')]
    public function index(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_admin') != '') {
            $em = $doctrine->getManager();
            $p = $em->getRepository(Person::class)->findOneBy(['name' => $session->get('admin_name')]);
            $mud =  $em->getRepository(Mudancas::class)->find($id);

            if ($mud != null) {
                $api = $em->getRepository(ApiToken::class)->findOneBy(['mud' => $mud]);
                if ($api != null) {
                    $token = $api->getToken();         
                    if ($token != null) {
                        $url = "http://10.100.1.245/ClientExteranlAcces/public/get/data/log";
                        //The data you want to send via POST
                        $fields = [
                            'token' => $token,
                            'id' => $mud->getId(),
                        ];

                        //url-ify the data for the POST
                        $fields_string = http_build_query($fields);

                        //open connection
                        $ch = curl_init();

                        //set the url, number of POST vars, POST data
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

                        //So that curl_exec returns the contents of the cURL; rather than echoing it
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        //execute post
                        $client = curl_exec($ch);
                        $cl = json_decode($client, true);
                        
                        return $this->render('log_client/index.html.twig', [
                            'controller_name' => 'LogClientController',
                            'login' => false,
                            'p' => $p,
                            'log' => $cl
                        ]);
                    } else {
                        return $this->redirectToRoute('mudAdmin', ['id' => $mud->getId()]);
                    }
                } else {
                    return $this->redirectToRoute('mudAdmin', ['id' => $mud->getId()]);
                }
                return $this->render('log_client/index.html.twig', [
                'controller_name' => 'LogClientController',
                'login' => false,
                'p' => $p
            ]);
            } else {
                return $this->redirectToRoute('lm');
            }
        }
    }
}
