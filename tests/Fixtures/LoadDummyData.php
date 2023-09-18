<?php

namespace Paysera\Tests\Fixtures;

use DateTime;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Paysera\Tests\Entity\Dummy;

class LoadDummyData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $dummy1 = new Dummy();
        $dummy1
            ->setName('name_1')
            ->setCreatedAt(new DateTime('29.04.2019'))
        ;

        $dummy2 = new Dummy();
        $dummy2
            ->setName('name_2')
            ->setCreatedAt(new DateTime('30.04.2019'))
        ;

        $manager->persist($dummy1);
        $manager->persist($dummy2);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
