<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CompanyType;
use App\Entity\Company;


class RegistrerController extends AbstractController
{
    // Methode pour l'inscription
    #[Route('/registrer', name: 'app_registrer', methods: ['get', 'post'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $company = new Company();

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($company);
            $entityManager->flush();

            $this->addFlash('success', 'Bravo, vous venez de rejoindre la famille WebQuote !');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registrer/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
