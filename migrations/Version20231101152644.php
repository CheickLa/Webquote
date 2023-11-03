<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231101152644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE service_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, quote_id_id INT NOT NULL, date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9065174472BB1336 ON invoice (quote_id_id)');
        $this->addSql('CREATE TABLE quote (id INT NOT NULL, client_id_id INT NOT NULL, date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B71CBF4DC2902E0 ON quote (client_id_id)');
        $this->addSql('CREATE TABLE service (id INT NOT NULL, service_category_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E19D9AD294118CA8 ON service (service_category_id_id)');
        $this->addSql('CREATE TABLE service_category (id INT NOT NULL, company_id_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF3A42FC38B53C32 ON service_category (company_id_id)');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174472BB1336 FOREIGN KEY (quote_id_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4DC2902E0 FOREIGN KEY (client_id_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD294118CA8 FOREIGN KEY (service_category_id_id) REFERENCES service_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FC38B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quote_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE service_category_id_seq CASCADE');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174472BB1336');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF4DC2902E0');
        $this->addSql('ALTER TABLE service DROP CONSTRAINT FK_E19D9AD294118CA8');
        $this->addSql('ALTER TABLE service_category DROP CONSTRAINT FK_FF3A42FC38B53C32');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_category');
    }
}
