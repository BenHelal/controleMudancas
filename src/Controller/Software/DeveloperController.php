<?php

namespace App\Controller\Software;

use App\Entity\Mudancas;
use App\Entity\Person;
use App\Entity\Steps;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DeveloperController extends AbstractController
{
    /**
     * Renders the Test TI page for the GestorController.
     *
     * @Route("/software/dev/steps/{id}", name="app_software_devs_steps")
     * @return Response
     */
    public function steps(ManagerRegistry $doctrine, Request $request, $id): Response
    {
        $session = new Session();
        $session = $request->getSession();
        if ($session->get('token_jwt') != '') {
            $em = $doctrine->getManager();
            $person =  $em->getRepository(Person::class)->findOneBy(['name' => $session->get('name')]);
            $mud = $em->getRepository(Mudancas::class)->find($id);
            $muds = $mud->getMudS();

            //steps Gestor 
            $sd = [];
            $s = [];
            $SD =  $muds->getStepsGestor();
            


            foreach ($SD as $key => $value) {
                # code...
                if($value->getApproveSol() =='Aprovar'){
                    array_push($sd, $value);
                }

            }
            foreach ($SD as $keys=> $val) {
                
                foreach ($val->getSteps() as $keys=> $values) {
                    # code...
                    array_push($s, $values);
                }
            }
            return $this->render('software/developer/index.html.twig', [
                'login' => 'null',
                'person' => $person,
                'm' => $mud,
                'muds' => $muds,
                'controller_name' => 'GestorController',
                'sd' => $sd,
                'step' => $s,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }


    /**
     * @Route("/update-task-status", name="update_task_status", methods={"POST"})
     */
    public function updateTaskStatus(ManagerRegistry $doctrine,Request $request)
    {
        // Retrieve the task ID and new status from the AJAX request
        $taskId = $request->request->get('taskId');
        $newStatus = $request->request->get('newStatus');

        // Fetch the task entity from the database
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Steps::class)->find($taskId);

        if (!$task) {
            return new JsonResponse(['message' => 'Task not found'], 404);
        }


        date_default_timezone_set("America/Sao_Paulo");
        $time = new \DateTime();
        $formattedTime = $time->format('Y-m-d H:i:s');
        // Update the task status
        $task->setStatus($newStatus); // Assuming you have a 'status' property in your entity
        $task->setDateStatue(strval($formattedTime ));

        // Persist the changes to the database
        $entityManager->persist($task);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Task status updated successfully']);
    }
}
