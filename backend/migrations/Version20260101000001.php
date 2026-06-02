<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260101000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema - users, seances, reservations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE users (
            id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL COMMENT \'(DC2Type:json)\',
            password VARCHAR(255) NOT NULL,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE seance (
            id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            created_by_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\',
            titre VARCHAR(255) NOT NULL,
            description LONGTEXT DEFAULT NULL,
            date_heure DATETIME NOT NULL,
            duree INT NOT NULL,
            places_max INT NOT NULL,
            lieu VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            INDEX IDX_DF35760AB03A8386 (created_by_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE reservation (
            id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            user_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            seance_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\',
            statut VARCHAR(20) NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_42C84955A76ED395 (user_id),
            INDEX IDX_42C84955E2904019 (seance_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF35760AB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955E2904019 FOREIGN KEY (seance_id) REFERENCES seance (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955E2904019');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF35760AB03A8386');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE reservation');
    }
}
