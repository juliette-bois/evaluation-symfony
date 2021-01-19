# Évaluation Symfony

## Objectif du projet
Développer une application à l'aide du framework Symfony.    	
L'application devra intégrer un front et un back office. 
L'espace d'adminstration permettra aux utilisateurs authentifiés d'administrer (ajouter, modifier, supprimer) les différents contenus.
Le projet devra permettre la gestion des pages, sections, médias et commentaires.

## Spécifications des différentes pages
1. Une home liste l'ensemble des articles de blog
2. Une page "article" permet d'en savoir plus sur cet article, notamment en voyant les commentaires associés.
	- si on est authentifié, on peut poster un commentaire
3. Une page "mes articles", accessible si on est authentifié et qui permet de voir ses articles et ses commentaires, de les modifier ou supprimer.
4. Une page "créer un article" permet de créer un article si on est authentifié
5. Une page de "Login" et "Register" pour l'authentification
6. Enfin, un statut "SuperAdmin" existe et c'est l'administrateur global du blog. Être authentifié en tant que "SuperAdmin" permet de modérer les commentaires avant publication, de supprimer ou modifier un commentaire ou un article de n'importe quel utilisateur.

## Tâches / Features
* Integration
* Front
* Comment
* Article
* Category
* Workflow

## Faire fonctionner le projet
### Installation  
Au minimum, il faut utiliser PHP en version 7.3, Symfony en version 5.1.8 et Composer en version 2.0.7
  
```bash  
git clone https://github.com/juliette-bois/evaluation-symfony.git  
  
cd evaluation-symfony/  
composer install --ignore-platform-reqs
``` 

## Usage  
### Start
Start server
```bash  
symfony server:start 
```  
or
```bash  
php -S localhost:8000 -t public
``` 

### Fixtures  
Load fixtures   
```bash  
php bin/console hautelook:fixtures:load  
```  
  
Load fixtures, database won't be purged  
```bash  
php bin/console hautelook:fixtures:load --append  
```  

## Usage  
### Start
Start server
```bash  
symfony server:start 
```  
or
```bash  
php -S localhost:8000 -t public
``` 

### Fixtures  
Load fixtures   
```bash  
php bin/console hautelook:fixtures:load  
```  
  
Load fixtures, database won't be purged  
```bash  
php bin/console hautelook:fixtures:load --append  
```  
  
## Features
* 
