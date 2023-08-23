<?php

namespace App\Controller;

use App\Entity\Post;

use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(): Response
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
    #[Route('/post/add', name: 'app_post_add',priority:1)]
    public function add(Request $request, EntityManagerInterface $entiyManager): Response
    {
        $post= new Post();
        $form = $this->createForm(PostType::class, $post);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setAuthor($this->getUser());
            $entiyManager->persist($post);
            $entiyManager->flush();
            
            
            $this->addFlash('success', 'Post added successfully');
            return $this->redirectToRoute('app_post');
        }
        return $this->render(
            'post/add.html.twig',
            [
                'form' => $form
            ]
        );
    }
}
