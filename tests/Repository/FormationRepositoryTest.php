<?php
namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Tests d'intégration du FormationRepository
 */
class FormationRepositoryTest extends KernelTestCase {

    private FormationRepository $repository;
    private $entityManager;

    protected function setUp(): void {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Formation::class);
    }

    private function creerFormation(string $titre, string $date): Formation {
        $formation = new Formation();
        $formation->setTitle($titre);
        $formation->setPublishedAt(new \DateTime($date));
        return $formation;
    }

    public function testAddFormation(): void {
        $nb = count($this->repository->findAll());
        $formation = $this->creerFormation('Test Add', '2024-01-01');
        $this->repository->add($formation);
        $this->assertCount($nb + 1, $this->repository->findAll());
    }

    public function testRemoveFormation(): void {
        $formation = $this->creerFormation('Test Remove', '2024-01-01');
        $this->repository->add($formation);
        $nb = count($this->repository->findAll());
        $this->repository->remove($formation);
        $this->assertCount($nb - 1, $this->repository->findAll());
    }

    public function testFindAllOrderByTitleAsc(): void {
        $formations = $this->repository->findAllOrderBy('title', 'ASC');
        $this->assertGreaterThan(0, count($formations));
        $premier = $formations[0]->getTitle();
        $dernier = end($formations)->getTitle();
        $this->assertLessThanOrEqual(0, strcmp($premier, $dernier));
    }

    public function testFindAllOrderByTitleDesc(): void {
        $formations = $this->repository->findAllOrderBy('title', 'DESC');
        $this->assertGreaterThan(0, count($formations));
    }

    public function testFindByContainValueTitle(): void {
        $formations = $this->repository->findByContainValue('title', 'Eclipse');
        foreach ($formations as $f) {
            $this->assertStringContainsStringIgnoringCase('Eclipse', $f->getTitle());
        }
    }

    public function testFindByContainValueVide(): void {
        $tous = $this->repository->findAll();
        $resultat = $this->repository->findByContainValue('title', '');
        $this->assertCount(count($tous), $resultat);
    }

    public function testFindAllLasted(): void {
        $formations = $this->repository->findAllLasted(2);
        $this->assertLessThanOrEqual(2, count($formations));
    }
}