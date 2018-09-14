<?php

namespace App\Controller;

use App\Entity\Album;
use Doctrine\Common\Collections\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/api", name="api_") */
class AlbumController extends AbstractController
{
    /**
     * @Route("/", name="albums_list")
     * @param array $albums
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index()
    {
        $albums = $this->getDoctrine()
                       ->getRepository(Album::class)
                       ->findAll();

        return $albums;
    }
}
