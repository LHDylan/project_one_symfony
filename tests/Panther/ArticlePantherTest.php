<?php

namespace App\Tests\Panther;

use Facebook\WebDriver\WebDriverBy;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Panther\PantherTestCase;

class ArticlePantherTest extends PantherTestCase
{
    protected $client;
    protected $databaseTool;

    protected function setUp(): void
    {
        $this->client = self::createPantherClient();

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__) . '/Fixtures/UserTestFixtures.yaml',
            dirname(__DIR__) . '/Fixtures/TagTestFixtures.yaml',
            dirname(__DIR__) . '/Fixtures/ArticleTestFixtures.yaml'
        ]);
    }

    public function testArticlePageNumberWithoutInterractions()
    {
        $crawler = $this->client->request('GET', '/articles');

        $this->assertCount(6, $crawler->filter('.blog-list .blog-card'));
    }

    public function testArticleShowMoreButton()
    {
        $crawler = $this->client->request('GET', '/articles');

        $this->client->waitFor('.btn-show-more', 2);

        $this->client->executeScript("document.querySelector('.btn-show-more').click()");

        $this->client->waitForEnabled('.btn-show-more', 3);

        $crawler = $this->client->refreshCrawler();

        $this->assertCount(12, $crawler->filter('.blog-list .blog-card'));
    }

    public function testArticleBySearchDataQueryInAjax()
    {
        $crawler = $this->client->request('GET', '/articles');

        $this->client->waitFor('.form-filter', 2);

        $search = $this->client->findElement(WebDriverBy::cssSelector('#query'));
        $search->sendkeys('Title-3');

        $this->client->waitFor('.content-response', 3);

        sleep(1);

        $crawler = $this->client->refreshCrawler();

        $this->assertCount(1, $crawler->filter('.blog-list .blog-card'));
    }

    // A mettre en fin de fichier test : sert à unset la database (RECOMMANDé)
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
