<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileController extends AbstractController
{
    /**
     * @Route("/assets/{id}/documentation/{file}", name="file_download")
     */
    public function download($id, $file)
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/assets/' . $id . '/documentation/' . $file;

        try {
            $file = new File($filePath);
        } catch (FileNotFoundException $e) {
            throw $this->createNotFoundException('The file does not exist');
        }

        return $this->file($file);
    }
}