<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Form\InvoiceType;
use App\Repository\ClientRepository;
use App\Repository\InvoiceRepository;
use App\Repository\QuoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard/invoice')]
class InvoiceController extends AbstractController
{
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
  public function new(Request $request, EntityManagerInterface $entityManager, QuoteRepository $quoteRepository): Response
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

      $this->addFlash('success', 'Facture créée');

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

  #[Route('/{id}', name: 'app_invoice_show', methods: ['GET'])]
  public function show(Invoice $invoice): Response
  {
    return $this->render('invoice/show.html.twig', [
      'invoice' => $invoice,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_invoice_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Invoice $invoice, EntityManagerInterface $entityManager): Response
  {
    $form = $this->createForm(InvoiceType::class, $invoice);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      $this->addFlash('success', 'Facture modifiée');

      return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('invoice/form.html.twig', [
      'invoice' => $invoice,
      'form' => $form,
      'title' => 'Modifier une facture',
      'buttonText' => 'Modifier',
      'icon' => 'ti-edit',
      'backPath' => 'app_invoice_index',
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

    return $this->redirectToRoute('app_invoice_index', [], Response::HTTP_SEE_OTHER);
  }
}
