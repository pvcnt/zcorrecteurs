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

namespace Zco\Bundle\QuizBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', null);
        $builder->add('description', null, ['attr' => ['class' => 'input-xxlarge']]);
        $builder->add('difficulte', 'choice', array('label' => 'Difficulté', 'choices' => \Quiz::LEVELS));
        $builder->add('Categorie', null, array(
            'label' => 'Catégorie',
            'class' => 'Categorie',
        ));
        $builder->add('aleatoire', null, [
                'label' => 'Nombre de réponses choisies dans un ordre aléatoire',
                'help' => 'Le fait de choisir zéro permet d\'afficher toutes les questions et dans l\'ordre (mode aléatoire désactivé).']
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \Quiz::class,
        ]);
    }
}