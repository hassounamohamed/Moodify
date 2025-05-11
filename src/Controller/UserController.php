<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this ->render('user/index.html.twig',[
            'users' => $users,
        ]);
    
    }
// src/Controller/UserController.php

// ...

#[Route('/user/create', name: 'app_user_create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    $user = new User();
    $form = $this->createForm(UserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Hash the password
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );
        
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_index');
    }

    return $this->render('user/create.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
