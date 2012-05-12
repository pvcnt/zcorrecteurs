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
namespace Zco\Bundle\RecrutementBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RecrutementType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('nom', null, array('label' => 'Intitulé', 'attr' => array('size' => 40)));
		$builder->add('date', null, array(
			'label' => 'Lancement du recrutement', 
			'input' => 'string', 
			'widget' => 'single_text',
		));
		$builder->add('date_fin_depot', null, array(
			'label' => 'Fin de dépôt des candidatures', 
			'required' => false, 
			'input' => 'string', 
			'widget' => 'single_text',
		));
		$builder->add('etat', 'choice', array('label' => 'État', 'choices' => \Recrutement::getEtats()));
		$builder->add('texte', 'zform', array('label' => 'Description'));
		$builder->add('redaction', null, array('label' => 'Rédaction requise ?', 'required' => false));
		$builder->add('Quiz', null, array(
			'label' => 'Épreuve de quiz', 
			'required' => false, 
			'class' => 'Quiz',
		));
		$builder->add('Groupe', null, array(
			'label' => 'Groupe concerné', 
			'required' => false, 
			'class' => 'Groupe',
		));
		$builder->add('test', null, array('label' => 'Test requis ?', 'required' => false));
		$builder->add('lien', 'url', array(
			'label' => 'Lien vers un sujet d\'aide', 
			'required' => false, 
			'attr' => array('size' => 40),
		));
	}

	public function getName()
	{
		return 'recrutement';
	}

	public function getDefaultOptions(array $options)
	{
		return array(
			'data_class' => '\Recrutement',
		);
	}
}