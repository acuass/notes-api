<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setEmail('jt@jt.com');
        $user1->setPassword(password_hash('test1', PASSWORD_DEFAULT));
        $user1->setCreateTime(new \DateTime(date('Y-m-d H:i:s')));
        $user1->setLastUpdateTime(new \DateTime(date('Y-m-d H:i:s')));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('j@j.com');
        $user2->setPassword(password_hash('test2', PASSWORD_DEFAULT));
        $user2->setCreateTime(new \DateTime(date('Y-m-d H:i:s')));
        $user2->setLastUpdateTime(new \DateTime(date('Y-m-d H:i:s')));
        $manager->persist($user2);


        $manager->flush();
    }
}
