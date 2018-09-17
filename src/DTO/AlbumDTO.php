<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class AlbumDTO
{
    /**
     * @var int
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\LessThan(255)
     * @var string
     */
    private $title;

    /**
     * AlbumDTO constructor.
     *
     * @param string $title
     */
    public function __construct(?string $title)
    {
        $this->title = $title;
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
