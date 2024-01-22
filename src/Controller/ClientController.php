<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    // Index
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(Request $request, ClientRepository $clientRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $agencyUser = $this->getUser()->getAgency();
        $clients = $clientRepository->findBy(['agency' => $agencyUser]);
    
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]); 
    }

    // New
    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('CREATE', $client);
            $client->setAgency($this->getUser()->getAgency());
            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Client ajouté avec succès');

            return $this->redirectToRoute('app_client_index', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/new.html.twig', [
            'client'     => $client,
            'form'       => $form,
            'buttonText' => 'Soumettre',
            'title'      => 'Inscrire un nouveau client',
            'icon'       => 'ti-playlist-add'
        ]);
    }

    // Edit
    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $client);
        
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Client modifié avec succès');

            return $this->redirectToRoute('app_client_index', ['id' => $client->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('client/edit.html.twig', [
            'client'     => $client,
            'form'       => $form,
            'buttonText' => 'Modifier',
            'title'      => 'Modifier les informations du client',
            'icon'       => 'ti-playlist-add'
        ]);
    }

    // Delete
    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {   
        $this->denyAccessUnlessGranted('DELETE', $client);
        
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            $entityManager->remove($client);    
            $entityManager->flush();

            $this->addFlash('success', 'Client supprimé avec succès');
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }
}
