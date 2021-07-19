<?php

namespace App\Model;

class Advert
{
    private string $url;

    private string $address;

    private int $roomCount;

    private int $area;

    private string $price;

    /**
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