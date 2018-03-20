<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', TextType::class, ['label' => 'Pseudo'])
                ->add('lastname', TextType::class, ['label' => 'Nom'])
                ->add('firstname', TextType::class, ['label' => 'Prénom'])
                ->add('email', EmailType::class, ['label' => 'Email'])
                //le mdp en clair que l'on va pas stoker en bdd
                //repeated 2 champ identique
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'Mot de passe'],
                    'second_options' => ['label' => 'Confirmation de mot de passe'],
                    //attribut required dans le champ de formulaire si le formulaire
                    //a été créé avec "registration" dans ses groupes de validation
                    'required'=> in_array('registration', (array)$options['validation_groups'])
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_user';
    }

}
