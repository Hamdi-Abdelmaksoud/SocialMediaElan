<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{  
    #[Route('/profile/{user}', name: 'app_profile')]
public function editProfile(User $user,PostRepository $postRepository): Response
{

    return $this->render('profile/index.html.twig', [
        'user' => $user,
        
    ]);
}
}
