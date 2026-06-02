<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private function makeUser(): User
    {
        $user = new User();
        $user->setEmail('test@exemple.fr');
        $user->setNom('Martin');
        $user->setPrenom('Sophie');
        $user->setPassword('HashedPassword123');
        return $user;
    }

    public function testUserAlwaysHasRoleUser(): void
    {
        $user = $this->makeUser();

        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testUserWithNoRolesSetStillHasRoleUser(): void
    {
        $user = new User();
        $user->setRoles([]);

        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testAdminHasBothRoles(): void
    {
        $user = $this->makeUser();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    public function testRolesAreUnique(): void
    {
        $user = $this->makeUser();
        $user->setRoles(['ROLE_USER', 'ROLE_USER']);

        $roles = $user->getRoles();
        $this->assertSame(array_unique($roles), $roles);
    }

    public function testGetUserIdentifierReturnsEmail(): void
    {
        $user = $this->makeUser();

        $this->assertSame('test@exemple.fr', $user->getUserIdentifier());
    }

    public function testTelephoneIsNullableByDefault(): void
    {
        $user = new User();

        $this->assertNull($user->getTelephone());
    }

    public function testSetAndGetTelephone(): void
    {
        $user = $this->makeUser();
        $user->setTelephone('0612345678');

        $this->assertSame('0612345678', $user->getTelephone());
    }

    public function testEraseCredentialsDoesNotThrow(): void
    {
        $user = $this->makeUser();

        // Ne doit pas lever d'exception
        $user->eraseCredentials();
        $this->assertTrue(true);
    }
}
