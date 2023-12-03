<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203132458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add agency_id to user table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD agency_id INT NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D649CDEADB2A ON "user" (agency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649CDEADB2A');
        $this->addSql('DROP INDEX IDX_8D93D649CDEADB2A');
        $this->addSql('ALTER TABLE "user" DROP agency_id');
    }
}
