<?php

namespace App\DataFixtures;

use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AlbumFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $album = new Album();
            $album->setTitle('Album '.$i);
            $manager->persist($album);
        }

        $manager->flush();
    }
}
