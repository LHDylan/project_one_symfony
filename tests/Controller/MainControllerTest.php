<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    protected $client;
    protected $databaseTool;

    // Initie le test
    public function setUp(): void
    {
        // Instancie un nouveau client
        $this->client = self::createClient();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__) . '/Fixtures/UserTestFixtures.yaml',
            dirname(__DIR__) . '/Fixtures/ArticleTestFixtures.yaml'
        ]);
    }

    public function testHomePageResponse()
    {
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    // Cherche un h1 sur la homepage
    public function testHomePageHeaderTitle()
    {
        $this->client->request('GET', '/');
        $this->assertSelectorTextContains('h1', 'Home Page');
    }

    public function testNumberArticleHomePage()
    {
        $crawler = $this->client->request('GET', '/');

        $this->assertCount(6, $crawler->filter('.blog-card'));
    }

    // A mettre en fin de fichier test : sert à unset la database (RECOMMANDé)
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
