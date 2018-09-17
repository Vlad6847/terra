<?php


namespace App\Service;


use App\DTO\AlbumDTO;
use App\Entity\Album;
use App\Repository\AlbumRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AlbumService
{
    private $albumRepository;
    private $imageRepository;

    /**
     * AlbumService constructor.
     *
     * @param AlbumRepository $albumRepository
     * @param ImageRepository $imageRepository
     */
    public function __construct(
        AlbumRepository $albumRepository,
        ImageRepository $imageRepository
    ) {
        $this->albumRepository = $albumRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @param int $imagesPerAlbum
     *
     * @return array
     */
    public function buildAlbumsWithLastImages(int $imagesPerAlbum = 3): array
    {
        $albums = $this->albumRepository->findAll();
        $result = [];

        $albumsCount = \count($albums);

        if (0 === $albumsCount) {
            return ['No albums'];
        }

        for ($i = 0; $i < $albumsCount; $i++) {
            $result[$i]['id']    = $albums[$i]->getId();
            $result[$i]['title'] = $albums[$i]->getTitle();

            $imagesCount = \count($albums[$i]->getImages());
            if (0 !== $imagesCount) {
                $images = $albums[$i]->getImages();
                $j      = 1;

                while ($j <= $imagesCount && $j <= $imagesPerAlbum) {
                    $result[$i]['images'][$j - 1]['id'] = $images[$imagesCount
                                                                  - $j]->getId(
                    );
                    $result[$i]['images'][$j - 1]['title']
                                                        = $images[$imagesCount
                                                                  - $j]->getTitle(
                    );

                    $description = $images[$imagesCount - $j]->getDescription();
                    if ('' !== $description) {
                        $result[$i]['images'][$j - 1]['description']
                            = $description;
                    }

                    $j++;
                }
            }
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param int     $id
     * @param int     $maxImagesFromDB
     *
     * @return View|mixed
     */
    public function buildAlbum(Request $request, int $id, int $maxImagesFromDB)
    {
        $album = $this->albumRepository->find($id);

        if (null === $album) {
            $result['error'] = 'Album with id = '.$id.' does not exist!';

            return View::create($result, Response::HTTP_OK);
        }

        $page = $request->query->get('page', 1);

        $qb = $this->imageRepository->findAllQueryBuilder($id);

        $adapter    = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);

        $pagerfanta->setMaxPerPage($maxImagesFromDB)
                   ->setCurrentPage($page);


        $result = [];

        $result[]['id']     = $album->getId();
        $result[0]['title'] = $album->getTitle();

        $images = [];

        foreach ($pagerfanta->getCurrentPageResults() as $image) {
            $images[] = $image;
        }

        $imagesCount = \count($images);

        if ($imagesCount > 0) {
            $i = 0;

            while ($i < $maxImagesFromDB
                   && $i < $imagesCount) {
                $result[0]['images'][$i]['id']    = $images[$i]->getId();
                $result[0]['images'][$i]['title'] = $images[$i]->getTitle();

                $description = $images[$i]->getDescription();

                if ('' !== $description) {
                    $result[0]['images'][$i]['description'] = $description;
                }

                $i++;
            }
        }

        return $result[0];
    }

    /**
     * @param Request                $request
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $em
     *
     * @return Album|View
     */
    public function postAlbum(Request $request, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $albumDTO   = new AlbumDTO($request->get('title'));
        $violations = $validator->validate($albumDTO);
        if (\count($violations) > 0) {
            $result = [];

            foreach ($violations as $violation) {
                $result[] = [
                    $violation->getPropertyPath() => $violation->getMessage(),
                ];
            }

            return View::create($result, Response::HTTP_OK);
        }

        $album = new Album();
        $album->setTitle($request->get('title'));

        $em->persist($album);
        $em->flush();

        return $album;
    }

    /**
     * @param EntityManagerInterface $em
     * @param int                    $id
     *
     * @return array
     */
    public function deleteAlbum(EntityManagerInterface $em, int $id): array
    {
        $album = $this->albumRepository->find($id);
        $result = [];

        if (null !== $album) {
            $em->remove($album);
            $em->flush();
            $result['result'] = 'deleted';
        } else {
            $result['error'] = 'Album with id = '.$id.' does not exists!';
        }

        return $result;
    }
}
