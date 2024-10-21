<?php

// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Set default role
            $user->setRoles(['ROLE_USER']);

            // Save the user to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Optionally add a flash message or redirect
            $this->addFlash('success', 'Registration successful!');

            return $this->redirectToRoute('app_register'); // or any other route
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
