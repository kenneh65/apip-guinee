<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;

class DepartementType extends AbstractType {

   

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('code')
                ->add('libelle')
               ->add('pays', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
                       return $formJ->getListPays($options['locale']);
                    }, 'property' => 'libelle','multiple' => false,
                    'label'=>'region.pays','placeholder'=>'message.selectionner','preferred_choices'=>function($val,$key) use (&$options){
                        if ($val->getPays()->getid()==$options['pays'])
                                return true;
                        return false;
                    }
                ))
                ->add('Region', 'entity', array('class' => 'BanquemondialeBundle:Region', 'property' => 'libelle', 'multiple' => false,'placeholder'=>' '))
        ;
       $builder->get('pays')->addModelTransformer(new CallbackTransformer(
                        // objet vers string
            function ($formeAsObject) {return (String)$formeAsObject;},
                        // string vers objet
            function ($formAsString) {return $formAsString->getPays();}
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Departement',
              'locale'=>'fr',
            'pays'=>'0'
        ));
    }

}
