<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160224163011 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_achievement ADD checked_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D4579D19306');
        $this->addSql('ALTER TABLE user_game ADD updated_at DATETIME DEFAULT NULL, ADD is_being_handled TINYINT(1) NOT NULL, ADD checked_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid)');
        $this->addSql('ALTER TABLE game_achievement ADD checked_at DATETIME NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_achievement DROP checked_at');
        $this->addSql('ALTER TABLE user_achievement DROP checked_at');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D4579D19306');
        $this->addSql('ALTER TABLE user_game DROP updated_at, DROP is_being_handled, DROP checked_at');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid) ON DELETE CASCADE');
    }
}
