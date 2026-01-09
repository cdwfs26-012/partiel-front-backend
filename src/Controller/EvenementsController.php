<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

final class EvenementsController extends AbstractController
{
    #[Route('/evenements', name: 'app_evenements')]
    public function index(EventRepository $eventRepository): Response
    {
        // On récupère tous les événements en base de données
        $events = $eventRepository->findAll();

        return $this->render('evenements/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/evenements/detail/{id}', name: 'app_evenements_detail')]
    public function show(Event $event, AdminUrlGenerator $adminUrlGenerator): Response
    {
        return $this->render('evenements/show.html.twig', [
            'event' => $event,
            'ea_url' => $adminUrlGenerator,
        ]);
    }
    #[Route('/evenements/participer/{id}', name: 'app_evenements_join')]
    public function join(Event $event, EntityManagerInterface $em): Response
    {
        // Sécurité : l'utilisateur doit être connecté [cite: 164]
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Ajout du participant via la méthode de l'entité
        $event->addParticipant($user);
        $em->flush();

        // Message flash pour confirmer l'action [cite: 195]
        $this->addFlash('success', 'Votre participation est confirmée.');

        return $this->redirectToRoute('app_evenements_detail', ['id' => $event->getId()]);
    }

    // src/Controller/EvenementsController.php

    #[Route('/evenements/{id}/avis/nouveau', name: 'app_avis_new')]
    public function newAvis(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        // Règle de gestion : Seul un participant authentifié peut créer un avis [cite: 159, 161, 164]
        $user = $this->getUser();
        if (!$user || !$event->getParticipants()->contains($user)) {
            $this->addFlash('danger', 'Vous devez participer à l\'événement pour laisser un avis.');
            return $this->redirectToRoute('app_evenements_detail', ['id' => $event->getId()]);
        }

        $avis = new Avis();
        // On pré-remplit les relations selon le MCD [cite: 131, 146]
        $avis->setEvenement($event);
        $avis->setAuteur($user);
        $avis->setCreatedAt(new \DateTimeImmutable());
        $avis->setAccept(0);          // Règle 1 : Modération par défaut [cite: 138, 185]

        // Ici, vous pouvez utiliser un formulaire AvisType ou traiter manuellement la requête
        // Pour cet exemple, on imagine un traitement simplifié via le formulaire
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Votre avis a été envoyé et est en attente de modération.');
            return $this->redirectToRoute('app_evenements_detail', ['id' => $event->getId()]);
        }

        return $this->render('avis/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/evenements/avis/modifier/{id}', name: 'app_avis_edit')]
    public function editAvis(Avis $avis, Request $request, EntityManagerInterface $em): Response
    {
        // Sécurité : Seul l'auteur de l'avis peut le modifier
        if ($avis->getAuteur() !== $this->getUser()) {
            $this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier cet avis.');
            return $this->redirectToRoute('app_profile');
        }

        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Règle d'or : Toute modification entraîne une nouvelle modération
            $avis->setAccept(0);

            $em->flush();

            $this->addFlash('success', 'Votre avis a été mis à jour et sera de nouveau modéré.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $avis->getEvenement(),
        ]);
    }

}
