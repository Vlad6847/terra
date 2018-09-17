<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class ImageDTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private $album_id;

    /**
     * ImageDTO constructor.
     *
     * @param string $title
     * @param string $description
     * @param int    $album_id
     */
    public function __construct(
        ?string $title,
        ?string $description,
        ?int $album_id
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->album_id = $album_id;
    }

    /**
     * @return int
     */
    public function getAlbumId(): ?int
    {
        return $this->album_id;
    }

    /**
     * @param int $album_id
     */
    public function setAlbumId(int $album_id): void
    {
        $this->album_id = $album_id;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @var string
     */
}
