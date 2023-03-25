<?php

namespace adamcameron\symfonythefasttrack\test\integration;

use PHPUnit\Framework\TestCase;
use \DOMDocument;
use \DOMXPath;

/** @testdox Tests of the whole installation */
class InstallationTest extends TestCase
{
    /** @testdox Nginx is serving PHP content on the host system */
    public function testNginxResponse()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "http://host.docker.internal:8062/phpinfo.php",
            CURLOPT_RETURNTRANSFER => 1
        ]);
        $response = curl_exec($ch);

        if (curl_error($ch)) {
            $this->fail(sprintf("curl failed with [%s]", curl_error($ch)));
        }
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));

        $document = new DOMDocument();
        $document->loadHTML($response, LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpathDocument = new DOMXPath($document);

        $title = $xpathDocument->query('/html/head/title[text()]');
        $this->assertCount(1, $title);
        $this->assertMatchesRegularExpression(
            "/PHP 8\.2\.\d - phpinfo\(\)/",
            $title[0]->textContent
        );
    }
}
