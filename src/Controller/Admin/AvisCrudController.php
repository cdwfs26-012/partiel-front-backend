<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class AvisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avis::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            // L'auteur et l'événement sont essentiels pour le MCD
            AssociationField::new('auteur', 'Participant'),
            AssociationField::new('evenement', 'Événement concerné'),

            // Système de notation requis par le programme
            IntegerField::new('note', 'Note / 5'),

            // Le commentaire
            TextEditorField::new('commentaire'),

            // Dates de suivi
            DateTimeField::new('created_at', 'Date de publication')
                ->onlyOnIndex(),

            // Le champ de modération (Règle 1)
            BooleanField::new('accept', 'Approuver (Règle 1 : Modération)')
                ->setHelp('Un avis non modéré n\'est pas visible par les autres')
        ];
    }
}
