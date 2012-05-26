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
 * Contrôleur gérant l'affichage d'un big-tuto.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class VoirBigTutoAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true, true);
		Page::$titre .= ' - Voir un big-tuto';

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

			if(!empty($_GET['cid']))
			{
				//Génération du saut de versions si besoin
				$SautVersions = '
				<div class="flot_droite">
					<form method="post" action="">
						Version du tutoriel :
						<select id="id" name="id" onchange="document.location=\'voir-big-tuto-\'+this.value+\'.html'.(!empty($_GET['cid']) ? '?cid='.$_GET['cid'] : '').'\';">';
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

			//Récupération d'un mini-tuto si spécifié
			if (!empty($_GET['id2']) && is_numeric($_GET['id2']))
			{
				//Infos sur le mini-tuto
				$parties = RecupererMiniTuto($_GET['id2']);
				$InfosTuto = InfosMiniTuto($_GET['id2']);
				$qcm       = RecupererQCM($_GET['id2']);
				foreach($qcm as $k => $q)
				{
					$qcm[$k]['reponses'] = RecupererReponses($q['question_id']);
				}

				//Infos sur le big-tuto
				$InfosBigTuto = InfosBigTuto($_GET['id']);
				$ListeParties = ListePartiesBigTuto($_GET['id']);
				$ListeMiniTutos = array();
				foreach($ListeParties as $v)
				{
					$ListeMiniTutos[$v['partie_id']] = ListeTutosPartie($v['partie_id']);
				}

				//Génération du saut rapide
				$nb = 0;
				$SautRapide = '
				<div class="flot_droite">
					<form method="get" action="">
						Saut rapide : <select id="id2" name="id2" onchange="document.location=\'voir-big-tuto-'.$_GET['id'].'-\' + this.value + \'.html'.(!empty($_GET['cid']) ? '?cid='.$_GET['cid'] : '').'\';">
							<option value="0">'.htmlspecialchars($InfosBigTuto['big_tuto_titre']).'</option>';
				foreach($ListeParties as $v)
				{
					if($nb != 0)
						$SautRapide .= '</optgroup>';
					$SautRapide .= '<optgroup label="'.htmlspecialchars($v['partie_titre']).'">';
					if($ListeMiniTutos[$v['partie_id']])
					{
						foreach($ListeMiniTutos[$v['partie_id']] as $t)
							$SautRapide .= '<option value="'.$t['mini_tuto_id'].'"'.($t['mini_tuto_id'] == $_GET['id2'] ? ' selected="selected"' : '').'>'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($t['mini_tuto_titre'])).'</a></li>';
					}
					$nb ++;
				}
				$SautRapide .= '
						</select>
						<noscript><input type="submit" value="Aller" /></noscript>
					</form>
				</div>';

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($InfosBigTuto['big_tuto_titre']) => 'voir-big-tuto-'.$InfosBigTuto['big_tuto_id'].'.html'.(isset($_GET['cid']) ? '?cid='.$_GET['cid'] : ''),
					htmlspecialchars($InfosTuto['mini_tuto_titre']) => 'voir-big-tuto-'.$_GET['id'].'-'.$_GET['id2'].'.html'.(isset($_GET['cid']) ? '?cid='.$_GET['cid'] : ''),
					'Voir le mini-tutoriel'
				));
				$this->get('zco_vitesse.resource_manager')->requireResources(array(
    			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
    			    '@ZcoCoreBundle/Resources/public/css/zcode.css'
        		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
        		));
        		
				return render_to_response('ZcoZcorrectionBundle::voirMiniTuto.html.php', array(
					's' => $s,
					'SautRapide' => $SautRapide,
					'SautVersions' => $SautVersions,
					'parties' => $parties,
					'qcm' => $qcm,
					'InfosTuto' => $InfosTuto,
					'InfosBigTuto' => $InfosBigTuto,
					'ListeParties' => $ListeParties,
					'ListeMiniTutos' => $ListeMiniTutos,
				));
			}

			//Sinon récupération du big-tuto
			else
			{
				$InfosTuto = InfosBigTuto($_GET['id']);
				$ListeParties = ListePartiesBigTuto($_GET['id']);

				$ListeMiniTutos = array();

				foreach($ListeParties as $v)
				{
					$ListeMiniTutos[$v['partie_id']] = ListeTutosPartie($v['partie_id']);
				}

				//Génération du saut rapide
				$nb = 0;
				$SautRapide = '
				<div class="flot_droite">
					<form method="get" action="">
						Saut rapide : <select id="id2" name="id2" onchange="document.location=\'voir-big-tuto-'.$_GET['id'].'-\' + this.value + \'.html'.(!empty($_GET['cid']) ? '?cid='.$_GET['cid'] : '').'\';">
							<option value="0" selected="selected">'.htmlspecialchars($InfosTuto['big_tuto_titre']).'</option>';
				foreach($ListeParties as $v)
				{
					if($nb != 0)
						$SautRapide .= '</optgroup>';
					$SautRapide .= '<optgroup label="'.htmlspecialchars($v['partie_titre']).'">';
					if($ListeMiniTutos[$v['partie_id']])
					{
						foreach($ListeMiniTutos[$v['partie_id']] as $t)
							$SautRapide .= '<option value="'.$t['mini_tuto_id'].'">'.str_replace('&amp;euro;', '&euro;', htmlspecialchars($t['mini_tuto_titre'])).'</a></li>';
					}
					$nb ++;
				}
				$SautRapide .= '
						</select>
						<noscript><input type="submit" value="Aller" /></noscript>
					</form>
				</div>';

				//Inclusion de la vue
				fil_ariane(array(
					htmlspecialchars($InfosTuto['big_tuto_titre']) => 'voir-big-tuto-'.$_GET['id'].'.html'.(isset($_GET['cid']) ? '?cid='.$_GET['cid'] : ''),
					'Voir le big-tutoriel'
				));
				$this->get('zco_vitesse.resource_manager')->requireResources(array(
    			    '@ZcoCoreBundle/Resources/public/css/tableaux_messages.css'
    			    '@ZcoCoreBundle/Resources/public/css/zcode.css'
        		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css',
        		));
        		
				return render_to_response('ZcoZcorrectionBundle::voirBigTuto.html.php', array(
					's' => $s,
					'SautRapide' => $SautRapide,
					'SautVersions' => $SautVersions,
					'InfosTuto' => $InfosTuto,
					'ListeParties' => $ListeParties,
					'ListeMiniTutos' => $ListeMiniTutos,
				));
			}
		}
	}
}
