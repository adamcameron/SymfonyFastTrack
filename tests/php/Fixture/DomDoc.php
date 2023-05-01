<?php

namespace adamcameron\symfonythefasttrack\tests\Fixture;

use Symfony\Component\HttpFoundation\Response;
use \DOMDocument;
use \DOMXPath;

class DomDoc
{
    public static function getContentAsXpathDocument(Response $result): DOMXPath
    {
        $content = $result->getContent();
        $document = new DOMDocument();
        $document->loadHTML($content, LIBXML_NOWARNING | LIBXML_NOERROR);

        return new DOMXPath($document);
    }
}
