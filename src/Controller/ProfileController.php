<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{user}', name: 'app_profile')]
    public function show(User $user, PostRepository $postRepository): Response
    {

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'posts' => $user->getPosts(),
            'events' => $postRepository->findby(["type"=>"event"]),

        ]);
    }
    #[Route('/profile/{user}/follows', name: 'app_profile_follows')]
    public function follows(User $user): Response
    {
        return $this->render('profile/follows.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/profile/{user}/followers', name: 'app_profile_followers')]
    public function followers(User $user): Response
    {
        return $this->render('profile/followers.html.twig', [
            'user' => $user
        ]);
    }
    // #[Route('/profile/edit', name: 'app_profile_edit', priority: 1)]
    // public function edit(Request $request, SluggerInterface $slugger, EntityManagerInterface $entiyManager): Response
    // {


    //     if ($request->isMethod('POST')) {
    //         /** @var User $currentUser */
    //         $currentUser = $this->getUser();
    //         $pic = $request->files->get('pic');
    //         if ($pic)
    //          {
    //             $originalFilename = pathinfo($pic->getClientOriginalName(), PATHINFO_FILENAME);
    //             // this is needed to safely include the file name as part of the URL
    //             $safeFilename = $slugger->slug($originalFilename);
    //             $newFilename = $safeFilename . '-' . uniqid() . '.' . $pic->guessExtension();
    //             // Move the file to the directory where brochures are stored
    //             try {
    //                 $pic->move(
    //                     $this->getParameter('brochures_directory'), //brochures_directory dans parametres  services.yaml
    //                     $newFilename
    //                 );
    //             } catch (FileException $e) {
    //                 // ... handle exception if something happens during file upload
    //             }
    //             $currentUser->setImage($newFilename);
    //             $entiyManager->persist($currentUser);
    //             $entiyManager->flush();
    //             return $this->render(
    //                 'profile/show.html.twig',
    //                 [
    //                     'user' => $currentUser,
    //                     'posts' => $currentUser->getPosts()
    //                 ]
    //             );
    //         }
    //     }
    //     return $this->render(
    //         '/profile/edit.html.twig'
    //     );
    // }
    #[Route('/profile/{user}/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request, UserPasswordHasherInterface $passwordHasher, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
         
            $password = $form->get('password')->getData();
    
            // Vérifiez le mot de passe actuel
            if ($passwordHasher->isPasswordValid($user, $password)) {
                // Mot de passe correct, effectuez les modifications
                $user = $form->getData();
    
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'profile edited successfully');
                $referer = $request->headers->get('referer');
                return $this->redirect($referer);
            } 
            else{
                $this->addFlash('error', 'wrong password');
                return $this->render('profile/edit.html.twig', [
                    'form' => $form->createView(),
                ]);

            }
        }
    
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}