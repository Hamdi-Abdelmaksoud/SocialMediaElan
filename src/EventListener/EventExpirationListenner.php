<?php
namespace App\EventListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Entity\Post;

class EventExpirationListener
{
    #[AsDoctrineListener(post: Post::postLoad, priority: 0, connection: 'default')]
    public function postLoad(Post $post, LifecycleEventArgs $args): void
    {
        // Vérifiez si la date de fin de l'événement est dépassée
        if ($post->getAlertExpiration() < new \DateTime() && $post->getAlertExpiration() != NULL) {
            $entityManager = $args->getEntityManager();
            // Supprimez l'événement de la base de données
            $entityManager->remove($post);
            $entityManager->flush(); 
        }
    }
}