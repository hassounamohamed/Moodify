<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'current_menu' => 'profile'
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/change-password', name: 'app_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Vérifier le mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'Mot de passe actuel incorrect');
                return $this->redirectToRoute('app_change_password');
            }
            
            // Encoder et sauvegarder le nouveau mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['newPassword']
                )
            );
            
            $em->flush();
            
            $this->addFlash('success', 'Mot de passe changé avec succès');
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}