<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $albums = $manager->getRepository(Album::class)->findAll();

        for ($i = 0; $i < 5; $i++) {
            $image = new Image();

            $image->setTitle('Image '.$i);

            if ($i % 2 === 0) {
                $image->setDescription('Description '.$i);
            }

            $image->setAlbum($albums[0]);

            $manager->persist($image);
        }

        $albumsCount = \count($albums);

        for ($j = 1; $j < $albumsCount; $j++) {
            for ($i = 0; $i < 25; $i++) {
                $image = new Image();

                $image->setTitle('Image '.$i);

                if ($i % 2 === 0) {
                    $image->setDescription('Description '.$i);
                }

                $image->setAlbum($albums[$j]);

                $manager->persist($image);
            }
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            AlbumFixtures::class,
        ];
    }
}
