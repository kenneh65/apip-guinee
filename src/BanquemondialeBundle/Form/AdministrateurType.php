<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Description of AdministrateurType
 *
 * @author DELL
 */
class AdministrateurType  extends AbstractType 
{
     protected $options;

    //protected $pays;

    public function __construct($options = null) {
        $this->options = $options;
        //$this->pays=$pays;
    }
    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $opts = $this->options;
        $builder
            //->add('idDossierDemande')
            ->add('nom')
            ->add('prenom')
            ->add('dateNaissance','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr'=>array('placeholder' => 'message.placeholder.date')))
            ->add('lieuNaissance')
            ->add('adresse')
            ->add('dureeMandat','integer')
            //->add('fonction')
            //->add('fonction','entity', array('class' => 'BanquemondialeBundle:Fonction', 'multiple' => false, 'property' => 'code','placeholder' => '-- selectionner la fonction --'))
    
            ->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'id'))

	;
                    
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Administrateur',            
            'langue'=>'BanquemondialeBundle\Entity\Langue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banquemondialebundle_administrateur';
    }
    //put your code here
}
