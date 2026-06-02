# FitReserve

Application de réservation de séances de coaching sportif pour Marc Durand, coach à Paris 8e.

**Stack :** Symfony 7 · Vue.js 3 · MySQL 8 · Docker

---

## Lancer le projet

```bash
# 1. Variables d'environnement
cp .env.example .env

# 2. Démarrer les conteneurs
docker compose up -d --build

# 3. Clés JWT (première fois uniquement)
docker exec -it fitreserve_backend php bin/console lexik:jwt:generate-keypair

# 4. Base de données
docker exec -it fitreserve_backend php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it fitreserve_backend php bin/console doctrine:fixtures:load --no-interaction
```

| Service | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000 |

---

## Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@fitreserve.fr | Admin1234! |
| Client | sophie.martin@email.fr | User1234! |

---

## Tests

```bash
# Backend (PHPUnit)
docker exec -it fitreserve_backend php bin/phpunit --testdox

# Frontend (Vitest)
docker exec -it fitreserve_frontend npm test
```

---

## CI/CD

Le pipeline GitHub Actions se déclenche à chaque push :
tests PHPUnit → tests Vitest → build Docker → déploiement SSH sur `main`.
