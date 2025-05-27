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
php bin/console make:controller NomDuController:


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

<!-- Pour rendre ce formulaire dynamique plus tard, tu utiliseras Symfony Forms.

Pour la validation visuelle : ajoute des classes conditionnelles ou des petits messages d’erreur dans les prochaines versions. -->

<!-- <div class="bg-contain bg-repeat-round md:flex flex-col items-center md:py-12 md:gap-6"
    style="background-image: url(&quot;{{ asset('build/images/background.png') }}&quot;)">

    <div class="w-full max-w-xl bg-gray-800/50 rounded-lg shadow-lg p-8 mx-auto">
        <h2 class="text-2xl font-bold mb-6 text-center text-yellow-400">Créer un compte</h2>

        <form>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="lastname" class="block text-sm font-medium text-white mb-1">Nom</label>
                    <input type="text" id="lastname" name="lastname" placeholder="Votre nom"
                        class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
                <div>
                    <label for="firstname" class="block text-sm font-medium text-white mb-1">Prénom</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Votre prénom"
                        class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                </div>
            </div>

            <div class="mb-4">
                <label for="pseudo" class="block text-sm font-medium text-white mb-1">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" placeholder="Votre pseudo"
                    class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="mb-4">
                <label for="age" class="block text-sm font-medium text-white mb-1">Âge</label>
                <input type="number" id="age" name="age" placeholder="Votre âge"
                    class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-white mb-1">Adresse email</label>
                <input type="email" id="email" name="email" placeholder="Votre email"
                    class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-white mb-1">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe"
                    class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-white mb-1">Confirmer le mot de
                    passe</label>
                <input type="password" id="confirm_password" name="confirm_password"
                    placeholder="Confirmez votre mot de passe"
                    class="w-full px-4 py-2 rounded-md bg-gray-700 text-white border border-gray-600 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400">
            </div>

            <button type="submit"
                class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded-md transition duration-200">
                Créer mon compte
            </button>
        </form>
    </div>
</div> -->