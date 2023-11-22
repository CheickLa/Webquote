<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegistrerController extends AbstractController
{
    #[Route('/registrer', name: 'app_registrer')]
    public function index(): Response
    {
        return $this->render('registrer/index.html.twig', [
            'controller_name' => 'RegistrerController',
        ]);
    }
}
