<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Utils\AssertTestTrait;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
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
        $users = $this->databaseTool->loadAliceFixture([
            \dirname(__DIR__) . '/Fixtures/UserTestFixtures.yaml',
            \dirname(__DIR__) . '/Fixtures/TagTestFixtures.yaml',
            \dirname(__DIR__) . '/Fixtures/ArticleTestFixtures.yaml',
        ]);

        $users = self::getContainer()->get(UserRepository::class)->count([]);

        $this->assertSame(7, $users);
    }

    public function getEntity()
    {
        return (new User())
            ->setEmail('exemple@test.com')
            ->setpassword('Test1234')
            ->setfName('FirstNameTest')
            ->setlName('LastNameTest')
            ->setAddress('Rue des tests')
            ->setzipCode('42210')
            ->setCity('TestVille');
    }

    public function testValideUserEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testNonUniqueEmail()
    {
        $user = $this->getEntity()
            ->setEmail('user.fr');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthEmail()
    {
        $user = $this->getEntity()
            ->setEmail('exeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeemple@test.fr');

        $this->assertHasErrors($user, 1);
    }

    public function testMinLengthPassword()
    {
        $user = $this->getEntity()
            ->setPassword('Test1');

        $this->assertHasErrors($user, 1);
    }

    public function testOneUppercaseLetterPassword()
    {
        $user = $this->getEntity()
            ->setPassword('test1234');

        $this->assertHasErrors($user, 1);
    }

    public function testOneLowerLetterPassword()
    {
        $user = $this->getEntity()
            ->setPassword('TEST1234');

        $this->assertHasErrors($user, 1);
    }

    public function testOneNumberPassword()
    {
        $user = $this->getEntity()
            ->setPassword('TeeeeeeeeeeeeeST');

        $this->assertHasErrors($user, 1);
    }

    public function testNoSpacesInPassword()
    {
        $user = $this->getEntity()
            ->setPassword('Test 1234');

        $this->assertHasErrors($user, 1);
    }

    public function testMinLengthFirstName()
    {
        $user = $this->getEntity()
            ->setfName('f');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthFirstName()
    {
        $user = $this->getEntity()
            ->setfName('fffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff');

        $this->assertHasErrors($user, 1);
    }

    public function testMinLengthLastName()
    {
        $user = $this->getEntity()
            ->setlName('l');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthLastName()
    {
        $user = $this->getEntity()
            ->setlName('lllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllllll');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthAddress()
    {
        $user = $this->getEntity()
            ->setAddress('xx rue des teeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeest');

        $this->assertHasErrors($user, 1);
    }

    public function testMinLengthZipCode()
    {
        $user = $this->getEntity()
            ->setzipCode('4210');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthZipCode()
    {
        $user = $this->getEntity()
            ->setzipCode('4222222222210');

        $this->assertHasErrors($user, 1);
    }

    public function testOnlyNumberZipCode()
    {
        $user = $this->getEntity()
            ->setzipCode('4221t');

        $this->assertHasErrors($user, 1);
    }

    public function testMaxLengthCity()
    {
        $user = $this->getEntity()
            ->setCity(' teeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeestVille');

        $this->assertHasErrors($user, 1);
    }
}
