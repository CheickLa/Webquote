<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/dashboard/service')]
class ServiceController extends AbstractController
{
  #[Route('/new', name: 'app_service_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $service = new Service();
    $form = $this->createForm(ServiceType::class, $service);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $this->denyAccessUnlessGranted('CREATE', $service);
      $entityManager->persist($service);
      $entityManager->flush();

      $this->addFlash('success', 'Prestation ajoutée');

      return $this->redirectToRoute('app_service_category_index', ['id' => $service->getServiceCategory()->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->render('service/form.html.twig', [
      'service' => $service,
      'form' => $form,
      'serviceCategories' => $this->getUser()->getAgency()->getServiceCategories(),
      'defaultCategoryId' => $request->query->get('id'),
      'title' => 'Ajouter une prestation',
      'buttonText' => 'Ajouter',
      'icon' => 'ti-playlist-add'
    ]);
  }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('EDIT', $service);
            $entityManager->flush();

            $this->addFlash('success', 'Prestation modifiée');

            return $this->redirectToRoute('app_service_category_index', [
                'id' => $service->getServiceCategory()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('service/form.html.twig', [
            'service' => $service,
            'form' => $form,
            'serviceCategories' => $this->getUser()->getAgency()->getServiceCategories(),
            'defaultCategoryId' => $service->getServiceCategory()->getId(),
            'title' => 'Modifier ' . $service->getName(),
            'buttonText' => 'Modifier',
            'icon' => 'ti-edit'
        ]);
    }

    #[Route('/{id}', name: 'app_service_delete', methods: ['POST'])]
    public function delete(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $serviceCategoryId = $service->getServiceCategory()->getId();

        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $this->denyAccessUnlessGranted('DELETE', $service);
            $entityManager->remove($service);
            $entityManager->flush();

            $this->addFlash('success', 'Prestation supprimée');
        }

        // On renvoie la catégorie de prestation pour rester sur celle-ci à l'affichage après la suppression
        return $this->redirectToRoute('app_service_category_index', ['id' => $serviceCategoryId], Response::HTTP_SEE_OTHER);
    }

    #[Route('/', name: 'app_service_get_by_category', methods: ['GET'])]
    public function getServices(Request $request): JsonResponse 
    {
        $id = $request->query->get('category_id');
        $serviceCategories = $this->getUser()->getAgency()->getServiceCategories();
        $serviceCategory = $serviceCategories->filter(fn($serviceCategory) => $serviceCategory->getId() == $id)->first();

        if (!$serviceCategory) {
            return new JsonResponse([
                'services' => [],
            ], Response::HTTP_NOT_FOUND);
        }

        $services = $serviceCategory->getServices();

        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'price' => $service->getPrice(),
            ];
        }

        return new JsonResponse([
          'services' => $data,
        ]);
    }
}
