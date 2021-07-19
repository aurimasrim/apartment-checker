<?php

namespace App\Crawler;

use App\Model\Advert as AdvertModel;

interface CrawlerInterface
{
    /**
     * @return AdvertModel[]
     */
    public function crawlAdverts(): array;
}
