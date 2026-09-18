<?php

namespace App\Controller\Admin;

use App\Entity\Trailer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class TrailerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trailer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Fahrzeug')
            ->setEntityLabelInPlural('Fahrzeuge')
            ->setDefaultSort(['name' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();

        yield ImageField::new('imageFilename', 'Foto')
            ->setBasePath('/uploads/trailers')
            ->setUploadDir('public/uploads/trailers')
            ->setUploadedFileNamePattern('[slug]-[contenthash].[extension]')
            ->setRequired(false)
            ->setHelp('JPG oder PNG, wird auf der Website als Titelbild angezeigt.');

        yield TextField::new('name', 'Titel');
        yield SlugField::new('slug', 'URL-Kürzel')
            ->setTargetFieldName('name')
            ->hideOnIndex();
        yield TextareaField::new('description', 'Beschreibung')->hideOnIndex();
        yield BooleanField::new('active', 'Auf der Website sichtbar');
    }
}
