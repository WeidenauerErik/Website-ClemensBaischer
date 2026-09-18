<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SUPER_ADMIN')]
final class AdminUserCrudController extends AbstractCrudController
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return AdminUser::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Admin-Benutzer')
            ->setEntityLabelInPlural('Admin-Benutzer')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('firstName', 'Vorname');
        yield TextField::new('lastName', 'Nachname');
        yield EmailField::new('email', 'E-Mail');

        yield ChoiceField::new('roles', 'Rolle')
            ->setChoices(['Admin' => 'ROLE_ADMIN', 'Super-Admin' => 'ROLE_SUPER_ADMIN'])
            ->allowMultipleChoices()
            ->renderExpanded(false)
            ->setHelp('Super-Admins können weitere Admin-Benutzer verwalten.');

        yield BooleanField::new('active', 'Aktiv');

        yield TextField::new('plainPassword', 'Passwort')
            ->setFormType(\Symfony\Component\Form\Extension\Core\Type\PasswordType::class)
            ->onlyOnForms()
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->setFormTypeOption('constraints', [new \Symfony\Component\Validator\Constraints\Length(min: 8, minMessage: 'Das Passwort muss mindestens {{ limit }} Zeichen lang sein.')])
            ->setHelp(Crud::PAGE_EDIT === $pageName ? 'Leer lassen, um das Passwort nicht zu ändern.' : 'Mindestens 8 Zeichen.');

        yield DateTimeField::new('createdAt', 'Erstellt am')->hideOnForm();
        yield DateTimeField::new('lastLoginAt', 'Letzter Login')->hideOnForm();
    }

    public function createEntity(string $entityFqcn): AdminUser
    {
        return new AdminUser('', '', '');
    }

    public function persistEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->hashPlainPassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(\Doctrine\ORM\EntityManagerInterface $entityManager, object $entityInstance): void
    {
        $this->hashPlainPassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function hashPlainPassword(object $entityInstance): void
    {
        if (!$entityInstance instanceof AdminUser) {
            return;
        }

        $plainPassword = $entityInstance->getPlainPassword();

        if (null !== $plainPassword && '' !== $plainPassword) {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $plainPassword));
        }

        $entityInstance->eraseCredentials();
    }
}
