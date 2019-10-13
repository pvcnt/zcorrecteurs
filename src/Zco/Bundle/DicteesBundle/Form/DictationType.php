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

namespace Zco\Bundle\DicteesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Zco\Bundle\CoreBundle\Form\Type\ZformType;
use Zco\Bundle\DicteesBundle\Domain\Dictation;

final class DictationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'Titre',
            'constraints' => [new Assert\NotBlank(), new Assert\Length(['max' => 255])],
        ]);
        $builder->add('level', ChoiceType::class, [
            'label' => 'Difficulté',
            'choices' => array_flip(Dictation::LEVELS),
            'choice_attr' => function ($choice, $key, $value) {
                return ['style' => 'color: ' . Dictation::COLORS[$choice]];
            },
        ]);

        $estimatedTimes = array();
        foreach (range(5, 55, 5) as $t) {
            $estimatedTimes[$t . ' minutes'] = $t;
        }
        $builder->add('estimated_time', ChoiceType::class, array(
            'label' => 'Temps estimé',
            'choices' => $estimatedTimes,
        ));

        $builder->add('text', TextareaType::class, [
            'label' => 'Texte',
            'attr' => ['class' => 'input-xxlarge', 'rows' => 10],
            'required' => false,
        ]);
        $builder->add('slow_voice', FileType::class, [
            'label' => 'Lecture lente',
            'required' => false,
            'constraints' => [new Assert\File([
                'maxSize' => sizeint(ini_get('upload_max_filesize')),
                'mimeTypes' => ['application/ogg', 'audio/mpeg'],
            ])],
        ]);
        $builder->add('fast_voice', FileType::class, [
            'label' => 'Lecture rapide',
            'required' => false,
            'constraints' => [new Assert\File([
                'maxSize' => sizeint(ini_get('upload_max_filesize')),
                'mimeTypes' => ['application/ogg', 'audio/mpeg'],
            ])],
        ]);
        $builder->add('author_first_name', TextType::class, [
            'label' => 'Prénom de l\'auteur',
            'required' => false,
            'constraints' => [new Assert\Length(['max' => 100])],
        ]);
        $builder->add('author_last_name', TextType::class, [
            'label' => 'Nom de l\'auteur',
            'required' => false,
            'constraints' => [new Assert\Length(['max' => 100])],
        ]);
        $builder->add('source', TextType::class, [
            'label' => 'Source',
            'required' => false,
            'constraints' => [new Assert\Length(['max' => 255])],
        ]);
        $builder->add('icon', FileType::class, [
            'label' => 'Icône',
            'required' => false,
            'constraints' => [new Assert\Image()],
        ]);
        $builder->add('description', ZformType::class, [
            'required' => false,
        ]);
        $builder->add('indications', ZformType::class, [
            'required' => false,
        ]);
        $builder->add('comments', ZformType::class, [
            'label' => 'Commentaires',
            'required' => false,
        ]);
        $builder->add('publish', CheckboxType::class, [
            'label' => 'Mettre en ligne',
            'required' => false,
        ]);
    }
}