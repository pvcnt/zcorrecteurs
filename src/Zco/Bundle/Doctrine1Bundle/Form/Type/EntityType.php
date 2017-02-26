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

namespace Zco\Bundle\Doctrine1Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zco\Bundle\Doctrine1Bundle\Form\ChoiceList\EntityChoiceList;
use Zco\Bundle\Doctrine1Bundle\Form\DataTransformer\EntityToIdTransformer;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new EntityToIdTransformer($options['choice_list']), true);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => null,
            'property' => null,
            'query' => null,
            'choices' => null,
        ]);
        $resolver->setDefault('choice_list', function (Options $options) {
            return new EntityChoiceList(
                $options['class'],
                $options['property'],
                $options['query'],
                $options['choices']
            );
        });
    }

    public function getName()
    {
        return 'entity';
    }

    public function getParent()
    {
        return 'choice';
    }
}
