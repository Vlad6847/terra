<?php

namespace App\Controller;

use App\Service\AlbumService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @Route("/api", name="api_") */
class AlbumController extends Controller
{
    /**
     * Lists all Albums.
     * @FOSRest\Get("/")
     *
     * @param AlbumService $lastImagesBuilder
     *
     * @return View
     */
    public function getAlbums(AlbumService $lastImagesBuilder): View
    {
        return View::create(
            $lastImagesBuilder->buildAlbumsWithLastImages(
                $this->getParameter('last_n_images')
            ),
            Response::HTTP_OK
        );
    }

    /**
     * Get album by id.
     * @FOSRest\Get("/album/{id}", requirements={"id" = "\d+"})
     *
     * @param Request      $request
     * @param              $id
     *
     * @param AlbumService $albumService
     *
     * @return View
     */
    public function getAlbum(
        Request $request,
        $id,
        AlbumService $albumService
    ): View {
        return View::create(
            $albumService->buildAlbum(
                $request,
                $id,
                $this->getParameter('max_images_from_db')
            ),
            Response::HTTP_OK
        );
    }

    /**
     * Create Album.
     * @FOSRest\Post("/album")
     *
     * @param Request                $request
     * @param ValidatorInterface     $validator
     * @param EntityManagerInterface $em
     * @param AlbumService           $albumService
     *
     * @return View
     */
    public function postAlbum(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        AlbumService $albumService
    ): View {

        return View::create($albumService->postAlbum($request, $validator, $em), Response::HTTP_CREATED);

    }

    /**
     * Delete Album.
     * @FOSRest\Delete("/album/{id}", requirements={"id" = "\d+"})
     *
     * @param                        $id
     *
     * @param EntityManagerInterface $em
     *
     * @param AlbumService           $albumService
     *
     * @return View
     */
    public function deleteAlbum($id, EntityManagerInterface $em, AlbumService $albumService): View
    {
        return View::create($albumService->deleteAlbum($em, $id), Response::HTTP_ACCEPTED);
    }
}
