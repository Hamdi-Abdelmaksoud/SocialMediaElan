<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LikeController extends AbstractController
{

    #[Route('/like/{id}', name: 'app_like')]
  
    public function like(Post $post ,EntityManagerInterface $entityManager,Request $request):Response
        {
          // Obtient l'utilisateur courant
      $currentUser = $this->getUser();
       // Ajoute l'utilisateur courant à la liste des personnes ayant aimé cette publication
      $post->addLikedBy($currentUser);
      // Persiste les changements en base de données
      $entityManager->persist($post);
      $entityManager->flush();
              // Récupère l'URL de la page précédente (le referer) pour rediriger l'utilisateur.
      $notif = New Notification();
      $notif->setSender($currentUser);
      $notif->setReciver($post->getAuthor());
      $notif->setType('like');
      $notif->setLink($post->getId());
      $entityManager->persist($notif);
      $entityManager->flush();

      $referer = $request->headers->get('referer');
      return $this->redirect($referer);

    }
    #[Route('/unlike/{id}', name: 'app_unlike')]
  
    public function unlike(Post $post,EntityManagerInterface $entityManager,Request $request):Response
        {
      $currentUser = $this->getUser();
      $post->removeLikedBy($currentUser);
      $entityManager->persist($post);
      
      $entityManager->flush();
      $referer = $request->headers->get('referer');
            return $this->redirect($referer);
           }
    }
