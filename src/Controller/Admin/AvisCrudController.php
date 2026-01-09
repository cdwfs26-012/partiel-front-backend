<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
class AvisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avis::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->getUser();

        if ($this->isGranted('ROLE_EDITOR') && !$this->isGranted('ROLE_ADMIN')) {
            $qb->join('entity.evenement', 'ev')
                ->andWhere('ev.responsable = :user')
                ->setParameter('user', $user);
        }

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        // On vérifie si l'utilisateur est ADMIN
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        return [
            IdField::new('id')->hideOnForm(),

            // Seul l'ADMIN peut changer le participant et l'événement
            AssociationField::new('auteur', 'Participant')
                ->setDisabled(!$isAdmin),

            AssociationField::new('evenement', 'Événement')
                ->setDisabled(!$isAdmin),

            // L'ADMIN et l'EDITOR peuvent changer la note
            IntegerField::new('note', 'Note / 5'),

            // L'ADMIN et l'EDITOR peuvent modifier le commentaire
            // On utilise TextareaField pour l'édition et TextField pour l'index
            TextareaField::new('commentaire', 'Message client')
                ->hideOnIndex(),
            TextField::new('commentaire', 'Message client')
                ->onlyOnIndex(),

            // L'ADMIN et l'EDITOR peuvent approuver/refuser
            BooleanField::new('accept', 'Approuver (Modération)')
                ->renderAsSwitch(true),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // On restreint la création (bouton "Add Avis Client") uniquement à l'ADMIN
            // L'EDITOR verra le bouton disparaître
            ->setPermission(Action::NEW, 'ROLE_ADMIN')

            ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis Client')
            ->setEntityLabelInPlural('Avis Clients')
            ->setDefaultSort(['accept' => 'ASC']);
    }
}
