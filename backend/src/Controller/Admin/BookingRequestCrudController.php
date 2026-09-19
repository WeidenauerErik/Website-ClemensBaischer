<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Entity\BookingRequest;
use App\Enum\BookingRequestStatus;
use App\Service\BookingManager;
use App\Service\BookingOverlapException;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class BookingRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BookingRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Buchungsanfrage')
            ->setEntityLabelInPlural('Buchungsanfragen')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('trailer', 'Anhänger')->autocomplete();
        yield TextField::new('firstName', 'Vorname')->hideOnForm();
        yield TextField::new('lastName', 'Nachname')->hideOnForm();
        yield TextField::new('email', 'E-Mail')->hideOnForm();
        yield TextField::new('phone', 'Telefon')->hideOnForm();
        yield TextareaField::new('message', 'Nachricht')->hideOnIndex()->hideOnForm();
        yield DateField::new('startDate', 'Von');
        yield DateField::new('endDate', 'Bis');
        yield TextField::new('startTime', 'Uhrzeit Abholung')->hideOnForm()->hideOnIndex();
        yield TextField::new('endTime', 'Uhrzeit Rückgabe')->hideOnForm()->hideOnIndex();

        yield ChoiceField::new('status', 'Status')
            ->setChoices([
                'Offen' => BookingRequestStatus::Pending,
                'Angenommen' => BookingRequestStatus::Approved,
                'Abgelehnt' => BookingRequestStatus::Declined,
                'Storniert' => BookingRequestStatus::Cancelled,
            ])
            ->renderAsBadges([
                'pending' => 'warning',
                'approved' => 'success',
                'declined' => 'danger',
                'cancelled' => 'secondary',
            ])
            ->hideOnForm();

        yield TextareaField::new('adminNote', 'Interne Notiz')->hideOnIndex();
        yield AssociationField::new('reviewedBy', 'Bearbeitet von')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('reviewedAt', 'Bearbeitet am')->hideOnForm()->hideOnIndex();
        yield DateTimeField::new('createdAt', 'Eingegangen am')->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        $approve = Action::new('approve', 'Annehmen')
            ->linkToCrudAction('approve')
            ->displayIf(static fn (BookingRequest $r) => BookingRequestStatus::Pending === $r->getStatus())
            ->asSuccessAction();

        $decline = Action::new('decline', 'Ablehnen')
            ->linkToCrudAction('decline')
            ->displayIf(static fn (BookingRequest $r) => BookingRequestStatus::Pending === $r->getStatus())
            ->asDangerAction()
            ->askConfirmation('Diese Anfrage wirklich ablehnen?');

        return $actions
            ->add(Crud::PAGE_INDEX, $approve)
            ->add(Crud::PAGE_DETAIL, $approve)
            ->add(Crud::PAGE_INDEX, $decline)
            ->add(Crud::PAGE_DETAIL, $decline)
            ->disable(Action::NEW, Action::DELETE);
    }

    #[AdminRoute(path: '/approve', name: '_approve')]
    public function approve(AdminContext $context, BookingManager $bookingManager): RedirectResponse
    {
        /** @var BookingRequest $bookingRequest */
        $bookingRequest = $context->getEntity()->getInstance();
        /** @var AdminUser $admin */
        $admin = $this->getUser();

        try {
            $bookingManager->approveRequest($bookingRequest, $admin);
            $this->addFlash('success', 'Anfrage angenommen, Buchung wurde im Kalender angelegt.');
        } catch (BookingOverlapException $e) {
            $this->addFlash('danger', 'Diese Anfrage überschneidet sich mit einer bestehenden Buchung und kann so nicht angenommen werden: '.$e->getMessage());
        } catch (\LogicException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($context->getRequest()->headers->get('referer') ?? $this->generateUrl('admin_dashboard'));
    }

    #[AdminRoute(path: '/decline', name: '_decline')]
    public function decline(AdminContext $context, BookingManager $bookingManager): RedirectResponse
    {
        /** @var BookingRequest $bookingRequest */
        $bookingRequest = $context->getEntity()->getInstance();
        /** @var AdminUser $admin */
        $admin = $this->getUser();

        try {
            $bookingManager->declineRequest($bookingRequest, $admin);
            $this->addFlash('success', 'Anfrage abgelehnt.');
        } catch (\LogicException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirect($context->getRequest()->headers->get('referer') ?? $this->generateUrl('admin_dashboard'));
    }
}
