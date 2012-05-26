<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

/**
 * Fichier générant le flux des derniers messages du forum.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class MessagesFluxAction extends Feed
{
	protected $link = URL_SITE;
	protected $lifetime = 2400;			//40 minutes

	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		return parent::execute();
	}

	protected function getObject()
	{
		$categorie = null;
		if (!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$categorie = InfosCategorie($_GET['id'], /* droits */ true);
			if (!$categorie)
			{
				throw new Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
			}
		}
		return $categorie;
	}

	protected function getTitle($object)
	{
		return !is_null($object) ?
			 'Derniers messages du forum '.htmlspecialchars($object['cat_nom']) :
			'Derniers messages du forum zCorrecteurs.fr';
	}

	protected function getDescription($object)
	{
		return !is_null($object) ?
			htmlspecialchars($object['cat_description']) : 'Participez aux sujets de discussion de notre forum';
	}

	protected function getItems($object)
	{
		include(__DIR__.'/../modeles/messages.php');
		$this->latest = array('pubtime' => 0);
		
		if ($object)
		{
			$categories = ListerEnfants($object['cat_id'], /* inclure */ true, /* droits */ true);

			$messages = array();
			foreach ($categories as $cat)
			{
				$m = ListerMessagesFlux(GROUPE_VISITEURS, $cat['cat_id']);
				$messages = array_merge($messages, $m[1]);
			}

			if ($messages)
			{
				usort($messages, function($a, $b) {
					return $a['message_id'] < $b['message_id'] ? 1 : -1;
				});

				$this->latest = $messages[0];
			}
			
			return $messages;
		}

		$messages = ListerMessagesFlux(GROUPE_VISITEURS, null);
		$this->latest = $messages[0];
		return $messages[1];
	}

	protected function getItemTitle($item)
	{
		return $item['sujet_titre'];
	}

	protected function getItemContent($item)
	{
		return $this->get('zco_parser.parser')->parse($item['message_texte'], array(
			'core.anchor_prefix' => $item['message_id'],
			'files.entity_id' => $item['message_id'],
			'files.entity_class' => 'ForumMessage',
		));
	}

	protected function getItemLink($item)
	{
		return URL_SITE.'/forum/sujet-'.$item['sujet_id'].'-'.$item['message_id'].'.html';
	}

	protected function getItemAuthorName($item)
	{
		return htmlspecialchars($item['utilisateur_pseudo']);
	}

	protected function getItemAuthorEmail($item)
	{
		return 'contact@zcorrecteurs.fr';
	}
}
