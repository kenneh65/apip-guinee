<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\PrefectureRepository;
use BanquemondialeBundle\Repository\RegionRepository;
use Symfony\Component\Form\CallbackTransformer;

class SousPrefectureType1 extends AbstractType {

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
        //$this->paysTraduit = $opts['paysTraduit'];        
        $this->pref = $opts['pref']; 
        
        //die(dump($this->pref));
        $builder               
               ->add('libelle')
               
               ->add('prefecture', 'entity', array(
                    'class' => 'BanquemondialeBundle:Prefecture','query_builder' => function (PrefectureRepository $pref) use(&$options) {
                       return $pref->getListPrefecture($this->pref);
                    },
                    'property' => 'libelle','multiple' => false,'required'=>true,
                    'placeholder'=>'message.selectionner'
                ))
                ->add('typeLocalite', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeLocalite',
                    'property' => 'libelle','multiple' => false,'required'=>true,
                    'placeholder'=>'message.selectionner'
                ))
                //->add('prefecture', 'entity', array('class' => 'BanquemondialeBundle:Region', 'property' => 'libelle', 'multiple' => false,'placeholder'=>' '))
        ;      
        
        
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\SousPrefectureCommune',
              'locale'=>'fr'
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'frmSousPrefecture';
    }

}
