<?php

namespace App\Controller;

use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\HttpFoundation\Request;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index(NotificationRepository $notificationRepository, EntityManagerInterface $entiyManager, PostRepository $postRepository): Response
    {
        if($this->getUser())
        {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        return $this->render('notification/notification.html.twig', [

            "notification" => $notificationRepository->findBy(
                [
                    'receiver' => $currentUser->getId(),
                    'is_read' => false
                ],
                ["created" => "DESC"]
            ),
            "notifications" => $notificationRepository->findBy(
                [
                    'receiver' => $currentUser->getID(),
                    'is_read' => true
                ],
                ["created" => "DESC"]
            ),


            'events' => $postRepository->findby(["type" => "event"]),
        ]);}
        else{
            return $this->redirectToRoute('app_login');
        }
    }
    #[Route('/notification/{notif}', name: 'app_notification_read')]
    public function read(Request $request, Notification $notif, NotificationRepository $notificationRepository, EntityManagerInterface $entiyManager, PostRepository $postRepository): Response
    {
        $notif->setIsRead(true);
        $entiyManager->persist($notif);
        $entiyManager->flush();
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
