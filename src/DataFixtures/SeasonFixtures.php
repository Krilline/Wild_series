<?php


namespace App\DataFixtures;

use App\Entity\Season;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use \Doctrine\Bundle\FixturesBundle\Fixture;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i = 0; $i < 20; $i++) {
            $season = new Season();
            $season->setNumber($faker->numberBetween($min= 1, $max=10));
            $season->setDescription($faker->text);
            $season->setYear($faker->numberBetween($min = 2000, $max = 2020));
            $season->setProgram($this->getReference('program_' . $faker->numberBetween($min = 0, $max = 5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}