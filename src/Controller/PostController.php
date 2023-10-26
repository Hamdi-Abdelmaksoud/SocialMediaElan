<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Entity\PostPics;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(PostRepository $posts): Response
    {
        return $this->render('post/_allposts.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }
  
    #[Security("is_granted('ROLE_USER')")]
    #[Route('/post/addtwo', name: 'app_post_addtwo')]
    public function addtwo(Request $request ,SluggerInterface $slugger, EntityManagerInterface $entiyManager, $type = 'post'): Response
    {
            // je vérifie le token
            $submittedToken = $request->request->get('token');
    if ($this->isCsrfTokenValid('add-post', $submittedToken))
    {
        if ($request->isMethod('POST'))
        {
            if($request->request->get('text') !=NULL)
            {
                $postText = $request->request->get('text');
                    if($request->request->has('type'))
                    {
                        $type = $request->request->get('type');
                    }
                $post = new Post();
                $post->setType($type);
                $post->setText($postText);
                $post->setAuthor($this->getUser());
                //on récupère les images
                $pics = $request->files->get('pics');
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
            if ($pics)
            {
                foreach ($pics as $pic)
                {
                    $originalFilename = pathinfo($pic->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $pic->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $pic->move(
                            $this->getParameter('brochures_directory'), //brochures_directory dans parametres  services.yaml
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $postPics = new PostPics();
                    $postPics->setPic($newFilename);
                    $postPics->setPost($post);
                    $post->addPic($postPics);
                }
            }
            $entiyManager->persist($post);
            $entiyManager->flush();
            $this->addFlash('success', 'Post added successfully');
            return $this->redirectToRoute('app_home');
        } 
    }
    $this->addFlash('error', 'Text cannot be empty. Please enter some text.');
    $referer = $request->headers->get('referer');
    return $this->redirect($referer);
    }
    $referer = $request->headers->get('referer');
    return $this->redirect($referer);
}

    // #[Route('/post/add', name: 'app_post_add')]
    // public function add(Request $request, SluggerInterface $slugger, EntityManagerInterface $entiyManager): Response
    // {
    //     $post = new Post();
    //     $form = $this->createForm(PostType::class, $post);
    //     $form->handleRequest($request);
    //     if ($form->isSubmitted() && $form->isValid()) {
    //         //on récupère les images
    //         $pics = $form->get('pic')->getData();
    //         // this condition is needed because the 'brochure' field is not required
    //         // so the PDF file must be processed only when a file is uploaded
    //         if ($pics) {
    //             foreach ($pics as $pic) {
    //                 $originalFilename = pathinfo($pic->getClientOriginalName(), PATHINFO_FILENAME);
    //                 // this is needed to safely include the file name as part of the URL
    //                 $safeFilename = $slugger->slug($originalFilename);
    //                 $newFilename = $safeFilename . '-' . uniqid() . '.' . $pic->guessExtension();
    //                 // Move the file to the directory where brochures are stored
    //                 try {
    //                     $pic->move(
    //                         $this->getParameter('brochures_directory'), //brochures_directory dans parametres  services.yaml
    //                         $newFilename
    //                     );
    //                 } catch (FileException $e) {
    //                     // ... handle exception if something happens during file upload
    //                 }
    //                 $postPics = new PostPics();
    //                 $postPics->setPic($newFilename);
    //                 $postPics->setPost($post);
    //                 $post->addPic($postPics);
    //             }
    //         }
    //         $post = $form->getData();
    //         $post->setAuthor($this->getUser());
    //         $entiyManager->persist($post);
    //         $entiyManager->flush();
    //         $this->addFlash('success', 'Post added successfully');
    //         return $this->redirectToRoute('app_home');
    //     }
    //     return $this->render(
    //         'post/add.html.twig',
    //         [
    //             'form' => $form
    //         ]
    //     );
    // }

    #[Route('/post/{post}', name: 'app_post_show')]
    public function showOne(Post $post): Response
    {

        return $this->render('post/showpost.html.twig', [
            'post' => $post
        ]);
    }
   
    // #[Route('/post/{post}/edit', name: 'app_post_edit')]
    // public function edit(Post $post = null, Request $request, EntityManagerInterface $entiyManager): Response
    // {
    //     if($post) {
    //         if($this->getUser() && $post->getAuthor() == $this->getUser()) {
    //             $form = $this->createForm(PostType::class, $post);
    //             $form->handleRequest($request);
    //             if ($form->isSubmitted() && $form->isValid()) {
    //                 $post = $form->getData();
    //                 $entiyManager->persist($post);
    //                 $entiyManager->flush();
    //                 $this->addFlash('success', 'Post updated successfully');
    //                 $referer = $request->headers->get('referer');
    //                 return $this->redirect($referer);
    //             }
    //             return $this->render(
    //                 'post/add.html.twig',
    //                 [
    //                     'form' => $form
    //                 ]
    //             );
    //         }else {
    //             $referer = $request->headers->get('referer');
    //             return $this->redirect($referer);
    //         }
    //     } else {
    //         $referer = $request->headers->get('referer');
    //         return $this->redirect($referer);
    //     }
    // }
    
    #[Route('/post/{post}/delete', name: 'app_post_delete')]
    public function delete(Post $post = null, EntityManagerInterface $entityManager,Request $request): Response
    {
        if($post)
        {
            if($this->getUser() && $post->getAuthor() == $this->getUser())
            {
                $entityManager->remove($post);
                $entityManager->flush();
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
            else {
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
        }    
        else 
        {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }  
    
    }
    #[Route('/post/{post}/edit', name: 'app_post_edit')]
    public function edit(Post $post = null,NotificationRepository $notificationRepository,PostRepository $postRepository, Request $request, EntityManagerInterface $entiyManager): Response
    {
        if($post) {
              
            if($this->getUser() && $post->getAuthor() == $this->getUser()) {
                /** @var User $currentUser */
                $currentUser=$this->getUser();
                $form = $this->createForm(PostType::class, $post);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $post = $form->getData();
                    $entiyManager->persist($post);
                    $entiyManager->flush();
                    $this->addFlash('success', 'Post updated successfully');
                    $referer = $request->headers->get('referer');
                    return $this->redirect($referer);
                }
                return $this->render(
                    'post/showpost.html.twig',
                    [
                        'form' => $form,
                        'post'=>$post,  
                          'events' => $postRepository->findby(["type"=>"event"]),
                        'notification' => $notificationRepository->findBy(
                            [
                                'receiver' => $currentUser->getId(),
                                'is_read' => false
                            ])
                        
                    ]
                );
            }else {
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
        } else {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
    }
}
