<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AgencyType;
use App\Entity\Agency;


class RegisterController extends AbstractController
{
    // Methode pour l'inscription
    #[Route('/register', name: 'app_register', methods: ['get', 'post'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $agency = new Agency();

        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($agency);
            $entityManager->flush();

            $this->addFlash('success', 'Bravo, vous venez de rejoindre la famille WebQuote !');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
