<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Service\FernetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends AbstractController
{
  #[Route('/payment', name: 'app_payment', methods: ['get', 'post'])]
  public function index(
    Request $request,
    FernetService $fernetService,
    InvoiceRepository $invoiceRepository,
    EntityManagerInterface $entityManager,
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
