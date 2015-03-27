<?php

namespace Easanles\AtletismoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/Pedro');

        $this->assertTrue($crawler->filter('html:contains("Hola Pedro")')->count() > 0);
    }
}
