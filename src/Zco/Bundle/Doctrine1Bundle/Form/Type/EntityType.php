<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\Doctrine1Bundle\Form\Type;

use Zco\Bundle\Doctrine1Bundle\Form\ChoiceList\EntityChoiceList;
use Zco\Bundle\Doctrine1Bundle\Form\EventListener\MergeCollectionListener;
use Zco\Bundle\Doctrine1Bundle\Form\DataTransformer\EntitiesToArrayTransformer;
use Zco\Bundle\Doctrine1Bundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class EntityType extends AbstractType
{
	public function buildForm(FormBuilder $builder, array $options)
	{
		if ($options['multiple'])
		{
			$builder
				->addEventSubscriber(new MergeCollectionListener())
				->prependClientTransformer(new EntitiesToArrayTransformer($options['choice_list']))
			;
		}
		else
		{
			$builder->prependClientTransformer(new EntityToIdTransformer($options['choice_list']));
		}
	}

	public function getDefaultOptions(array $options)
	{
		$defaultOptions = array(
			'class'			=> null,
			'property'		=> null,
			'query'	     	=> null,
			'choices'	   	=> null,
			'autocomplete' 	=> false,
		);
		
		$options = array_replace($defaultOptions, $options);

		if (!isset($options['choice_list']))
		{
			$defaultOptions['choice_list'] = new EntityChoiceList(
				$options['class'],
				$options['property'],
				$options['query'],
				$options['choices']
			);
		}

		return $defaultOptions;
	}
	
	public function getName()
	{
		return 'entity';
	}
	
	public function getParent(array $options)
	{
		return $options['autocomplete'] ? 'text' : 'choice';
	}
}
