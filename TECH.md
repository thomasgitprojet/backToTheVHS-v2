# 🧠 TECH.md — BackToTheVHS v2

## 🔎 Présentation
**BackToTheVHS v2** est une application web basée sur **Symfony 7.2**, servie par **FrankenPHP** et orchestrée via **Docker Compose**.  
Le projet propose une architecture moderne, performante et modulaire intégrant un front-end construit avec **Webpack Encore** et **TailwindCSS**.

---

## ⚙️ Architecture technique

### 🧩 Environnement global
| Composant | Technologie | Description |
|------------|--------------|--------------|
| Serveur web | **Caddy** (via FrankenPHP) | Serveur HTTP natif + support Mercure |
| Application PHP | **FrankenPHP 1 / PHP 8.3** | Serveur d’application intégré |
| Framework | **Symfony 7.2** | Backend principal |
| Base de données | **MySQL 8** | SGBD relationnel |
| ORM | **Doctrine ORM 3.3** | Mapping et migrations |
| Frontend | **Webpack Encore + TailwindCSS** | Compilation JS/CSS moderne |
| Containerisation | **Docker Compose** | Gestion multi-services |
| Outils dev | **Xdebug**, **Mailpit**, **phpMyAdmin** | Débogage, mails, DB |

---

## 🧱 Structure Docker

### Services définis
| Service | Rôle | Ports |
|----------|------|-------|
| `php` | Application Symfony sous FrankenPHP | 80 / 443 |
| `database` | Base de données MySQL | 3306 |
| `phpmyadmin` | Interface web MySQL | 8080 |
| `mailer` | Serveur SMTP local (Mailpit) | 1025 / 8025 |

### Volumes
- `caddy_data` → persistance de la configuration Caddy  
- `caddy_config` → stockage des certificats  
- `database_data` → données MySQL  

### Fichiers principaux
- `compose.yaml` → configuration de base  
- `compose.override.yaml` → environnement de développement  
- `compose.prod.yaml` → overrides pour la production  
- `Dockerfile` → build multi-stage (dev / prod / base)

---

## 🧰 Backend : Symfony 7.2

### Dépendances principales (`composer.json`)
- `doctrine/orm`, `doctrine/migrations`
- `symfony/security-bundle`, `twig-bundle`, `form`, `validator`
- `symfony/mailer`, `symfony/asset`, `symfony/webpack-encore-bundle`
- `knplabs/knp-paginator-bundle`
- `runtime/frankenphp-symfony` (intégration native FrankenPHP)

### Dépendances de développement
- `symfony/maker-bundle`
- `doctrine/doctrine-fixtures-bundle`

### Structure de base
```php
src/
├── Controller/
├── Entity/
├── Repository/
├── Form/
├── Security/
└── Kernel.php  # Classe principale du micro-kernel Symfony
🎨 Frontend
Stack
Webpack Encore 5.1

TailwindCSS 3.4

PostCSS + Autoprefixer

Node.js 20.x

Scripts disponibles
bash
Copier le code
npm run dev     # build de développement
npm run watch   # build continu (auto-reload)
npm run build   # build de production
Configuration principale : webpack.config.js
Compilation JS et CSS avec versioning

Découpage automatique des bundles

Copie des images depuis assets/images → public/build/images

Utilisation de postcss-loader pour TailwindCSS

⚡ Installation & Démarrage
1️⃣ Prérequis
Docker & Docker Compose installés

Node.js ≥ 20.x (pour build local)

Port 80 et 443 disponibles

2️⃣ Cloner le dépôt
bash
Copier le code
git clone https://github.com/thomasgitprojet/backToTheVHS-v2.git
cd backToTheVHS-v2
3️⃣ Créer le fichier d’environnement
bash
Copier le code
cp .env.example .env
# puis configurer les variables (mot de passe DB, secrets, etc.)
4️⃣ Lancer le projet en développement
bash
Copier le code
docker compose up --build
L’application sera disponible sur :
👉 http://localhost
👉 PhpMyAdmin : http://localhost:8080
👉 Mailpit : http://localhost:8025

5️⃣ Générer les assets
bash
Copier le code
npm install
npm run dev
6️⃣ Exécuter les migrations
bash
Copier le code
docker compose exec php php bin/console doctrine:migrations:migrate
🚀 Passage en production
Build de l’image optimisée
bash
Copier le code
docker compose -f compose.yaml -f compose.prod.yaml build
Lancement en mode prod
bash
Copier le code
docker compose -f compose.yaml -f compose.prod.yaml up -d
Caractéristiques du mode prod :

Cache Symfony activé

Autoloader optimisé

Pas d’installation de dépendances dev

Environnement APP_ENV=prod

🧩 Outils utiles
Outil	Commande	Description
Console Symfony	docker compose exec php bin/console	Exécute des commandes Symfony
Doctrine Fixtures	bin/console doctrine:fixtures:load	Charge des données de test
Xdebug	XDEBUG_MODE=debug	Active le débogueur
Mailpit	localhost:8025	Visualisation des e-mails envoyés
PhpMyAdmin	localhost:8080	Interface MySQL

🧠 Points forts techniques
Serveur FrankenPHP intégré, sans FPM → démarrage rapide & haute performance

Stack Docker modulaire (dev / prod)

Hot reload du code et des assets via --watch

Pipeline front moderne (Tailwind + Encore + PostCSS)

Mercure Hub intégré (push temps réel possible)

Séparation claire des environnements et persistance des volumes

