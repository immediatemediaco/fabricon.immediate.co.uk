<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210228122408 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE person_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE talk_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE person (id INT NOT NULL, name VARCHAR(255) NOT NULL, is_moderator BOOLEAN NOT NULL, is_organiser BOOLEAN NOT NULL, is_speaker BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE talk (id INT NOT NULL, organiser_id INT DEFAULT NULL, moderator_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, duration VARCHAR(255) NOT NULL, q_and_aduration VARCHAR(255) DEFAULT NULL, slack_channel VARCHAR(255) DEFAULT NULL, slack_channel_url VARCHAR(255) DEFAULT NULL, teams_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9F24D5BBA0631C12 ON talk (organiser_id)');
        $this->addSql('CREATE INDEX IDX_9F24D5BBD0AFA354 ON talk (moderator_id)');
        $this->addSql('COMMENT ON COLUMN talk.duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('COMMENT ON COLUMN talk.q_and_aduration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('CREATE TABLE talk_person (talk_id INT NOT NULL, person_id INT NOT NULL, PRIMARY KEY(talk_id, person_id))');
        $this->addSql('CREATE INDEX IDX_7C0CD8056F0601D5 ON talk_person (talk_id)');
        $this->addSql('CREATE INDEX IDX_7C0CD805217BBB47 ON talk_person (person_id)');
        $this->addSql('ALTER TABLE talk ADD CONSTRAINT FK_9F24D5BBA0631C12 FOREIGN KEY (organiser_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE talk ADD CONSTRAINT FK_9F24D5BBD0AFA354 FOREIGN KEY (moderator_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE talk_person ADD CONSTRAINT FK_7C0CD8056F0601D5 FOREIGN KEY (talk_id) REFERENCES talk (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE talk_person ADD CONSTRAINT FK_7C0CD805217BBB47 FOREIGN KEY (person_id) REFERENCES person (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE talk DROP CONSTRAINT FK_9F24D5BBA0631C12');
        $this->addSql('ALTER TABLE talk DROP CONSTRAINT FK_9F24D5BBD0AFA354');
        $this->addSql('ALTER TABLE talk_person DROP CONSTRAINT FK_7C0CD805217BBB47');
        $this->addSql('ALTER TABLE talk_person DROP CONSTRAINT FK_7C0CD8056F0601D5');
        $this->addSql('DROP SEQUENCE person_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE talk_id_seq CASCADE');
        $this->addSql('DROP TABLE person');
        $this->addSql('DROP TABLE talk');
        $this->addSql('DROP TABLE talk_person');
    }
}
