<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230921171418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C3675E82E');
        $this->addSql('DROP INDEX IDX_6A2CA10C3675E82E ON media');
        $this->addSql('ALTER TABLE media DROP id_trick');
        $this->addSql('ALTER TABLE trick ADD media_id INT NOT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id)');
        $this->addSql('CREATE INDEX IDX_D8F0A91EEA9FDD75 ON trick (media_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media ADD id_trick INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C3675E82E FOREIGN KEY (id_trick) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C3675E82E ON media (id_trick)');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EEA9FDD75');
        $this->addSql('DROP INDEX IDX_D8F0A91EEA9FDD75 ON trick');
        $this->addSql('ALTER TABLE trick DROP media_id');
    }
}
