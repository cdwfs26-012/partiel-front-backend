# Partiel Front-Backend

Ce projet contient le front-end et le back-end d'une application de test.

## Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/cdwfs26-012/partiel-front-backend
cd partiel-front-backend
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

Créez un fichier `.env.local` en vous inspirant du fichier `.env`.

Assurez-vous de configurer correctement la base de données et les autres variables nécessaires.

### 4. Importer la base de données (si nécessaire)

Si votre projet utilise une base de données, importez les migrations ou le dump fourni.

## Utilisateurs de test

Tous les comptes utilisent le mot de passe : **testing**

| Rôle   | Email              |
|--------|-------------------|
| Admin  | test@gmail.com    |
| Simple | test3@gmail.com   |
| Editor | test@gmail.com    |

> **Note :** L'éditeur est responsable de son propre événement.

## Lancement du serveur

Vous pouvez démarrer le serveur de développement de deux manières :

### Avec PHP

```bash
php -S localhost:8000 -t public
```

### Avec Symfony

```bash
symfony server:start
```

Ensuite, ouvrez votre navigateur sur : **http://localhost:8000**

## Commandes utiles

### Vider le cache

```bash
php bin/console cache:clear
```
