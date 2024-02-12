<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AgencyType;
use App\Entity\Agency;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\AgencyRepository;
use App\Security\AppAuthenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Service\EmailService;
use Symfony\Component\Form\FormError;

class RegisterController extends AbstractController
{
    private $emailService;

    private const EMAIL_TEMPLATE_ID = 5638161;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    // Methode pour l'inscription
    #[Route('/register', name: 'app_register', methods: ['get', 'post'])]
    public function index(Request $request, AgencyRepository $agencyRepository): Response
    {
        $agency = new Agency();

        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // On vérifie si le SIREN existe déjà en base, si oui on affiche une erreur
            $siren = $form->get('siren');
            $sirenData = $siren->getData();
            $agencyExists = $agencyRepository->findOneBy(['siren' => $sirenData]);
            if ($agencyExists) {
                $siren->addError(new FormError('Ce SIREN est déjà utilisé'));
                return $this->render('register/index.html.twig', [
                    'form' => $form->createView()
                ]);
            }

            // On stocke l'agence en session pour l'enregistrer avec l'utilisateur
            $request->getSession()->set('agency', serialize($agency));
            return $this->redirectToRoute('app_register_user');
          }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/register/user', name: 'app_register_user')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $agency = unserialize($request->getSession()->get('agency'));
        if (!$agency) {
            return $this->redirectToRoute('app_register');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($agency);
            $user->setAgency($agency);

            $request->getSession()->remove('agency');

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Envoie du mail
            $this->emailService->sendEmail(self::EMAIL_TEMPLATE_ID,$user->getEmail());

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('register/user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
