<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\ClientRepository;
use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/quote')]
class QuoteController extends AbstractController
{
    #[Route('/', name: 'app_quote_index', methods: ['GET'])]
    public function index(Request $request, ClientRepository $clientRepository, QuoteRepository $quoteRepository): Response
    {
        $clients = $this->getUser()->getAgency()->getClients();
        $client = $clients[0] ?? null;

        if ($request->query->has('id')) {
          $selectedClient = $request->query->get('id');
          $client = $clientRepository->find($selectedClient);
          $this->denyAccessUnlessGranted('VIEW', $client);
        }

        if ($client) {
          $quotes = $quoteRepository->findBy(
            ['client' => $client],
            ['date' => Criteria::DESC]
          ); 
        } else {
          $quotes = [];
        }
        
        return $this->render('quote/index.html.twig', [
            'clients' => $clients,
            'client' => $client,
            'quotes' => $quotes,
        ]);
    }

    #[Route('/new', name: 'app_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quote = new Quote();
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('CREATE', $quote);
            $entityManager->persist($quote);
            $entityManager->flush();

            $this->addFlash('success', 'Devis créé');

      return $this->redirectToRoute('app_quote_index', [
        'id' => $quote->getClient()->getId(),
      ], Response::HTTP_SEE_OTHER);
        }

        // TODO Implémenter un select intermédiaire pour choisir 
        // la catégorie de prestations 
        $services = $this->getUser()->getAgency()->getServices();
        $clients = $this->getUser()->getAgency()->getClients();

        return $this->render('quote/form.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'services' => $services,
            'clients' => $clients,
            'currentClient' => $request->query->get('id'),
            'title' => 'Créer un devis',
            'buttonText' => 'Créer',
            'icon' => 'ti-plus',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_quote_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('EDIT', $quote);
            $entityManager->flush();

            $this->addFlash('success', 'Devis modifié');

      return $this->redirectToRoute('app_quote_index', [
        'id' => $quote->getClient()->getId(),
      ], Response::HTTP_SEE_OTHER);
        }

        $services = $this->getUser()->getAgency()->getServiceCategories()->map(function ($serviceCategory) {
            return $serviceCategory->getServices()->toArray();
        });
        $services = array_merge(...$services);
        $clients = $this->getUser()->getAgency()->getClients();
        $currentService = $quote->getService()->getId();
        $currentClient = $quote->getClient()->getId();

        return $this->render('quote/form.html.twig', [
            'quote' => $quote,
            'form' => $form,
            'services' => $services,
            'clients' => $clients,
            'currentService' => $currentService,
            'currentClient' => $currentClient,
            'title' => 'Modifier un devis',
            'buttonText' => 'Modifier',
            'icon' => 'ti-edit',
        ]);
    }

    #[Route('/{id}', name: 'app_quote_delete', methods: ['POST'])]
    public function delete(Request $request, Quote $quote, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quote->getId(), $request->request->get('_token'))) {
            $this->denyAccessUnlessGranted('DELETE', $quote);
            $entityManager->remove($quote);
            $entityManager->flush();

            $this->addFlash('success', 'Devis supprimé');
        }

        return $this->redirectToRoute('app_quote_index', [], Response::HTTP_SEE_OTHER);
    }
}
