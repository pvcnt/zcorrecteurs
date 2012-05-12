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

/**
 * Contrôleur gérant l'affichage d'un mini-tuto.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class VoirMiniTutoAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre .= ' - Voir un mini-tuto';

		//Si on a envoyé une version à voir, on redirige
		if(!empty($_POST['id']) && is_numeric($_POST['id']))
		{
			return new Symfony\Component\HttpFoundation\RedirectResponse('voir-mini-tuto-'.$_POST['id'].'.html'.(!empty($_GET['cid']) ? '?cid='.$_GET['cid'] : ''));
		}

		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(138, '/zcorrection/', MSG_ERROR);
		}
		else
		{
			//Récupération des informations de correction si nécessaire
			if(!empty($_GET['cid']) && is_numeric($_GET['cid']))
			{
				$s = InfosCorrection($_GET['cid']);
				if(empty($s))
					return redirect(139, '', MSG_ERROR);
			}

			//Récupération du mini-tuto et du QCM
			$parties = RecupererMiniTuto($_GET['id']);
			$InfosTuto = InfosMiniTuto($_GET['id']);
			$qcm = RecupererQCM($_GET['id']);
			foreach($qcm as $k => $q)
			{
				$qcm[$k]['reponses'] = RecupererReponses($q['question_id']);
			}

			//Génération du saut de versions si ebsoin
			if(!empty($_GET['cid']))
			{
				$SautVersions = '
				<div class="flot_droite">
					<form method="post" action="">
						Version du tutoriel :
						<select id="id" name="id" onchange="document.location=\'voir-mini-tuto-\'+this.value+\'.html'.(!empty($_GET['cid']) ? '?cid='.$_GET['cid'] : '').'\';">';
				if(verifier('voir_tutos_attente'))
					$SautVersions .= '<option value="'.$s['soumission_id_tuto'].'"'.($s['soumission_id_tuto'] == $_GET['id'] ? ' selected="selected"' : '').'>Tutoriel envoyé</option>';
				if(verifier('voir_tutos_correction'))
				{
					if(!empty($s['correction_id']))
						$SautVersions .= '<option value="'.$s['id_tuto_correction'].'"'.($s['id_tuto_correction'] == $_GET['id'] ? ' selected="selected"' : '').'>Tutoriel en correction</option>';
					if(!empty($s['recorrection_id']))
						$SautVersions .= '<option value="'.$s['id_tuto_recorrection'].'"'.($s['id_tuto_recorrection'] == $_GET['id'] ? ' selected="selected"' : '').'>Tutoriel en recorrection</option>';
				}
				$SautVersions .= '
						</select>
						<noscript><input type="submit" value="Aller" /></noscript>
					</form>
				</div>';
			}
			else
				$SautVersions = '';

			//Inclusion de la vue
			$titre = isset($s) ? $s : $InfosTuto;
			fil_ariane(array(
				htmlspecialchars($titre['mini_tuto_titre']) => 'voir-mini-tuto-'.$_GET['id'].'.html'.(isset($_GET['cid']) ? '?cid='.$_GET['cid'] : ''),
				'Voir le mini-tutoriel'
			));
			$this->get('zco_vitesse.resource_manager')->requireResources(array(
			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css',
			    '@ZcoCoreBundle/Resources/public/css/zcode.css',
    		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
    		));
    		
			return render_to_response('ZcoZcorrectionBundle::voirMiniTuto.html.php', array(
				's' => (isset($s) ? $s : null),
				'SautVersions' => $SautVersions,
				'parties' => $parties,
				'qcm' => $qcm,
				'InfosTuto' => $InfosTuto,
			));
		}
	}
}
