<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Service\PdfService;
use App\Service\EmailService;
use App\Repository\QuoteRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboard/quote')]
class QuoteController extends AbstractController
{
    private $emailService;

    private const EMAIL_TEMPLATE_ID = 5684096;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

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
        'elements' => $quotes,
      ]);
    }

    #[Route('/new', name: 'app_quote_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PdfService $pdf, ClientRepository $clientRepository): Response
    {
        $quote = new Quote();

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('CREATE', $quote);
          
            // Cible le client sélectionné afin d'exploiter son Email pour l'envoie de mail avec pdf joint
            $clientId = $form->get('client')->getData();
            $client = $clientRepository->find($clientId);

            $currentServices = $quote->getServices();

            $quote->setClient($client);

            $entityManager->persist($quote);
            $entityManager->flush();

            // Génère le pdf
            $html = $this->render('pdf/devis.html.twig', [
              'devis'  => $quote->getId(),
              'client' => $quote->getClient(),
              'amount' => $quote->getAmount(),
              'currentServices' => $currentServices,
              'date'   => $quote->getDate(),
              'mail'   => $quote->getClient()->getEmail() 
            ]);

            $fileName = 'Devis_'. $quote->getId();
            $pdf->generatePdf($html,$fileName);

            // Génère du lien de paiement d'un devis
            $paymentLink = $this->generateUrl('app_payment', [
              'quote_id' => $quote->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            
            // Envoie du mail
            $pdfFilePath = 'pdf/'.$fileName.'.pdf';
            $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID, $quote->getClient()->getEmail(), '', '', $pdfFilePath, $fileName, $paymentLink);
            
            $this->addFlash('success', 'Devis créé');

            return $this->redirectToRoute('app_quote_index', [
              'id' => $quote->getClient()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $serviceCategories = $this->getUser()->getAgency()->getServiceCategories();
        $services = $serviceCategories->first()->getServices();
        $clients = $this->getUser()->getAgency()->getClients();

        return $this->render('quote/form.html.twig', [
          'quote' => $quote,
          'form' => $form,
          'serviceCategories' => $serviceCategories,
          'services' => $services,
          'clients' => $clients,
          'currentClient' => $request->query->get('id'),
          'currentServices' => [],
          'title' => 'Ajouter un devis',
          'buttonText' => 'Ajouter',
          'icon' => 'ti-playlist-add',
        ]);
    }

    // Route temporaire pour la page de paiement test
    #[Route('/payment', name: 'app_payment')]
    public function payment(Request $request): Response
    {
      // C'est là que se feront les traitement du paiement
  
      // return new Response('Page de paiement pour la facture '.$quote->getId());
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

      $serviceCategories = $this->getUser()->getAgency()->getServiceCategories();
      $services = $serviceCategories->first()->getServices();
      $clients = $this->getUser()->getAgency()->getClients();
      $currentServices = $quote->getServices();
      $currentClient = $quote->getClient()->getId();

      return $this->render('quote/form.html.twig', [
        'quote' => $quote,
        'form' => $form,
        'serviceCategories' => $serviceCategories, 
        'services' => $services,
        'clients' => $clients,
        'currentServices' => $currentServices,
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

    return $this->redirectToRoute('app_quote_index', [
      'id' => $quote->getClient()->getId(),
    ], Response::HTTP_SEE_OTHER);
  }
}
