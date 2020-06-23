<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use ParametrageBundle\Repository\PoleRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CircuitType extends AbstractType {

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
        $this->typOpTraduit = $opts['typeOpTraduit'];
        $this->formeJTraduit = $opts['formeJTraduit'];

        $builder
                ->add('ordre')
                ->add('typeOperation', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeOperationTraduction',
                    'query_builder' => function (TypeOperationTraductionRepository $typOp)use(&$opts) {
                        $langue = $opts['langue'];
                        return $typOp->getListOperationByLanque($langue->getId());
                    }, 'property' => 'libelle', //'choice_value' => 'typeOperation.id',
                    //'placeholder' => 'message.selectionner.operation',
                    'multiple' => false
                ))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle', //'choice_value' => 'formeJuridique.id',
                    'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
                ))
                //->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole', 'placeholder' => 'message.selectionner.pole', 'multiple' => false))
                ->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole',
                    'query_builder' => function (PoleRepository $formJ)use(&$opts) {
                        return $formJ->getListeDesPoleTraitant();
                    }, 'choice_value' => 'id',
                    'placeholder' => 'message.selectionner.pole', 'multiple' => false, 'choice_translation_domain' => true))
                ->add('typeDossier', null, array('placeholder' => 'message.selectionner', 'property' => 'libelle', 'required' => true, 'choice_translation_domain' => true))

        ;
        $builder->get('typeOperation')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($operationAsObject) {
            return $this->typOpTraduit;
        },
                // string vers objet
                function ($operationAsString) {
            return $operationAsString->getTypeOperation();
        }
        ));
        $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($formeAsObject) {


            return $this->formeJTraduit;
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
            'data_class' => 'BanquemondialeBundle\Entity\Circuit'
        ));
    }

}
