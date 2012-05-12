<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Créations des tables nécessaires au fonctionnement du module du gestionnaire 
 * de fichiers.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class Version20120329163519 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$this->addSql("CREATE TABLE zcov2_file (id BIGINT AUTO_INCREMENT primary key, user_id BIGINT, license_id BIGINT, thumbnail_id BIGINT, name VARCHAR(255) NOT NULL, extension VARCHAR(50) NOT NULL, path VARCHAR(255) NOT NULL, major_mime VARCHAR(20) NOT NULL, minor_mime VARCHAR(30) NOT NULL, type INT DEFAULT 0 NOT NULL, size INT NOT NULL, width INT, height INT, date DATETIME NOT NULL, edition_date DATETIME NOT NULL, quota_affected tinyint(1) NOT NULL DEFAULT 1) ENGINE = INNODB");
		$this->addSql("CREATE TABLE zcov2_file_license (id BIGINT AUTO_INCREMENT primary key, file_id BIGINT NOT NULL, license_id BIGINT NOT NULL, pseudo VARCHAR(255) NOT NULL, date DATETIME NOT NULL) ENGINE = INNODB");
		$this->addSql("CREATE TABLE zcov2_file_thumbnail (id BIGINT AUTO_INCREMENT primary key, file_id BIGINT, width INT, height INT, size INT NOT NULL, path VARCHAR(255) NOT NULL) ENGINE = INNODB");
		$this->addSql("CREATE TABLE zcov2_file_usage (id BIGINT AUTO_INCREMENT primary key, file_id BIGINT NOT NULL, thumbnail_id BIGINT, part BIGINT NOT NULL, entity_class VARCHAR(255) NOT NULL, entity_id BIGINT NOT NULL) ENGINE = INNODB");
		$this->addSql("CREATE TABLE zcov2_license (id BIGINT AUTO_INCREMENT primary key, name VARCHAR(255), logo_url VARCHAR(255), summary_url VARCHAR(255), fulltext_url VARCHAR(255), keep_author TINYINT(1), keep_same_license TINYINT(1), can_be_modified TINYINT(1), commercial_use_allowed TINYINT(1)) ENGINE = INNODB");
	}

	public function down(OutputInterface $output)
	{
		$this->addSql("DROP TABLE zcov2_file");
		$this->addSql("DROP TABLE zcov2_file_license");
		$this->addSql("DROP TABLE zcov2_file_thumbnail");
		$this->addSql("DROP TABLE zcov2_file_usage");
		$this->addSql("DROP TABLE zcov2_license");
	}
}