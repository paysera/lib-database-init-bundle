<?php

namespace Paysera\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Paysera\Tests\Entity\Dummy;

class LoadDummyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $dummy1 = new Dummy();
        $dummy1
            ->setName('name_1')
            ->setCreatedAt(new \DateTime('-1 day'))
        ;

        $dummy2 = new Dummy();
        $dummy2
            ->setName('name_2')
            ->setCreatedAt(new \DateTime())
        ;

        $manager->persist($dummy1);
        $manager->persist($dummy2);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}