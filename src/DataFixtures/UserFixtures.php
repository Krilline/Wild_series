<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
        $subscriber = new User();
        $subscriber->setEmail('andraurelien@yahoo.fr');
        $subscriber->setRoles(['ROLE_SUBSCRIBER']);
        $subscriber->setPassword($this->passwordEncoder->encodePassword(
            $subscriber,
            'subscriberpassword'
        ));

        $manager->persist($subscriber);

        $admin = new User();
        $admin->setEmail('paulendor@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));

        $manager->persist($admin);

        $manager->flush();
    }
}