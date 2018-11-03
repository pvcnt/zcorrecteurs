<?php

namespace Zco\Bundle\CategoriesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class CategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, [
            'label' => 'Nom de la catégorie',
            'constraints' => [new Assert\NotBlank()],
            'attr' => ['class' => 'input-xxlarge'],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => ['class' => 'input-xxlarge'],
        ]);
        $builder->add('url', TextType::class, [
            'label' => 'URL',
            'required' => false,
            'attr' => ['class' => 'input-xxlarge'],
        ]);
        $builder->add('url_redir', UrlType::class, [
            'label' => 'URL de redirection',
            'required' => false,
            'attr' => ['class' => 'input-xxlarge'],
        ]);
        $builder->add('archive', CheckboxType::class, [
            'label' => 'Catégorie archivée',
            'required' => false,
        ]);
        $builder->add('parent', ChoiceType::class, [
            'label' => 'Catégorie parente',
            'choices' => $options['parent_choices'],
            'choice_translation_domain' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'parent_choices' => [],
        ));
        $resolver->setAllowedTypes('parent_choices', array('array', '\Traversable'));
    }
}