<?php


namespace Lenvendo\Service\Bookmark;


use DOMDocument;

class ResponseParser
{
    const META_DESCRIPTION = 'description';

    const META_KEYWORDS = 'keywords';

    public function parseMeta(string $content): array
    {
        $doc = new DOMDocument();
        $doc->strictErrorChecking = false;
        @$doc->loadHTML($content);
        $result = [];

        $this->getTitle($doc, $result);
        $this->getFavicon($doc, $result);
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
        $title = $nodes->item(0)->nodeValue;
        $result['title'] = trim($title);
    }

    /**
     * @param DOMDocument $doc
     * @param array $result
     */
    private function getFavicon(DOMDocument $doc, array &$result): void
    {
        $xml = simplexml_import_dom($doc);
        $arr = $xml->xpath('//link[@rel="shortcut icon" or @rel="icon"]');
        $link = $arr[0]['href'] ?? '';
        if ($link) {
            $scheme = parse_url($link, PHP_URL_SCHEME);
            if (empty($scheme)) {
                $link = 'http://' . ltrim($link, '/');
            }
            $result['favicon'] = $link;
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
}