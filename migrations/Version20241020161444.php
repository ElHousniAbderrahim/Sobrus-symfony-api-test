<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020161444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_article (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(100) NOT NULL, publication_date DATETIME NOT NULL, creation_date DATETIME NOT NULL, content LONGTEXT NOT NULL, keywords JSON NOT NULL COMMENT \'(DC2Type:json)\', slug VARCHAR(255) NOT NULL, cover_picture_ref VARCHAR(255) NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_EECCB3E5F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_article ADD CONSTRAINT FK_EECCB3E5F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_article DROP FOREIGN KEY FK_EECCB3E5F675F31B');
        $this->addSql('DROP TABLE blog_article');
        $this->addSql('DROP TABLE user');
    }
}
