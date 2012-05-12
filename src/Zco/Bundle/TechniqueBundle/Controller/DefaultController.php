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

namespace Zco\Bundle\TechniqueBundle\Controller;

use Symfony\Component\Yaml\Yaml;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Contrôleur gérant les actions de maintenance technique du site.
 *
 * @author Savageman, Zopieux, mwsaz
*/
class DefaultController extends Controller
{
    /**
     * Affiche les informations sur les données mises en cache.
     *
     * @author Savageman <savageman@zcorrecteurs.fr>
     */
	public function gestionCachesAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		$cache = $this->get('zco_core.cache');

		if (!empty($_GET['supprimer']))
		{
			$cache->delete($_GET['supprimer']);
			return redirect(7);
		}
		if (!empty($_GET['tout_supprimer']) && 1 == $_GET['tout_supprimer'])
		{
			$cache->flush();
			return redirect(8);
		}

		//Inclusion de la vue
		\Page::$titre = 'Gérer les fichiers caches du site';
		fil_ariane('Gestion des fichiers en cache');
		
		return render_to_response(array('caches' => $cache->getIndex()));
	}

    /**
     * Permet de modifier la configuration du site en temps réel.
     *
     * @author Zopieux
     */
	public function configurationAction()
	{
		\zCorrecteurs::VerifierFormatageUrl();
		\Page::$titre = 'Modifier la configuration du site';
		
		$def = function($array, $key, $alt = '')
        {
        	return (in_array($key, array_keys($array))) ? $array[$key] : $alt;
        };

		define('T_STR', 'string');
		define('T_INT', 'integer');
		define('T_BOOL', 'boolean');
		define('T_FLOAT', 'float');
		define('FICHIER_CONFIG', APP_PATH . '/config/constants.yml');

		// si le formulaire est envoyé, on écrit le fichier (et la config si changements)
		if (count($_POST) > 0)
		{
			$to_parse = array_keys($def($_POST, 'is', array()));
			$checked = array_keys($def($_POST, 'ch', array()));
			unset($_POST['is'], $_POST['ch']);

			$refinal = array();
			$refinal += $_POST;

			foreach ($to_parse as $item)
			{
				if (in_array($item, $checked))
				{
					$refinal[$item] = true;
				}
				else
				{
					$refinal[$item] = false;
				}
			}

			$journal = $this->ecrireYAML($refinal, FICHIER_CONFIG);
			if (count($journal) > 0)
			{
				$_SESSION['journal_config'] = $journal;
				return redirect(3, '');
			}
			else
			{ // pas de changement
				return redirect(9, '', MSG_OK, 0);
			}
		}

		// on reçoit le log des modifications, alors on le donne au template
		if (isset($_SESSION['journal_config']))
		{
			$journalModif = $_SESSION['journal_config'];
			unset($_SESSION['journal_config']);
		}
		else
		{
			$journalModif = null;
		}

		// listage pour le template
		$data = Yaml::parse(FICHIER_CONFIG);
		$groupes = $data['groups'];
		$configuration = array();

		foreach (array_keys($data['groups']) as $key)
		{
			$configuration[$key] = array();
		}

		foreach ($data['constants'] as $const => $attribs)
		{
			$groupe = $def($attribs, 'group', 'nogroup');
			$configuration[$groupe][$const] = array(
				'value' => $def($attribs, 'value', '0'),
				'desc' => $def($attribs, 'desc', '<span class="code2">'. $const . '</span> (pas de description)'),
				'help' => $def($attribs, 'help'),
				'type' => $def($attribs, 'type', T_STR));
		}

		fil_ariane('Modifier la configuration du site');
		$this->get('zco_vitesse.resource_manager')->requireResource('@ZcoCoreBundle/Resources/public/css/zcode.css');
		
		return render_to_response(array(
			'configuration' => $configuration,
			'groupes' => $groupes,
			'journalModif' => $journalModif,
		));
	}

    /**
     * Affiche les statistiques et l'état d'APC.
     *
     * @author mwsaz <mwsaz@zcorrecteurs.fr>
     */
	public function apcAction()
	{
		return render_to_response(array());
	}
	
	/**
     * Ecrit la configuration dans le fichier YAML.
     *
     * @author Zopieux
     * @param  array $changements
     * @param  string $fichier
     * @return array
     */
	private function ecrireYAML(array $changements, $fichier)
	{
    	$data = Yaml::parse($fichier);

    	$journal = array();
    	foreach($changements as $const => $nouvelle_val)
    	{
    		$ancienne_val = $data['constants'][$const]['value'];
    		if($ancienne_val != $nouvelle_val)
    		{
    			$data['constants'][$const]['value'] = $nouvelle_val;
    			$journal[$const] = array($ancienne_val, $nouvelle_val);
    		}
    	}

    	$entete = "# Fichier de configuration des constantes
    # =======================================
    # C'est moi que vous devez modifier pour ajouter une constante !
    #
    # desc  est la description de la constante
    # value est sa valeur
    # type  est son type : string, integer, boolean (et float)
    # help  est un supplément à la description (aide) ; c'est facultatif
    # group est le groupe auquel appartient le paramètre
    #
    # /!\ Le type est utile pour l'interface d'administration, vous ne pouvez pas
    # l'omettre.
    #
    # Les groupes sont d'une utilité purement esthétique et organisationnelle.\n\n";

    	if (count($journal) > 0)
    	{
    		$yaml = Yaml::dump($data, 3);
    		file_put_contents($fichier, $entete.$yaml);
    	}
    	
    	return $journal;
    }
}
