<?php

namespace App\Controller\Admin;

use App\Repository\TrailerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[AdminDashboard(routePath: '/admin', routeName: 'admin_dashboard')]
final class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly TrailerRepository $trailers,
        private readonly AdminUrlGeneratorInterface $adminUrlGenerator,
    ) {
    }

    public function index(): RedirectResponse
    {
        // The booking-requests inbox is what an admin needs to see first.
        $url = $this->adminUrlGenerator->setController(BookingRequestCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dashboard')
            ->setLocales(['de']);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Buchungen');
        yield MenuItem::linkTo(BookingRequestCrudController::class, 'Anfragen', 'fa fa-inbox');

        foreach ($this->trailers->findAllActive() as $trailer) {
            yield MenuItem::linkToRoute($trailer->getName().' Kalender', 'fa fa-calendar', 'admin_dashboard_calendar', ['trailerId' => $trailer->getId()]);
        }

        yield MenuItem::section('Verwaltung');
        yield MenuItem::linkTo(TrailerCrudController::class, 'Fahrzeuge', 'fa fa-truck');
        yield MenuItem::linkTo(AdminUserCrudController::class, 'Admin-Benutzer', 'fa fa-users')
            ->setPermission('ROLE_SUPER_ADMIN');
    }
}
