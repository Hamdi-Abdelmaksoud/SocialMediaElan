<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/{user}', name: 'app_profile')]
    public function show(User $user, PostRepository $postRepository, NotificationRepository $notificationRepository): Response
    {

        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'posts' => $user->getPosts(),
            'events' => $postRepository->findby(["type" => "event"]),
            'notification' => $notificationRepository->findnotification($user->getId())

        ]);
    }
    #[Route('/profile', name: 'app_myProfile')]
    public function myProfile(PostRepository $postRepository, NotificationRepository $notificationRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'posts' => $user->getPosts(),
            'events' => $postRepository->findby(["type" => "event"]),
            'notification' => $notificationRepository->findnotification($user->getId())

        ]);
    }
    #[Route('/profile', name: 'app_profile_mode')]
    public function mode(EntityManagerInterface $entityManager,Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setDarkMode(1);
        $entityManager->persist($user);
        $entityManager->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
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

    #[Route('/profile/edit', name: 'app_profile_edit', priority: 1)]
    public function editProfile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        PostRepository $postRepository,
        NotificationRepository $notificationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();
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
            } else {
                $this->addFlash('error', 'wrong password');
                return $this->render('profile/edit.html.twig', [
                    'form' => $form->createView(),
                    'events' => $postRepository->findby(["type" => "event"]),
                    'notification' => $notificationRepository->findnotification($user->getId())
                ]);
            }
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'events' => $postRepository->findby(["type" => "event"]),
            'notification' => $notificationRepository->findnotification($user->getId())
        ]);
    }
    #[Route('/profile/delete', name: 'app_profile_delete', priority: 1)]
    public function delete(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); //On prend l'utilisateur actuel 
        $entityManager->remove($user);
        $entityManager->flush();
        return  $this->redirectToRoute('app_register');
    }
    // public function setModePrefere()
    // {
    //     // Définir le mode préféré de l'utilisateur
    //     $modePrefere = 'clair';

    //     // Créer un cookie avec un nom distinctif en ajoutant un préfixe
    //     $cookie = new Cookie('monsite_mode_prefere', $modePrefere, time() + 3600 * 24 * 365); // expire après 1 an

    //     // Ajouter le cookie à la réponse
    //     $response = new Response();
    //     $response->headers->setCookie($cookie);
    //     $response->send();

    //     // ... Faire quelque chose d'autre après avoir défini le cookie

    //     return $this->redirectToRoute('nom_de_votre_route');
    // }
}
