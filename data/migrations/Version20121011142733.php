<?php

use Zco\Bundle\Doctrine1Bundle\Migrations\AbstractMigration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe de migration auto-générée. Modifiez-la selon vos besoins !
 */
class Version20121011142733 extends AbstractMigration
{
	public function up(OutputInterface $output)
	{
		$stmt = $this->dbh->prepare('SELECT * FROM zcov2_node WHERE class_name = "Blog"');
		$stmt->execute();
		$nodes = $stmt->fetchAll();
		$nodesPk = array();
		foreach ($nodes as $node)
		{
			$nodesPk[$node['class_id']] = $node['id'];
		}

		$this->addSql("CREATE TABLE if not exists zcov2_comment (
			id BIGINT AUTO_INCREMENT PRIMARY KEY, 
			node_id BIGINT NOT NULL, user_id BIGINT, 
			modifier_id BIGINT, 
			ip BIGINT NOT NULL, 
			content text NOT NULL, 
			created DATETIME NOT NULL, 
			updated DATETIME
		) ENGINE = INNODB");

		$stmt = $this->dbh->prepare('SELECT * FROM zcov2_blog_commentaires');
		$stmt->execute();
		$comments = $stmt->fetchAll();
		foreach ($comments as $comment)
		{
			$this->addSql(
				'INSERT INTO zcov2_comment(node_id, user_id, modifier_id, ip, content, created, updated) '.
				'VALUES('.$nodesPk[$comment['commentaire_id_billet']].', '.$comment['commentaire_id_utilisateur'].', '.
				($comment['commentaire_id_edite'] ?: 'NULL').', '.$comment['commentaire_ip'].', '.
				$this->dbh->quote($comment['commentaire_texte']).', "'.$comment['commentaire_date'].'", '.
				($comment['commentaire_edite_date'] && $comment['commentaire_edite_date'] != '0000-00-00 00:00:00' ? '"'.$comment['commentaire_edite_date'].'"' : 'NULL').')'
			);
		}
	}

	public function down(OutputInterface $output)
	{
		$this->addSql("DROP TABLE zcov2_comment");
	}
}