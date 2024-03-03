<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DesignGuideController extends AbstractController
{
    #[Route('/design_guide', name: 'app_design_guide')]
    public function index(): Response
    {
        return $this->render('design_guide/index.html.twig', [
            'controller_name' => 'DesignGuideController',
        ]);
    }
}
