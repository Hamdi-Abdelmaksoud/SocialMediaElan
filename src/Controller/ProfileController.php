<?php
namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
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
use App\Service\DateFormatter;
class ProfileController extends AbstractController
{
     private $dateFormatter;
    public function __construct(DateFormatter $dateFormatter)
    {
        $this->dateFormatter = $dateFormatter;
    }
    
    // ...
 
    #[Route('/profile/{user}', name: 'app_profile')]
    public function show(User $user,  DateFormatter $dateFormatter,PostRepository $postRepository,UserRepository $userRepository ,NotificationRepository $notificationRepository): Response
    {
        if($this->getUser())
        {    
           /** @var User $currentUser */
           $currentUser=$this->getUser(); 
           $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'follow'=>NULL,
            'followers'=>NULL,
            'posts' => $postRepository->findBy(['author' => $currentUser], ['created' => 'DESC']),
            'dateFormatter' => $dateFormatter,
            'events' => $postRepository->findby(["type" => "event"]),
            'sugges'=>$userRepository->findSuggestions($authors,$currentUser->getCity()),
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $user->getId(),
                    'is_read' => false
                ])

        ]);
    }
    else
    {
    return $this->redirectToRoute('app_login');
    }
    }
    #[Route('/profile/follow/{user}', name: 'app_profile_follow')]
    public function showFollow(User $user,  DateFormatter $dateFormatter,PostRepository $postRepository,UserRepository $userRepository ,NotificationRepository $notificationRepository): Response
    {
        if($this->getUser())
        {    
           /** @var User $currentUser */
           $currentUser=$this->getUser(); 
           $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'follow'=>$user->getFollows(),
            'followers'=>NULL,
            'posts' => NULL,
            'dateFormatter' => $dateFormatter,
            'events' => $postRepository->findby(["type" => "event"]),
            'sugges'=>$userRepository->findSuggestions($authors,$currentUser->getCity()),
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $user->getId(),
                    'is_read' => false
                ])

        ]);
    }
    else
    {
    return $this->redirectToRoute('app_login');
    }
    }
    #[Route('/profile/follower/{user}', name: 'app_profile_follower')]
    public function showFollowers(User $user,  DateFormatter $dateFormatter,PostRepository $postRepository,UserRepository $userRepository ,NotificationRepository $notificationRepository): Response
    {
        if($this->getUser())
        {    
           /** @var User $currentUser */
           $currentUser=$this->getUser(); 
           $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('profile/show.html.twig', [
            'user' => $user,
            'followers'=>$user->getFollowers(),
            'follow'=>NULL,
            'posts' => NULL,
            'dateFormatter' => $dateFormatter,
            'events' => $postRepository->findby(["type" => "event"]),
            'sugges'=>$userRepository->findSuggestions($authors,$currentUser->getCity()),
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $user->getId(),
                    'is_read' => false
                ])

        ]);
    }
    else
    {
    return $this->redirectToRoute('app_login');
    }
    }
    #[Route('/profile', name: 'app_myProfile')]
    public function myProfile(PostRepository $postRepository,DateFormatter $dateFormatter,UserRepository $userRepository ,NotificationRepository $notificationRepository): Response
    {
        if($this->getUser())
        {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
        return $this->render('profile/show.html.twig', [
            'user' => $currentUser,
            'follow'=>NULL,
            'followers'=>NULL,
            'posts' =>$postRepository->findBy(['author' => $currentUser], ['created' => 'DESC']),
            'sugges'=>$userRepository->findSuggestions($authors,$currentUser->getCity()),
            'events' => $postRepository->findby(["type" => "event"]),
            'dateFormatter' => $dateFormatter,
            'notification' => $notificationRepository->findBy(
                [
                    'receiver' => $currentUser->getId(),
                    'is_read' => false
                ])

        ]);}
        else{
            return $this->redirectToRoute('app_login');
        }
    }
    #[Route('/darkmode', name: 'app_profile_mode')]
    public function mode(EntityManagerInterface $entityManager,Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        if($user->isDarkMode())
        {
            
        $user->setDarkMode(false);
        }
        else
        {
            $user->setDarkMode(true);
        }
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
     #[Route('/profile/pic/edit', name: 'app_profile_pic_edit', priority: 1)]
     public function edit(Request $request, SluggerInterface $slugger, EntityManagerInterface $entiyManager): Response
     {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('add-pic',  $request->request->get('token'))) 
            {
             /** @var User $currentUser */
             $currentUser = $this->getUser();
            
             if ($request->files->has('pic')) {
                $pic = $request->files->get('pic');
             }
             else
             {
                $this->addFlash('error', 'no pic selected');
                $referer = $request->headers->get('referer');
                return $this->redirect($referer); 
             }   
                $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
                $maxFileSize = 5 * 1024 * 1024; // 5 MB    
                $fileExtension = $pic->guessExtension();
                if (!in_array(strtolower($fileExtension), $allowedFileTypes)) {
                    $this->addFlash('error', 'please choose a valid extension (jpeg,jpg,png,gif)');
                    $referer = $request->headers->get('referer');
                    return $this->redirect($referer); 
                }
            
                // Validate file size
                if ($pic->getSize() > $maxFileSize) {
                    $this->addFlash('error', 'the file size should be less tha, 5MB');
                    $referer = $request->headers->get('referer');
                    return $this->redirect($referer); 
                }         
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
                 $currentUser->setImage($newFilename);
                $entiyManager->persist($currentUser);
                 $entiyManager->flush();
                 $referer = $request->headers->get('referer');
                 return $this->redirect($referer);
             
         }
        }
        $this->addFlash('error', 'invalid CSRF');
         $referer = $request->headers->get('referer');
         return $this->redirect($referer);
     }


     
     
     
     #[Route('/profile/edite', name: 'app_profile_edite', priority: 1)]
     public function editProfile(
         Request $request,
         UserPasswordHasherInterface $passwordHasher,
         PostRepository $postRepository,
         UserRepository $userRepository,
         NotificationRepository $notificationRepository,
         EntityManagerInterface $entityManager,
         DateFormatter $dateFormatter
         
         ): Response {
             /** @var User $currentUser */
             $currentUser = $this->getUser();
             $authors = array_merge($currentUser->getFollows()->toArray(), [$currentUser]);
             $form = $this->createForm(UserType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $password = $form->get('password')->getData();
            
            // Vérifiez le mot de passe actuel
            if ($passwordHasher->isPasswordValid($currentUser, $password)) {
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
                    
                    'notification' => $notificationRepository->findBy(
                        [
                            'receiver' => $currentUser->getId(),
                            'is_read' => false
                            ])
                        ]);
                    }
                }
                
                return $this->render('profile/edit.html.twig', [
                    'form' => $form->createView(),
                    'dateFormatter' => $dateFormatter,
                    'events' => $postRepository->findby(["type" => "event"]),
                    'sugges'=>$userRepository->findSuggestions($authors,$currentUser->getCity()),
                    'notification' =>  $notificationRepository->findBy(
                        [
                            'receiver' => $currentUser->getId(),
                            'is_read' => false
                            ])
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
    #[Route('/error/', name: 'app_error')]
    public function error(): Response
    {
       return $this->render('profile/error.html.twig');
    }
}

