<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        for($i = 0; $i < 5; $i++) {
            $faker = Factory::create();
            $subscriber = new User();
            $subscriber->setEmail($faker->unique()->email);
            $subscriber->setName($faker->unique()->name);
            $subscriber->setRoles(['ROLE_SUBSCRIBER']);
            $subscriber->setPassword($this->passwordEncoder->encodePassword(
                $subscriber,
                'subscriberpassword'
            ));
            $manager->persist($subscriber);
        }

        $admin = new User();
        $admin->setEmail('paulendor@gmail.com');
        $admin->setName('Paul The Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        $manager->flush();
    }
}