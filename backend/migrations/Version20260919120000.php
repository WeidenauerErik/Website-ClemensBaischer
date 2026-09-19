<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260919120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optional pickup/return time (HH:MM) to booking and booking_request.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking ADD start_time VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD end_time VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking_request ADD start_time VARCHAR(5) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking_request ADD end_time VARCHAR(5) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE booking DROP start_time');
        $this->addSql('ALTER TABLE booking DROP end_time');
        $this->addSql('ALTER TABLE booking_request DROP start_time');
        $this->addSql('ALTER TABLE booking_request DROP end_time');
    }
}
