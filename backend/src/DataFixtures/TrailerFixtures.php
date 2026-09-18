<?php

namespace App\DataFixtures;

use App\Entity\Trailer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class TrailerFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $trailer1 = new Trailer('Kühlanhänger 1', 'kuehlanhaenger-1');
        $trailer1->setDescription('Geräumiger Kühlanhänger, ideal für Feste, Feiern und die Gastronomie.');

        $trailer2 = new Trailer('Kühlanhänger 2', 'kuehlanhaenger-2');
        $trailer2->setDescription('Zweiter Kühlanhänger mit gleicher Ausstattung für größere Events.');

        $manager->persist($trailer1);
        $manager->persist($trailer2);
        $manager->flush();
    }
}
