<?php


namespace App\Service;


use App\DTO\ImageDTO;
use App\Entity\Image;
use App\Repository\AlbumRepository;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImageService
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
     * @param Request            $request
     * @param ValidatorInterface $validator
     *
     * @return array
     */
    public function validateImageInfo(
        Request $request,
        ValidatorInterface $validator
    ): array {
        $imageDTO = new ImageDTO(
            $request->get('title'),
            $request->get('description'),
            $request->get('album_id')
        );

        $violations = $validator->validate($imageDTO);

        $result = [];
        if (\count($violations) > 0) {

            foreach ($violations as $violation) {
                $result['error'][$violation->getPropertyPath()]
                    = $violation->getMessage();
            }

        }

        return $result;
    }

    /**
     * @param Request                $request
     * @param EntityManagerInterface $em
     *
     * @return Image
     */
    public function postImageInfo(
        Request $request,
        EntityManagerInterface $em
    ): Image {
        $album = $this->albumRepository->find($request->get('album_id'));
        $image = new Image();
        $image->setTitle($request->get('title'));
        $image->setDescription($request->get('description') ?? '');
        $image->setAlbum($album);

        $em->persist($image);
        $em->flush();

        return $image;
    }

    /**
     * @param int                    $id
     * @param EntityManagerInterface $em
     *
     * @return bool
     */
    public function deleteImage(int $id, EntityManagerInterface $em): bool
    {
        $image = $this->imageRepository->find($id);

        if (null !== $image) {
            $em->remove($image);
            $em->flush();

            return true;
        }

        return false;
    }
}
