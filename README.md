# TalentMatch

Application Laravel pour les équipes RH marocaines. TalentMatch aide les recruteurs à présélectionner les candidats en analysant les CV par rapport aux offres d'emploi à l'aide de l'IA.

## Fonctionnalités

- **Gestion des offres d'emploi** — Création, consultation et suivi des offres
- **Analyse IA des CV** — Analyse structurée avec score de correspondance, compétences extraites et recommandation
- **Assistant conversationnel** — Agent IA avec mémoire de conversation et appels d'outils (données réelles)
- **Comparaison de candidats** — Comparaison côte à côte pour une même offre
- **Classement automatique** — Classement des candidats par score de correspondance

## Stack technique

- **Framework** Laravel 13 + Breeze
- **Base de données** MySQL
- **IA** laravel/ai SDK avec sortie structurée JSON
- **File d'attente** Database queue (analyse CV asynchrone)
- **Tests** Pest 4
- **Frontend** Blade + Tailwind CSS + Alpine.js

## Prérequis

- PHP 8.3
- Composer
- MySQL
- Node.js & NPM

## Installation

```bash
cp .env.example .env
# Configurez votre base de données dans .env
composer install
npm install
npm run build
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

## Démarrage

```bash
composer run dev
```

Dans un second terminal, lancez le worker de file d'attente pour l'analyse des CV :

```bash
php artisan queue:work --tries=3 --backoff=30
```

## Tests

```bash
php artisan test --compact
```

## Spécifications OpenSpec

Ce projet utilise OpenSpec pour la gestion des spécifications. Les specs se trouvent dans `openspec/specs/` et les changements archivés dans `openspec/changes/archive/`.

## Licence

MIT
