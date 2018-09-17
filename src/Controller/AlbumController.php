<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\View\View;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Adapter\DoctrineAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/** @Route("/api", name="api_") */
class AlbumController extends Controller
{
    /**
     * Lists all Albums.
     * @FOSRest\Get("/")
     *
     * @return View
     */
    public function getAlbums(): View
    {
        $albumRepository = $this->getDoctrine()->getRepository(Album::class);

        $albums = $albumRepository->findAll();
        $result = [];

        $albumsCount = \count($albums);

        for ($i = 0; $i < $albumsCount; $i++) {
            $result[$i]['id']    = $albums[$i]->getId();
            $result[$i]['title'] = $albums[$i]->getTitle();

            $imagesCount = \count($albums[$i]->getImages());
            if (0 !== $imagesCount) {
                $images = $albums[$i]->getImages();
                $j      = 1;

                while ($j <= $imagesCount && $j < 4) {
                    $result[$i]['images'][$j - 1]['id'] = $images[$imagesCount - $j]->getId();
                    $result[$i]['images'][$j - 1]['title'] = $images[$imagesCount - $j]->getTitle();

                    $description = $images[$imagesCount - $j]->getDescription();
                    if ('' !== $description) {
                        $result[$i]['images'][$j - 1]['description']
                            = $description;
                    }

                    $j++;
                }
            }
        }

        return View::create($result, Response::HTTP_OK, []);
    }

    /**
     * Get album by id.
     * @FOSRest\Get("/album/{id}", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param         $id
     *
     * @return View
     */
    public function getAlbum(Request $request, $id): View
    {
        $albumRepository = $this->getDoctrine()->getRepository(Album::class);
        $imageRepository = $this->getDoctrine()->getRepository(Image::class);

        $album = $albumRepository->find($id);

        if (null === $album) {
            return View::create('', Response::HTTP_NO_CONTENT, []);
        }

        $page = $request->query->get('page', 1);

        $qb = $imageRepository->findAllQueryBuilder();

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = (new Pagerfanta($adapter))
            ->setMaxPerPage($this->getParameter('max_images_from_db'))
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

            while ($i < $this->getParameter('max_images_from_db') && $i < $imagesCount) {
                $result[0]['images'][$i]['id']      = $images[$i]->getId();
                $result[0]['images'][$i]['title']   = $images[$i]->getTitle();

                $description = $images[$i]->getDescription();

                if ('' !== $description) {
                    $result[0]['images'][$i]['description'] = $description;
                }

                $i++;
            }
        }

        return View::create($result[0], Response::HTTP_OK, []);
    }

    /**
     * Create Album.
     * @FOSRest\Post("/album")
     *
     * @param Request                $request
     *
     * @param EntityManagerInterface $em
     *
     * @return View
     */
    public function postAlbum(
        Request $request,
        EntityManagerInterface $em
    ): View {
        $album = new Album();
        $album->setTitle($request->get('title'));

        $em->persist($album);
        $em->flush();

        return View::create($album, Response::HTTP_CREATED, []);

    }

    /**
     * Delete Album.
     * @FOSRest\Delete("/album/{id}", requirements={"id" = "\d+"})
     *
     * @param                        $id
     *
     * @param EntityManagerInterface $em
     *
     * @return View
     */
    public function deleteAlbum($id, EntityManagerInterface $em): View
    {
        $repository = $this->getDoctrine()->getRepository(Album::class);

        $album = $repository->find($id);

        if (null !== $album) {
            $em->remove($album);
            $em->flush();
        } else {

            return View::create('', Response::HTTP_NO_CONTENT, []);
        }

        return View::create('Deleted', Response::HTTP_ACCEPTED, []);
    }
}
