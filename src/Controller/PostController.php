<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Form\PostType;
use App\Entity\PostPics;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Service\DateFormatter;


class PostController extends AbstractController
{
    
    private $dateFormatter;

    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }
    
    #[Route('/post', name: 'app_post')]
    public function index(PostRepository $posts): Response
    {
        return $this->render('post/_allposts.html.twig', [
            'posts' => $posts->findAll(),
        ]);
    }

    //     #[Security("is_granted('ROLE_USER')")]
    //     #[Route('/post/addtwo', name: 'app_post_addtwo')]
    //     public function addtwo(Request $request ,SluggerInterface $slugger, EntityManagerInterface $entiyManager, $type = 'post'): Response
    //     {
    //             // je vérifie le token
    //             $submittedToken = $request->request->get('token');
    //     if ($this->isCsrfTokenValid('add-post', $submittedToken))
    //     {
    //         if ($request->isMethod('POST'))
    //         {
    //             if($request->request->get('text') !=NULL)
    //             {
    //                 $postText = $request->request->get('text');
    //                 $post = new Post();
    //                 $post->setAlertExpiration(null);
    //                 if($request->request->has('expiration'))
    //                 {
    //                     $expirationString = $request->request->get('expiration');

    // // Convertissez la chaîne en un objet DateTime
    // $expirationDate = DateTime::createFromFormat('Y-m-d', $expirationString);
    //                     $post->setAlertExpiration($expirationDate);
    //                     $type='event';
    //                 }

    //                 $post->setType($type);
    //                 $post->setText($postText);
    //                 $post->setAuthor($this->getUser());
    //                 //on récupère les images
    //                 $pics = $request->files->get('pics');
    //                 // this condition is needed because the 'brochure' field is not required
    //                 // so the PDF file must be processed only when a file is uploaded
    //             if ($pics)
    //             {
    //                 foreach ($pics as $pic)
    //                 {
    //                     $originalFilename = pathinfo($pic->getClientOriginalName(), PATHINFO_FILENAME);
    //                     // this is needed to safely include the file name as part of the URL
    //                     $safeFilename = $slugger->slug($originalFilename);
    //                     $newFilename = $safeFilename . '-' . uniqid() . '.' . $pic->guessExtension();
    //                     // Move the file to the directory where brochures are stored
    //                     try {
    //                         $pic->move(
    //                             $this->getParameter('brochures_directory'), //brochures_directory dans parametres  services.yaml
    //                             $newFilename
    //                         );
    //                     } catch (FileException $e) {
    //                         // ... handle exception if something happens during file upload
    //                     }
    //                     $postPics = new PostPics();
    //                     $postPics->setPic($newFilename);
    //                     $postPics->setPost($post);
    //                     $post->addPic($postPics);
    //                 }
    //             }
    //             $entiyManager->persist($post);
    //             $entiyManager->flush();
    //             $this->addFlash('success', 'Post added successfully');
    //             return $this->redirectToRoute('app_home');
    //         } 
    //     }
    //     $this->addFlash('error', 'Text cannot be empty. Please enter some text.');
    //     $referer = $request->headers->get('referer');
    //     return $this->redirect($referer);
    //     }
    //     $referer = $request->headers->get('referer');
    //     return $this->redirect($referer);
    // }
    #[Security("is_granted('ROLE_USER')")]
    #[Route('/post/addtwo', name: 'app_post_addtwo')]
    public function addtwo(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager, $type = 'post'): Response
    {
        // Vérifiez si la requête est de type POST
        if ($request->isMethod('POST')) {
            // Vérifiez si le jeton CSRF est valide
            $submittedToken = $request->request->get('token');
            if ($this->isCsrfTokenValid('add-post', $submittedToken)) {
                // Vérifiez si le champ 'text' n'est pas vide
                $postText = $request->request->get('text');
                if ($postText !== null && $postText !== '') {
                    $post = new Post();
                    $post->setAlertExpiration(null);

                    // Vérifiez s'il y a une date d'expiration dans la requête
                    if ($request->request->has('expiration')) {
                        $expirationString = $request->request->get('expiration');
                        // Convertissez la chaîne en un objet DateTime
                        $expirationDate = DateTime::createFromFormat('Y-m-d', $expirationString);
                        if ($expirationDate instanceof \DateTimeInterface) {
                            $post->setAlertExpiration($expirationDate);
                            $type = 'event';
                        }
                    }

                    $post->setType($type);
                    $post->setText($postText);
                    $post->setAuthor($this->getUser());

                    //on récupère les images
                    $pics = $request->files->get('pics');
                    // this condition is needed because the 'brochure' field is not required
                    // so the PDF file must be processed only when a file is uploaded
                    if ($pics) {
                        foreach ($pics as $pic) {
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

                    // Persistez l'objet post en base de données
                    $entityManager->persist($post);
                    $entityManager->flush();

                    $this->addFlash('success', 'Post added successfully');
                    return $this->redirectToRoute('app_home');
                } else {
                    $this->addFlash('error', 'Text cannot be empty. Please enter some text.');
                }
            } else {
                $this->addFlash('error', 'Invalid CSRF token.');
            }
        } else {
            $this->addFlash('error', 'Invalid request method.');
        }

        // Redirigez l'utilisateur vers la page précédente en cas d'erreur
        $referer = $request->headers->get('referera');
        return $this->redirect($referer);
    }
    #[Route('repost/{post}', name: 'app_repost')]
    public function repost(Post $post = Null, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()) {
            if ($post != NUll) {
                /** @var User $currentUser */
                $currentUser = $this->getUser();
                $newPost = new Post();
                $newPost->setText($post->getText());
                $newPost->setAuthor($currentUser);
                $newPost->setAlertExpiration(null);
                $type = 'post';
                $newPost->setType($type);
                if ($post->getPics() != null) {
                    foreach ($post->getPics() as $pic) {
                        $newPic = new PostPics;
                        $newPic->setPic($pic->getPIc());
                        $newPic->setPost($newPost);
                        $newPost->addPic($newPic);
                    }
                }
                // Persistez l'objet post en base de données
                $entityManager->persist($newPost);
                $entityManager->flush();
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            } else {
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
        } else {
            return $this->redirectToRoute('app_login');
        }
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

    #[Route('/post/{post}', name: 'app_show_post')]
    public function showOne(Post $post,DateFormatter $dateFormatter, UserRepository $userRepository, PostRepository $postRepository, NotificationRepository $notificationRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('post/showpost.html.twig', [
            'post' => $post,  'events' => $postRepository->findby(["type" => "event"]),
            'date'=>$dateFormatter->format($post->getCreated()),
            'sugges' => $userRepository->findSuggestions($authors, $currentUser->getCity()),
            'dateFormatter' => $dateFormatter,
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $currentUser->getId(),
                    'is_read' => false
                ]
            )
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
    public function delete(Post $post = null, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($post) {
            if ($this->getUser() && $post->getAuthor() == $this->getUser()) {
                $entityManager->remove($post);
                $entityManager->flush();
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            } else {
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
        } else {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
    }
    #[Route('/post/{post}/edit', name: 'app_post_edit')]
    public function edit(Post $post = null, DateFormatter $dateFormatter,UserRepository $userRepository,NotificationRepository $notificationRepository, PostRepository $postRepository, Request $request, EntityManagerInterface $entiyManager): Response
    {
        if ($post) {

            if ($this->getUser() && $post->getAuthor() == $this->getUser()) {
                /** @var User $currentUser */
                $currentUser = $this->getUser();
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
                $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
                return $this->render(
                    'post/showpost.html.twig',
                    [
                        'form' => $form,
                        'post' => $post,
                        'sugges' => $userRepository->findSuggestions($authors, $currentUser->getCity()),
                        'date'=>$dateFormatter->format($post->getCreated()),
                        'dateFormatter' => $dateFormatter,
                        'events' => $postRepository->findby(["type" => "event"]),
                        'notification' => $notificationRepository->findBy(
                            [
                                'receiver' => $currentUser->getId(),
                                'is_read' => false
                            ]
                        )

                    ]
                );
            } else {
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            }
        } else {
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
    }
}
