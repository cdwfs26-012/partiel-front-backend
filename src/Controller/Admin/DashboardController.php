<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\AvisRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    // On déclare les propriétés pour stocker les repositories
    private AvisRepository $avisRepository;
    private EventRepository $eventRepository;
    private UserRepository $userRepository;

    // Injection des dépendances via le constructeur
    public function __construct(
        AvisRepository $avisRepository,
        EventRepository $eventRepository,
        UserRepository $userRepository
    ) {
        $this->avisRepository = $avisRepository;
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
    }

    public function index(): Response
    {
        // On utilise les repositories injectés au lieu du container->get()
        $stats = [
            'totalAvis' => $this->avisRepository->count([]),
            'avisEnAttente' => $this->avisRepository->count(['accept' => 0]), // Règle 1: Modération [cite: 138, 185]
            'totalEvents' => $this->eventRepository->count([]),
            'totalUsers' => $this->userRepository->count([]),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Relais & Châteaux - Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');


        yield MenuItem::linkToCrud('Avis', 'fa fa-comments', Avis::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Administration système');
            yield MenuItem::linkToCrud('Événements', 'fa fa-calendar', Event::class);
            yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-user', User::class);
        }
    }
}
