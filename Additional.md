# Documentation Technique

## Technologies utilisées

### Framework principal : Symfony

Symfony a été choisi pour ce projet car :
- Framework PHP robuste et maintenu activement
- Excellente documentation et large communauté
- Architecture scalable permettant de faire évoluer l'application
- Écosystème de bundles réutilisables
- Sécurité intégrée et bonnes pratiques

### Administration : EasyAdmin

Pour l'interface d'administration, nous utilisons **EasyAdmin** car :
- Bundle Symfony sécurisé et prêt à l'emploi
- Interface d'administration fonctionnelle clé en main
- Gestion native des CRUD (Create, Read, Update, Delete)
- Dashboard personnalisable
- Gestion des rôles intégrée
- Réduction significative du temps de développement

> **Note :** Des problèmes de gestion des rôles ont été rencontrés lors de l'intégration initiale, mais ils ont été résolus après plusieurs tests et ajustements.

## Structure des données

L'application gère trois entités principales :

### 1. Users (Utilisateurs)
- Authentification et gestion des comptes
- Système de rôles hiérarchisés
- Participation aux événements

### 2. Events (Événements)
- Création et gestion des événements
- Attribution d'un éditeur responsable
- Gestion des participants

### 3. Avis (Reviews/Comments)
- Feedback des participants sur les événements
- Système de modération avant publication
- Lien avec l'utilisateur et l'événement

## Système de rôles et permissions

### Hiérarchie des rôles

| Rôle | Accès | Permissions |
|------|-------|-------------|
| **USER** | Pages publiques, profil | Participer aux événements, laisser des avis |
| **EDITOR** | `/admin` (limité) | Modérer les avis de ses propres événements |
| **ADMIN** | `/admin` (complet) | Accès total, gestion des utilisateurs et promotions |

### Détails des permissions

#### USER (utilisateur standard)
- ❌ Accès refusé à `/admin`
- ✅ Inscription et connexion
- ✅ Participation aux événements
- ✅ Rédaction d'avis (nécessite participation)

#### EDITOR (éditeur)
- ✅ Accès à `/admin`
- ✅ Visualisation des avis
- ✅ Modération des avis **uniquement pour les événements dont il est responsable**
- ❌ Ne peut pas modifier les autres événements

#### ADMIN (administrateur)
- ✅ Accès complet à `/admin`
- ✅ Modération de tous les avis
- ✅ Gestion de tous les événements
- ✅ Promotion des utilisateurs (USER → EDITOR)
- ✅ Gestion complète des utilisateurs

## Flux utilisateur

### 1. Inscription et authentification
```
Visiteur → /register → Création compte (rôle USER par défaut) → /login → Accès authentifié
```

### 2. Participation à un événement
```
USER connecté → Liste des événements → Participer → Accès au formulaire d'avis
```

### 3. Modération des avis
```
Participant → Rédige un avis → Statut "En attente"
                                    ↓
                    EDITOR/ADMIN → Modération → Validation → Avis visible publiquement
```

### 4. Promotion d'utilisateur
```
ADMIN → /admin → Gestion utilisateurs → Sélection USER → Promotion EDITOR
```

## URLs principales
| URL                         | Accès           | Description                  |
|-----------------------------|----------------|------------------------------|
| `/`                         | Public         | Page d'accueil               |
| `/login`                    | Public         | Page de connexion            |
| `/register`                 | Public         | Page d'inscription           |
| `/evenements`               | Public         | Liste des événements         |
| `/admin`                    | Editor, Admin  | Interface d'administration   |
| `/evenements/detail/1`      | Public         | Détail d'un événement        |
| `/evenements/1/avis/nouveau`| Authentifié    | Ajout d'un commentaire       |

## Fonctionnalités clés

### Système de participation
- Un utilisateur doit être connecté pour participer
- La participation débloque la possibilité de laisser un avis
- Chaque utilisateur ne peut participer qu'une fois par événement

### Modération des avis
- Les avis nécessitent validation avant publication
- Les éditeurs ne modèrent que leurs événements
- Les admins peuvent modérer tous les avis

### Gestion des événements
- Chaque événement a un éditeur responsable
- L'éditeur gère la modération de son événement
- Les admins ont accès à tous les événements
