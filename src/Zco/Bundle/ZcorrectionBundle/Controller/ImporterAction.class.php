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
 * Contrôleur gérant l'import d'un tutoriel corrigé.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class ImporterAction extends ZcorrectionActions
{
	public function execute()
	{
		zCorrecteurs::VerifierFormatageUrl(null, true);
		Page::$titre = 'Importer une correction';


		if (empty($_GET['id']) || !is_numeric($_GET['id']))
		{
			return redirect(138, '/', MSG_ERROR);
		}
		else
		{
			$s = InfosCorrection($_GET['id']);
			if(
				(($s['id_correcteur'] == $_SESSION['id'] || $s['id_recorrecteur'] == $_SESSION['id']) && verifier('zcorriger')) || verifier('zcorrection_editer_tutos')
			)
			{
				if(empty($s))
				{
					return redirect(139, '/', MSG_ERROR);
				}
				if(!empty($_FILES['tuto']) AND $_FILES['tuto']['size'] > 0)
                {
					//Récupération du contenu du fichier importé.
                    if (UPLOAD_ERR_OK == $_FILES['tuto']['error'])
                    {
						$contenu = file_get_contents($_FILES['tuto']['tmp_name']);
						unlink($_FILES['tuto']['tmp_name']);
                    }
                    else
                    {
                        return redirect(0, $_SERVER['REQUEST_URI'], MSG_ERROR);
                    }
					
					//Import du tutoriel.
					if(preg_match('`^.?<\?xml version="1.0"(?: encoding="([a-z0-9_-]+)")?\?>`is', $contenu, $encodage))
                    {
                        $encodage = !empty($encodage[1]) ? strtolower($encodage[1]) : 'utf-8';

           	            if (in_array($encodage, array('utf-8', 'iso-8859-1', 'iso-8859-15')))
						{
                                                	$tuto = new DomDocument();
                                                	$tuto->loadXML($contenu);
							if ($s['soumission_type_tuto'] == MINI_TUTO)
							{
								$tuto = $tuto->getElementsByTagName('minituto');
								$tuto = $tuto->item(0);
								$id_tuto = parse_mini_tuto($tuto, $encodage);
							}
							else
							{
								$tuto = $tuto->getElementsByTagName('bigtuto');
								$tuto = $tuto->item(0);
								$id_tuto = parse_big_tuto($tuto, $encodage);
							}
						}
						else
						{
							return redirect(11, '', MSG_ERROR);
						}
					}
					else
					{
						return redirect(12, 'fiche-tuto-'.$_GET['id'].'.html', MSG_ERROR);
					}
					
					//Mise à jour de la correction pour prendre en compte le tutoriel.
					ChangerTutorielCorrige(!empty($s['recorrection_id']) ? $s['recorrection_id'] : $s['correction_id'], $id_tuto);
					return redirect(13, 'fiche-tuto-'.$_GET['id'].'.html');
				}
				
				//Inclusion de la vue
				fil_ariane(array(
					$s['soumission_type_tuto'] == MINI_TUTO ? htmlspecialchars($s['mini_tuto_titre']) : htmlspecialchars($s['big_tuto_titre']) => 'fiche-tuto-'.$_GET['id'].'.html',
					'Importer ma correction'
				));
				$this->get('zco_vitesse.resource_manager')->requireResource(
        		    '@ZcoZcorrectionBundle/Resources/public/css/correction.css'
        		);
        		
				return render_to_response(array('s' => $s));
			}
			else
			{
				throw new Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
			}
		}
	}
}
