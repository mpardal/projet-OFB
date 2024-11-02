<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Admin;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un nouvel administrateur
        $admin = new Admin();
        $admin->setEmail('admin@admin.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');

        // Hachage du mot de passe
        $password = $this->passwordHasher->hashPassword($admin, 'MotDePasseSécurisé123!');
        $admin->setPassword($password);

        // Sauvegarde dans la base de données
        $manager->persist($admin);
        $manager->flush();
    }
}