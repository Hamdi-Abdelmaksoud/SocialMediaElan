<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Notification;


class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
    #[Route('/post/{post}/comment', name: 'app_post_comment')]
    public function addComment(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) 
        {
            $commentText = $request->request->get('comment');
            $comment = new Comment();
            $comment->setText($commentText);
            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $entityManager->persist($comment);
            $entityManager->flush();
            $notif = New Notification();
            $notif->setSender($this->getUser());
            $notif->setReceiver($post->getAuthor());
            $notif->setType('comment');
            $notif->setLink($post->getId());
            $notif->setPost($post);
            $entityManager->persist($notif);
            $entityManager->flush();
            
            $this->addFlash('success', 'comment added successfully');
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    #[Route('/post/{post}/{comment}/edit', name: 'app_comment_edit')]
    public function edit(Post $post, Comment $comment, Request $request, EntityManagerInterface $entiyManager): Response
    {  
         if (!$this->getUser()){
        $currentUser = $this->getUser();
       return $this->redirectToRoute('app_login');
      
    } 
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $comment = $form->getData();

            $entiyManager->persist($comment);
            $entiyManager->flush();
            $this->addFlash('success', 'commentt updated successfully');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        return $this->render(
            'post/comment.html.twig',
            [
                'formComment' => $form
            ]
        );
    }
    #[Route('/post/{post}/{comment}/delete', name: 'app_comment_delete')]
    public function delete(Post $post, Comment $comment, Request $request, EntityManagerInterface $entiyManager): Response
    {
        $entiyManager->remove($comment);
        $entiyManager->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
