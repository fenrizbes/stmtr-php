<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160224183703 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_achievement (id BIGINT AUTO_INCREMENT NOT NULL, steamid BIGINT DEFAULT NULL, `key` VARCHAR(255) DEFAULT NULL, checked_at DATETIME DEFAULT NULL, INDEX IDX_3F68B664BCF81F98 (steamid), INDEX IDX_3F68B6648A90ABA9 (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_game (id BIGINT AUTO_INCREMENT NOT NULL, steamid BIGINT DEFAULT NULL, gameid INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, is_being_handled TINYINT(1) NOT NULL, checked_at DATETIME DEFAULT NULL, INDEX IDX_59AA7D45BCF81F98 (steamid), INDEX IDX_59AA7D4579D19306 (gameid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B6648A90ABA9 FOREIGN KEY (`key`) REFERENCES game_achievement (`key`)');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_achievement CHANGE gameid gameid INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_achievement');
        $this->addSql('DROP TABLE user_game');
        $this->addSql('ALTER TABLE game_achievement CHANGE gameid gameid INT NOT NULL');
    }
}
