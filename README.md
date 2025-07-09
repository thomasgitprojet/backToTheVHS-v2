# Symfony Docker

A [Docker](https://www.docker.com/)-based installer and runtime for the [Symfony](https://symfony.com) web framework,
with [FrankenPHP](https://frankenphp.dev) and [Caddy](https://caddyserver.com/) inside!

![CI](https://github.com/dunglas/symfony-docker/workflows/CI/badge.svg)

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to set up and start a fresh Symfony project
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Features

* Production, development and CI ready
* Just 1 service by default
* Blazing-fast performance thanks to [the worker mode of FrankenPHP](https://github.com/dunglas/frankenphp/blob/main/docs/worker.md) (automatically enabled in prod mode)
* [Installation of extra Docker Compose services](docs/extra-services.md) with Symfony Flex
* Automatic HTTPS (in dev and prod)
* HTTP/3 and [Early Hints](https://symfony.com/blog/new-in-symfony-6-3-early-hints) support
* Real-time messaging thanks to a built-in [Mercure hub](https://symfony.com/doc/current/mercure.html)
* [Vulcain](https://vulcain.rocks) support
* Native [XDebug](docs/xdebug.md) integration
* Super-readable configuration

**Enjoy!**

## Docs

1. [Options available](docs/options.md)
2. [Using Symfony Docker with an existing project](docs/existing-project.md)
3. [Support for extra services](docs/extra-services.md)
4. [Deploying in production](docs/production.md)
5. [Debugging with Xdebug](docs/xdebug.md)
6. [TLS Certificates](docs/tls.md)
7. [Using MySQL instead of PostgreSQL](docs/mysql.md)
8. [Using Alpine Linux instead of Debian](docs/alpine.md)
9. [Using a Makefile](docs/makefile.md)
10. [Updating the template](docs/updating.md)
11. [Troubleshooting](docs/troubleshooting.md)

## License

Symfony Docker is available under the MIT License.

## Credits

Created by [Kévin Dunglas](https://dunglas.dev), co-maintained by [Maxime Helias](https://twitter.com/maxhelias) and sponsored by [Les-Tilleuls.coop](https://les-tilleuls.coop).

##  Commandes utiles de MakerBundle

Symfony MakerBundle permet de générer rapidement du code pour votre projet. Voici quelques commandes essentielles :

##  Créer un contrôleur :
docker-compose exec php bin/console make:controller NomDuController:


##  Créer une entité (table en base de données) :
docker-compose exec php bin/console make:entity Account
docker-compose exec php bin/console make:entity Status
docker-compose exec php bin/console make:entity Movies
docker-compose exec php bin/console make:entity Commande
docker-compose exec php bin/console make:entity Product
docker-compose exec php bin/console make:entity Publication


##  Créer une migration pour la base de données :
docker-compose exec php bin/console make:migration


##  Créer un formulaire :
docker-compose exec php bin/console make:form

##  Créer une fixture :
docker-compose exec php php bin/console doctrine:fixtures:load

##  Créer un utilisateur (avec sécurité) :
php bin/console make:user


##  Créer un CRUD complet :
php bin/console make:crud NomDeLEntite


##  Créer une commande :
docker-compose exec php bin/console make:command


##  Ajouter un film :
docker-compose exec php bin/console app:add-movies

 Solution 2 : Faire un dépôt template GitHub (idéal pour plusieurs projets)
Si tu veux réutiliser cette config souvent, crée un dépôt template sur GitHub.

1️⃣ Convertir ton dépôt actuel en "template"
Va sur GitHub dans ton dépôt public.
Settings → Repository Template → Enable template repository
2️⃣ Créer un nouveau projet basé sur ce template
Va sur GitHub → New Repository
Sélectionne "Choose a template" et choisis ton dépôt modèle.
Coche "Private" pour que le dépôt soit privé.
✅ Cette méthode est top car chaque projet n'est pas un fork mais commence avec la même config.


## installion tailwind

docker-compose exec php composer require symfony/webpack-encore-bundle
docker-compose exec php npm install
docker-compose exec php npm install -D tailwindcss postcss-loader autoprefixer
docker-compose exec php npx tailwindcss init --full
docker-compose exec php npm run dev

## fontSize tailwind

fontSize: {
      'xs': '.75rem',
      'sm': '.875rem',
      'tiny': '.875rem',
      'base': '1rem',
      'lg': '1.125rem',
      'xl': '1.25rem',
      '2xl': '1.5rem',
      '3xl': '1.875rem',
      '4xl': '2.25rem',
      '5xl': '3rem',
      '6xl': '4rem',
      '7xl': '5rem',
    }

## Sécurité les 3 principaux types d’injections et protections

### 1. Injection SQL

        But : insérer du code SQL malveillant dans un champ de formulaire pour manipuler la base de données (ex : email = ' OR 1=1 --).

        Exemple d'attaque :

        sql
            SELECT * FROM users WHERE email = '' OR 1=1 --';

        Protection :

            Utiliser Doctrine ou PDO avec des requêtes préparées (? ou :param).

            Ne jamais concaténer directement les entrées utilisateur dans les requêtes SQL.

            Valider et filtrer les entrées côté serveur.


### 2. Injection XSS (Cross-Site Scripting)

        But : injecter du JavaScript malveillant dans une page web pour voler des données (ex : cookies, identifiants).

        Exemple d'attaque :

            <script>alert('XSS')</script>

        Protection :

            Utiliser Twig qui échappe automatiquement les variables avec {{ }}.

            Éviter d’utiliser |raw sauf si c’est nécessaire et sûr.

            Filtrer les entrées utilisateur (ex : via htmlspecialchars, validation HTML, etc.).


### 3. CSRF (Cross-Site Request Forgery)

        But : forcer un utilisateur connecté à effectuer une action non voulue sur un autre site.

        Exemple d'attaque :
            Un formulaire malveillant soumis automatiquement à l’insu de l’utilisateur.

        Protection :

            Utiliser les tokens CSRF avec Symfony :

            Dans security.yaml : enable_csrf: true

            Dans les formulaires : <input type="hidden" name="_csrf_token" value="{{ csrf_token('id') }}">

            Vérifier systématiquement les tokens côté serveur.

            Restreindre les actions sensibles à la méthode POST.

## Schéma illustré

     +--------------------+
      | Saisie utilisateur |
      +--------------------+
                |
                v
      +----------------------+
      | movieTitle (input)   |
      +----------------------+
                |
                v
     [ JS → AJAX GET /api/movies/search?term=Ince ]
                |
                v
    +------------------------------+
    | Symfony Controller           |
    | - MoviesRepository           |
    | - Renvoie JSON [{id, title}] |
    +------------------------------+
                |
                v
    +---------------------------+
    | Liste suggestions (HTML) |
    | onClick : remplissage     |
    |  - movieTitle             |
    |  - movieId (hidden)       |
    +---------------------------+
                |
                v
    [ Soumission formulaire ]
                |
                v
    +--------------------------+
    | Controller Symfony       |
    | - Récupère movieId       |
    | - setMovie(find($id))    |
    | - persist + flush        |
    +--------------------------+

    
<!-- <img src="{{ asset('uploads/' ~ product.image) }}" alt="Image du produit"> -->