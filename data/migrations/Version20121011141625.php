<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version20121011141625 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$this->addSql("CREATE TABLE zcov2_node (
			id BIGINT AUTO_INCREMENT PRIMARY KEY, 
			title VARCHAR(255) NOT NULL, 
			permalink VARCHAR(255), 
			published datetime, 
			created DATETIME NOT NULL, 
			updated DATETIME,
			class_name VARCHAR(100) NOT NULL,
			class_id INTEGER NOT NULL
		) ENGINE = INNODB");

		$stmt = $this->dbh->prepare('SELECT * 
			FROM zcov2_blog 
			LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id');
		$stmt->execute();
		$nodes = $stmt->fetchAll();
		foreach ($nodes as $node)
		{
			$this->addSql('INSERT INTO zcov2_node(title, permalink, published, created, updated, class_name, class_id) '.
				'VALUES('.$this->dbh->quote($node['version_titre']).', "/blog/billet-'.$node['blog_id'].'-'.rewrite($node['version_titre']).'.html", '.
				($node['blog_date_publication'] && $node['blog_date_publication'] != '0000-00-00 00:00:00' ? '"'.$node['blog_date_publication'].'"' : 'NULL').', '.
				'"'.$node['blog_date'].'", '.
				($node['blog_date_edition'] && $node['blog_date_edition'] != '0000-00-00 00:00:00' ? '"'.$node['blog_date_edition'].'"' : '').', '.
				'"Blog", '.$node['blog_id'].')');
		}
	}

	public function down(OutputInterface $output)
	{
		$this->addSql("DROP TABLE zcov2_node");
	}
}