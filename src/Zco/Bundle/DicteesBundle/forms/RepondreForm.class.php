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
 * Formulaire de réponse à une soumission.
 *
 * @author mwsaz <mwsaz@zcorrecteurs.fr>
 */
class RepondreForm extends Form
{
	protected function configure()
	{
		$reponses = array('Non', 'Oui');
		$this->addWidget('accepter', new Widget_Radio(array('choices' => $reponses)));
		$this->addWidget('commentaire', new Widget_Textarea);

		$this->setHelpText('accepter', 'Choisissez si vous souhaitez ou non '
			.'que la dictée soit rendue publique.');
		$this->setLabel('commentaire', 'Commentaires');
		$this->attachFieldset(array(
			'accepter' => 'Réponse',
			'commentaire' => 'Réponse',
		));

		$this->setValidators(array(
			'accepter' => new Validator_Choices(array('choices' => array_keys($reponses))),
			'commentaire' => new Validator_String
		));
	}
}
