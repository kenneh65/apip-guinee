<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\DocumentTraductionRepository;
use BanquemondialeBundle\Repository\FonctionTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;

class PieceJointeType extends AbstractType {

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
        $this->docTraduit = $opts['docTraduit'];
        $this->fonctionTraduit = $opts['fonctionTraduit'];
        $builder
                ->add('typeOperation', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeOperationTraduction',
                    'query_builder' => function (TypeOperationTraductionRepository $typOp)use(&$opts) {
                        $langue = $opts['langue'];
                        return $typOp->getListOperationByLanque($langue->getId());
                    },/*'choice_value'=>'typeOperation.id',*/ 'property' => 'libelle', 'placeholder' => 'message.selectionner', 'multiple' => false))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    },/*'choice_value'=>'formeJuridique.id',*/ 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner'
                ))
                ->add('document', 'entity', array(
                    'class' => 'BanquemondialeBundle:DocumentTraduction',
                    'query_builder' => function (DocumentTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListDocumentByLanque($langue->getId());
                    },/*'choice_value'=>'document.id', */'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner'
                ))
                ->add('fonction', 'entity', array(
                    'class' => 'BanquemondialeBundle:FonctionTraduction', 'required' => false,
                    'query_builder' => function (FonctionTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        $idl = $langue->getId(); //$langue->getId()
                        return $formJ->getListFonctionByLangue($idl);
                    },/*'choice_value'=>'fonction.id', */'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner'
                ))
        ;
        $builder->get('typeOperation')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($operationAsObject) {
            return $this->typOpTraduit;
            ;
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
        $builder->get('document')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($docAsObject) {
            return $this->docTraduit;
        },
                // string vers objet
                function ($docAsString) {
            return $docAsString->getDocument();
        }));
        $builder->get('fonction')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($fctAsObject) {
            return $this->fonctionTraduit;
        },
                // string vers objet
                function ($fctAsString) {
            if ($fctAsString) {
                return $fctAsString->getFonction();
            }
        }));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\PieceJointe'
        ));
    }

}
