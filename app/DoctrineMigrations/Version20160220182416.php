<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160220182416 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D4579D19306');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D45BCF81F98');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_achievement DROP FOREIGN KEY FK_48263DF379D19306');
        $this->addSql('ALTER TABLE game_achievement ADD CONSTRAINT FK_48263DF379D19306 FOREIGN KEY (gameid) REFERENCES game (gameid) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_achievement DROP FOREIGN KEY FK_48263DF379D19306');
        $this->addSql('ALTER TABLE game_achievement ADD CONSTRAINT FK_48263DF379D19306 FOREIGN KEY (gameid) REFERENCES game (gameid)');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D45BCF81F98');
        $this->addSql('ALTER TABLE user_game DROP FOREIGN KEY FK_59AA7D4579D19306');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid)');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid)');
    }
}
