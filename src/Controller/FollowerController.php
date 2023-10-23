<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class FollowerController extends AbstractController
{
    #[Route('/follow/{id}', name: 'app_follow')]
    public function follow(User $userToFollow,ManagerRegistry $managerRegistry,EntityManagerInterface $entityManager,Request $request): Response
    {
         /** @var User $currentUser */
         $currentUser = $this->getUser();
         // Vérification pour s'assurer que l'utilisateur courant ne se suit pas lui-même.
         if ($userToFollow->getId() != $currentUser->getId()) {
             $currentUser->follow($userToFollow);
             // Persiste les changements en base de données.
             $managerRegistry->getManager()->flush();
         }
         $notif = New Notification();
         $notif->setSender($currentUser);
         $notif->setReceiver($userToFollow);
         $notif->setType('follow');
         $notif->setLink($currentUser->getId());
         $entityManager->persist($notif);
         $entityManager->flush();
   
          // Récupère l'URL de la page précédente (le referer) pour rediriger l'utilisateur.
         $referer = $request->headers->get('referer');
         return $this->redirect($referer);
    }
    #[Route('/unfollow/{id}', name: 'app_unfollow')]
    public function unfollow(User $userToUnfollow,ManagerRegistry $managerRegistry,Request $request): Response
    {
           /** @var User $currentUser */
           $currentUser = $this->getUser();

           $currentUser->unfollow($userToUnfollow);
           $managerRegistry->getManager()->flush();
   
   
           $referer = $request->headers->get('referer');
           return $this->redirect($referer);
       }
    }

