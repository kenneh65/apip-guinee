<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use ParametrageBundle\Repository\PoleRepository;
use Symfony\Component\Form\CallbackTransformer;

class TarificationType extends AbstractType {

    protected $options;

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
        $this->typeOpTraduit = $opts['typeOpTraduit'];
        $this->formJTraduit = $opts['formeJTraduit'];
        $builder
                ->add('montant',null,array('required'=>true))
                //->add('pole', null, array('placeholder' => 'message.selectionner','required'=>true))
                ->add('typeDossier', null, array('placeholder' => 'message.selectionner', 'property' => 'libelle', 'required'=>true, 'choice_translation_domain' => true))
                ->add('libelleTarification', null, array('placeholder' => 'message.selectionner', 'property' => 'libelle', 'required'=>true))
				->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole','query_builder' => function (PoleRepository $pole){
                        return $pole->getPolesActifs();
                    }, 'property' => 'nom',
                    'multiple' => false, 'placeholder' => 'message.selectionner', 'choice_translation_domain' => true
                ))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner'
                ))
                ->add('typeOperation', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeOperationTraduction',
                    'query_builder' => function (TypeOperationTraductionRepository $typOp)use(&$opts) {
                        $langue = $opts['langue'];
                        return $typOp->getListOperationByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    //'placeholder' => 'message.selectionner', 
                    'multiple' => false))
        ;
        $builder->get('typeOperation')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($operationAsObject) {
            return $this->typeOpTraduit; //$operationAsObject;
        },
                // string vers objet
                function ($operationAsString) {
            return $operationAsString->getTypeOperation();
        }
        ));
        $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
                // objet vers string

                function ($formAsObject) {
            return $this->formJTraduit; //$formAsObject ;
        },
                // string vers objet
                function ($formAsString) {
            return $formAsString->getFormeJuridique();
        }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\Tarification'
        ));
    }

}
