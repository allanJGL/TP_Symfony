<?php

namespace App\Form;

use App\Entity\Editor;
use App\Entity\Videogame;
use App\Repository\EditorRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class VideogameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('os')
            ->add('description')
            ->add('release_date', BirthdayType::class)
            ->add('editor', EntityType::class, [
                'class' => Editor::class,
                'query_builder' => function (EditorRepository $editors) {
                    return $editors->createQueryBuilder('e')
                        ->orderBy('e.name');
                },
                'choice_label' => 'name',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Videogame::class,
        ]);
    }
}
