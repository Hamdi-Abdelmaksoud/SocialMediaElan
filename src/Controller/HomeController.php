<?php

namespace App\Controller;

use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/events', name: 'app_events')]
    public function events(PostRepository $postRepository,NotificationRepository $notificationRepository): Response
    {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            if (!$currentUser){
               return $this->redirectToRoute('app_login');
              
            }
        return $this->render('home/events.html.twig',
        [
        'events'=>$postRepository->findBy(["type"=>"event"],["created"=>"DESC"]),
        'notification' => $notificationRepository->findBy(
            [
                'receiver' => $currentUser->getId(),
                'is_read' => false
            ])
        ]);
    }
    #[Route('/home', name: 'app_home')]
    public function homePosts(PostRepository $postRepository,NotificationRepository $notificationRepository): Response
    {
       
        /** @var User $currentUser */
        $currentUser = $this->getUser();
     if (!$currentUser){
        return $this->redirectToRoute('app_login');
       
     }
     else{
        $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('home/index.html.twig', [
            'posts'=>$postRepository->findBy(['author' => $authors], ['created' => 'DESC']),
            'events' => $postRepository->findby(["type"=>"event"]),
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $currentUser->getId(),
                    'is_read' => false
                ])
                 
        ]); 
     }
    }
}
