<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203110819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove old company table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf419eb6921');
        $this->addSql('ALTER TABLE service_category DROP CONSTRAINT fk_ff3a42fc979b1ad6');
        $this->addSql('DROP SEQUENCE company_id_seq CASCADE');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP INDEX idx_6b71cbf419eb6921');
        $this->addSql('ALTER TABLE quote RENAME COLUMN client_id TO invoice_id');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF42989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6B71CBF42989F1FD ON quote (invoice_id)');
        $this->addSql('DROP INDEX idx_ff3a42fc979b1ad6');
        $this->addSql('ALTER TABLE service_category DROP company_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE company_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company (id INT NOT NULL, siret VARCHAR(14) NOT NULL, name VARCHAR(255) NOT NULL, legal_status VARCHAR(255) NOT NULL, sector VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, sales DOUBLE PRECISION NOT NULL, role BOOLEAN NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF42989F1FD');
        $this->addSql('DROP INDEX UNIQ_6B71CBF42989F1FD');
        $this->addSql('ALTER TABLE quote RENAME COLUMN invoice_id TO client_id');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf419eb6921 FOREIGN KEY (client_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_6b71cbf419eb6921 ON quote (client_id)');
        $this->addSql('ALTER TABLE service_category ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT fk_ff3a42fc979b1ad6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ff3a42fc979b1ad6 ON service_category (company_id)');
    }
}
