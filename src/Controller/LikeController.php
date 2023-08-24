<?php

namespace App\Controller;

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
  
    public function like(Post $post,EntityManagerInterface $entityManager,Request $request):Response
        {
      $currentUser = $this->getUser();
      $post->addLikedBy($currentUser);
      $entityManager->persist($post);
      
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
