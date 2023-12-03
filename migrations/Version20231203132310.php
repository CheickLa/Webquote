<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203132310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add agency_id to service_category table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_category ADD agency_id INT NOT NULL');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FCCDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF3A42FCCDEADB2A ON service_category (agency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_category DROP CONSTRAINT FK_FF3A42FCCDEADB2A');
        $this->addSql('DROP INDEX IDX_FF3A42FCCDEADB2A');
        $this->addSql('ALTER TABLE service_category DROP agency_id');
    }
}
