<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Service\PdfService;
use App\Service\EmailService;
use App\Service\FernetService;
use App\Repository\QuoteRepository;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/dashboard/invoice')]
class InvoiceController extends AbstractController
{
  private $emailService;

  private const EMAIL_TEMPLATE_ID = 5702540;
  private const EMAIL_TEMPLATE_ID_2 = 5737876;

  public function __construct(EmailService $emailService)
  {
      $this->emailService = $emailService;
  }

  #[Route('/', name: 'app_invoice_index', methods: ['GET'])]
  public function index(
    Request $request,
    ClientRepository $clientRepository,
    InvoiceRepository $invoiceRepository,
  ): Response
  {
    $clients = $this->getUser()->getAgency()->getClients();
    $client = $clients[0] ?? null;

    if ($request->query->has('id')) {
      $selectedClient = $request->query->get('id');
      $client = $clientRepository->find($selectedClient);
      $this->denyAccessUnlessGranted('VIEW', $client);
    }

    if ($client) {
      $quotes = $client->getQuotes();

      $invoices = [];
      foreach ($quotes as $quote) {
        $invoices[] = $invoiceRepository->findOneBy(['quote' => $quote]);
      }
      // Remove null values from array
      $invoices = array_filter($invoices);

      // Merge quote title and price into invoice
      $elements = [];
      foreach ($invoices as $invoice) {
        $quote = $invoice->getQuote();
        $elements[] = [
          'id' => $invoice->getId(),
          'date' => $invoice->getDate(),
          'paid' => $invoice->isPaid(),
          'title' => $quote->getTitle(),
          'amount' => $quote->getAmount(),
        ];
      }
    } else {
      $elements = [];
    }

    return $this->render('invoice/index.html.twig', [
      'clients' => $clients,
      'client' => $client,
      'elements' => $elements,
    ]);
  }

  #[Route('/new', name: 'app_invoice_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager, QuoteRepository $quoteRepository, PdfService $pdf, FernetService $fernetService): Response
  {
    $quoteId = $request->query->getInt('quote_id');
    $quote = $quoteRepository->find($quoteId);

    if (!$quote) {
      return $this->redirectToRoute('app_quote_index');
    }
    $this->denyAccessUnlessGranted('VIEW', $quote);

    $invoice = new Invoice();
    $invoice->setQuote($quote);

    $form = $this->createForm(InvoiceType::class, $invoice);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($invoice);
      $entityManager->flush();

      //Génère le lien de paiement d'un devis
      $encodedInvoiceId = $fernetService->encode($invoice->getId());
  
      $paymentLink = $this->generateUrl('app_payment', [
          'id' => $encodedInvoiceId,
      ], UrlGeneratorInterface::ABSOLUTE_URL);

      // Envoi le mail de paiement de la facture que si le client n'a pas déjà payé en espèce
      if (!$invoice->isPaid())
      {
        $fileName= 'Facture_'. $invoice->getId();
        $pdfFilePath = 'pdf/'.$fileName.'.pdf';

        $pdf->generatePdf($this->render("pdf/facture.html.twig", [
          'facture'   => $invoice->getId(),
          'client'    => $invoice->getQuote()->getClient()->getName(),
          'prix'      => $invoice->getQuote()->getAmount(),
          'services'  => $invoice->getQuote()->getServices(),
          'date'      => $invoice->getQuote()->getDate(),
          'mail'      => $invoice->getQuote()->getClient()->getEmail()
          ]), $fileName);
  
        $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID, $invoice->getQuote()->getClient()->getEmail(), '', '', $pdfFilePath, $fileName, $paymentLink);
      
        $this->addFlash('success', 'Facture créée avec succès');
      } 
      else 
      {
        $fileName= 'recu_'. $invoice->getId();
        $pdfFilePath = 'pdf/'.$fileName.'.pdf';
  
        $pdf->generatePdf($this->render("pdf/recu.html.twig", [
          'prix'      => $invoice->getQuote()->getAmount(),
          ]), $fileName);
  
        $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID_2, $invoice->getQuote()->getClient()->getEmail(), '', '', $pdfFilePath, $fileName, '');
        $this->addFlash('success', 'Facture créée avec succès');
      }
     
      return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('invoice/form.html.twig', [
      'invoice' => $invoice,
      'form' => $form,
      'title' => 'Ajouter une facture',
      'buttonText' => 'Ajouter',
      'icon' => 'ti-plus',
      'backPath' => 'app_quote_index',
    ]);
  }

  #[Route('/{id}', name: 'app_invoice_delete', methods: ['POST'])]
  public function delete(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
  {
    if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
      $entityManager->remove($invoice);
      $entityManager->flush();

      $this->addFlash('success', 'Facture supprimée');
    }

    return $this->redirectToRoute('app_invoice_index', [
      'id' => $invoice->getQuote()->getClient()->getId()
    ], Response::HTTP_SEE_OTHER);
  }

  // Télécharger une facture
  #[Route('/{id}/download', name: 'app_invoice_pdf', methods: ['GET'])]
  public function pdf(Invoice $invoice, PdfService $pdf): Response
  { 
    $agency= $this->getUser()->getAgency();

    if(!$agency){
      $this->denyAccessUnlessGranted('VIEW', $invoice);
    }
  
    // Génère le pdf et l'affiche dans le navigateur
    $pdfContent = $pdf->showPdf($this->render("pdf/facture.html.twig", [
      'facture'  => $invoice->getId(),
      'client'  => $invoice->getQuote()->getClient()->getName(),
      'prix'  => $invoice->getQuote()->getAmount(),
      'services'  => $invoice->getQuote()->getServices(),
      'date'   => $invoice->getQuote()->getDate(),
      'mail'   => $invoice->getQuote()->getClient()->getEmail()
    ]));

    $response = new Response($pdfContent);
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; fileName="Facture_' . $invoice->getId() . '.pdf"');
  
    return $response; 
  }

  // Relance paiement de la facture
  #[Route('/{id}/mail', name: 'app_invoice_mail', methods: ['GET'])]
  public function mail(Request $request, Invoice $invoice, PdfService $pdf, FernetService $fernetService): Response
  {
    $agency = $this->getUser()->getAgency();

    if (!$agency) {
        $this->denyAccessUnlessGranted('VIEW', $invoice);
    }

    $fileName= 'Facture_'. $invoice->getId();
    $pdfFilePath = 'pdf/'.$fileName.'.pdf';

    $pdf->generatePdf($this->render("pdf/facture.html.twig", [
      'facture'  => $invoice->getId(),
      'client'  => $invoice->getQuote()->getClient()->getName(),
      'prix'  => $invoice->getQuote()->getAmount(),
      'services'  => $invoice->getQuote()->getServices(),
      'date'   => $invoice->getQuote()->getDate(),
      'mail'   => $invoice->getQuote()->getClient()->getEmail()
      ]), $fileName);

    // Génère le lien de paiement d'une facture
    $encodedInvoiceId = $fernetService->encode($invoice->getId());

    $paymentLink = $this->generateUrl('app_payment', [
        'id' => $encodedInvoiceId,
    ], UrlGeneratorInterface::ABSOLUTE_URL);

    $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID, $invoice->getQuote()->getClient()->getEmail(), '', '', $pdfFilePath, $fileName, $paymentLink);
    
    $this->addFlash('success', 'Facture renvoyée au client avec succès');

    return $this->redirectToRoute('app_invoice_index', [
      'id' => $invoice->getQuote()->getClient()->getId()
    ], Response::HTTP_SEE_OTHER);
  }
}
