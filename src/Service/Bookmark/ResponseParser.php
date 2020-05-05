<?php


namespace Lenvendo\Service\Bookmark;

use DOMDocument;

class ResponseParser
{
    const META_DESCRIPTION = 'description';

    const META_KEYWORDS = 'keywords';

    /**
     * @var string
     */
    private $imagesPath;

    /**
     * ResponseParser constructor.
     *
     * @param string $imagesPath
     */
    public function __construct(string $imagesPath)
    {
        $this->imagesPath = $imagesPath;
    }

    public function parseMeta(string $content, string $urlDomain): array
    {
        $doc = new DOMDocument();
        $doc->strictErrorChecking = false;
        @$doc->loadHTML($content);
        $result = [];

        $this->getTitle($doc, $result);
        $this->getFavicon($doc, $urlDomain, $result);
        $this->getMeta($doc, $result);

        return $result;
    }

    /**
     * @param DOMDocument $doc
     * @param array $result
     */
    private function getTitle(DOMDocument $doc, array &$result): void
    {
        $nodes = $doc->getElementsByTagName('title');
        $title = null;
        if ($nodes->length > 0) {
            $title = $nodes->item(0)->nodeValue;
        }
        $result['title'] = $title ? trim($title) : '';
    }

    /**
     * @param DOMDocument $doc
     * @param string $urlDomain
     * @param array $result
     */
    private function getFavicon(DOMDocument $doc, string $urlDomain, array &$result): void
    {
        $link = $this->parseDocForFavicon($doc);
        if ($link) {
            $url = $this->fixFaviconLink($link, $urlDomain);
            $favicon = $this->faviconRequest($url);
            if ($this->isFaviconValid($favicon)) {
                $imagePath = $this->storeFavicon($favicon, $url, $urlDomain);
                if ($imagePath) {
                    $result['favicon'] = $imagePath;
                }
            }
        }
    }

    /**
     * @param DOMDocument $doc
     * @param array $result
     */
    private function getMeta(DOMDocument $doc, array &$result): void
    {
        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            $attribute = $meta->getAttribute('name');
            if (in_array($attribute, [self::META_DESCRIPTION, self::META_KEYWORDS])) {
                $result[$attribute] = trim($meta->getAttribute('content'));
            }
        }
    }

    /**
     * @param $link
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function faviconRequest(string $link): \Psr\Http\Message\ResponseInterface
    {
        $client = new \GuzzleHttp\Client();
        $favicon = $client->request('GET', $link);

        return $favicon;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $favicon
     * @param $link
     * @param string $urlDomain
     * @return string|null
     */
    private function storeFavicon(\Psr\Http\Message\ResponseInterface $favicon, $link, string $urlDomain): ?string
    {
        $imageName = parse_url(basename($link), PHP_URL_PATH);
        $favicon->getBody()->rewind();

        if (!is_dir("{$this->imagesPath}$urlDomain")) {
            mkdir("{$this->imagesPath}$urlDomain", 0777);
        }
        $fileStored = file_put_contents("{$this->imagesPath}$urlDomain/{$imageName}", $favicon->getBody());
        if ($fileStored) {
            return "$urlDomain/{$imageName}";
        }

        return null;
    }

    private function isFaviconValid(\Psr\Http\Message\ResponseInterface $favicon): bool
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $iconType = finfo_buffer($finfo, $favicon->getBody());

        return in_array($iconType, ['image/x-icon', 'image/png', 'image/jpeg']);
    }

    /**
     * @param string $link
     * @param string $urlDomain
     * @return string
     */
    private function fixFaviconLink(string $link, string $urlDomain): string
    {
        $urlArr = parse_url($link);
        if (empty($urlArr['scheme'])) {
            $urlArr['scheme'] = 'http';
        }
        if (empty($urlArr['host'])) {
            $urlArr['host'] = $urlDomain;
        }

        return http_build_url($urlArr);
    }

    /**
     * @param DOMDocument $doc
     * @return \SimpleXMLElement|null
     */
    private function parseDocForFavicon(DOMDocument $doc): ?string
    {
        $xml = simplexml_import_dom($doc);
        $icons = $xml->xpath('//link[@rel="shortcut icon" or @rel="icon"]');
        $link = null;
        /** @var \SimpleXMLElement $icon */
        foreach ($icons as $icon) {
            $link = $icon[0]['href'];
            if ($link) {
                break;
            }
        }
        return $link;
    }
}