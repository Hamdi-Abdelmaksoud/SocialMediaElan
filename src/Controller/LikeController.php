<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LikeController extends AbstractController
{

  #[Route('/like/{id}', name: 'app_like')]

  public function like(Post $post, EntityManagerInterface $entityManager, Request $request): Response
  {
    if ($this->getUser() && $post != null) {
      // Obtient l'utilisateur courant
      $currentUser = $this->getUser();
      // Ajoute l'utilisateur courant à la liste des personnes ayant aimé cette publication
      $post->addLikedBy($currentUser);
      // Persiste les changements en base de données
      $entityManager->persist($post);
      $entityManager->flush();
      // Récupère l'URL de la page précédente (le referer) pour rediriger l'utilisateur.
      $notif = new Notification();
      $notif->setSender($currentUser);
      $notif->setReceiver($post->getAuthor());
      $notif->setType('like');
      $notif->setLink($post->getId());
      $notif->setPost($post);
      $entityManager->persist($notif);
      $entityManager->flush();

      $referer = $request->headers->get('referer');
      return $this->redirect($referer);
    } else
      $referer = $request->headers->get('referer');
    return $this->redirect($referer);
  }
  #[Route('/unlike/{id}', name: 'app_unlike')]
  public function unlike(Post $post, EntityManagerInterface $entityManager,PostRepository $postRepository ,Request $request): Response
  {
    
    //on verifie si l'utilisateur est connecté et la poste existe
    if ($this->getUser() && $postRepository->find($post)!== null)
    {
      $currentUser = $this->getUser();
      //on vérifie si l'utilisateur a liké ce poste
      if($post->getLikedBy()->contains($currentUser))
      {
        $post->removeLikedBy($currentUser);
        $entityManager->persist($post);
        $entityManager->flush();
      }
      $referer = $request->headers->get('referer');
      return $this->redirect($referer);
    }
     else 
    {
      $referer = $request->headers->get('referer');
      return $this->redirect($referer);
    }
  }
}
