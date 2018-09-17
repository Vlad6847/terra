<?php


namespace App\DTO;


class AlbumDTO
{
    private $title;

    /**
     * AlbumDTO constructor.
     *
     * @param $title
     */
    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
