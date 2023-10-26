<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\NotificationRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
 
    #[Route('/message/{id}', name: 'app_message_send')]
    public function send(Request $request, User $recipient,
     NotificationRepository $notificationRepository, 
    EntityManagerInterface $entiyManager, MessageRepository $messageRepository,
     PostRepository $postRepository): Response
    {
        if($this->getUser())
        {       
             /** @var User $currentUser */
            $currentUser = $this->getUser();
        //création d'une nouvelle instance de classe message
        $message = new Message;
        /*  on envoie l'objet message stocker 
        dans la variable $message vide au formulaire de type message*/
        $form = $this->createForm(MessageType::class, $message);
        //récuppération des donées enovyées de not formulaire
        $form->handleRequest($request);
        //si les donées sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();//on affecte les données du form dans notre objet 
            $now = new \DateTime();
            $message->setSender($currentUser);//l'utilisateur connecté en session
            $message->setRecipient($recipient);//l'utilisateur à qui on envoie le message
            $message->setCreated($now);
            $entiyManager->persist($message);//signaler à doctrine les changements 
            $entiyManager->flush();//synchorniser avec la base de données 
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);//pour rester sur la meme page
        }
        //si le formulaire pas encore submitter 
        return $this->render('message/index.html.twig', [
            'form' => $form->createView(),//formulaire d'ajout message
            'discussion' => $messageRepository->findDiscussion($recipient, $this->getUser()),
            //l'ancienne discussion
            'recipient' => $recipient,//à qui on veut envoyer le message on le réccupére de l'url
            'events' => $postRepository->findby(["type" => "event"]),//les evenelents pour le sidebar
            'notification' => $notificationRepository
            ->findBy(
                [
                    'receiver' => $currentUser->getId(),
                    'is_read' => false
                ])
        //les notifs pour le sidebar
        ]);}
        else {
            $this->redirectToRoute('app_login');
        }
    }
}
