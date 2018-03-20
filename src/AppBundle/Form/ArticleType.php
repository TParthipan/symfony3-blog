<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add(
                        'title', TextType::class, [
                    'label' => 'Titre'
                        ]
                )
                ->add(
                        'content', TextareaType::class, [
                    'label' => 'Contenu'
                        ]
                )
                ->add(
                        'description', TextareaType::class, [
                    'label' => 'Description'
                        ]
                )
                ->add(
                        'category',
                        //select sur une entité
                        EntityType::class, [
                    'label' => 'Catégorie',
                    //nom de l'entité
                    'class' => 'AppBundle:Category',
                    //nom du champ qui va s'afficher dans les option
                    'choice_label' => 'name',
                    'placeholder' => 'Choisissez une catégorie'
                        ]
                )
                ->add(
                        'image', FileType::class, [
                    'label' => 'Image',
                    //champ optionnel
                    'required' => FALSE
                        ]
                )
        ;
        //$option['data']=l'entité article
        if (!is_null($options['data']->getImage())) {
            $builder->add(
                    'remove_image',
                    CheckboxType::class,
                    [
                        'label'=>'Supprimer l\'image',
                        'required'=>FALSE,
                        //champ non mappé avec l'entité article
                        'mapped'=>FALSE
                    ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_article';
    }

}
