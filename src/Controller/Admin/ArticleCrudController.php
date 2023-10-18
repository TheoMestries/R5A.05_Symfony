<?php
namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Titre'),
            TextareaField::new('content', 'Contenu'),
            DateTimeField::new('publicationDate', 'Date de publication')->setFormat('d/M/Y H:mm'),
            TextField::new('scpNumber', 'Numéro SCP'),
            TextField::new('status', 'Statut'),
            AssociationField::new('author', 'Auteur')
                ->setCrudController(UserCrudController::class)
                ->setFormTypeOption('choice_label', function ($user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                })
        ];
    }
}
