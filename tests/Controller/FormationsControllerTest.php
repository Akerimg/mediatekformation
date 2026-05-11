<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels de la page formations
 */
class FormationsControllerTest extends WebTestCase {

    public function testPageFormationsAccessible(): void {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseIsSuccessful();
    }

    public function testTriParTitreAsc(): void {
        $client = static::createClient();
        $client->request('GET', '/formations/tri/title/ASC');
        $this->assertResponseIsSuccessful();
    }

    public function testTriParTitreDesc(): void {
        $client = static::createClient();
        $client->request('GET', '/formations/tri/title/DESC');
        $this->assertResponseIsSuccessful();
    }

    public function testTriParPlaylist(): void {
        $client = static::createClient();
        $client->request('GET', '/formations/tri/name/ASC/playlist');
        $this->assertResponseIsSuccessful();
    }

    public function testFiltreParTitre(): void {
        $client = static::createClient();
        $client->request('POST', '/formations/recherche/title', ['recherche' => 'Eclipse']);
        $this->assertResponseIsSuccessful();
    }

    public function testAccesPageDetailFormation(): void {
        $client = static::createClient();
        $client->request('GET', '/formations/formation/1');
        $this->assertResponseIsSuccessful();
    }
}
