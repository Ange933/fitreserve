<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Reservation;
use App\Entity\Seance;
use PHPUnit\Framework\TestCase;

class SeanceTest extends TestCase
{
    private function makeSeance(int $placesMax): Seance
    {
        $seance = new Seance();
        $seance->setTitre('Séance test');
        $seance->setDateHeure(new \DateTime('+1 week'));
        $seance->setDuree(60);
        $seance->setPlacesMax($placesMax);
        return $seance;
    }

    private function addReservation(Seance $seance, string $statut): Reservation
    {
        $r = new Reservation();
        $r->setStatut($statut);
        $seance->addReservation($r);
        return $r;
    }

    public function testPlacesRestantesWithNoReservations(): void
    {
        $seance = $this->makeSeance(10);

        $this->assertSame(10, $seance->getPlacesRestantes());
    }

    public function testPlacesRestantesWithConfirmedReservations(): void
    {
        $seance = $this->makeSeance(10);
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'confirmee');

        $this->assertSame(7, $seance->getPlacesRestantes());
    }

    public function testPlacesRestantesIgnoresCancelledReservations(): void
    {
        $seance = $this->makeSeance(10);
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'annulee');
        $this->addReservation($seance, 'annulee');
        $this->addReservation($seance, 'annulee');

        // 2 confirmées, 3 annulées → 8 restantes
        $this->assertSame(8, $seance->getPlacesRestantes());
    }

    public function testPlacesRestantesReturnsZeroWhenFull(): void
    {
        $seance = $this->makeSeance(1);
        $this->addReservation($seance, 'confirmee');

        $this->assertSame(0, $seance->getPlacesRestantes());
    }

    public function testPlacesRestantesNeverGoesNegative(): void
    {
        $seance = $this->makeSeance(1);
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'confirmee');
        $this->addReservation($seance, 'confirmee');

        $this->assertGreaterThanOrEqual(0, $seance->getPlacesRestantes());
        $this->assertSame(0, $seance->getPlacesRestantes());
    }

    public function testOnlyEnAttenteReservationsCountAsOccupied(): void
    {
        $seance = $this->makeSeance(5);
        $this->addReservation($seance, 'en_attente');
        $this->addReservation($seance, 'en_attente');
        $this->addReservation($seance, 'annulee');

        // en_attente bloque une place, annulee non
        $this->assertSame(3, $seance->getPlacesRestantes());
    }

    public function testIsDisponibleByDefault(): void
    {
        $seance = $this->makeSeance(5);

        $this->assertTrue($seance->isDisponible());
    }

    public function testToggleDisponibilite(): void
    {
        $seance = $this->makeSeance(5);

        $seance->setDisponible(false);
        $this->assertFalse($seance->isDisponible());

        $seance->setDisponible(true);
        $this->assertTrue($seance->isDisponible());
    }

    public function testAllCancelledReservationsLeavesAllPlacesAvailable(): void
    {
        $seance = $this->makeSeance(3);
        $this->addReservation($seance, 'annulee');
        $this->addReservation($seance, 'annulee');
        $this->addReservation($seance, 'annulee');

        $this->assertSame(3, $seance->getPlacesRestantes());
    }
}
