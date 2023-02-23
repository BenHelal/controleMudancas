<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222131600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE departemant_mudancass DROP FOREIGN KEY FK_DFF9C4BEEC585363');
        $this->addSql('ALTER TABLE departemant_mudancass DROP FOREIGN KEY FK_DFF9C4BE5768A208');
        $this->addSql('ALTER TABLE departemant_process DROP FOREIGN KEY FK_3434BFCB7EC2F574');
        $this->addSql('ALTER TABLE departemant_process DROP FOREIGN KEY FK_3434BFCB217BBB47');
        $this->addSql('ALTER TABLE departemant_process DROP FOREIGN KEY FK_3434BFCB5768A208');
        $this->addSql('DROP TABLE departemant_mudancas');
        $this->addSql('DROP TABLE departemant_mudancass');
        $this->addSql('DROP TABLE departemant_process');
        $this->addSql('DROP TABLE nansen');
        $this->addSql('DROP TABLE process_departemant');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE departemant_mudancas (departemant_id INT NOT NULL, mudancas_id INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departemant_mudancass (id INT AUTO_INCREMENT NOT NULL, mudancas_id INT DEFAULT NULL, departemant_id INT DEFAULT NULL, INDEX IDX_DFF9C4BE5768A208 (departemant_id), INDEX IDX_DFF9C4BEEC585363 (mudancas_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE departemant_process (id INT AUTO_INCREMENT NOT NULL, process_id INT DEFAULT NULL, departemant_id INT DEFAULT NULL, person_id INT DEFAULT NULL, comment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_3434BFCB217BBB47 (person_id), INDEX IDX_3434BFCB7EC2F574 (process_id), INDEX IDX_3434BFCB5768A208 (departemant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE nansen (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE process_departemant (process_id INT NOT NULL, departemant_id INT NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE departemant_mudancass ADD CONSTRAINT FK_DFF9C4BEEC585363 FOREIGN KEY (mudancas_id) REFERENCES mudancas (id)');
        $this->addSql('ALTER TABLE departemant_mudancass ADD CONSTRAINT FK_DFF9C4BE5768A208 FOREIGN KEY (departemant_id) REFERENCES departemant (id)');
        $this->addSql('ALTER TABLE departemant_process ADD CONSTRAINT FK_3434BFCB7EC2F574 FOREIGN KEY (process_id) REFERENCES process (id)');
        $this->addSql('ALTER TABLE departemant_process ADD CONSTRAINT FK_3434BFCB217BBB47 FOREIGN KEY (person_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE departemant_process ADD CONSTRAINT FK_3434BFCB5768A208 FOREIGN KEY (departemant_id) REFERENCES departemant (id)');
    }
}
