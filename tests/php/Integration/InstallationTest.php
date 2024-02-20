<?php

namespace adamcameron\symfonythefasttrack\tests\Integration;

use PHPUnit\Framework\TestCase;
use \DOMDocument;
use \DOMXPath;
use Symfony\Component\HttpFoundation\Response;

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
        $this->assertEquals(Response::HTTP_OK, curl_getinfo($ch, CURLINFO_HTTP_CODE));

        $document = new DOMDocument();
        $document->loadHTML($response, LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpathDocument = new DOMXPath($document);

        $title = $xpathDocument->query('/html/head/title[text()]');
        $this->assertCount(1, $title);
        $this->assertMatchesRegularExpression(
            "/PHP 8\.2\.\d - phpinfo\(\)/",
            $title[0]->textContent,
            "Could not find title 'PHP 8.2.x - phpinfo()'"
        );
    }

    /** @testdox It serves the Symfony welcome page after installation */
    public function testSymfonyWelcomeScreenDisplays()
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "http://host.docker.internal:8062/",
            CURLOPT_RETURNTRANSFER => 1
        ]);
        $response = curl_exec($ch);

        if (curl_error($ch)) {
            $this->fail(sprintf("curl failed with [%s]", curl_error($ch)));
        }
        $this->assertEquals(Response::HTTP_NOT_FOUND, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $document = new \DOMDocument();

        $document->loadHTML($response, LIBXML_NOWARNING | LIBXML_NOERROR);

        $xpathDocument = new \DOMXPath($document);

        $hasTitle = $xpathDocument->query('/html/head/title[text() = "Welcome to Symfony!"]');
        $this->assertCount(1, $hasTitle, "Could not find title 'Welcome to Symfony!'");
    }

    /** @testdox It can run the Symfony console in a shell */
    public function testSymfonyConsoleRuns()
    {
        $appRootDir = dirname(__DIR__, 3);

        exec("{$appRootDir}/bin/console --help", $output, $returnCode);

        $this->assertEquals(0, $returnCode);
        $this->assertNotEmpty($output);
    }
}
