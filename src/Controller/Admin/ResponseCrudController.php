<?php
namespace App\Controller\Admin;

use App\Entity\Response;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ResponseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Response::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextareaField::new('content', 'Content'),
            DateTimeField::new('publicationDate', 'Publication Date')->setFormat('d/M/Y H:mm'),
            AssociationField::new('author', 'Author'),
            AssociationField::new('article', 'Article')
        ];
    }
}
