<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160226183717 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B6648A90ABA9');
        $this->addSql('DROP INDEX idx_3f68b6648a90aba9 ON user_achievement');
        $this->addSql('CREATE INDEX IDX_3F68B6644E645A7E ON user_achievement (`key`)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B6644E645A7E FOREIGN KEY (`key`) REFERENCES game_achievement (`key`)');
        $this->addSql('CREATE INDEX updated_at_index ON game (updated_at)');
        $this->addSql('CREATE INDEX updated_at_index ON user_game (updated_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX updated_at_index ON game');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B6644E645A7E');
        $this->addSql('DROP INDEX idx_3f68b6644e645a7e ON user_achievement');
        $this->addSql('CREATE INDEX IDX_3F68B6648A90ABA9 ON user_achievement (`key`)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B6648A90ABA9 FOREIGN KEY (`key`) REFERENCES game_achievement (`key`)');
        $this->addSql('DROP INDEX updated_at_index ON user_game');
    }
}
