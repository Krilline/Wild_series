<?php


namespace App\DataFixtures;

use Faker;
use App\Entity\Actor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use \Doctrine\Bundle\FixturesBundle\Fixture;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for($i = 0; $i < 50; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference('program_' . $faker->numberBetween($min = 0, $max = 5)));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}