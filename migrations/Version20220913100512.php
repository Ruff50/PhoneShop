<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220913100512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande_details (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, commande_id INT NOT NULL, produit_nom VARCHAR(255) NOT NULL, produit_prix DOUBLE PRECISION NOT NULL, produit_tva DOUBLE PRECISION NOT NULL, qte INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_849D792A4584665A (product_id), INDEX IDX_849D792A82EA2E54 (commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande_details ADD CONSTRAINT FK_849D792A4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE commande_details ADD CONSTRAINT FK_849D792A82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE commande_details');
        $this->addSql('ALTER TABLE product CHANGE tva tva DOUBLE PRECISION DEFAULT NULL, CHANGE image image TEXT DEFAULT NULL');
    }
}
