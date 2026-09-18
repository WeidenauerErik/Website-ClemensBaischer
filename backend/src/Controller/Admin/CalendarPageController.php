<?php

namespace App\Controller\Admin;

use App\Repository\TrailerRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[AdminRoute(
    path: '/calendar/{trailerId}',
    name: '_calendar',
    options: ['requirements' => ['trailerId' => '\d+']],
)]
final class CalendarPageController extends AbstractController
{
    public function __invoke(int $trailerId, TrailerRepository $trailers): Response
    {
        $trailer = $trailers->find($trailerId);
        if (null === $trailer) {
            throw $this->createNotFoundException('Trailer not found.');
        }

        return $this->render('admin/calendar/index.html.twig', [
            'trailer' => $trailer,
        ]);
    }
}
