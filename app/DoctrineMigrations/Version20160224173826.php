<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160224173826 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_achievement CHANGE checked_at checked_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user_game CHANGE checked_at checked_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE game_achievement CHANGE checked_at checked_at DATETIME DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_achievement CHANGE checked_at checked_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_achievement CHANGE checked_at checked_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_game CHANGE checked_at checked_at DATETIME NOT NULL');
    }
}
