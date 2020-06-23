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
use ParametrageBundle\Repository\CnssRepository;
use ParametrageBundle\Entity\Cnss;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Description of AssocieType
 *
 * @author DELL
 */
class CnssType extends AbstractType 
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
            //->add('personnel')
			/*->add('personnel', 'choice', array('choices' => array(true => "Oui  ", false => "Non  "),
            'multiple' => false,
            'expanded' => true,
            'preferred_choices' => array(false),
            'empty_value' => '- Choisissez une option -',
            'empty_data'  => -1
            )
            )*/
			->add('personnel', 'choice', array(
                'choices' => array(
                    true => 'oui',
                    false => 'non'
                ),
                'multiple' => false,
                'placeholder' => 'select',
				'data' => 1,
            ))
			->add('datePremierEmbauche','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => true))
			->add('effectifHomme')
			->add('effectifFemme')
			->add('effectifApprentis')
			->add('effectifTotal','text',array('read_only' =>'true'))//
			/*->add('personnelDomestique', 'choice', array('choices' => array(true => "Oui  ", false => "Non  "),
            'multiple' => false,
            'expanded' => true,
            'preferred_choices' => array(false),
            'empty_value' => '- Choisissez une option -',
            'empty_data'  => -1
            )
            )*/
			->add('personnelDomestique', 'choice', array(
                'choices' => array(
                    true => 'oui',
                    false => 'non'
                ),
                'multiple' => false,
                'placeholder' => 'select'
            ))
			//->add('personnelDomestique')//, CheckboxType::class
			->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'id'))
			;                               
        
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Cnss'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frmCnss';
    }
}
