<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 */
class Advert
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $address;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $roomCount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $price;

    /**
     * Advert constructor.
     * @param string $url
     * @param string $address
     * @param int $roomCount
     * @param int $area
     * @param string $price
     */
    public function __construct(string $url, string $address, int $roomCount, int $area, string $price)
    {
        $this->url = $url;
        $this->address = $address;
        $this->roomCount = $roomCount;
        $this->area = $area;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return int
     */
    public function getRoomCount(): int
    {
        return $this->roomCount;
    }

    /**
     * @return int
     */
    public function getArea(): int
    {
        return $this->area;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }
}