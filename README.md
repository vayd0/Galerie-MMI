# 📸 Squish - Galerie Photo

> Votre galerie photo en ligne pour partager et découvrir de superbes images.

## ✨ Fonctionnalités

- Création et gestion d'albums photo
- Upload de photos (fichiers ou URLs)
- Système de tags et notation
- Partage d'albums entre utilisateurs
- Recherche et filtrage
- Authentification sécurisée
- Interface responsive avec animations

## 🛠️ Technologies

| Backend | Frontend | Outils |
|---------|----------|---------|
| Laravel 10 | TailwindCSS 4 | Vite |
| MySQL | JavaScript ES6+ | Composer |
| Laravel Fortify | GSAP | NPM |
| | Font Awesome | |

## 🚀 Installation

### Prérequis
- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL/MariaDB

### Étapes

```bash
# Cloner le projet
git clone https://github.com/vayd0/Galerie-MMI.git
cd Galerie-MMI

# Installer les dépendances
composer install
npm install

# Configuration
cp .env.example .env
php artisan key:generate

# Publier les vues Fortify
php artisan vendor:publish --provider="Laravel\Fortify\FortifyServiceProvider"

# Base de données (configurer .env puis)
php artisan migrate
php artisan storage:link

# Compiler les assets
npm run build

# Lancer l'application
npm run dev
php artisan serve
```

Accéder à : `http://localhost:8000`