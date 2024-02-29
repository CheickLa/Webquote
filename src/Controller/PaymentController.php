<?php

namespace App\Controller;

use App\Service\PdfService;
use App\Service\EmailService;
use App\Service\FernetService;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
  private $emailService;

  private const EMAIL_TEMPLATE_ID = 5737876;

  public function __construct(EmailService $emailService)
  {
      $this->emailService = $emailService;
  }

  #[Route('/payment', name: 'app_payment', methods: ['get', 'post'])]
  public function index(
    Request $request,
    FernetService $fernetService,
    InvoiceRepository $invoiceRepository,
    EntityManagerInterface $entityManager,
    PdfService $pdf
  ): Response
  {
    if ($request->isMethod('POST')) {
      $data = json_decode($request->getContent(), true);
      $invoiceId = $fernetService->decode($data['id']);
      $invoice = $invoiceRepository->find($invoiceId);
      if (!$invoice || $invoice->isPaid()) {
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
      }

      $invoice->setPaid(true);
      $entityManager->persist($invoice);
      $entityManager->flush();

      // Envoie le reçu de paiement au client
      $fileName= 'recu_'. $invoice->getId();
      $pdfFilePath = 'pdf/'.$fileName.'.pdf';

      $pdf->generatePdf($this->render("pdf/recu.html.twig", [
        'prix'      => $invoice->getQuote()->getAmount(),
        ]), $fileName);

      $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID, $invoice->getQuote()->getClient()->getEmail(), '', '', $pdfFilePath, $fileName, '');
  
      return new JsonResponse(null, Response::HTTP_OK);
    }

    $invoiceId = $request->query->get('id');
    if (!$invoiceId) {
      return $this->redirectToRoute('app_home');
    }

    $invoiceId = $fernetService->decode($invoiceId);
    $invoice = $invoiceRepository->find($invoiceId);
    if (!$invoice || $invoice->isPaid()) {
      return $this->redirectToRoute('app_home');
    } 

    return $this->render('payment/index.html.twig', [
      'invoice' => $invoice,
    ]);
  }
}
