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

/**
 */
class RecrutementCandidatureTable extends Doctrine_Table
{
	public function listerCandidaturesMembre($uid)
	{
		//Récupération des candidatures du membre
		$query = Doctrine_Query::create()
			->select('c.candidature_id, c.candidature_id_recrutement, c.candidature_date, c.candidature_etat, r.recrutement_id, r.recrutement_etat, r.recrutement_nom, r.recrutement_prive, r.recrutement_nb_personnes, g.groupe_nom, g.groupe_class')
			->from('RecrutementCandidature c')
			->leftJoin('c.Recrutement r')
			->leftJoin('r.Groupe g')
			->where('c.candidature_id_utilisateur = ?', $uid)
			->andWhere('etat <> ?', \RecrutementCandidature::REDACTION)
			->orderBy('r.recrutement_etat ASC, c.candidature_date DESC');

		return $query->execute();
	}
	
	public function recuperer($id)
	{
		return $this->recupererQuery()
			->addSelect('r.*')
			->where('c.id = ?', $id)
			->leftJoin('c.Recrutement r')
			->fetchOne();
	}

	public function listerRecrutement($id, $tri = 'id')
	{
		return $this->recupererQuery()
		    ->addSelect('l.commentaire_id, l.participe')
		    ->addSelect('(SELECT MAX(m.id) FROM RecrutementCommentaire m WHERE m.candidature_id = c.id) AS dernier_commentaire')
		    ->addSelect('(SELECT COUNT(*) FROM RecrutementAvis a1 WHERE a1.candidature_id = c.id AND a1.type = 0) AS nb_oui')
		    ->addSelect('(SELECT COUNT(*) FROM RecrutementAvis a2 WHERE a2.candidature_id = c.id AND a2.type = 1) AS nb_non')
		    ->addSelect('(SELECT COUNT(*) FROM RecrutementAvis a3 WHERE a3.candidature_id = c.id AND a3.type = 2) AS nb_reserve')
		    ->addSelect('(SELECT a4.type FROM RecrutementAvis a4 WHERE a4.candidature_id = c.id AND a4.utilisateur_id = '.((int) $_SESSION['id']).') AS mon_avis')
			->where('recrutement_id = ?', $id)
			->andWhere('etat <> ?', \RecrutementCandidature::REDACTION)
			->leftJoin('c.LuNonLu l WITH l.utilisateur_id = '.((int) $_SESSION['id']))
			->orderBy($tri)
			->execute();
	}
	
	public function recupererIdCandidatureSuivante(\RecrutementCandidature $candidature)
	{
		return $this->createQuery()
			->select('id')
			->where('candidature_id > ?', $candidature['id'])
			->andWhere('recrutement_id = ?', $candidature['Recrutement']['id'])
			->andWhere('etat <> ?', \RecrutementCandidature::REDACTION)
			->orderBy('id')
			->fetchOne();
	}
	
	public function recupererIdCandidaturePrecedente(\RecrutementCandidature $candidature)
	{
		return $this->createQuery()
			->select('id')
			->where('candidature_id < ?', $candidature['id'])
			->andWhere('recrutement_id = ?', $candidature['Recrutement']['id'])
			->andWhere('etat <> ?', \RecrutementCandidature::REDACTION)
			->orderBy('id')
			->fetchOne();
	}
	
	public function recupererRecrutementUtilisateur($recrutementId, $utilisateurId)
	{
		return $this->recupererQuery()
			->where('recrutement_id = ?', $recrutementId)
			->andWhere('utilisateur_id = ?', $utilisateurId)
			->fetchOne();
	}
	
	private function recupererQuery()
	{
		return $this->createQuery('c')
			->select('c.*, u.id, u.pseudo, co.id, co.pseudo, a.id, a.pseudo')
			->leftJoin('c.Utilisateur u')
			->leftJoin('c.Admin a')
			->leftJoin('c.Correcteur co');
	}
}