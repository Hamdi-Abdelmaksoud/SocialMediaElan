<?php
namespace App\EventListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\PostRemoveEventArgs;
use App\Entity\Post;

class PostAlertListener
{
    #[AsDoctrineListener(event: Events::postRemove, priority: 5, connection: 'default')]
    public function postRemove(PostRemoveEventArgs $args): void  // Utilisez postRemove ici
    {
       
        $post = $args->getObject();
        if ($post instanceof Post) {
            if ($post->getAlertExpiration() < new \DateTime()) {
                $entityManager = $args->getObjectManager();
                $entityManager->remove($post);
                $entityManager->flush();
            }
        }
    }
}