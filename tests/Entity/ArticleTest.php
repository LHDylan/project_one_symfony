<?php

namespace App\Test\Entity;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Tests\Utils\AssertTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleTest extends KernelTestCase
{
    use AssertTestTrait;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testRepositoryCount()
    {
        $articles = $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__).'/Fixtures/UserTestFixtures.yaml',
            \dirname(__DIR__).'/Fixtures/TagTestFixtures.yaml',
            \dirname(__DIR__).'/Fixtures/ArticleTestFixtures.yaml',
        ]);

        $articles = self::getContainer()->get(ArticleRepository::class)->count([]);

        $this->assertSame(20, $articles);
    }

    public function getEntity()
    {
        $user = self::getContainer()->get(UserRepository::class)->find(1);
        $tag = self::getContainer()->get(TagRepository::class)->find(1);

        return (new Article())
            ->setTitle('Article')
            ->setContent('Description de test')
            ->setUser($user)
            ->setActive(true)
            ->addTag($tag);
    }

    public function testValideArticleEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testNonUniqueTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('Title-1');

        $this->assertHasErrors($article, 1);
    }

    public function testMinLengthTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('T');

        $this->assertHasErrors($article, 1);
    }

    public function testMaxLengthTitleArticle()
    {
        $article = $this->getEntity()
            ->setTitle('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

        $this->assertHasErrors($article, 1);
    }

    public function testMinLengthContentArticle()
    {
        $article = $this->getEntity()
            ->setContent('C');

        $this->assertHasErrors($article, 1);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
