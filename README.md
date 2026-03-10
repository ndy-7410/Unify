# 🚀 Unify - Gestionnaire de Projets Collaboratif

Bienvenue sur le dépôt officiel d'**Unify**. 
Ce projet a été développé par **Andy** dans le cadre de son projet de fin d'année au lycée. L’objectif principal d’Unify est d’offrir une plateforme sécurisée et facile à utiliser pour organiser et suivre l’avancement de projets.

## 📖 Présentation

Unify est une application web SaaS (Software as a Service) qui permet aux utilisateurs de créer des espaces de travail, de lister des tâches et d'inviter des collaborateurs. Le projet intègre une gestion complète de rôles, un système de notifications en temps réel et une logique de monétisation via abonnements.

## ✨ Fonctionnalités Principales

* **🔒 Authentification & Sécurité :** Inscription, connexion, et gestion de profil sécurisée (hachage des mots de passe, requêtes SQL préparées via PDO pour éviter les injections).
* **📁 Gestion de Projets :** Création, modification et suppression de projets avec définition de dates limites (deadlines).
* **✅ Suivi des Tâches Avancé :** * Ajout de tâches avec description, statut (À faire, En cours, Terminé, etc.) et niveau de priorité (Urgent, Important, etc.).
    * Sauvegarde automatique (Auto-save) en arrière-plan sans rechargement de page grâce à AJAX.
    * Tri dynamique des tâches par statut, priorité ou échéance.
* **🤝 Collaboration en Temps Réel :** * Invitation de collaborateurs par adresse email.
    * Système de notifications dynamique intégré à la barre de navigation (AJAX Polling) pour accepter/refuser les invitations.
* **💳 Système d'Abonnement (Monétisation) :** * Intégration d'un système de forfaits (Starter, Pro, Business).
    * Restrictions côté serveur basées sur le forfait (limite de projets et de collaborateurs).
    * Intégration prévue avec l'API **Lemon Squeezy** via Webhooks pour valider les paiements.

## 🛠️ Technologies Utilisées

Ce projet est construit avec des technologies web standards et ne nécessite aucun framework backend lourd :

* **Frontend :** HTML5, CSS3, JavaScript (Vanilla ES6), framework **Bootstrap 5.3.8** pour un design responsive et moderne.
* **Backend :** PHP 8+ (Système de sessions, architecture procédurale).
* **Base de données :** MySQL (Communication sécurisée via l'extension PHP PDO).
* **Paiement :** API Lemon Squeezy.

## ⚙️ Installation et Déploiement en Local

Pour faire tourner Unify sur votre machine locale (idéal pour tester avec WAMP, XAMPP ou MAMP) :

1. **Cloner le dépôt :**
   ```bash
   git clone [https://github.com/votre-nom-utilisateur/unify.git](https://github.com/votre-nom-utilisateur/unify.git)
   cd unify
