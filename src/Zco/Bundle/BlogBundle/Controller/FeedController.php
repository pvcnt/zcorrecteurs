<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Zco\Bundle\BlogBundle\Controller;

use Zco\Bundle\BlogBundle\Feed\FeedController as BaseFeedController;
use Zco\Bundle\ContentBundle\Domain\CategoryDAO;

/**
 * Fichier générant le flux du blog (global ou par catégorie).
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class FeedController extends BaseFeedController
{
	protected $link = URL_SITE;
	protected $itemAuthorEmail = 'contact@zcorrecteurs.fr';

	protected function getTitle($object)
	{
		if(!is_null($object))
			return $object['cat_nom'];
		else
			return 'zCorrecteurs.fr';
	}

	protected function getDescription($object)
	{
		if(!is_null($object))
			return $object['cat_description'];
		else
			return 'Des questions sur l\'orthographe ? Envie d\'en savoir plus sur notre belle langue française ? Vous êtes au bon endroit !';
	}

	protected function getItems($object)
	{
		$dbh = \Doctrine_Manager::connection()->getDbh();

		// Récupération des billets
		$ordre = 'DESC';
		$stmt = $dbh->prepare('SELECT blog_id, blog_image, blog_date_publication, '
			.'version_titre, version_sous_titre, version_intro, version_texte, '
			.'blog_lien_topic, blog_commentaires, UNIX_TIMESTAMP(blog_date_publication) as pubtime '
			.'FROM zcov2_blog '
			.'LEFT JOIN zcov2_blog_versions ON blog_id_version_courante = version_id '
			.'WHERE blog_date_publication <= NOW() '
			.'AND blog_etat = '.BLOG_VALIDE.' '
			.($object ? 'AND blog_id_categorie=:id ': '')
			.'ORDER BY blog_date_publication '.$ordre.' '
			.'LIMIT 0, 5');
		$object && $stmt->bindParam(':id', $object['cat_id']);
		$stmt->execute();
		$billets_ = $stmt->fetchAll();
		$ids = $billets = array();

		$this->latest = array('pubtime' => 0);
		foreach($billets_ as &$billet)
		{
			// On récupère les ids, et on en profite pour réindexer
			$ids[] = $billet['blog_id'];
			$billet['auteurs'] = array();
			$billets[$billet['blog_id']] = &$billet;
			if($billet['pubtime'] > $this->latest['pubtime'])
				$this->latest = $billet;
		}

		// Récupération des auteurs des billets
        if (count($ids) > 0) {
            $stmt = $dbh->prepare('SELECT blog_id, utilisateur_id, utilisateur_pseudo, '
                . 'CASE WHEN utilisateur_afficher_mail = 1 THEN utilisateur_email '
                . 'ELSE :contact END AS utilisateur_email '
                . 'FROM zcov2_blog_auteurs '
                . 'LEFT JOIN zcov2_utilisateurs ON auteur_id_utilisateur = utilisateur_id '
                . 'LEFT JOIN zcov2_blog ON auteur_id_billet = blog_id '
                . 'WHERE auteur_statut > 1 AND blog_id IN (' . implode(',', $ids) . ')');
            $stmt->bindParam(':contact', $this->itemAuthorEmail);
            $stmt->execute();
            $auteurs = $stmt->fetchAll();

            // Ajout des auteurs à chaque billet
            foreach ($auteurs as &$auteur)
                $billets[$auteur['blog_id']]['auteurs'][] = &$auteur;
        }

		return $billets;
	}

	protected function getObject()
	{

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$categorie = CategoryDAO::InfosCategorie($_GET['id']);
			return !empty($categorie) ? $categorie : null;
		}
		return null;
	}

	protected function getItemTitle($item)
	{
		return htmlspecialchars($item['version_titre']).
			(!empty($item['version_sous_titre']) ?
			' / '.htmlspecialchars($item['version_sous_titre']) : '');
	}

	protected function getItemDescription($item)
	{
		return $this->get('zco_parser.parser')->parse($item['version_intro'], array(
			'files.entity_id' => $item['blog_id'],
			'files.entity_class' => 'Blog',
			'files.part' => 1,
		));
	}

	protected function getItemContent($item)
	{
		return $this->get('zco_parser.parser')->parse($item['version_texte'], array(
			'files.entity_id' => $item['blog_id'],
			'files.entity_class' => 'Blog',
			'files.part' => 2,
		));
	}

	protected function getItemLink($item)
	{
		return $this->getItemGuid($item);
	}

	protected function getItemGuid($item)
	{
		return URL_SITE.'/blog/billet-'.$item['blog_id'].'-'.rewrite($item['version_titre']).'.html';
	}

	protected function getItemAuthors($item)
	{
		$aut = array();
		foreach($item['auteurs'] as &$a)
			$aut[] = array(htmlspecialchars($a['utilisateur_pseudo']), $a['utilisateur_email']);
		return $aut;
	}

	protected function getItemComments($item)
	{
		if(!empty($item['blog_lien_topic']) && $item['blog_commentaires'] == COMMENTAIRES_TOPIC)
		{
			return URL_SITE.'/'.$item['blog_lien_topic'];
		}
		elseif($item['blog_commentaires'] == COMMENTAIRES_OK)
		{
			return URL_SITE.'/blog/billet-'.$item['blog_id'].'-'.rewrite($item['version_titre']).'.html#commentaires';
		}
	}

	protected function getItemEnclosureUrl($item)
	{
		return URL_SITE.'/'.htmlspecialchars($item['blog_image']);
	}
}
