<?php

namespace App\Crawler;

use App\Model\Advert as AdvertModel;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class AlioCrawler implements CrawlerInterface
{
    private HttpBrowser $browser;
    private string $urlToCrawl;

    public function __construct(string $alioUrlToCrawl)
    {
        $this->browser = new HttpBrowser(HttpClient::create());
        $this->urlToCrawl = $alioUrlToCrawl;
    }

    /**
     * {@inheritDoc}
     */
    public function crawlAdverts(): array
    {
        return $this->browser->request('GET', $this->urlToCrawl)
            ->filter('#main-content-center .main_a_c_b')
            ->each(
                function (Crawler $node, $i): AdvertModel {
                    $description = $node->filter('.descriptions_price_b .description')->text();

                    return new AdvertModel(
                        $node->filter('.vertiselink')->first()->link()->getUri(),
                        $this->resolveAddress($node),
                        $this->extractRoomCount($description),
                        $this->extractArea($description),
                        $node->filter('.descriptions_price_b .main_price')->text(),
                    );
                }
            );
    }

    private function resolveAddress(Crawler $node): string
    {
        $moreDescription = $node->filter('.description_more')->first()->html();
        $decodedMoreDescription = \htmlspecialchars_decode($moreDescription);
        if (\trim($decodedMoreDescription, " \t\n\r\0\x0B" . \chr(194) . \chr(160)) !== '') {
            return \str_replace(' ', '', $decodedMoreDescription);
        }

        return $node->filter('.desc_m_a_b .vertiselink')->first()->text();
    }

    private function extractRoomCount(string $description): int
    {
        return \preg_match('/(\d)\s+kamb./', $description, $matches) !== false ? (int)$matches[1] : 0;
    }

    private function extractArea(string $description): int
    {
        return \preg_match('/(\d+)\s*m²/', $description, $matches) !== false ? (int)$matches[1] : 0;
    }
}
