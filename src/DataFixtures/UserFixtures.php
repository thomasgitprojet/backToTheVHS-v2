<?php

namespace App\DataFixtures;

use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        if ($_ENV['APP_ENV'] !== 'dev' && $_ENV['APP_ENV'] !== 'staging') {
            return;
        }

        $accountsData = [
            [
                'name' => 'Pirate',
                'firstName' => 'Pixel',
                'pseudo' => 'pixelpirate',
                'dateBirth' => '1990-01-01',
                'email' => 'pixel.pirate@example.com',
                'password' => 'Ahoy2025!',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Stardust',
                'firstName' => 'Luna',
                'pseudo' => 'lunastardust',
                'dateBirth' => '1988-04-15',
                'email' => 'luna.stardust@example.com',
                'password' => 'Moonwalk42$',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Fox',
                'firstName' => 'Quantum',
                'pseudo' => 'quantumfox',
                'dateBirth' => '1992-09-20',
                'email' => 'quantum.fox@example.com',
                'password' => 'QF!xT1me',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Ninja',
                'firstName' => 'Neon',
                'pseudo' => 'neonninja',
                'dateBirth' => '1995-07-12',
                'email' => 'neon.ninja@example.com',
                'password' => 'Shuriken#88',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Ghost',
                'firstName' => 'Crimson',
                'pseudo' => 'crimsonghost',
                'dateBirth' => '1991-11-01',
                'email' => 'crimson.ghost@example.com',
                'password' => 'RedPhantom7',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Raven',
                'firstName' => 'Echo',
                'pseudo' => 'echoraven',
                'dateBirth' => '1989-03-25',
                'email' => 'echo.raven@example.com',
                'password' => 'Echo!Raven99',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Samurai',
                'firstName' => 'Cyber',
                'pseudo' => 'cybersamurai',
                'dateBirth' => '1993-06-06',
                'email' => 'cyber.samurai@example.com',
                'password' => 'Samurai#2077',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Queen',
                'firstName' => 'Frostbyte',
                'pseudo' => 'frostbytequeen',
                'dateBirth' => '1994-12-30',
                'email' => 'frostbyte.queen@example.com',
                'password' => 'IceQueen_123',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Flare',
                'firstName' => 'Solar',
                'pseudo' => 'solarflare',
                'dateBirth' => '1990-10-10',
                'email' => 'solar.flare@example.com',
                'password' => 'Sunburst@77',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Zeppelin',
                'firstName' => 'Electric',
                'pseudo' => 'electriczeppelin',
                'dateBirth' => '1992-08-22',
                'email' => 'electric.zeppelin@example.com',
                'password' => 'Zep!2025$',
                'roles' => ['ROLE_USER'],
            ],
            [
                'name' => 'Super',
                'firstName' => 'Admin',
                'pseudo' => 'adminvhs',
                'dateBirth' => '1980-05-05',
                'email' => 'admin@backtothevhs.com',
                'password' => 'VHS_Admin!42',
                'roles' => ['ROLE_ADMIN'],
            ],
        ];

        foreach ($accountsData as $data) {
            $account = new Account();
            $account->setName($data['name']);
            $account->setFirstName($data['firstName']);
            $account->setPseudo($data['pseudo']);
            $account->setDateBirth(new \DateTime($data['dateBirth']));
            $account->setEmail($data['email']);
            $account->setRoles($data['roles']);

            $hashedPassword = $this->passwordHasher->hashPassword($account, $data['password']);
            $account->setPassword($hashedPassword);

            $manager->persist($account);
        }

        $manager->flush();
    }
}