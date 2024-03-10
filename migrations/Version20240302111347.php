<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302111347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_item ADD advert_id INT NOT NULL, DROP description, DROP url');
        $this->addSql('ALTER TABLE carousel_item ADD CONSTRAINT FK_33577A28D07ECCB6 FOREIGN KEY (advert_id) REFERENCES advert (id)');
        $this->addSql('CREATE INDEX IDX_33577A28D07ECCB6 ON carousel_item (advert_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carousel_item DROP FOREIGN KEY FK_33577A28D07ECCB6');
        $this->addSql('DROP INDEX IDX_33577A28D07ECCB6 ON carousel_item');
        $this->addSql('ALTER TABLE carousel_item ADD description LONGTEXT DEFAULT NULL, ADD url VARCHAR(255) DEFAULT NULL, DROP advert_id');
    }
}
