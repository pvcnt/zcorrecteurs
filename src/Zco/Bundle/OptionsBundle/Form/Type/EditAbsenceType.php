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

namespace Zco\Bundle\OptionsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zco\Bundle\CoreBundle\Form\Type\ZformType;

/**
 * Formulaire permettant de modifier le profil d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditAbsenceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('absence_start_date', DateType::class, array(
            'label' => 'Absent à partir du',
            'input' => 'string',
            'widget' => 'single_text',
            'format' => 'Y-MM-dd',
        ));
        $builder->add('absence_end_date', DateType::class, array(
            'label' => 'Absent jusqu\'au',
            'required' => false,
            'input' => 'string',
            'widget' => 'single_text',
            'format' => 'Y-MM-dd',
        ));
        $builder->add('absence_reason', ZformType::class, array(
            'label' => 'Raison de mon absence',
            'required' => false,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \Utilisateur::class,
            'validation_groups' => array('absence'),
        ]);
    }
}