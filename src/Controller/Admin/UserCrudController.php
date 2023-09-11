<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
     return $crud
            ->setPaginatorPageSize(15); 
        
    }
    public function configureActions(Actions $actions): Actions
    {
        // Supprime l'action new user
      
        $actions->disable(Action::NEW);

        return $actions;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('firstName')->hideOnForm(),
            TextField::new('lastName')->hideOnForm(),
            TextField::new('email')->hideOnForm(),
            ImageField::new('image')->setBasePath('uploads/userPic/')->hideOnForm(),
            ArrayField::new('roles')
            // ->setChoices([
            //     'Admin' => 'ROLE_ADMIN',
            //     'Utilisateur' => 'ROLE_USER'
            // ])
            
         
            
        ];
    }
    
}
