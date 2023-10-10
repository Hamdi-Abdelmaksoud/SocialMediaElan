<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Null_;

class NotificationController extends AbstractController
{
    #[Route('/notification', name: 'app_notification')]
    public function index( NotificationRepository $notificationRepository,EntityManagerInterface $entiyManager,PostRepository $postRepository): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        $notifs= $notificationRepository->findnotification($currentUser->getId());
        foreach($notifs as $notif)
        {
            $notif->setIsRead('1');
            $entiyManager->persist($notif);
            $entiyManager->flush();
        }
        return $this->render('notification/notification.html.twig', [
            "notifications" => $notificationRepository->findby(["reciver"=>$currentUser->getId()]),
            "notification"=>Null,
            'events' => $postRepository->findby(["type"=>"event"]),
        ]);
    }
}
