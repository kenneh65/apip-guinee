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
use BanquemondialeBundle\Entity\FonctionTraduction;
use BanquemondialeBundle\Repository\FonctionTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
/**
 * Description of CommissionnaireAuCompte
 *
 * @author DELL
 */
class CommissionnaireAuCompteType extends AbstractType {
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
            ->add('nom','text',array('label'=>'commissaire.nom','attr'=>array('class'=>'form-control')))
            ->add('prenom','text',array('label'=>'commissaire.prenom','attr'=>array('class'=>'form-control'), 'required' => false))
            ->add('dateNaissance','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'required' => false))
            ->add('lieuNaissance','text',array('label'=>'commissaire.lieu_naissance','attr'=>array('class'=>'form-control'), 'required' => false))
            ->add('adresse','textarea',array('label'=>'commissaire.adresse','attr'=>array('class'=>'form-control')))
			->add('telfax','text',array('label'=>'telephone','attr'=>array('class'=>'form-control'), 'required' => false))
			->add('bp','text',array('label'=>'bp','attr'=>array('class'=>'form-control'), 'required' => false))
			->add('email','text',array('label'=>'email','attr'=>array('class'=>'form-control'), 'required' => false))
			->add('types','text',array('label'=>'type','attr'=>array('class'=>'form-control'), 'required' => false))
			->add('numeroAffiliation','text',array('label'=>'numero_affiliation','attr'=>array('class'=>'form-control')))
			->add('actif', CheckboxType::class, array('label' => 'numero_affiliation','required' => false))			
	;                           
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\CommissionnaireAuCompte',            
            'langue'=>'BanquemondialeBundle\Entity\Langue'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banquemondialebundle_commissionnaireAuCompte';
    }
    //put your code here
}
