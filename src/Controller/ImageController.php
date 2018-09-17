<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;


/** @Route("/api", name="api_") */
class ImageController extends AbstractController
{

    /**
     * Post images info.
     * @FOSRest\Post("/images")
     *
     * @param Request                $request
     *
     * @param EntityManagerInterface $em
     *
     * @return View
     */
    public function postImagesInfo(Request $request, EntityManagerInterface $em): View
    {
        $image = new Image();
        $image->setTitle($request->get('title'));
        $image->setDescription($request->get('description'));
        $image->setAlbum($request->get('album_id'));

        $em->persist($image);
        $em->flush();

        return View::create('Posted', Response::HTTP_CREATED , []);

    }

    /**
     * Delete image.
     * @FOSRest\Delete("/image/{id}", requirements={"id" = "\d+"})
     *
     * @param                        $id
     *
     * @param EntityManagerInterface $em
     *
     * @return View
     */
    public function deleteImage($id, EntityManagerInterface $em): View
    {
        $repository = $this->getDoctrine()->getRepository(Image::class);

        $image = $repository->find($id);

        if (null !== $image) {
            $em->remove($image);
            $em->flush();
        } else {

            return View::create('', Response::HTTP_NO_CONTENT, []);
        }

        return View::create('Deleted', Response::HTTP_ACCEPTED, []);
    }
}
