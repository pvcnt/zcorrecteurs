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

namespace Zco\Bundle\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zco\Bundle\PagesBundle\Entity\Contact;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('auteur_prenom', TextType::class, [
            'label' => 'Prénom de l\'auteur',
            'required' => false,
        ]);
        $builder->add('auteur_nom', TextType::class, [
            'label' => 'Nom de l\'auteur',
        ]);
        $builder->add('auteur_autres', TextType::class, [
            'label' => 'Source',
            'required' => false,
        ]);
        $builder->add('contenu', TextareaType::class, [
            'label' => 'Contenu',
            'attr' => ['class' => 'input-xxlarge', 'rows' => 4],
        ]);
        $builder->add('statut', CheckboxType::class, [
            'label' => 'Activée',
            'required' => false,
        ]);
    }
}