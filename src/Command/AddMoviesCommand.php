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