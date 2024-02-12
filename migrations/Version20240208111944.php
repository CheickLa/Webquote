<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208111944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Allow multiple services per quote';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quote_service (quote_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY(quote_id, service_id))');
        $this->addSql('CREATE INDEX IDX_E723256DB805178 ON quote_service (quote_id)');
        $this->addSql('CREATE INDEX IDX_E723256ED5CA9E6 ON quote_service (service_id)');
        $this->addSql('ALTER TABLE quote_service ADD CONSTRAINT FK_E723256DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote_service ADD CONSTRAINT FK_E723256ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf4ed5ca9e6');
        $this->addSql('DROP INDEX idx_6b71cbf4ed5ca9e6');
        $this->addSql('ALTER TABLE quote DROP service_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote_service DROP CONSTRAINT FK_E723256DB805178');
        $this->addSql('ALTER TABLE quote_service DROP CONSTRAINT FK_E723256ED5CA9E6');
        $this->addSql('DROP TABLE quote_service');
        $this->addSql('ALTER TABLE quote ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf4ed5ca9e6 FOREIGN KEY (service_id) REFERENCES service (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6b71cbf4ed5ca9e6 ON quote (service_id)');
    }
}
