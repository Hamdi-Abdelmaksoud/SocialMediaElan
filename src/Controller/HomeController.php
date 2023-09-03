<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function followsPosts(PostRepository $postRepository, UserRepository $userRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getFollows() !=null) 
        {
            return $this->render('home/index.html.twig', [
                'posts' => $postRepository->findPostsFromFollows(
                    $currentUser->getFollows()
                ),
            ]);
        } else 
        {
            return $this->render('home/index.html.twig', [
                'friends' => $userRepository->findAll(),

            ]);
        }
    }
}
