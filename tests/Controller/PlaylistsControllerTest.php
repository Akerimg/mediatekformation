<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests fonctionnels de la page playlists
 */
class PlaylistsControllerTest extends WebTestCase {

    public function testPagePlaylistsAccessible(): void {
        $client = static::createClient();
        $client->request('GET', '/playlists');
        $this->assertResponseIsSuccessful();
    }

    public function testTriParNomAsc(): void {
        $client = static::createClient();
        $client->request('GET', '/playlists/tri/name/ASC');
        $this->assertResponseIsSuccessful();
    }

    public function testTriParNbFormationsDesc(): void {
        $client = static::createClient();
        $client->request('GET', '/playlists/tri/nbFormations/DESC');
        $this->assertResponseIsSuccessful();
    }

    public function testFiltreParNom(): void {
        $client = static::createClient();
        $client->request('POST', '/playlists/recherche/name', ['recherche' => 'Cours']);
        $this->assertResponseIsSuccessful();
    }

    public function testAccesPageDetailPlaylist(): void {
        $client = static::createClient();
        $client->request('GET', '/playlists/playlist/1');
        $this->assertResponseIsSuccessful();
    }
}
