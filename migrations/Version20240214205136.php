<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214205136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD datedebut DATE DEFAULT NULL, ADD datefin DATE DEFAULT NULL');
        
        // Set existing rows to a valid default date if necessary
        $this->addSql('UPDATE event SET datedebut = \'2022-01-01\' WHERE datedebut = \'0000-00-00\'');
        $this->addSql('UPDATE event SET datefin = \'2022-01-01\' WHERE datefin = \'0000-00-00\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL');
        
        // Drop the newly added columns
        $this->addSql('ALTER TABLE event DROP datedebut, DROP datefin');
    }
}
