<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241102230242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE attachments (id INT AUTO_INCREMENT NOT NULL, url LONGTEXT NOT NULL, type VARCHAR(10) NOT NULL, exhibitor_group_id INT NOT NULL, INDEX IDX_47C4FAD65C043938 (exhibitor_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE competition (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, archived TINYINT(1) NOT NULL, image LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE dashboard_article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, archived TINYINT(1) NOT NULL, image LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, address VARCHAR(255) NOT NULL, zip_code VARCHAR(5) NOT NULL, city VARCHAR(100) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, weez_event_id INT NOT NULL, archived TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, archived TINYINT(1) NOT NULL, image LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE exhibitor (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, exhibitor_group_id INT NOT NULL, UNIQUE INDEX UNIQ_B2A03D20E7927C74 (email), INDEX IDX_B2A03D205C043938 (exhibitor_group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE exhibitor_group (id INT AUTO_INCREMENT NOT NULL, group_name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, website TINYTEXT DEFAULT NULL, email_contact VARCHAR(180) NOT NULL, archived TINYINT(1) NOT NULL, event_id INT NOT NULL, UNIQUE INDEX UNIQ_3254E223F1A28EF7 (email_contact), INDEX IDX_3254E22371F7E88B (event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, fonction VARCHAR(255) NOT NULL, archived TINYINT(1) NOT NULL, image LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE attachments ADD CONSTRAINT FK_47C4FAD65C043938 FOREIGN KEY (exhibitor_group_id) REFERENCES exhibitor_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE exhibitor ADD CONSTRAINT FK_B2A03D205C043938 FOREIGN KEY (exhibitor_group_id) REFERENCES exhibitor_group (id)');
        $this->addSql('ALTER TABLE exhibitor_group ADD CONSTRAINT FK_3254E22371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attachments DROP FOREIGN KEY FK_47C4FAD65C043938');
        $this->addSql('ALTER TABLE exhibitor DROP FOREIGN KEY FK_B2A03D205C043938');
        $this->addSql('ALTER TABLE exhibitor_group DROP FOREIGN KEY FK_3254E22371F7E88B');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE attachments');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE dashboard_article');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE exhibitor');
        $this->addSql('DROP TABLE exhibitor_group');
        $this->addSql('DROP TABLE member');
    }
}
