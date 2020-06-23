<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use BanquemondialeBundle\Repository\RegionRepository;
use Symfony\Component\Form\CallbackTransformer;

class PrefectureType extends AbstractType {

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
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $opts = $this->options;   
        $this->paysTraduit = $opts['paysTraduit'];        
        //$this->region = $opts['region']; 
        
        $builder               
               ->add('libelle')
               ->add('pays', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction','mapped'=>false,
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
                       return $formJ->getPaysResidenceByCodeLanque($options['locale']);
                    }, 'property' => 'libelle','multiple' => false,
                    'label'=>'region.pays','placeholder'=>'message.selectionner','preferred_choices'=>function($val,$key) use (&$options){
                        if ($val->getPays()->getid()==$options['pays'])
                                return true;
                        return false;
                    }
                ))
                    ->add('region', 'entity', array(
                    'class' => 'BanquemondialeBundle:Region',
                    'query_builder' => function (RegionRepository $formJ) use(&$options) {
                       return $formJ->getRegionByPaysdeResidence();
                    }, 'property' => 'libelle','multiple' => false,
                    'placeholder'=>'message.selectionner'
                ))
                //->add('region', 'entity', array('class' => 'BanquemondialeBundle:Region', 'property' => 'libelle', 'multiple' => false,'placeholder'=>' '))
        ;
       $builder->get('pays')->addModelTransformer(new CallbackTransformer(
                        // objet vers string
            function ($formeAsObject) {return $this->paysTraduit;},
                        // string vers objet
            function ($formAsString) {return $formAsString->getPays();}
        ));
        
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Prefecture',
              'locale'=>'fr',
            'pays'=>'0'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'frmPrefecture';
    }

}
