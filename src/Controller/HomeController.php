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
    public function homePosts(PostRepository $postRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
     if (!$currentUser){
        return $this->redirectToRoute('app_login');
       
     }
     else{
        $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('home/index.html.twig', [
            'posts'=>$postRepository->findHomePosts($authors),
            'events' => $postRepository->findby(["type"=>"event"]),
                 
        ]); 
     }
    }
}
