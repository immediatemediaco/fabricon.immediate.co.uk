<?php

namespace App\DataFixtures;

use App\Entity\Conference;
use App\Entity\Settings;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $settings = $manager->find(Settings::class, 1);
        if (null !== $settings) {
            return;
        }

        $conference = new Conference();
        $conference->setName('June 2023');
        $conference->setSlug('june-2023');
        $conference->setSiteTitle('FabriCon June 2023');
        $conference->setDate(DateTime::createFromFormat('j-M-Y', '9-June-2023'));

        $manager->persist($conference);

        $settings = new Settings();
        $settings->setCurrentConference($conference);
        $settings->setTrack1Description('Track 1 is for everyone. No technical knowledge about Fabric is required');
        $settings->setTrack2Description(
            'Track 2 is for a targeted audience. ' .
            'Viewers will benefit the most if they have some existing technical knowledge'
        );
        $settings->setIntroduction('Lorem ipsum dolor sit amet, consectetur adipisicing elit. Amet asperiores blanditiis
            est neque, quas quis unde voluptatum? Accusantium autem dolore eaque impedit libero, nobis officia quam
            repellendus? Atque, nobis repellat!');

        $manager->persist($settings);

        $manager->flush();
    }
}
