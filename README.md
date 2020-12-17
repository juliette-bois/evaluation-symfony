# evaluation-symfony

L'objectif du projet est de développer une application à l'aide du framework Symfony permettant d'implémenter les grands principes d'un **CMS**. 

L'application devra intégrer un front et un back office. L'espace d'adminstration permettra aux utilisateurs authentifiés d'administrer les différents contenus. Le projet devra permettre la gestion des pages, sections, médias et commentaires.

## Spécifications
1. Page : permet de renseigner l'ensemble des données nécessaires pour la publication d'une page web (titre, description, contenus, seo, status,....)
2. Media : permet la gestion des fichiers et métas données associées
3. Section : une section permet le regroupement de pages ou de médias afin de catégoriser l'information
4. Commentaire : possibilité de réagir à une page ou un média, les commentaires doivent être modérés avant publication

> Par exemple en page d'accueil, je souhaite lister un ensemble de page de mon choix et mettre en valeur des photos dans une "sidebar"

## Objectifs
* Appréhender les concepts et briques principales de Symfony
* Identifier problématique(s) et proposer une solution 
* Imaginer et concevoir une application
* Travailler en groupe de façon efficace
* Gestion de projet (tâches, planning,..)

Phase 1
========

Travail réalisé en binôme permettant la mise en place du socle applicatif. Le développement est réalisé en programmation par pairs ; les rôles sont inversées au cours de l'après-midi.

## Etapes
1. Jour 1
	1. Créer un dépôt Git principal qui sera utilisé par le binôme
	2. Initialisation d'un projet Symfony
	3. Implémentation d'une fonctionnalité
	4. Refactoring du code
2. Jour 2
	1. Modéliser le schéma de données
	2. Réalisation d'un CRUD
	3. Sécuriser le back-office

## Tâches
1. Jour 1
	1. Définition d'un model "Comment" devant implémenter l'interface "CommentInterface" (pas de base de données pour le moment)
	2. Gestion d'un formulaire permettant la soumission du commentaire, les informations doivent être contrôlées (NotBlank,...) avant d'être envoyées
	3. Signaler par mail qu'un commentaire doit être modéré avec [Composant Mailer](https://symfony.com/doc/current/mailer.html)
	4. Réusinage du code, la logique permettant d'envoyer l'email ne doit être ni dans le controller ni dans le formulaire mais à un endroit permettant sa réutilisation
2. Jour 2
	1. Génération des entités Doctrine pour répondre aux spécifications
	2. Implémentation d'un CRUD (à la main) pour la gestion d'une entitée précédemment créée
	3. Contrôler l'ensemble des données renseignées avant de sauvegarder les informations
	4. Mettre l'ensemble de vos informations textuelles dans un fichier de traductions  
	5. Sécuriser l'accès au back-office via un système d'authentification

Phase 2
========

Travail en individuel, l'objectif est d'utiliser le "workflow" collaboratif spécifié au cours de la matinée. Les groupes devront s'organiser de manière autonome en définissant les étapes, distribuer les tâches aux membres du groupe et plannifier leur implémentation.

## Tâches
* Gestion de l'ensemble des entitées dans le back-office
* Contrôler l'ensemble des données pouvant être renseignées via les formulaires de l'application
* Mettre l'ensemble de vos informations textuelles dans un fichier de traductions
* Implémenter un système d'upload pour l'entité "Media"
* Gestion d'un système de "slug" pour l'entité "Page" pour "humaniser" les urls
* Utilisation d'un éditeur visuel pour la gestion du contenu d'une page : html et/ou markdown,..
* Implémenter une API via [API Platfom](https://api-platform.com/) permettant de mettre à disposition les informations pour une gestion du front via un framework javascript : React, VueJS,...
* Utiliser le [composant Workflow](https://symfony.com/doc/current/workflow.html) pour gérer le status de vos entitées et déclencher les actions nécessaires suite à un changement

> Attention à la gestion des priorités en spécifiant les tâches indispensables pour l'utilisation de la plateforme
