<?php

namespace App\Controller;

use App\Repository\AgencyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SettingsController extends AbstractController
{
    #[Route('/dashboard/settings', name: 'app_settings')]
    public function index(Request $request, AgencyRepository $agencyRepository): Response
    {
        $agencies = $agencyRepository->findAll();
        $selectedAgencyId = $request->query->get('id');

        if ($selectedAgencyId) {
            if ($this->isGranted('ROLE_ADMIN')) {
                $selectedAgency = $agencyRepository->find($selectedAgencyId);

                if ($selectedAgency) {
                    $user = $selectedAgency->getUsers()->first();

                    if ($user) {
                        $email = $user->getEmail();

                        return $this->redirectToRoute('app_dashboard', ['_switch_user' => $email]);
                    }
                }
            } else {
                throw new AccessDeniedException('Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
            }
        }

        return $this->render('settings/index.html.twig', [
            'agencies' => $agencies,
        ]);
    }
}