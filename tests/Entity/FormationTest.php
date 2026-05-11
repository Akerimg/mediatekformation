<?php
namespace App\Tests\Entity;

use App\Entity\Formation;
use PHPUnit\Framework\TestCase;

/**
 * Test unitaire de la classe Formation
 */
class FormationTest extends TestCase {

    /**
     * Teste que getPublishedAtString() retourne une chaine au format dd/mm/yyyy
     */
    public function testGetPublishedAtStringFormat(): void {
        $formation = new Formation();
        $date = new \DateTime('2021-01-04 17:00:12');
        $formation->setPublishedAt($date);
        $this->assertEquals('04/01/2021', $formation->getPublishedAtString());
    }

    /**
     * Teste que getPublishedAtString() retourne une chaine vide si la date est null
     */
    public function testGetPublishedAtStringNull(): void {
        $formation = new Formation();
        $this->assertEquals('', $formation->getPublishedAtString());
    }
}
