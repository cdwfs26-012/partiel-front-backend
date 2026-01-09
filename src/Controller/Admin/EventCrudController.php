<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        // Seul l'ADMIN peut accéder à la création/édition/suppression des événements et utilisateurs
        return $actions
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');
    }
    // src/Controller/Admin/EventCrudController.php

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Nom de l\'évènement');

        // Le responsable (celui qui modère les avis selon la Règle 1)
        yield AssociationField::new('responsable', 'Responsable (Modérateur)')
            ->setRequired(true);

        // Nouveau champ pour gérer les participants (Relation Many-to-Many)
        yield AssociationField::new('participants', 'Liste des Participants')
            ->setFormTypeOptions([
                'by_reference' => false,
            ])
            ->setHelp('Inscrivez ici les utilisateurs participant à cet évènement');

        yield CollectionField::new('avis', 'Avis reçus')->onlyOnIndex();
    }
}
