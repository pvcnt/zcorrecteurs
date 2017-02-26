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
 * Requêtes sur la table des utilisateurs.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class UtilisateurTable extends Doctrine_Table
{
	const LIKE_EXACT = 0;
	const LIKE_BEGIN = 1;
	const LIKE_END = 2;
	const LIKE_CONTAINS = 3;
	
	/**
	 * Effectue une requête par critères sur la table.
	 *
	 * @param  array $query
	 * @param  integer|null $hydrationMode
	 * @return \Doctrine_Collection
	 */
	public function query(array $query, $hydrationMode = null)
	{
		return $this->getQuery($query)->execute(array(), $hydrationMode);
	}
	
	/**
	 * Renvoie une requête par critères sur la table.
	 *
	 * @param  array $query
	 * @return \Doctrine_Query
	 */
	public function getQuery(array $options)
	{
		$query = $this->getBaseQuery();
		
		if (!empty($options['pseudo']))
		{
			$type = isset($options['#pseudo_like']) 
				&& in_array($options['#pseudo_like'], range(0, 3)) 
				? $options['#pseudo_like'] : 0;
			
			if ($type == self::LIKE_EXACT)
			{
				$query->andWhere('pseudo = ?', $options['pseudo']);
			}
			elseif ($type == self::LIKE_BEGIN)
			{
				$query->andWhere('pseudo LIKE ?', $options['pseudo'].'%');
			}
			elseif ($type == self::LIKE_END)
			{
				$query->andWhere('pseudo LIKE ?', '%'.$options['pseudo']);
			}
			elseif ($type == self::LIKE_CONTAINS)
			{
				$query->andWhere('pseudo LIKE ?', '%'.$options['pseudo'].'%');
			}
		}
		if (!empty($options['#order_by']))
		{
			if ($options['#order_by'][0] === '-')
			{
				$query->orderBy(substr($options['#order_by'], 1).' DESC');
			}
			else
			{
				$query->orderBy($options['#order_by']);
			}
		}
		if (isset($options['group']))
		{
			$query->andWhere('groupe_id = ?', $options['group']);
		}
		if (isset($options['secondary_group']))
		{
			$query->innerJoin('u.SecondaryGroups sg')
				->andWhereIn('sg.groupe_id', (array) $options['secondary_group']);
		}
		
		return $query;
	}
	
	/**
	 * Récupère une liste d'utilisateurs par leur adresse courriel.
	 *
	 * @param  string $email L'adresse courriel à chercher
	 * @param  integer $hydrationMode
	 * @return \Doctrine_Collection
	 */
	public function getByEmail($email, $hydrationMode = null)
	{
		return $this->getByEmailQuery($email)->execute(array(), $hydrationMode);
	}
	
	public function getOneByEmail($email, $hydrationMode = null)
	{
		return $this->getByEmailQuery($email)->fetchOne(array(), $hydrationMode);
	}
	
	public function countByEmail($email)
	{
		return $this->getByEmailQuery($email)->count();
	}
	
	public function getByEmailQuery($email)
	{
		$email = str_replace('*', '%', $email);

		return $this->getBaseQuery()
			->where('u.utilisateur_email LIKE ?', $email)
			->orderBy('u.utilisateur_pseudo');
	}
	
	public function listerEquipe()
	{
		return $this->createQuery('u')
			->select('u.pseudo, u.id, u.avatar, g.id, g.nom, g.class')
			->from('Utilisateur u')
			->leftJoin('u.Groupe g')
			->where('g.team = ?', true)
			->andWhere('g.id <> ?', GROUPE_ANCIENS)
			->orderBy('u.pseudo')
			->execute();
	}
	
	public function listerAvatarsEquipe()
	{
		return $this->createQuery('u')
			->select('u.pseudo, u.id, u.avatar')
			->from('Utilisateur u')
			->leftJoin('u.Groupe g')
			->where('g.team = ?', true)
			->andWhere('g.id <> ?', GROUPE_ANCIENS)
			->andWhere('u.avatar <> ?', '')
			->orderBy('RAND()')
			->execute();
	}
	
	public function listerAnciens()
	{
		return $this->createQuery('u')
			->select('u.pseudo, u.id, g.id, g.nom, g.class')
			->from('Utilisateur u')
			->leftJoin('u.Groupe g')
			->where('g.id = ?', GROUPE_ANCIENS)
			->orderBy('u.pseudo')
			->execute();
	}
	
	public function getById($id)
	{
		return $this->getByIdQuery($id)->fetchOne();
	}
	
	public function getByIdFull($id)
	{
		return $this->getByIdQuery($id)
			->addSelect('s.*, g2.*')
			->leftJoin('u.SecondaryGroups s')
			->leftJoin('s.Group g2')
			->fetchOne();
	}
	
	public function getByIdQuery($id)
	{
		return $this->getBaseQuery()->where('u.id = ?', $id);
	}

	public function getByNonValid($hydrationMode = null)
	{
		return $this->getByNonValidQuery()->execute(array(), $hydrationMode);
	}
	
	public function getByNonValidQuery()
	{
		return $this->getBaseQuery()->where('u.valide = ?', false);
	}
	
	public function countByPseudo($pseudo)
	{
		return $this->getByPseudoQuery($pseudo)->count();
	}
	
	public function getByPseudo($pseudo, $hydrationMode = null)
	{
		return $this->getByPseudoQuery($pseudo)->execute(array(), $hydrationMode);
	}
	
	public function getOneByPseudo($pseudo)
	{
		return $this->getByPseudoQuery($pseudo)->fetchOne();
	}
	
	public function getByPseudoQuery($pseudo)
	{
		return $this->getBaseQuery()->where('u.pseudo = ?', $pseudo);
	}
	
	/**
	 * Génère un nouveau mot de passe pour le membre.
	 * Retourne un array(mot de passe en clair, clé de validation) si réussite.
	 * Retourne false sinon.
	 * 
	 * @param string $email L'email du membre.
	 * @return array|false
	 */
	public function generateNewPassword($email)
	{
		$pass = $this->generateRandomPassword();
		$cle = sha1(uniqid(rand(), true));

		$num = $this->createQuery()
			->update()
			->set('utilisateur_nouveau_mot_de_passe', '?', sha1($pass))
			->set('utilisateur_hash_validation', '?', $cle)
			->addWhere('utilisateur_email = ?', $email)
			->addWhere('utilisateur_valide = ?', true)
			->execute();

		return $num ? array($pass, $cle) : false;
	}
	
	public function generateRandomPassword($len = 8)
	{
		return substr(sha1(uniqid(rand(), true)), 0, $len);
	}

	/**
	 * Confirme un nouveau mot de passe à partir d'un hash.
	 * 
	 * @param  string $hash La clé de validation.
	 * @return boolean La démarche a-t-elle réussi ?
	 */
	public function confirmNewPassword($hash)
	{	return $this->createQuery()
			->update()
			->set('utilisateur_mot_de_passe', 'utilisateur_nouveau_mot_de_passe')
			->where('utilisateur_hash_validation = ?', $hash)
			->execute();
	}

	public function getIdByPseudo($pseudo)
	{
		return $this->createQuery()
			->select('id')
			->where('pseudo = ?', $pseudo)
			->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
	}
	
	public function getPays()
	{
		$rows = $this->createQuery('u')
			->select('u.utilisateur_localisation, COUNT(*) AS nombre')
			->groupBy('u.utilisateur_localisation')
			->execute();
		$total = $this->createQuery('u')->count();

		$ret = array();
		foreach ($rows as $row)
		{
			if (!empty($row['localisation']) && !in_array($row['localisation'], array('-', 'Inconnu')))
				$ret[] = array('pays' => $row['localisation'], 'nombre' => $row['nombre'], 'pourcent' => $row['nombre']/$total);
		}
		return $ret;
	}

	public function compterMembresAge()
	{
		return $this->createQuery('u')
			->where('u.utilisateur_date_naissance IS NOT NULL')
			->andWhere('u.utilisateur_date_naissance <> \'0000-00-00\'')
			->count();
	}

	public function getAgeMembres($groupe = null)
	{
		if ($ages = Container::getService('zco_core.cache')->get('membres_ages'.$groupe))
		{
			return $ages;
		}

		//@TODO calcul de l'âge en DQL. Aucune idée de comment faire.

		$dbh = Doctrine_Manager::connection()->getDbh();
		$q = $dbh->prepare('SELECT COUNT(utilisateur_id) AS nombre, '
			.'CASE utilisateur_date_naissance '
				.'WHEN NULL THEN 0 '
				.'ELSE DATEDIFF(NOW(), utilisateur_date_naissance) DIV 365 '
			.'END AS age '
			.'FROM zcov2_utilisateurs '
			.'WHERE utilisateur_date_naissance IS NOT NULL '
			.'AND utilisateur_date_naissance <> \'0000-00-00\''
			.($groupe !== null ? 'AND utilisateur_id_groupe = :g ' : '')
			.'GROUP BY age ORDER BY age');

		$groupe !== null && $q->bindParam('g', $groupe);
		$q->execute();
		$r2 = $q->fetchAll();

		if (!$r2)
			return array();

		// Réindexer avec l'âge comme clé
		$r = array();
		foreach($r2 as $a)
			$r[$a['age']] = $a['nombre'];

		$ageMin = $r2[0]['age'];
		$ageMax = $r2[count($r2) - 1]['age'];

		$tailleTranche = 6;  // Taille d'une tranche
		$tranchesMax   = 15; // Nombre de tranches tenant sur le graphique
		$ages = array();

		// Découper les âges en tranches
		$nombreTranches = $cle_old = 0;
		$plafondAges = $ageMax;
		for ($i = $ageMin; $i <= $ageMax; $i++)
		{
			$tranche = floor($i / $tailleTranche) * $tailleTranche;
			$cle = $tranche.' - '.($tranche + $tailleTranche - 1);

			if ($cle_old !== $cle)
			{
				$cle_old = $cle;
				$nombreTranches++;

				if ($nombreTranches >= $tranchesMax
				    && $ageMax > ($tranche + $tailleTranche - 1))
				{
					$plafondAges = $tranche - 1;
					break;
				}
			}

			if (!isset($ages[$cle]))
				$ages[$cle] = 0;
			$ages[$cle] += isset($r[$i]) ? $r[$i] : 0;
		}

		// Ceux qui sont trop vieux pour être affichés
		if ($ageMax > $plafondAges)
		{
			$ages['> '.$plafondAges] = 0;
			for ($i = $plafondAges + 1; $i <= $ageMax; $i++)
			{
				$ages['> '.$plafondAges]
					+= isset($r[$i]) ? $r[$i] : 0;
			}
		}

		Container::getService('zco_core.cache')->Set('membres_ages'.$groupe, $ages, 3600 * 24);
		return $ages;
	}
	
	public function insert(\Utilisateur $user)
	{
		//Affectation de données par défaut indispensables avant enregistrement.
		if ($user->getGroupId() === null)
		{
			$user->setGroupId(GROUPE_DEFAUT);
		}
		if ($user->getRegistrationDate() === null)
		{
			$user->setRegistrationDate(date('Y-m-d H:i:s'));
		}
		if ($user->getRegistrationHash() === null)
		{
			$user->setRegistrationHash(sha1(uniqid()));
		}
		$user->save();
		
		//Ajout des préférences en recopiant celles par défaut.
		$defaultPrefs = \Doctrine_Core::getTable('UserPreference')->find(0);
		$userPrefs    = $defaultPrefs->copy();
		$userPrefs->setUserId($user->getId());
		$userPrefs->save();
	}
	
	/**
	 * Confirme un compte utilisateur à partir d'un id et d'un hash.
	 *
	 * @param  integer $userId L'id de l'utilisateur
	 * @param  string $hash Le hash de validation
	 * @return boolean La confirmation a-t-elle été faite avec succès ?
	 */
	public function confirmAccount($userId, $hash)
	{
		$user = $this->find($userId);
		
		if ($user->getRegistrationHash() !== $hash || $user->isAccountValid())
		{
			return false;
		}
		
		$user->setAccountValid(true);
		$user->save();
		
		return true;
	}

	/**
	 * Confirme une adresse courriel à partir d'un hash.
	 *
	 * @param  string $hash Le hash de validation
	 * @return boolean La confirmation a-t-elle été faite avec succès ?
	 */
	public function confirmEmail($hash)
	{
		$user = $this->createQuery()
			->select('*')
			->where('validation_hash = ?', $hash)
			->fetchOne();

		if (!$user)
		{
			return false;
		}

		$user->setEmail($user->getNewEmail());
		$user->setValidationHash('');
		$user->save();

		return true;
	}

	/**
	 * Supprime tous les comptes n'étant pas validés depuis plus d'un jour.
	 *
	 * @return integer Nombre de comptes supprimés
	 */
	public function purge()
	{
		return $this->createQuery()
			->delete()
			->where('valide = ?', false)
			->andWhere('date_inscription <= NOW() - INTERVAL 1 DAY')
			->execute();
	}

	/**
	 * Met à jour les absences qui doivent débuter et celles qui doivent 
	 * se terminer.
	 */
	public function purgeAbsences()
	{
		//Désactivation des absences dont la date de fin est passée.
		$this->createQuery()
			->update()
			->set('absent', '?', false)
			->set('absence_reason', '?', '')
			->set('absence_start_date', new \Doctrine_Expression('NULL'))
			->set('absence_end_date', new \Doctrine_Expression('NULL'))
			->where('absent = ?', true)
			->andWhere('absence_end_date IS NOT NULL')
			->andWhere('absence_end_date < NOW()')
			->execute();

		//Activation des absences dont la date de début est passé.
		$this->createQuery()
			->update()
			->set('absent', '?', true)
			->where('absent = ?', false)
			->andWhere('absence_start_date IS NOT NULL')
			->andWhere('absence_start_date < NOW()')
			->execute();
	}
	
	public function getMarkersForMap($router)
	{
		$users = $this->getBaseQuery()
			->where('address <> \'\' AND latitude <> \'\' AND longitude <> \'\'')
			->execute();
		$markers = array();
		
		foreach ($users as $k => $user)
		{
			if (!verifier('modifier_adresse', 0, $user->getGroupId()))
			{
				$users->remove($k);
				continue;
			}
			
			$path = '/uploads/membres/carte/';
			$fichier = $path.'g'.$user->getGroupId().'.png';
			if (!is_file(BASEPATH.'/web/'.$fichier) || time() - filemtime(BASEPATH.'/web/'.$fichier) >= 3600 * 24)
			{
				// Chargement de l'image, transparence
				$image = imagecreatefrompng(BASEPATH.'/web/'.$path.'marqueur.png');
				imagesavealpha($image, true);
				imagealphablending($image, false);

				$largeur = imagesx($image);
				$hauteur = imagesy($image);

				$groupe = $this->colorToRGB($user->getGroup()->getCssClass());

				// On prend le pixel du milieu en espérant que c'est
				// la couleur de la zone centrale
				$reference = $this->applyColor($image, (int)($largeur / 2), (int)($hauteur / 2));
				
				for ($x = 0; $x < $largeur; $x++)
				{
					for ($y = 0; $y < $hauteur; $y++)
					{
						$couleur = $this->applyColor($image, $x, $y);

						$ratios = array();
						foreach(array('red', 'green', 'blue') as $c)
						{
							$ratios[$c] = ($couleur[$c] ? (float)$couleur[$c] / (float)$reference[$c] : 0);
							$ratios[$c] > 1 && $ratios[$c] = 1;
						}

						$nouvelleCouleur = imagecolorallocatealpha($image,
							(int)($groupe[0] * $ratios['red']),
							(int)($groupe[1] * $ratios['green']),
							(int)($groupe[2] * $ratios['blue']),
							$couleur['alpha']);

						imagesetpixel($image, $x, $y, $nouvelleCouleur);
					}
				}

				imagepng($image, BASEPATH.'/web/'.$fichier);
			}
			
			$markers[] = array(
        	    'latitude' => $user->getLatitude(),
        	    'longitude' => $user->getLongitude(), 
        	    'img' => $fichier,
        	    'pseudo' => $user->getUsername(),
        	    'avatar' => $user->getAvatar(),
				'id' => $user->getId(),
        	    'url' => '<a href="'.$router->generate('zco_user_profile', array('id' => $user->getId(), 'slug' => rewrite($user->getUsername()))).'">Voir son profil</a>',
        	);
		}
		
		return $markers;
	}

	/**
	 * Retourne les composants RVB d'une couleur.
	 *
	 * @param string $couleur La couleur, format RGB/HTML/hexadécimal.
	 * @return array Rouge, vert, bleu
	 */
	protected function colorToRGB($couleur)
	{
		$couleur = strtoupper($couleur);
		if (substr($couleur, 0, 4) === 'RGB(')
		{
			$couleur = substr($couleur, 4, -1);
			$composants = explode(',', $couleur);
			$composants = array_merge($composants, array(0, 0, 0));
			return array(
				(int)$composants[0],
				(int)$composants[1],
				(int)$composants[2]);
		}
		elseif ($couleur[0] == '#') // Hexadécimal
		{
			$couleur .= '000000';
			return array(
				hexdec(substr($couleur, 1, 2)),
		                hexdec(substr($couleur, 3, 2)),
		                hexdec(substr($couleur, 5, 2)));
		}
		
		$couleurs = array( // NOM => array(rouge, vert, bleu)
			'BLACK'		=> array(0, 0, 0),
			'BLUE'		=> array(0, 0, 255),
			'GREEN'		=> array(0, 255, 0),
			'PURPLE'	=> array(128, 0, 128),
			'RED'		=> array(255, 0, 0),
			'WHITE'		=> array(0, 0, 0));
		
		return isset($couleurs[$couleur]) ? $couleurs[$couleur] : $couleurs['BLACK'];
	}
	
	protected function applyColor($i, $x, $y)
	{
		return imagecolorsforindex($i, imagecolorat($i, $x, $y));
	}
	
	protected function getBaseQuery()
	{
		return $this->createQuery('u')
			->select('u.*, g.*')
			->leftJoin('u.Groupe g');
	}
}