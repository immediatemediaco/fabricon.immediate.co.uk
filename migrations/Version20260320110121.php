<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260320110121 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move track descriptions from Settings to Conference; restructure Slot from paired-track to per-track rows with date field';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add new columns to conference (nullable initially)
        $this->addSql('ALTER TABLE conference ADD track1_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE conference ADD track2_description TEXT DEFAULT NULL');

        // Step 2: Copy track descriptions from settings row (id=1) to all conference rows
        $this->addSql('UPDATE conference SET track1_description = (SELECT track1_description FROM settings WHERE id = 1), track2_description = (SELECT track2_description FROM settings WHERE id = 1)');

        // Step 3: Drop track descriptions from settings
        $this->addSql('ALTER TABLE settings DROP track1_description');
        $this->addSql('ALTER TABLE settings DROP track2_description');

        // Step 4: Drop old slot foreign key constraints and indexes
        $this->addSql('ALTER TABLE slot DROP CONSTRAINT fk_ac0e20672b52406b');
        $this->addSql('ALTER TABLE slot DROP CONSTRAINT fk_ac0e206739e7ef85');
        $this->addSql('DROP INDEX idx_ac0e20672b52406b');
        $this->addSql('DROP INDEX idx_ac0e206739e7ef85');

        // Step 5: Add new slot columns as nullable
        $this->addSql('ALTER TABLE slot ADD talk_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE slot ADD track SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE slot ADD date DATE DEFAULT NULL');

        // Step 6: Transform slot data — insert per-track rows from paired-track rows
        // For slots with track1: insert a new track=1 row
        $this->addSql('
            INSERT INTO slot (id, conference_id, start_time, end_time, talk_id, track, date, break_details)
            SELECT nextval(\'slot_id_seq\'), s.conference_id, s.start_time, s.end_time, s.track1_id, 1,
                   COALESCE((SELECT start_date FROM conference WHERE id = s.conference_id), CURRENT_DATE),
                   NULL
            FROM slot s
            WHERE s.track1_id IS NOT NULL
        ');
        // For slots with track2: insert a new track=2 row
        $this->addSql('
            INSERT INTO slot (id, conference_id, start_time, end_time, talk_id, track, date, break_details)
            SELECT nextval(\'slot_id_seq\'), s.conference_id, s.start_time, s.end_time, s.track2_id, 2,
                   COALESCE((SELECT start_date FROM conference WHERE id = s.conference_id), CURRENT_DATE),
                   NULL
            FROM slot s
            WHERE s.track2_id IS NOT NULL
        ');
        // For break slots (no track1 or track2): update in place with date from conference
        $this->addSql('
            UPDATE slot SET
                date = COALESCE((SELECT start_date FROM conference WHERE id = slot.conference_id), CURRENT_DATE),
                track = NULL
            WHERE track1_id IS NULL AND track2_id IS NULL
        ');
        // Delete original rows that had track1 or track2 (they have been replaced by inserts above)
        $this->addSql('DELETE FROM slot WHERE track1_id IS NOT NULL OR track2_id IS NOT NULL');

        // Step 7: Now that all rows have date populated, set NOT NULL constraint
        $this->addSql('ALTER TABLE slot ALTER COLUMN date SET NOT NULL');

        // Step 8: Drop the old paired-track columns
        $this->addSql('ALTER TABLE slot DROP track1_id');
        $this->addSql('ALTER TABLE slot DROP track2_id');

        // Step 9: Add FK and index for new talk_id column
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT FK_AC0E20676F0601D5 FOREIGN KEY (talk_id) REFERENCES talk (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_AC0E20676F0601D5 ON slot (talk_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');

        // Restore track descriptions to settings
        $this->addSql('ALTER TABLE settings ADD track1_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE settings ADD track2_description TEXT DEFAULT NULL');
        $this->addSql('UPDATE settings SET track1_description = (SELECT track1_description FROM conference WHERE id = (SELECT current_conference_id FROM settings LIMIT 1)), track2_description = (SELECT track2_description FROM conference WHERE id = (SELECT current_conference_id FROM settings LIMIT 1))');
        $this->addSql('ALTER TABLE conference DROP track1_description');
        $this->addSql('ALTER TABLE conference DROP track2_description');

        // Restore slot to paired-track model
        $this->addSql('ALTER TABLE slot DROP CONSTRAINT FK_AC0E20676F0601D5');
        $this->addSql('DROP INDEX IDX_AC0E20676F0601D5');
        $this->addSql('ALTER TABLE slot ADD track1_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE slot ADD track2_id INT DEFAULT NULL');

        // Merge per-track rows back: pair track1 and track2 slots at same time
        // Copy talk_id into track1_id/track2_id on break rows first
        $this->addSql('UPDATE slot SET track1_id = talk_id WHERE track = 1');
        $this->addSql('UPDATE slot SET track2_id = talk_id WHERE track = 2');

        // For paired rows: for each track=1 row, find matching track=2 row (same conference, start_time) and copy track2_id
        $this->addSql('
            UPDATE slot t1 SET track2_id = (
                SELECT t2.talk_id FROM slot t2
                WHERE t2.conference_id = t1.conference_id
                  AND t2.start_time = t1.start_time
                  AND t2.track = 2
                LIMIT 1
            )
            WHERE t1.track = 1
        ');

        // Delete track=2 rows (they are now merged into track=1 rows)
        $this->addSql('DELETE FROM slot WHERE track = 2');

        $this->addSql('ALTER TABLE slot DROP track');
        $this->addSql('ALTER TABLE slot DROP date');
        $this->addSql('ALTER TABLE slot DROP talk_id');

        $this->addSql('ALTER TABLE slot ADD CONSTRAINT fk_ac0e20672b52406b FOREIGN KEY (track2_id) REFERENCES talk (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE slot ADD CONSTRAINT fk_ac0e206739e7ef85 FOREIGN KEY (track1_id) REFERENCES talk (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_ac0e20672b52406b ON slot (track2_id)');
        $this->addSql('CREATE INDEX idx_ac0e206739e7ef85 ON slot (track1_id)');
    }
}
