<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160226195510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_achievement (id BIGINT AUTO_INCREMENT NOT NULL, steamid BIGINT DEFAULT NULL, checked_at DATETIME DEFAULT NULL, `game_achievement_id` BIGINT DEFAULT NULL, INDEX IDX_3F68B664BCF81F98 (steamid), INDEX IDX_3F68B664974E4C9F (`game_achievement_id`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_achievement (id BIGINT AUTO_INCREMENT NOT NULL, gameid INT DEFAULT NULL, `key` VARCHAR(255) NOT NULL, percentage NUMERIC(7, 4) NOT NULL, checked_at DATETIME DEFAULT NULL, INDEX IDX_48263DF379D19306 (gameid), INDEX achievement_key_index (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664974E4C9F FOREIGN KEY (`game_achievement_id`) REFERENCES game_achievement (`id`) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_achievement ADD CONSTRAINT FK_48263DF379D19306 FOREIGN KEY (gameid) REFERENCES game (gameid) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664974E4C9F');
        $this->addSql('DROP TABLE user_achievement');
        $this->addSql('DROP TABLE game_achievement');
    }
}
