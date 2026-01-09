<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        // Seul l'ADMIN peut accéder à la création/édition/suppression des événements et utilisateurs
        return $actions
            ->setPermission(Action::NEW, 'ROLE_ADMIN')
            ->setPermission(Action::EDIT, 'ROLE_ADMIN')
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');
    }
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield EmailField::new('email', 'Email');

        // Gestion des rôles pour différencier Responsables et Admins [cite: 173]
        yield ChoiceField::new('roles', 'Permissions')
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Responsable d\'évènement' => 'ROLE_RESPONSABLE',
                'Utilisateur standard' => 'ROLE_USER',
            ])
            ->allowMultipleChoices()
            ->renderAsBadges();

        // Optionnel : Voir les évènements auxquels l'utilisateur participe
        yield AssociationField::new('participations', 'Évènements rejoints')
            ->onlyOnDetail();
    }
}
