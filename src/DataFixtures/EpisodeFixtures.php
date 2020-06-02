<?php


namespace App\DataFixtures;

use App\Entity\Episode;
use App\Service\Slugify;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use \Doctrine\Bundle\FixturesBundle\Fixture;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i = 0; $i < 50; $i++) {
            $episode = new Episode();
            $slugify = new Slugify();
            $episode->setTitle($faker->sentence($nbWords = 4, $variableNbWords = true));
            $episode->setNumber($faker->numberBetween($min= 1, $max= 12));
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $episode->setSynopsis($faker->text);
            $episode->setSeason($this->getReference('season_' . $faker->numberBetween($min = 0, $max = 19)));
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}