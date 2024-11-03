<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Competition;
use App\Entity\DashboardArticle;
use App\Entity\Event;
use App\Entity\Exercise;
use App\Entity\Exhibitor;
use App\Entity\ExhibitorGroup;
use App\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin fixtures
        for ($i = 0; $i < 10; $i++) {
            $admin = new Admin();
            $admin->setEmail($faker->unique()->email);
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password123'));
            $admin->setFirstName($faker->firstName);
            $admin->setLastName($faker->lastName);
            $admin->setArchived(false);

            $manager->persist($admin);
        }

        // Competition fixtures
        for ($i = 0; $i < 5; $i++) {
            $competition = new Competition();
            $competition->setTitle($faker->sentence(3));
            $competition->setText($faker->paragraph);
            $competition->setImage($faker->imageUrl());
            $competition->setArchived(false);

            $manager->persist($competition);
        }

        // DashboardArticle fixtures
        for ($i = 0; $i < 5; $i++) {
            $article = new DashboardArticle();
            $article->setTitle($faker->sentence(3));
            $article->setDescription($faker->paragraph);
            $article->setImage($faker->imageUrl());
            $article->setArchived(false);

            $manager->persist($article);
        }

        // Event fixtures
        $events = [];
        for ($i = 0; $i < 3; $i++) {
            $event = new Event();
            $event->setTitle($faker->sentence(3));
            $event->setDescription($faker->paragraph);
            $event->setAddress($faker->address);
            $event->setZipCode($faker->postcode);
            $event->setCity($faker->city);
            $event->setStartDate($faker->dateTimeBetween('-1 year', '+1 year'));
            $event->setEndDate($faker->dateTimeBetween('+1 year', '+2 years'));
            $event->setBanner($faker->imageUrl());
            $event->setWeezEventId($faker->randomNumber());
            $event->setArchived(false);

            $manager->persist($event);
            $events[] = $event;
        }

        // ExhibitorGroup fixtures
        $groups = [];
        foreach ($events as $event) {
            for ($i = 0; $i < 2; $i++) {
                $group = new ExhibitorGroup();
                $group->setGroupName($faker->company);
                $group->setDescription($faker->paragraph);
                $group->setWebsite($faker->url);
                $group->setEmailContact($faker->unique()->email);
                $group->setArchived(false);
                $group->setEvent($event);

                $manager->persist($group);
                $groups[] = $group;
            }
        }

        // Exhibitor fixtures
        foreach ($groups as $group) {
            for ($i = 0; $i < 5; $i++) {
                $exhibitor = new Exhibitor();
                $exhibitor->setEmail($faker->unique()->email);
                $exhibitor->setRoles(['ROLE_EXHIBITOR']);
                $exhibitor->setPassword($this->passwordHasher->hashPassword($exhibitor, 'password123'));
                $exhibitor->setFirstName($faker->firstName);
                $exhibitor->setLastName($faker->lastName);
                $exhibitor->setArchived(false);
                $exhibitor->setExhibitorGroup($group);

                $manager->persist($exhibitor);
            }
        }

        // Exercise fixtures
        for ($i = 0; $i < 5; $i++) {
            $exercise = new Exercise();
            $exercise->setTitle($faker->sentence(3));
            $exercise->setDescription($faker->paragraph);
            $exercise->setImage($faker->imageUrl());
            $exercise->setArchived(false);

            $manager->persist($exercise);
        }

        // Member fixtures
        for ($i = 0; $i < 10; $i++) {
            $member = new Member();
            $member->setFirstName($faker->firstName);
            $member->setLastName($faker->lastName);
            $member->setFonction($faker->jobTitle);
            $member->setImage($faker->imageUrl());
            $member->setArchived(false);

            $manager->persist($member);
        }

        $manager->flush();
    }
}