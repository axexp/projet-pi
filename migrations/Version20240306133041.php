<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306133041 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, idpanier_id INT DEFAULT NULL, etat VARCHAR(255) NOT NULL, total DOUBLE PRECISION NOT NULL, adresse VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6EEAA67D89663B89 (idpanier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id INT AUTO_INCREMENT NOT NULL, total DOUBLE PRECISION NOT NULL, username VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panierproduct (idpanier_id INT NOT NULL, idproduct_id INT NOT NULL, qt INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_9F80013089663B89 (idpanier_id), INDEX IDX_9F800130882D7B60 (idproduct_id), PRIMARY KEY(idpanier_id, idproduct_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D89663B89 FOREIGN KEY (idpanier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panierproduct ADD CONSTRAINT FK_9F80013089663B89 FOREIGN KEY (idpanier_id) REFERENCES panier (id)');
        $this->addSql('ALTER TABLE panierproduct ADD CONSTRAINT FK_9F800130882D7B60 FOREIGN KEY (idproduct_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE event CHANGE nb_places_r nb_places_r INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D89663B89');
        $this->addSql('ALTER TABLE panierproduct DROP FOREIGN KEY FK_9F80013089663B89');
        $this->addSql('ALTER TABLE panierproduct DROP FOREIGN KEY FK_9F800130882D7B60');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE panierproduct');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE event CHANGE nb_places_r nb_places_r INT DEFAULT NULL');
    }
}
