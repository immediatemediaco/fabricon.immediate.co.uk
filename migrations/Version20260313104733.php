<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260313104733 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Replace single conference date field with startDate and endDate for date range support';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conference RENAME COLUMN date TO start_date');
        $this->addSql('ALTER TABLE conference ADD end_date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE conference ALTER COLUMN start_date SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE conference DROP end_date');
        $this->addSql('ALTER TABLE conference RENAME COLUMN start_date TO date');
        $this->addSql('ALTER TABLE conference ALTER COLUMN date DROP NOT NULL');
    }
}
