<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{  
    #[Route('/profile/{user}', name: 'app_profile')]
public function editProfile(User $user,PostRepository $postRepository): Response
{

    return $this->render('profile/show.html.twig', [
        'user' => $user,
        'posts'=>$user->getPosts(),
        
    ]);
}
#[Route('/profile/{user}/follows', name: 'app_profile_follows')]
public function follows(User $user): Response
{
    return $this->render('profile/follows.html.twig', [
        'user' => $user
    ]);
}

#[Route('/profile/{user}/followers', name: 'app_profile_followers')]
public function followers(User $user): Response
{
    return $this->render('profile/followers.html.twig', [
        'user' => $user
    ]);
}

}
