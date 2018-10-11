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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Contrôleur gérant la comparaison entre deux versions d'un billet.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ComparaisonAction extends BlogActions
{
	public function execute()
	{
		//Si on envoie les id de deux versions à comparer
		if(!empty($_GET['id']) && !empty($_GET['id2']) && is_numeric($_GET['id']) && is_numeric($_GET['id2']))
		{
			$infos_new = InfosVersion($_GET['id']);
			$infos_old = InfosVersion($_GET['id2']);

			if(empty($infos_new) || empty($infos_old))
				throw new AccessDeniedHttpException();

			if($infos_new['version_id_billet'] == $infos_old['version_id_billet'])
			{
				//On récupère des infos sur le billet
				$_GET['id'] = $infos_new['version_id_billet'];
				$ret = $this->initBillet();
				if($ret instanceof Response)
					return $ret;
				Page::$titre .= ' - Historique des versions';

				if($this->verifier_voir && (verifier('blog_voir_versions') || $this->autorise == true))
				{
					$InfosBillet = InfosBillet($_GET['id']);
					$InfosBillet = $InfosBillet[0];

					$texte_new = $infos_new['version_texte'];
					$texte_old = $infos_old['version_texte'];
					$intro_new = $infos_new['version_intro'];
					$intro_old = $infos_old['version_intro'];

					$this->diff_intro = $this->diff($intro_old, $intro_new);
					$this->diff_texte = $this->diff($texte_old, $texte_new);

					//Inclusion de la vue
					fil_ariane($InfosBillet['cat_id'], array(
						htmlspecialchars($InfosBillet['version_titre']) => 'admin-billet-'.$_GET['id'].'.html',
						'Historique des versions' => 'versions-'.$_GET['id'].'.html',
						'Comparaison'));
					$this->get('zco_core.resource_manager')->requireResource(
        			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
        			);

					return render_to_response(array_merge(
						$this->getVars(),
						compact('infos_old', 'infos_new')
					));
				}
				else
					throw new AccessDeniedHttpException();
			}
		}
		return new NotFoundHttpException();

		//TODO : Sinon on affiche juste le formulaire de choix
	}

    /**
     * Réalise un diff entre deux chaines de caractères.
     *
     * @param string $old L'ancienne chaine de caractères.
     * @param string $new La nouvelle chaine de caractères.
     * @return string
     */
    private function diff($old, $new)
    {
        include_once(BASEPATH . '/lib/diff/diff.php');
        include_once(BASEPATH . '/lib/diff/htmlformatter.php');

        $old = explode("\n", strip_tags($old));
        $new = explode("\n", strip_tags($new));

        $diff = new Diff($old, $new);
        $formatter = new HTMLDiffFormatter();

        return $formatter->format($diff);
    }
}
