<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260918113231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD customer_first_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD customer_last_name VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD customer_email VARCHAR(180) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking ADD customer_phone VARCHAR(40) DEFAULT NULL');
        $this->addSql('ALTER TABLE booking_request ADD phone VARCHAR(40) DEFAULT NULL');
        $this->addSql("UPDATE booking_request SET phone = '' WHERE phone IS NULL");
        $this->addSql('ALTER TABLE booking_request ALTER phone SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP customer_first_name');
        $this->addSql('ALTER TABLE booking DROP customer_last_name');
        $this->addSql('ALTER TABLE booking DROP customer_email');
        $this->addSql('ALTER TABLE booking DROP customer_phone');
        $this->addSql('ALTER TABLE booking_request DROP phone');
    }
}
