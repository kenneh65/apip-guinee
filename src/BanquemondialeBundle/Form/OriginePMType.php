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
use ParametrageBundle\Repository\TypeOrigineRepository;
use ParametrageBundle\Entity\TypeOrigine;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Description of AssocieType
 *
 * @author DELL
 */
class OriginePMType extends AbstractType 
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
		//$this->definedTypeOrigine = $options['definedTypeOrigine'];
		
        $opts = $this->options;
        $builder
            //->add('typeOrigine','entity', array('class' => 'ParametrageBundle:TypeOrigine', 'multiple' => false, 'property' => 'libelle', 'placeholder' => 'select'))
			->add('typeOrigine', 'entity', array(
                    'class' => 'ParametrageBundle:TypeOrigine',
                    'query_builder' => function (TypeOrigineRepository $formJ)use(&$opts) {
                        $typeOrigine = $opts['typeOrigine'];
                        return $formJ->getTypeOriginePP($typeOrigine);
                    }, 'property' => 'libelle',
                    'multiple' => false,'placeholder' => 'select'
                ))
			->add('nomExploitant','text', array('required' => false))
			->add('prenomExploitant','text', array('required' => false))
			->add('adresseExploitant','text', array('required' => false))
			->add('rccmExploitant','text', array('required' => false))
			->add('nomCommercial','text', array('required' => false))
			->add('sigleOuEnseigne','text', array('required' => false))
			->add('dateOuverture','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => false))
			->add('loueurFondExploitant','text', array('required' => false))
			->add('siExploitant', 'choice', array(
                'choices' => array(
					true => 'oui',
                    false => 'non'				    
                ),
                'multiple' => false,
				'required' => true,
                'placeholder' => 'select'
            ))
			//->add('siExploitant'), CheckboxType::class
			//->add('siEtablissementSecondaire'), CheckboxType::class
			->add('siEtablissementSecondaire', 'choice', array(
                'choices' => array(
					true => 'oui',
                    false => 'non'
                ),
                'multiple' => false,
				'required' => true,
                'placeholder' => 'select'
            ))
			->add('adresseEtablissementSecondaire','text', array('required' => false))
			->add('activiteEtablissementSecondaire','text', array('required' => false))
			->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'id'))
			;                               
        
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\OriginePM'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'frmOriginePM';
    }
}
