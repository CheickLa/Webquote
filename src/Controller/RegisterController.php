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
use App\Security\AppAuthenticator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
class RegisterController extends AbstractController
{
    // Methode pour l'inscription
    #[Route('/register', name: 'app_register', methods: ['get', 'post'])]
    public function index(Request $request): Response
    {
        $agency = new Agency();

        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
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
