<?php

namespace Zco\Bundle\GroupesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, [
            'label' => 'Nom du groupe',
            'constraints' => [new Assert\NotBlank()],
        ]);
        $builder->add('logo', TextType::class, [
            'label' => 'URL du logo masculin',
            'required' => false,
        ]);
        $builder->add('logo_feminin', TextType::class, [
            'label' => 'URL du logo féminin',
            'required' => false,
        ]);
        $builder->add('team', CheckboxType::class, [
            'label' => 'Équipe',
            'required' => false,
        ]);
        $builder->add('secondaire', CheckboxType::class, [
            'label' => 'Groupe secondaire',
            'required' => false,
        ]);
    }

}