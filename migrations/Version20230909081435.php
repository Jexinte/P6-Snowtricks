<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230909081435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD is_banner TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE trick DROP embed_url, DROP main_banner_file_path');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP is_banner');
        $this->addSql('ALTER TABLE trick ADD embed_url VARCHAR(255) DEFAULT NULL, ADD main_banner_file_path VARCHAR(255) NOT NULL');
    }
}
