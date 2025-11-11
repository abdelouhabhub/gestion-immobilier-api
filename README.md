# API Gestion Immobilière

API REST Laravel pour la gestion de biens immobiliers - Test Technique Digitup Company

---

## 🚀 Installation

### Prérequis

- PHP >= 8.2
- Composer
- PostgreSQL >= 16
- Git

### Commandes d'Installation

**1. Cloner le projet**
git clone https://github.com/abdelouhabhub/gestion-immobilier-api.git
cd gestion-immobilier-ap

**2. Installer les dépendances**
composer install

**3. Configurer l'environnement**
copy .env.example.env
php artisan key:generate

**4. Éditer `.env` et configurer PostgreSQL**
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=
DB_USERNAME=postgres
DB_PASSWORD=

**5. Créer la base de données**
CREATE DATABASE gestion_immobilier;

**6. Exécuter migrations et seeders**
php artisan migrate --seed

**7. Créer le lien symbolique**
php artisan storage:link

**8. Lancer le serveur**
php artisan serve

## 🏗️ Architecture 3 Couches

### Schéma du Flux
![architecture-3-layers.drawio (1).png](..%2Farchitecture-3-layers.drawio%20%281%29.png)

### Composants de l'Architecture

**1. Controller (PropertyController.php)**
- Reçoit les requêtes HTTP
- Valide avec `StorePropertyRequest`, `UpdatePropertyRequest`
- Autorise avec `PropertyPolicy`
- Appelle le Service avec DTOs

**2. Service (PropertyService.php)**
- Contient la logique métier
- Transforme `CreatePropertyDTO`, `UpdatePropertyDTO`
- Appelle le Repository via `PropertyRepositoryInterface`

**3. Repository (PropertyRepository.php)**
- Implémente `PropertyRepositoryInterface`
- Gère toutes les queries Eloquent
- Filtres : ville, type, prix, statut
- Recherche full-text
- Pagination

**4. DTOs (Data Transfer Objects)**
- `CreatePropertyDTO` : Création
- `UpdatePropertyDTO` : Modification
- `FilterPropertiesDTO` : Filtres de recherche

### Sécurité & Bonnes Pratiques

- Form Requests (validation)
- Policy (autorisation)
- API Resources (formatage JSON)
- Injection de dépendances
- DTOs entre couches
- Gestion erreurs formatées JSON

### Features

- Soft deletes sur properties
- Documentation SWAGGER
---

## 🧪 Tests

Exécuter les tests:
php artisan test

**Résultat :** 11 tests passent (44 assertions)

**Tests inclus :**
- Authentification
- Autorisation par rôle
- CRUD biens immobiliers
- Filtres

## 📚 Documentation API (Swagger)
Documentation interactive disponible après installation :
Accès : `http://localhost:8000/docs`

La documentation Swagger permet de :
- Explorer tous les endpoints
- Tester les requêtes directement
- Voir les schémas de données
- Exemples de réponses

## 👨‍💻 Développeur

**Abdelouahab BOUMARAF**  
Master en Software Engineering  
Test Technique Digitup Company - Novembre 2025
