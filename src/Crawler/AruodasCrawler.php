<?php

namespace App\Crawler;

use App\Model\Advert as AdvertModel;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class AruodasCrawler implements CrawlerInterface
{
    private HttpBrowser $browser;

    private string $urlToCrawl;

    public function __construct(string $aruodasUrlToCrawl)
    {
        $this->browser = new HttpBrowser(HttpClient::create());
        $this->urlToCrawl = $aruodasUrlToCrawl;
    }

    /**
     * {@inheritDoc}
     */
    public function crawlAdverts(): array
    {
        return $this->browser->request('GET', $this->urlToCrawl)
            ->filter('body tbody > tr.list-row')
            ->reduce(
                function (Crawler $node, $i) {
                    return \trim($node->text()) !== '' && $node->attr('style') !== 'display: none;';
                }
            )
            ->each(
                function (Crawler $node, $i): AdvertModel {
                    return new AdvertModel(
                        $node->filter('.list-adress > h3 > a')->first()->link()->getUri(),
                        $this->formatAddress(
                            $node->filter('.list-adress > h3 > a')->html()
                        ),
                        (int)$node->filter('td.list-RoomNum')->first()->text(),
                        (int)$node->filter('td.list-AreaOverall')->first()->text(),
                        $node->filter('.list-adress > .price > .list-item-price')->text(),
                    );
                }
            );
    }

    /**
     * @param string $addressHtml
     * @return string
     */
    protected function formatAddress(string $addressHtml): string
    {
        return \str_replace('<br>', ', ', $addressHtml);
    }
}
