<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230315165154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Acceslog ADD state_code VARCHAR(255) DEFAULT NULL, ADD state_name VARCHAR(255) DEFAULT NULL, ADD area_code VARCHAR(255) DEFAULT NULL, ADD dma_code VARCHAR(255) DEFAULT NULL, ADD country_code VARCHAR(255) DEFAULT NULL, ADD country_name VARCHAR(255) DEFAULT NULL, ADD continent_code VARCHAR(255) DEFAULT NULL, ADD continent_name VARCHAR(255) DEFAULT NULL, ADD location_radius VARCHAR(255) DEFAULT NULL, ADD time_zone VARCHAR(255) DEFAULT NULL, DROP stateCode, DROP StateName, DROP areaCode, DROP dmaCode, DROP countryCode, DROP countryName, DROP continentCode, DROP continentName, DROP locationRadius, DROP timeZone, CHANGE ipAdress ip_adress VARCHAR(255) NOT NULL, CHANGE dateCreation date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EB19DEAEEE FOREIGN KEY (mud_id) REFERENCES mudancas (id)');
        $this->addSql('ALTER TABLE Client CHANGE cliId cli_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE email ADD CONSTRAINT FK_E7927C7419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_E7927C7419EB6921 ON email (client_id)');
        $this->addSql('ALTER TABLE mudancas ADD client_id INT DEFAULT NULL, ADD desc_client VARCHAR(1024) DEFAULT NULL, CHANGE start_mudancas start_mudancas DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE mudancas ADD CONSTRAINT FK_8329091719EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_8329091719EB6921 ON mudancas (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE acceslog ADD stateCode VARCHAR(255) DEFAULT NULL, ADD StateName VARCHAR(255) DEFAULT NULL, ADD areaCode VARCHAR(255) DEFAULT NULL, ADD dmaCode VARCHAR(255) DEFAULT NULL, ADD countryCode VARCHAR(255) DEFAULT NULL, ADD countryName VARCHAR(255) DEFAULT NULL, ADD continentCode VARCHAR(255) DEFAULT NULL, ADD continentName VARCHAR(255) DEFAULT NULL, ADD locationRadius VARCHAR(255) DEFAULT NULL, ADD timeZone VARCHAR(255) DEFAULT NULL, DROP state_code, DROP state_name, DROP area_code, DROP dma_code, DROP country_code, DROP country_name, DROP continent_code, DROP continent_name, DROP location_radius, DROP time_zone, CHANGE ip_adress ipAdress VARCHAR(255) NOT NULL, CHANGE date_creation dateCreation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE client CHANGE cli_id cliId VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EB19EB6921');
        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EB19DEAEEE');
        $this->addSql('ALTER TABLE email DROP FOREIGN KEY FK_E7927C7419EB6921');
        $this->addSql('DROP INDEX IDX_E7927C7419EB6921 ON email');
        $this->addSql('ALTER TABLE mudancas DROP FOREIGN KEY FK_8329091719EB6921');
        $this->addSql('DROP INDEX IDX_8329091719EB6921 ON mudancas');
        $this->addSql('ALTER TABLE mudancas DROP client_id, DROP desc_client, CHANGE start_mudancas start_mudancas DATE DEFAULT NULL');
    }
}
