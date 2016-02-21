<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160220175054 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_game (steamid BIGINT NOT NULL, gameid INT NOT NULL, INDEX IDX_59AA7D45BCF81F98 (steamid), INDEX IDX_59AA7D4579D19306 (gameid), PRIMARY KEY(steamid, gameid)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D45BCF81F98 FOREIGN KEY (steamid) REFERENCES user (steamid)');
        $this->addSql('ALTER TABLE user_game ADD CONSTRAINT FK_59AA7D4579D19306 FOREIGN KEY (gameid) REFERENCES game (gameid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_game');
    }
}
