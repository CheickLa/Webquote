<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203131618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add new agency and client tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE agency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE client_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agency (id INT NOT NULL, siren VARCHAR(9) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, agency_id INT NOT NULL, siren VARCHAR(9) NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, legal_status VARCHAR(255) DEFAULT NULL, sector VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C7440455CDEADB2A ON client (agency_id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE agency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE client_id_seq CASCADE');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455CDEADB2A');
        $this->addSql('DROP TABLE agency');
        $this->addSql('DROP TABLE client');
    }
}
