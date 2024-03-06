# BileMo

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/e7f96e9448fc4fde978001c8463e5f9b)](https://app.codacy.com/gh/boaicha/Bilemo/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

Ce projet est réalisé dans le cadre de la formation de développeur d'application PHP/Symfony chez OpenClassrooms.

La mission est de créer une api.

Voici les différentes technologies utilisées dans ce projet :
-   Symfony - PHP

## Installation

Cloner mon projet

```bash
gh repo clone https://github.com/boaicha/Bilemo.git
```

Modifier les variables d'environnement DATABASE_URL dans .env ou .env.local

```bash
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=14&charset=utf8"
```

Installer les dépendances avec Composer

```bash
composer install
```

Créer la base de données

```bash
php bin/console doctrine:database:create
```

Créer les tables de la base de données

```bash
php bin/console doctrine:schema:update --force
```

Insérer un jeu de données

```bash
php bin/console doctrine:fixtures:load
```

Lancer Symfony

```bash
symfony server:start
```

Se connecter avec les identifiants suivant

```bash
nom d\'utilisateur: johndoe@gmail.com
mot de passe: password
```

Et tout devrait fonctionner sans soucis !


## Fonctionnalités

- Consulter la liste des produits BileMo
- Consulter les détails d’un produit BileMo
- Consulter la liste des utilisateurs inscrits liés à un customer
- Consulter le détail d’un utilisateur inscrit lié à un customer
- Ajouter un nouvel utilisateur lié à un customer
- Supprimer un utilisateur ajouté par un customer.
