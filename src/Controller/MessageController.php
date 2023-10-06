<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    // #[Route('/message', name: 'app_message')]
    //  public function index(): Response
    //  {

    //    return $this->render('message/index.html.twig', [
    //          'controller_name' => 'MessageController',
    //     ]);
    //  }
     #[Route('/message/{id}', name: 'app_message_send')]
    public function send(Request $request, User $recipient, EntityManagerInterface $entiyManager,MessageRepository $messageRepository,PostRepository $postRepository): Response
     {
         $message = new Message;

         $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
             $message = $form->getData();
             $now = new \DateTime();

         $message->setSender($this->getUser());
             $message->setRecipient($recipient);
             $message->setCreated($now);
            $entiyManager->persist($message);
            $entiyManager->flush();

             $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
        return $this->render('message/index.html.twig', [
             'form' => $form->createView(),
             'discussion' => $messageRepository->findDiscussion($recipient,$this->getUser()),
        'recipient' => $recipient,
        'events' => $postRepository->findby(["type"=>"event"])
         ]);
     }
     
 }
