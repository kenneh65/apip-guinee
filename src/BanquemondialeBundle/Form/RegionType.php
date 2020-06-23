<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
class RegionType extends AbstractType
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
        $this->paysTraduit = $opts['paysTraduit'];
        $builder
            ->add('libelle','text',array('label'=>'region.libelle'))
            ->add('pays', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ)use(&$options) {
                        //$langue = $opts['langue'];
                        //$idl = $langue->getId();
                        return $formJ->getPaysResidenceByCodeLanque($options['locale']);
                    }, 'property' => 'libelle',
                    'multiple' => false, 'label'=>'region.pays'
                ))
			// 'disabled' =>'true', 'read_only' =>'true'
            /*->add('pays', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
                       return $formJ->getListPays($options['locale']);
                    }, 'property' => 'libelle','multiple' => false,
                    'label'=>'region.pays'
                ))*/
			
        ;
         $builder->get('pays')->addModelTransformer(new CallbackTransformer(
                        // objet vers string
            function ($formeAsObject) {
                return $this->paysTraduit;            
            },
                        // string vers objet
            function ($formAsString) {return $formAsString->getPays();}
        ));
 
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Region',
            'locale'=>'fr'
        ));
    }
}
