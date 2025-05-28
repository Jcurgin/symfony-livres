# 📚 Application de gestion de livres - Symfony 7

## 🚀 Objectif
Cette mini-application Symfony permet la gestion d'une collection de livres avec une interface web simple et fonctionnelle : ajout, consultation, édition, suppression, et gestion d’une bibliothèque personnelle.

---

## 🛠️ Pré-requis

- PHP >= 8.2
- Composer
- MySQL
- Symfony CLI
- Git

---

## 📦 Installation

```bash
git clone https://github.com/votre-utilisateur/nom-du-repo.git
cd nom-du-repo
composer install
cp .env .env.local
# Modifier les informations de connexion BDD dans .env.local si nécessaire
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
mkdir -p public/uploads/covers
```

> ⚠️ Ne pas oublier de donner les droits d’écriture sur le dossier `public/uploads/covers` :
```bash
chmod -R 775 public/uploads/covers
```

---

## ▶️ Lancement

```bash
symfony server:start
```

Accéder à l’application sur : [http://localhost:8000](http://localhost:8000)

---

## 👥 Utilisateurs

Un utilisateur admin est préchargé via les fixtures :

- **Email :** `admin@example.com`
- **Mot de passe :** `admin1234`

---

## ✅ Fonctionnalités

- CRUD complet pour les livres
- Upload d’images de couverture
- Authentification via Symfony Security
- Gestion des rôles (admin / utilisateur)
- Catégorisation des livres
- Ajout de livres à la bibliothèque personnelle
- Filtres de catégories (page d’accueil & mon compte)
- Affichage conditionnel des boutons selon les rôles
- Pagination, messages flash
- Design responsive avec Bootstrap

---

## 🧠Choix techniques

- **Symfony 7** : pour bénéficier des dernières améliorations.
- **Doctrine ORM** : gestion relationnelle avec `ManyToMany` pour `books` ↔ `users`.
- **Fixtures** : pour précharger des données de test et faciliter la démonstration.
- **Twig** : pour le rendu HTML simple et rapide.
- **Bootstrap** : pour une UI claire et responsive sans surcharge graphique.
- **Gestion des rôles** : pour protéger les routes sensibles (`ROLE_ADMIN`).
- **Table pivot `book_user`** : pour que plusieurs utilisateurs puissent ajouter les mêmes livres sans duplication.


## evolution 
✅ Performance & bonnes pratiques
 Recherche par titre, auteur ou description.
 
 Pagination des livres (ex : KnpPaginator).

 Tri des livres (par titre, date, etc).
 
 Gestion avancée des rôles avec Voter pour affiner les permissions (ex. : seul un admin peut    modifier/supprimer un livre).

 Utilisation du cache (ex: annotations doctrine ou HTTP).
 
 Upload multiple ou suppression automatique des fichiers liés.

 Filtrage par plusieurs catégories ou tags.

✅ Expérience utilisateur
 Messages flash pour chaque action importante (ajout, suppression, erreur).
 
 Gestion du compte, possibilité de partager sa bibliothèque.
 
 Affichage d’un loader ou feedback sur actions longues (facultatif).

✅ Organisation / code
 Services pour séparer certaines logiques métier (ex: gestion d’image).

 Regroupement des routes par préfixe + nom (book_, category_, etc.).

 Refonte des formulaires pour éviter de dupliquer du code (FormType centralisés, form themes...).

✅ Tests & maintenance
 Tests fonctionnels ou unitaires de base.

 Fixtures complètes avec plusieurs entités (livres, catégories, admin, user...).

 Linter ou outils de code quality (PHPStan, Psalm, etc).
