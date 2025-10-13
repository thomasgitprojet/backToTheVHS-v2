<?php

namespace App\Command;

use App\Entity\Movies;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:add-movies',
    description: 'Ajoute une liste de films dans la table movies',
)]
class AddMoviesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $moviesToAdd = [
            'Retour vers le futur',
            'Jurassic Park',
            'E.T. l’extra-terrestre',
            'Terminator',
            'Ghostbusters',
            'Gremlins',
            'Top Gun',
            'The Breakfast Club',
            'Full Metal Jacket',
            'Scarface',
            'Indiana Jones et les Aventuriers de l’arche perdue',
            'Rain Man',
            'Piège de cristal (Die Hard)',
            'Batman (1989)',
            'Total Recall',
            'Edward aux mains d’argent',
            'Les Affranchis (Goodfellas)',
            'Forrest Gump',
            'Pulp Fiction',
            'Matrix',
            'Fight Club',
            'Léon',
            'La Haine',
            'Le Grand Bleu',
            'La Cité de la peur',
            'Clueless',
            'The Truman Show',
            'Philadelphia',
            'Le Silence des agneaux',
            'Scream',
            'Le Parrain',
            'Blade Runner',
            'Alien, le huitième passager',
            'Aliens, le retour',
            'Predator',
            'RoboCop',
            'Les Goonies',
            'Beetlejuice',
            'Les Gremlins 2',
            'Retour vers le futur II',
            'Retour vers le futur III',
            'L’Arme fatale',
            'L’Arme fatale 2',
            'Mad Max 2: Le Défi',
            'Mad Max: Au-delà du dôme du tonnerre',
            'Les Aventuriers du bout du monde',
            'Highlander',
            'Karate Kid',
            'Karate Kid II',
            'Rocky',
            'Rocky II',
            'Rocky III',
            'Rocky IV',
            'Rambo',
            'Rambo II : La mission',
            'Rambo III',
            'Terminator 2 : Le Jugement dernier',
            'Les Dents de la mer',
            'Les Dents de la mer 2',
            'Les Dents de la mer 3',
            'Le Cinquième élément',
            'Star Wars : Un nouvel espoir',
            'Star Wars : L’Empire contre-attaque',
            'Star Wars : Le Retour du Jedi',
            'Chinatown',
            'Les Blues Brothers',
            'Danse avec les loups',
            'L’Histoire sans fin',
            'Labyrinthe',
            'Willow',
            'Hook ou la revanche du Capitaine Crochet',
            'Men in Black',
            'Independence Day',
            'Jurassic Park : Le Monde perdu',
            'The Mask',
            'Speed',
            'Point Break',
            'Les Visiteurs',
            'Le Dîner de cons',
            'Les Bronzés font du ski',
            'Les Sous-doués',
            'La Boum',
            'La Boum 2',
            'Un jour sans fin',
            'Madame Doubtfire',
        ];

        $added = 0;

        foreach ($moviesToAdd as $title) {
            $existingMovie = $this->entityManager
                ->getRepository(Movies::class)
                ->findOneBy(['title' => $title]);

            if (!$existingMovie) {
                $movie = new Movies();
                $movie->setTitle($title);
                $this->entityManager->persist($movie);
                $added++;
            }
        }

        $this->entityManager->flush();

        $io->success("$added film(s) ajouté(s) avec succès à la base de données.");

        return Command::SUCCESS;
    }
}