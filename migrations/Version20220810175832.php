<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220810175832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discipline ADD teacher_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3F41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('CREATE INDEX IDX_75BEEE3F41807E1D ON discipline (teacher_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3F41807E1D');
        $this->addSql('DROP INDEX IDX_75BEEE3F41807E1D ON discipline');
        $this->addSql('ALTER TABLE discipline DROP teacher_id');
    }
}
