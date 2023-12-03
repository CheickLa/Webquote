<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231203132123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '(tmp) Remove quote and invoice tables to focus on the rest';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quote_id_seq CASCADE');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT fk_6b71cbf42989f1fd');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_90651744db805178');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE invoice');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE quote (id INT NOT NULL, invoice_id INT NOT NULL, date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_6b71cbf42989f1fd ON quote (invoice_id)');
        $this->addSql('CREATE TABLE invoice (id INT NOT NULL, quote_id INT NOT NULL, date DATE NOT NULL, amount DOUBLE PRECISION NOT NULL, status BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_90651744db805178 ON invoice (quote_id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT fk_6b71cbf42989f1fd FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_90651744db805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
