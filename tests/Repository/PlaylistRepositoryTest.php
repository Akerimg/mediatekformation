<?php
namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests d'intégration du PlaylistRepository
 */
class PlaylistRepositoryTest extends KernelTestCase {

    private PlaylistRepository $repository;
    private $entityManager;

    protected function setUp(): void {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Playlist::class);
    }

    public function testAddPlaylist(): void {
        $nb = count($this->repository->findAll());
        $playlist = new Playlist();
        $playlist->setName('Test Playlist');
        $this->repository->add($playlist);
        $this->assertCount($nb + 1, $this->repository->findAll());
        $this->repository->remove($playlist);
    }

    public function testRemovePlaylist(): void {
        $playlist = new Playlist();
        $playlist->setName('Test Remove Playlist');
        $this->repository->add($playlist);
        $nb = count($this->repository->findAll());
        $this->repository->remove($playlist);
        $this->assertCount($nb - 1, $this->repository->findAll());
    }

    public function testFindAllOrderByNameAsc(): void {
        $playlists = $this->repository->findAllOrderByName('ASC');
        $this->assertGreaterThan(0, count($playlists));
        $premier = $playlists[0]->getName();
        $dernier = end($playlists)->getName();
        $this->assertLessThanOrEqual(0, strcasecmp($premier, $dernier));
    }

    public function testFindAllOrderByNameDesc(): void {
        $playlists = $this->repository->findAllOrderByName('DESC');
        $this->assertGreaterThan(0, count($playlists));
    }

    public function testFindAllOrderByNbFormationsAsc(): void {
        $playlists = $this->repository->findAllOrderByNbFormations('ASC');
        $this->assertGreaterThan(0, count($playlists));
        $nbPremier = count($playlists[0]->getFormations());
        $nbDernier = count(end($playlists)->getFormations());
        $this->assertLessThanOrEqual($nbDernier, $nbPremier);
    }

    public function testFindAllOrderByNbFormationsDesc(): void {
        $playlists = $this->repository->findAllOrderByNbFormations('DESC');
        $this->assertGreaterThan(0, count($playlists));
        $nbPremier = count($playlists[0]->getFormations());
        $nbDernier = count(end($playlists)->getFormations());
        $this->assertGreaterThanOrEqual($nbDernier, $nbPremier);
    }

    public function testFindByContainValueVide(): void {
        $tous = $this->repository->findAllOrderByName('ASC');
        $resultat = $this->repository->findByContainValue('name', '');
        $this->assertCount(count($tous), $resultat);
    }
}
