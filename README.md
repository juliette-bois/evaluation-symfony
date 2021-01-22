# Évaluation Symfony

## Objectif du projet
Développer une application à l'aide du framework Symfony. Ici il s'agira d'un blog avec des articles et des commentaires.	

## Spécifications des différentes pages
1. Une home liste l'ensemble des articles de blog. Un filtre permet de filtrer les articles affichés par catégorie.
2. Une page "article" permet d'en savoir plus sur cet article, notamment en voyant les commentaires associés.
	- si on est authentifié, on peut poster un commentaire
3. Une page "mes articles", accessible si on est authentifié et qui permet de voir ses articles et ses commentaires, de les modifier ou supprimer.
4. Une page "créer un article" permet de créer un article si on est authentifié
5. Une page de "Login" et "Register" pour l'authentification
6. Enfin, un statut "SuperAdmin" existe et c'est l'administrateur global du blog. Être authentifié en tant que "SuperAdmin" permet de modérer les commentaires avant publication, de supprimer ou modifier un commentaire ou un article de n'importe quel utilisateur. Pour être "SuperAdmin", il faut [créer un utilisateur](http://localhost:8000/registration) puis aller changer à la main le champs `is_admin` en mettant 1.

## Faire fonctionner le projet
### Installation  
Au minimum, il faut utiliser PHP en version 7.3, Symfony en version 5.2.1 et Composer en version 2.0.8
Rester sur la branche master
  
```bash  
git clone https://github.com/juliette-bois/evaluation-symfony.git  
  
cd evaluation-symfony/www/
composer install --ignore-platform-reqs
composer require symfony/workflow:5.2.1 --ignore-platform-reqs
``` 

## Usage  
### 1. Create database with migrations   
```bash  
php bin/console doctrine:migrations:migrate
```

### 2. Load fixtures    
```bash  
php bin/console doctrine:fixtures:load  
```
### 3. Start server
```bash  
php -S localhost:8000 -t public
``` 

### 4. Mailtrap
Dans le .env, modifier la variable MAILER_DSN ligne 23 avec votre congif smtp via Mailtrap
  
## Features
* Fixtures pour créer un jeu de donnée avec PHP faker
* Filtres avec Twig (date, raw)
* Filtrer les articles affichés par catégorie
* Traductions (avec symfony/translation)
* Authentification (avec make:auth)
* Envoie d'emails quand on écrit un commentaire (avec symfony/mailer)
* Utilisation de Doctrine
* Admin/User
* CRUD pour les articles, commentaires et catégories (seulement pour les admins)
* Relation entre les entités
* Pouvoir uploder des images
* Créations de formulaires + validations
* Protection des routes
* Protections des formulaires (avec symfony/security-csrf)
* Sécuriser le blog (avec symfony/security-bundle)
* Utilisation du composant Workflow pour les entités Article et Comment avec 3 status : TO_REVIEW, PUBLISHED et REJECTED. On imagine que lorsqu'un utilisateur authentifié poste un article (ou un commentaire), cela envoie un mail à l'admin (via Mailer). L'article (ou le commentaire) aura alors un état TO_REVIEW tant que l'admin ne validera pas l'article (ou le commentaire) pour le rendre PUBLISHED et être publié sur le blog. Ou bien il pourra le refuser. L'article (ou le commentaire) aura alors un état REFUSED. Dans ces deux derniers cas, l'utilisateur en question recevra un email pour savoir si son post a été accepté ou refusé par l'admin.
