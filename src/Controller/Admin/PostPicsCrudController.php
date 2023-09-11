<?php

namespace App\Controller\Admin;

use App\Entity\PostPics;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class PostPicsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PostPics::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        // Supprime l'action Edit
        $actions->disable(Action::EDIT);
        $actions->disable(Action::NEW);

        return $actions;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
           ImageField::new('pic')->setBasePath('uploads/userPic/'),
           
        ];
    }
    
}
