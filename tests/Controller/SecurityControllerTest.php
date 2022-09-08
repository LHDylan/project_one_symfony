<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Controller Test
 */
class SecurityControllerTestTest extends WebTestCase
{
    protected $client;
    protected $databaseTool;
    protected $userRepository;

    // Initie le test
    protected function setUp(): void
    {
        // Instancie un nouveau client
        $this->client = self::createClient();

        $this->userRepository = self::getContainer()->get(UserRepository::class);

        $this->databaseTool = self::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadAliceFixture([
            dirname(__DIR__) . '/Fixtures/UserTestFixtures.yaml'
        ]);
    }

    public function testLoginPageResponse()
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
    }

    // Cherche un h1 sur la homepage
    public function testLoginPageHeaderTitle()
    {
        $this->client->request('GET', '/login');
        $this->assertSelectorTextContains('h1', 'Log in');
    }

    public function testRegisterPageResponse()
    {
        $this->client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
    }

    // Cherche un h1 sur la homepage
    public function testRegisterPageHeaderTitle()
    {
        $this->client->request('GET', '/register');
        $this->assertSelectorTextContains('h1', 'Sign up');
    }

    public function testAdminUserNotConnected()
    {
        $this->client->request('GET', '/admin/user');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testAdminArticleNotConnected()
    {
        $this->client->request('GET', '/admin/article');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testAdminForbiddenForUser()
    {
        // retrieve the test user
        $user = $this->userRepository->find(3);

        // simulate $testUser being logged in
        $this->client->loginUser($user);

        $this->client->request('GET', '/admin/user');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminUserPageAllowedForAdminUser()
    {
        // retrieve the test user
        $userAdmin = $this->userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $this->client->loginUser($userAdmin);

        $this->client->request('GET', '/admin/user');
        $this->assertResponseIsSuccessful();
    }

    public function testAdminArticlePageAllowedForAdminUser()
    {
        // retrieve the test user
        $userAdmin = $this->userRepository->findOneByEmail('test@test.com');

        // simulate $testUser being logged in
        $this->client->loginUser($userAdmin);

        $this->client->request('GET', '/admin/article');
        $this->assertResponseIsSuccessful();
    }

    public function testAdminUserPageForbiddenForEditorUser()
    {
        // retrieve the test user
        $userEditor = $this->userRepository->findOneByEmail('editor@test.com');

        // simulate $testUser being logged in
        $this->client->loginUser($userEditor);

        $this->client->request('GET', '/admin/user');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAdminTagPageAllowedForEditorUser()
    {
        // retrieve the test user
        $userEditor = $this->userRepository->findOneByEmail('editor@test.com');

        // simulate $testUser being logged in
        $this->client->loginUser($userEditor);

        $this->client->request('GET', '/admin/tag');
        $this->assertResponseIsSuccessful();
    }

    public function testAdminArticlePageAllowedForEditorUser()
    {
        // retrieve the test user
        $userEditor = $this->userRepository->findOneByEmail('editor@test.com');

        // simulate $testUser being logged in
        $this->client->loginUser($userEditor);

        $this->client->request('GET', '/admin/article');
        $this->assertResponseIsSuccessful();
    }

    public function testSubmitRegistrationForm()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'John@Doe.com',
            'user[password]' => 'Test1234',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '42210'
        ]);

        $this->client->submit($form);

        $newUser = $this->userRepository->findOneByEmail('John@Doe.com');

        if (!$newUser) {
            throw new Exception('Registration failed');
        }

        $this->assertResponseRedirects();
    }

    public function testSubmitRegistrationFormWithInvalideEmail()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'FalseRegister@FalseRegister',
            'user[password]' => 'Test1234',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '42210'
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Veuillez rentrer un email valide.');
    }

    public function testSubmitRegistrationFormWithEmailAlreadyRegistered()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'test@test.com',
            'user[password]' => 'Test1234',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '42210'
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'This email is already used by another user.');
    }

    public function testSubmitRegistrationFormWithInvalidePassword()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'John@Doe.com',
            'user[password]' => 'T',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '42210'
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Votre mot de passe doit comporter au moins 6 caractères, une lettre majuscule, une lettre miniscule et 1 chiffre sans espace blanc');
    }

    public function testSubmitRegistrationFormWithBlankPassword()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'John@Doe.com',
            'user[password]' => '',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '42210'
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Please enter a password');
    }

    public function testSubmitRegistrationFormWithInvalideZipcode()
    {
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Sign up')->form([
            'user[fName]' => 'John',
            'user[lName]' => 'Doe',
            'user[email]' => 'John@Doe.com',
            'user[password]' => 'Test1234',
            'user[address]' => 'XX rue demo address',
            'user[city]' => 'demo city',
            'user[zipCode]' => '4221'
        ]);

        $this->client->submit($form);
        $this->assertSelectorTextContains('.invalid-feedback', 'Veuillez rentrer un code postal valide.');
    }

    // A mettre en fin de fichier test : sert à unset la database (RECOMMANDé)
    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
