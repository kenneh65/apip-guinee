<?php

namespace DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServiceJuriqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denominationCommercial')
            ->add('isPiecesIdentite')
            ->add('isDenomination')
            ->add('isConformiteJuridique')
            ->add('isCapital')
            ->add('isDuree')
            ->add('isActivites')
            ->add('remarqueVerificateur')
            ->add('dateVerification')
            // ->add('isCreated')
            // ->add('utilisateur')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DefaultBundle\Entity\ServiceJurique'
        ));
    }
}
