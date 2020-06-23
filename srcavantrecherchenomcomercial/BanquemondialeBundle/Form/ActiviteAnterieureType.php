<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActiviteAnterieureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', 'text', array('attr'=>array('placeholder' => 'placeholder.mois.annee'), 'required' => true))           
            ->add('dateFin', 'text', array('attr'=>array('placeholder' => 'placeholder.mois.annee'), 'required' => true))
            ->add('precedentRccm')
            ->add('natureActivite')
            ->add('etablissementPrincipal')
            ->add('etablissementSecondaire')
            ->add('rccmEtabSecondaire')
            ->add('adresse')
            ->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'denominationSociale'))			
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\ActiviteAnterieure'
        ));
    }
}
