<?php

namespace FavorisBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class FavorisAddType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('url', UrlType::class, array('required'=>true))
            ->add('title',TextType::class, array('required'=>false))
            ->add('description',TextType::class, array('required'=>false))
            ->add('position', IntegerType::class, array('required'=>false))
            ->add('favicon', HiddenType::class, array('required'=>false))
            ->add('directory', EntityType::class, array(
                'class' => 'FavorisBundle:Directory',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.title', 'ASC');
                },
                'choice_label' => 'title',
            ))

            ->add('submit', SubmitType::class);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FavorisBundle\Entity\Favoris'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'favorisbundle_favoris_add';
    }
}
