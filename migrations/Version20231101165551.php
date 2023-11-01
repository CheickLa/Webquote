<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101165551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_9065174472bb1336');
        $this->addSql('DROP INDEX uniq_9065174472bb1336');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN quote_id_id TO quote_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_90651744DB805178 ON invoice (quote_id)');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf4dc2902e0');
        $this->addSql('DROP INDEX idx_6b71cbf4dc2902e0');
        $this->addSql('ALTER TABLE quote RENAME COLUMN client_id_id TO client_id');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF419EB6921 FOREIGN KEY (client_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6B71CBF419EB6921 ON quote (client_id)');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT fk_e19d9ad294118ca8');
        $this->addSql('DROP INDEX idx_e19d9ad294118ca8');
        $this->addSql('ALTER TABLE service RENAME COLUMN service_category_id_id TO service_category_id');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2DEDCBB4E FOREIGN KEY (service_category_id) REFERENCES service_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E19D9AD2DEDCBB4E ON service (service_category_id)');
        $this->addSql('ALTER TABLE service_category DROP CONSTRAINT fk_ff3a42fc38b53c32');
        $this->addSql('DROP INDEX idx_ff3a42fc38b53c32');
        $this->addSql('ALTER TABLE service_category RENAME COLUMN company_id_id TO company_id');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FC979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FF3A42FC979B1AD6 ON service_category (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE service_category DROP CONSTRAINT FK_FF3A42FC979B1AD6');
        $this->addSql('DROP INDEX IDX_FF3A42FC979B1AD6');
        $this->addSql('ALTER TABLE service_category RENAME COLUMN company_id TO company_id_id');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT fk_ff3a42fc38b53c32 FOREIGN KEY (company_id_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ff3a42fc38b53c32 ON service_category (company_id_id)');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_90651744DB805178');
        $this->addSql('DROP INDEX UNIQ_90651744DB805178');
        $this->addSql('ALTER TABLE invoice RENAME COLUMN quote_id TO quote_id_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_9065174472bb1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_9065174472bb1336 ON invoice (quote_id_id)');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF419EB6921');
        $this->addSql('DROP INDEX IDX_6B71CBF419EB6921');
        $this->addSql('ALTER TABLE quote RENAME COLUMN client_id TO client_id_id');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf4dc2902e0 FOREIGN KEY (client_id_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6b71cbf4dc2902e0 ON quote (client_id_id)');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD2DEDCBB4E');
        $this->addSql('DROP INDEX IDX_E19D9AD2DEDCBB4E');
        $this->addSql('ALTER TABLE service RENAME COLUMN service_category_id TO service_category_id_id');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT fk_e19d9ad294118ca8 FOREIGN KEY (service_category_id_id) REFERENCES service_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_e19d9ad294118ca8 ON service (service_category_id_id)');
    }
}
