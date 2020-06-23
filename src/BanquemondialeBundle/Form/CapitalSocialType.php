<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
/**
 * Description of CapitalSocialType
 *
 * @author fgueye
 */
class CapitalSocialType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('id')
                ->add('formeJuridique')
                ->add('denominationSociale')
                ->add('capitalSocial',TextType::class,array('required'=>true))
                ->add('apportNumeraire',TextType::class,array('required'=>true))
                ->add('apportNature',TextType::class,array('required'=>true));
    }
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemande'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'banquemondialebundle_dossierDemande';
    }
}
