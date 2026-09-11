# TeamFlow API

API REST de gestion d'équipes et de tâches avec authentification par jeton (Laravel Sanctum) et contrôle d'accès basé sur les rôles (`admin`, `manager`, `member`), propre à chaque équipe.

Construit avec **Laravel 11**, **Sanctum**, **Pest** (tests), **Docker** et une pipeline **CI GitHub Actions**.

## Pourquoi ce projet

Ce projet démontre, sur un cas d'usage concret (gestion d'équipes/tâches façon Trello simplifié), les pratiques attendues sur un poste Laravel de niveau professionnel :

- Authentification API par jeton (Sanctum) — inscription, connexion, déconnexion
- Autorisation à deux niveaux : `Policies` Laravel (Gate) + middleware de rôle personnalisé (`team.role`)
- Modélisation relationnelle propre (pivot `team_user` avec rôle par équipe, contraintes de clés étrangères, index)
- Form Requests dédiées pour la validation
- API Resources pour un format de réponse JSON cohérent et contrôlé
- Suite de tests **Pest** couvrant l'authentification, les permissions d'équipe et les règles d'accès aux tâches
- **Dockerisation** complète (PHP-FPM + Nginx + MySQL via `docker-compose`)
- **Intégration continue** (GitHub Actions) : installation, migration, lint (Pint), tests — à chaque push
- Documentation API au format **OpenAPI 3** (`docs/openapi.yaml`)
- **Interface Vue 3 + TypeScript** consommant l'API (authentification, équipes, tableau de tâches) — preuve fullstack au-delà du seul back-end

## Modèle de rôles

| Rôle      | Peut faire |
|-----------|------------|
| `admin`   | Tout gérer sur l'équipe (membres, tâches), supprimer l'équipe si propriétaire |
| `manager` | Gérer les membres et toutes les tâches de l'équipe |
| `member`  | Créer des tâches, modifier uniquement celles qui lui sont assignées |

## Démarrage rapide

### Avec Docker (recommandé)

```bash
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

L'API est alors disponible sur `http://localhost:8080/api`.

### En local (sans Docker)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Lancer les tests

```bash
php artisan test
# ou, avec couverture de style de code
vendor/bin/pint --test
```

## Exemple d'utilisation

```bash
# Inscription
curl -X POST http://localhost:8080/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Owess","email":"owess@example.com","password":"password123","password_confirmation":"password123"}'

# Création d'une équipe (avec le token reçu ci-dessus)
curl -X POST http://localhost:8080/api/teams \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"name":"Squad Alpha"}'
```

Documentation complète des endpoints : [`docs/openapi.yaml`](docs/openapi.yaml) (importable dans Postman/Insomnia ou visualisable sur [editor.swagger.io](https://editor.swagger.io)).

## Interface web (Vue 3 + TypeScript)

Un client SPA est inclus dans `resources/` : authentification, liste des équipes, tableau de tâches (façon Kanban) consommant directement l'API ci-dessus via un jeton Sanctum stocké côté client.

```bash
npm install
npm run dev      # serveur de dev sur http://localhost:5173 (proxy /api -> :8080)
npm run build    # build de production dans public/build
npm run type-check
```

Stack : Vue 3 (Composition API, `<script setup>`), TypeScript strict, Vue Router, Pinia, Axios, Tailwind CSS, Vite.

## Stack technique

**Back-end**
- PHP 8.3 / Laravel 11
- Laravel Sanctum (auth API par jeton)
- MySQL 8 (Docker) / SQLite (tests)
- Pest 3 (tests)
- Laravel Pint (style de code, PSR-12)
- Docker & docker-compose
- GitHub Actions (CI)

**Front-end**
- Vue 3 + TypeScript, Vue Router, Pinia
- Vite, Tailwind CSS, Axios

## Auteur

**Akpa Salomon Owess** — Développeur Fullstack, spécialisation Laravel (PHP)
[Portfolio](https://aso2-owess.github.io/portfolio-owess/) · [GitHub](https://github.com/ASO2-Owess) · owesssalomon08@gmail.com
