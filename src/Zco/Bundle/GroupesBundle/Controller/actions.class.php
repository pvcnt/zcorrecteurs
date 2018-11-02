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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Zco\Bundle\CategoriesBundle\Domain\CategoryDAO;

/**
 * Contrôleur gérant les actions sur les groupes et les droits.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class GroupesActions extends Controller
{
	public function __construct()
	{
		include_once(__DIR__.'/../modeles/droits.php');
	}

	/**
	 * Affiche la liste des groupes.
	 */
	public function executeIndex()
	{
	    if (!verifier('groupes_gerer')) {
	        throw new AccessDeniedHttpException();
        }
		fil_ariane('Gestion des groupes');

		return render_to_response(array(
			'ListerGroupes'				=> ListerGroupes(),
			'ListerGroupesSecondaires'	=> ListerGroupesSecondaires(),
		));
	}

	/**
	 * Ajoute un nouveau groupe.
	 */
	public function executeAjouter()
	{
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Ajouter un groupe';

		//Si on veut ajouter un groupe
		if(!empty($_POST['nom']))
		{
			AjouterGroupe();
			return redirect('Le groupe a bien été ajouté.', 'index.html');
		}

		fil_ariane('Ajouter un groupe');
        $ListerGroupes = array_filter(ListerGroupes(), function($group) {
            return $group['groupe_code'] != \Groupe::ANONYMOUS;
        });

		return render_to_response(array('ListerGroupes' => $ListerGroupes));
	}

	/**
	 * Modifie un groupe.
	 */
	public function executeEditer()
	{
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Modifier un groupe';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//Si on veut éditer un groupe
			if(!empty($_POST['nom']))
			{
				EditerGroupe($_GET['id']);
				return redirect('Le groupe a bien été modifié.', 'index.html');
			}

			$InfosGroupe = InfosGroupe($_GET['id']);
			if(empty($InfosGroupe))
				throw new NotFoundHttpException();

			fil_ariane('Modifier un groupe');

			return render_to_response(array('InfosGroupe' => $InfosGroupe));
		}
		else
            throw new NotFoundHttpException();
	}

	/**
	 * Supprime un groupe.
	 */
	public function executeSupprimer()
	{
        if (!verifier('groupes_gerer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Supprimer un groupe';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			//Si on veut supprimer le groupe
			if(isset($_POST['confirmer']))
			{
				SupprimerGroupe($_GET['id']);
				$this->get('cache')->save('dernier_refresh_droits', time(), 0);

				return redirect('Le groupe a bien été supprimé.', 'index.html');
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new RedirectResponse('index.html');
			}

			$InfosGroupe = InfosGroupe($_GET['id']);
			if(empty($InfosGroupe))
                throw new NotFoundHttpException();

			fil_ariane('Supprimer un groupe');
			return render_to_response(array('InfosGroupe' => $InfosGroupe));
		}
		else
            throw new NotFoundHttpException();
	}

	/**
	 * Vérifier la liste des droits attribués à un groupe.
	 */
	public function executeVerifier()
	{
        if (!verifier('groupes_changer_droits')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Vérification des droits d\'un groupe';

		$ListerGroupes = ListerGroupes();
		$ListerDroits = ListerDroits();

		if(isset($_POST['id']))
		{
			$_GET['id'] = $_POST['id'];
			$_POST = null;
		}

		//Infos sur le groupe si besoin
		if(isset($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosGroupe = InfosGroupe($_GET['id']);
			if(empty($InfosGroupe))
				throw new NotFoundHttpException();
		}

		//Listage des droits
		if(!empty($InfosGroupe))
			$Droits = VerifierDroitsGroupe($_GET['id']);
		else
			$Droits = null;

		//Inclusion de la vue
		fil_ariane('Vérifier les droits d\'un groupe');

		return render_to_response(array(
			'InfosGroupe' => $InfosGroupe,
			'ListerGroupes' => $ListerGroupes,
			'ListerDroits' => $ListerDroits,
			'Droits' => $Droits,
		));
	}

	/**
	 * Change un membre de groupe.
	 */
	public function executeChangerMembreGroupe()
	{
        if (!verifier('groupes_changer_membre')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Changer un membre de groupe';

        include_once(__DIR__.'/../../UserBundle/modeles/utilisateurs.php');
		if(!empty($_POST['pseudo']))
		{
			$InfosUtilisateur = InfosUtilisateur($_POST['pseudo']);
			unset($_POST['pseudo']);
		}
		elseif(!empty($_GET['id']))
			$InfosUtilisateur = InfosUtilisateur($_GET['id']);

		if(isset($InfosUtilisateur))
		{
			if(empty($InfosUtilisateur))
                throw new NotFoundHttpException();

			$_GET['id'] = $InfosUtilisateur['utilisateur_id'];

			if(!isset($_POST['groupe']))
			{
				$ListerGroupes = array_filter(ListerGroupes(), function($group) {
				    return $group['groupe_code'] != \Groupe::ANONYMOUS;
                });
			}
			elseif(!empty($_POST['groupe']) && is_numeric($_POST['groupe']))
			{
				$_POST['id'] = $_GET['id'];
				ChangerGroupeUtilisateur();
				$this->get('cache')->save('dernier_refresh_droits', time(), 0);

				return redirect('Le membre a bien été changé de groupe.', 'changer-membre-groupe-'.$_GET['id'].'.html');
			}
			else
				$ListerGroupes = null;

			if (isset($_POST['changement_groupes_secondaires']))
			{
				ModifierGroupesSecondairesUtilisateur(
					$_GET['id'],
					isset($_POST['groupes_secondaires']) ? $_POST['groupes_secondaires'] : array()
				);
				$this->get('cache')->save('dernier_refresh_droits', time(), 0);

				return redirect(
				    'Le membre a bien été changé de groupe.',
					'/groupes/changer-membre-groupe-'
					.$InfosUtilisateur['utilisateur_id'].'-'
					.rewrite($InfosUtilisateur['utilisateur_pseudo']).'.html');
			}

			$GroupesSecondaires = ListerGroupesSecondairesUtilisateur($InfosUtilisateur['utilisateur_id']);
			$ListerGroupesSecondaires = ListerGroupesSecondaires();
			$temp = array();
			foreach($GroupesSecondaires as $groupe)
			{
				$temp[] = $groupe['groupe_id'];
			}
			$GroupesSecondaires = $temp;
		}
		else
		{
			$ListerGroupes = null;
			$InfosUtilisateur = null;
			$GroupesSecondaires = null;
		}

		$pseudo = isset($InfosUtilisateur) ? $InfosUtilisateur['utilisateur_pseudo'] : '';

		//Inclusion de la vue
		fil_ariane('Changer un membre de groupe');

		return render_to_response(array(
			'ListerGroupes' => $ListerGroupes,
			'ListerGroupesSecondaires' => isset($ListerGroupesSecondaires) ? $ListerGroupesSecondaires : null,
			'pseudo' => $pseudo,
			'InfosUtilisateur' => $InfosUtilisateur,
			'GroupesSecondaires' => $GroupesSecondaires,
		));
	}

	/**
	 * Modifie la liste des droits attribués à un groupe.
	 */
	public function executeDroits()
	{
        if (!verifier('groupes_changer_droits')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Changement des droits d\'un groupe';

		$ListerGroupes = array_merge(ListerGroupes(), ListerGroupesSecondaires());
		$ListerDroits = ListerDroits();

		//Infos sur le groupe si besoin
		if($_GET['id'] != '' && is_numeric($_GET['id']))
		{
			$InfosGroupe = InfosGroupe($_GET['id']);
			if(empty($InfosGroupe))
				throw new NotFoundHttpException();
		}
		else
		{
			$InfosGroupe = null;
		}
		//Infos sur le droit si besoin
		if(!empty($_GET['id2']) && is_numeric($_GET['id2']))
		{
			$InfosDroit = InfosDroit($_GET['id2']);
			if(empty($InfosDroit))
                throw new NotFoundHttpException();
		}
		else
		{
			$InfosDroit = null;
		}

		//Listage des catégories nécessaires et récupération de la valeur du droit si besoin
		$ValeurNumerique = null;
		if(!empty($InfosDroit) && !empty($InfosGroupe))
		{
			if($InfosDroit['droit_choix_categorie'] == 1)
			{
				$ListerEnfants = CategoryDAO::ListerEnfants($InfosDroit, true);
			}
			else
			{
				$ListerEnfants = null;
			}

			$ValeurDroit = RecupererValeurDroit($_GET['id2'], $_GET['id']);
			if(!$InfosDroit['droit_choix_categorie'] && !empty($ValeurDroit) && $InfosDroit['droit_choix_binaire'])
				$ValeurDroit = $ValeurDroit[0];
			elseif(!$InfosDroit['droit_choix_categorie'] && !empty($ValeurDroit) && !$InfosDroit['droit_choix_binaire'])
				$ValeurNumerique = $ValeurDroit[0]['gd_valeur'];
			elseif($InfosDroit['droit_choix_categorie'] && !$InfosDroit['droit_choix_binaire'])
			{
				foreach($ValeurDroit as $d)
				{
					if($d['gd_valeur'] != 0)
						$ValeurNumerique = $d['gd_valeur'];
				}
			}
			else
				$ValeurNumerique = '';
		}
		else
		{
			$ValeurDroit = null;
			$ListerEnfants = null;
			$ValeurNumerique = null;
		}

		//Si on veut modifier
		if(isset($_POST['modifier']) && !empty($InfosDroit) && !empty($InfosGroupe))
		{
			//En cas de droit simple (sans sélection de catégorie)
			if(!$InfosDroit['droit_choix_binaire'] && !$InfosDroit['droit_choix_categorie'])
			{
				EditerDroitGroupe($_GET['id'], $InfosDroit['droit_id_categorie'], $_GET['id2'], (int)$_POST['valeur']);
			}
			elseif(!$InfosDroit['droit_choix_categorie'])
			{
				EditerDroitGroupe($_GET['id'], $InfosDroit['droit_id_categorie'], $_GET['id2'], isset($_POST['valeur']) ? 1 : 0);
			}
			//Sinon droit appliquable par catégorie
			else
			{
				//Pour éviter des erreurs
				if(empty($_POST['cat']))
					$_POST['cat'] = array();

				//$done = array();
				foreach($ListerEnfants as $e)
				{
					//Si on doit ajouter le droit
					if(in_array($e['cat_id'], $_POST['cat']))
					{
						if(!$InfosDroit['droit_choix_binaire'])
							$valeur = (int)$_POST['valeur'];
						else
							$valeur = 1;
						EditerDroitGroupe($_GET['id'], $e['cat_id'], $_GET['id2'], $valeur);
					}
					//Sinon on le retire
					else
					{
						//if(!in_array($e['cat_id'], $done))
						EditerDroitGroupe($_GET['id'], $e['cat_id'], $_GET['id2'], 0);
					}
				}
			}

			//Suppression des caches
			$this->get('cache')->delete('droits_groupe_'.$_GET['id']);

			return redirect(
			    'Le droit de ce groupe a bien été mis à jour.',
                'droits-'.$_GET['id'].'-'.$_GET['id2'].'.html'
            );
		}

		//Inclusion de la vue
		fil_ariane('Changer les droits d\'un groupe');
		$this->get('zco_core.resource_manager')->requireResource(
		    '@ZcoCoreBundle/Resources/public/css/zcode.css'
		);

		return render_to_response(array(
			'InfosGroupe' => $InfosGroupe,
			'ListerGroupes' => $ListerGroupes,
			'ListerDroits' => $ListerDroits,
			'InfosDroit' => $InfosDroit,
			'ListerEnfants' => $ListerEnfants,
			'ValeurDroit' => $ValeurDroit,
			'ValeurNumerique' => $ValeurNumerique,
		));
	}

	/**
	 * AAffiche la liste de tous les droits.
	 */
	public function executeGestionDroits()
	{
        if (!verifier('droits_gerer')) {
            throw new AccessDeniedHttpException();
        }
		fil_ariane('Gestion des droits');

		return render_to_response(array(
			'ListerDroits' => ListerDroits(),
			'ListerCategories' => CategoryDAO::ListerCategories(),
		));
	}

	/**
	 * Supprime un droit.
	 */
	public function executeSupprimerDroit()
	{
        if (!verifier('droits_gerer')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Supprimer un droit';

		if(!empty($_GET['id']) && is_numeric($_GET['id']))
		{
			$InfosDroit = InfosDroit($_GET['id']);
			if(empty($InfosDroit))
                throw new NotFoundHttpException();

			//Si on veut supprimer le droit
			if(isset($_POST['confirmer']))
			{
				SupprimerDroit($_GET['id']);

				return redirect('Le droit a bien été supprimé.', 'gestion-droits.html');
			}
			//Si on annule
			elseif(isset($_POST['annuler']))
			{
				return new RedirectResponse('gestion-droits.html');
			}

			//Inclusion de la vue
			fil_ariane(array('Gestion des droits' => 'gestion-droits.html', 'Supprimer un droit'));

			return render_to_response(array('InfosDroit' => $InfosDroit));

		}
		else
            throw new NotFoundHttpException();
	}

	/**
	 * Action affichant l'historique des changements de groupe.
	 * @author Vanger
	 */
	public function executeHistoriqueGroupes()
	{
        if (!verifier('groupes_changer_membre')) {
            throw new AccessDeniedHttpException();
        }
		Page::$titre = 'Historique des changements de groupe';

		$NombreDeChangements = CompterChangementHistorique();
		$NombreDePages = ceil($NombreDeChangements / 20);
		$_GET['p'] = (!empty($_GET['p']) && is_numeric($_GET['p'])) ? $_GET['p'] : 1;
		$Debut = ($_GET['p']-1) * 20;

		$TableauPage = liste_pages($_GET['p'], $NombreDePages, $NombreDeChangements, 20, 'historique-groupes-p%s.html', false);
		$Changements = ListerChangementGroupe($Debut, 20);

		//Inclusion de la vue
		fil_ariane('Historique des changements de groupe');

		return render_to_response(array(
			'NombreDeChangements' => $NombreDeChangements,
			'TableauPage' => $TableauPage,
			'Changements' => $Changements,
		));
	}
}
