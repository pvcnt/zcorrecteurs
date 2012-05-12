<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Liste des messages d'un membre.
 *
 * @author mwsaz@zcorrecteurs.fr
 */
class MessagesAction extends ForumActions
{
	public function execute()
	{
		if (empty($_GET['id']))
			return redirect(353, 'index.html', MSG_ERROR);

		$membre = Doctrine_Query::create()
			->select('u.pseudo, u.avatar, u.sexe, g.nom, g.class, g.logo, g.logo_feminin')
			->from('Utilisateur u')
			->leftJoin('u.Groupe g')
			->where('u.id = ?', $_GET['id'])
			->execute()
			->offsetGet(0);
		if (!$membre)
			return redirect(353, 'index.html', MSG_ERROR);

		zCorrecteurs::VerifierFormatageUrl($membre->pseudo, true, false, 1);
		Page::$titre = 'Messages de '.htmlspecialchars($membre->pseudo);
		fil_ariane(Page::$titre);

		$_GET['p'] = (int)$_GET['p'] > 0 ? (int)$_GET['p'] : 1;
		if($_GET['p'] > 1)
			Page::$titre .= ' - Page '.(int)$_GET['p'];

		$categoriesAutorisees = array();
		foreach (ListerEnfants(GetIDCategorie('forum'), true, true) as $cat)
			$categoriesAutorisees[] = $cat['cat_id'];

		$paginator = Doctrine_Core::getTable('ForumMessage')
			->messagesUtilisateur($membre->id, $categoriesAutorisees);

		try
		{
		    $pager = $paginator->createView($_GET['p']);
		    $pager->setUri('messages-'.$membre->id.'-p%d-'.rewrite($membre->pseudo).'.html');
		}
		catch (\InvalidArgumentException $e)
		{
		    throw new NotFoundHttpException('La page demandée n\'existe pas.');
		}

		return render_to_response(
		    'ZcoForumBundle::messages_membre.html.php', compact('membre', 'pager')
		);
	}
}
