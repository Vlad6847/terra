<?php

namespace App\Controller;

use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/** @Route("/api", name="api_") */
class ImageController extends AbstractController
{

    /**
     * Post images info.
     * @FOSRest\Post("/images")
     *
     * @param Request $request
     *
     * @param EntityManagerInterface $em
     *
     * @param ValidatorInterface $validator
     *
     * @param ImageService $imageService
     *
     * @return View
     */
    public function postImagesInfo(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ImageService $imageService
    ): View {

        $imageService->validateImageInfo($request, $validator);
        $result = $imageService->postImageInfo($request, $em);

        return View::create($result, Response::HTTP_CREATED);

    }

    /**
     * Delete image.
     * @FOSRest\Delete("/image/{id}", requirements={"id" = "\d+"})
     *
     * @param                        $id
     *
     * @param EntityManagerInterface $em
     *
     * @param ImageService           $imageService
     *
     * @return View
     */
    public function deleteImage(
        $id,
        EntityManagerInterface $em,
        ImageService $imageService
    ): View {
        if ($imageService->deleteImage($id, $em)) {

            return View::create(['result' => 'deleted'], Response::HTTP_ACCEPTED);
        }

        return View::create(['error' => 'Image with id = '.$id.' not found'], Response::HTTP_ACCEPTED);
    }
}
