<?php

namespace App\Controller;

use App\Entity\ServiceCategory;
use App\Form\ServiceCategoryType;
use App\Repository\ServiceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard/service/category')]
class ServiceCategoryController extends AbstractController
{
  #[Route('/', name: 'app_service_category_index', methods: ['GET', 'POST'])]
  #[IsGranted('ROLE_USER')]
  public function index(Request $request, ServiceCategoryRepository $serviceCategoryRepository): Response
  {
    $serviceCategories = $serviceCategoryRepository->findBy(
      ['agency' => $this->getUser()->getAgency()]
    );

    $serviceCategory = $serviceCategories[0] ?? null;
    if ($request->isMethod('POST')) {
      if ($this->isCsrfTokenValid('select', $request->request->get('_token'))) {
        $selected = $request->request->get('selected');
        $serviceCategory = $serviceCategoryRepository->find($selected);
        $this->denyAccessUnlessGranted('VIEW', $serviceCategory);
      }
    }

    return $this->render('service_category/index.html.twig', [
      'service_categories' => $serviceCategories,
      'service_category' => $serviceCategory,
    ]);
  }

  #[Route('/new', name: 'app_service_category_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $serviceCategory = new ServiceCategory();
    $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $serviceCategory->setAgency($this->getUser()->getAgency());
      $entityManager->persist($serviceCategory);
      $entityManager->flush();

      return $this->redirectToRoute('app_service_category_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('service_category/form.html.twig', [
      'service_category' => $serviceCategory,
      'form' => $form,
      'title' => 'Ajouter une catégorie de prestations',
      'buttonText' => 'Ajouter'
    ]);
  }

  #[Route('/edit/{id}', name: 'app_service_category_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, ServiceCategory $serviceCategory, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('EDIT', $serviceCategory);

    $form = $this->createForm(ServiceCategoryType::class, $serviceCategory);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      return $this->redirectToRoute('app_service_category_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('service_category/form.html.twig', [
      'service_category' => $serviceCategory,
      'form' => $form,
      'title' => 'Modifier ' . $serviceCategory->getName(),
      'buttonText' => 'Modifier'
    ]);
  }

  #[Route('/delete/{id}', name: 'app_service_category_delete', methods: ['GET', 'POST'])]
  public function delete(Request $request, ServiceCategory $serviceCategory, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('DELETE', $serviceCategory);

    if ($request->isMethod('POST')) {
      if ($this->isCsrfTokenValid('delete' . $serviceCategory->getId(), $request->request->get('_token'))) {
        $entityManager->remove($serviceCategory);
        $entityManager->flush();
      }

      return $this->redirectToRoute('app_service_category_index', [], Response::HTTP_SEE_OTHER);
    } else {
      return $this->render('service_category/delete.html.twig', [
        'service_category' => $serviceCategory,
      ]);
    }
  }
}
