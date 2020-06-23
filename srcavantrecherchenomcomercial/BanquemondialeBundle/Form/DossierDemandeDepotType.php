<?php

namespace BanquemondialeBundle\Form;

use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\TypeDossierRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DossierDemandeDepotType extends AbstractType {

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
        $this->typOpTraduit = $opts['typOpTraduit'];
        $this->formeJTraduit = $opts['formeJTraduit'];
        $this->pref = null;
        //$pays = $this->pays;
        $builder
                ->add('typeOperation', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeOperationTraduction',
                    'query_builder' => function (TypeOperationTraductionRepository $typOp)use(&$opts) {
                        $langue = $opts['langue'];
                        return $typOp->getListOperationByLanque($langue->getId());
                    }, 'property' => 'libelle', 'multiple' => false))
                ->add('typeDossier', 'entity', array('class' => 'BanquemondialeBundle:TypeDossier',
                    'query_builder' => function (TypeDossierRepository $typDoss) {
                        return $typDoss->getListeTypeDossierDepot();
                    }, 'placeholder' => 'message.selectionner', 'property' => 'libelle', 'required' => true, 'choice_translation_domain' => true))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
                ))
                ->add('denominationSociale')
                ->add('nomCommercial')

                /*
                  ->add('categorie', 'entity', array('class' => 'ParametrageBundle:CategorieActiviteTraduction', 'placeholder' => 'message.selectionner', 'property' => 'libelle'
                  , 'mapped' => 'false', 'choice_value' => 'categorieActivite.id','required'=>false))
                 */
                ->add('adresseSiege', 'textarea')
                ->add('email', 'email', array('required' => true))
                ->add('telephonePromoteur', null, array('attr' => array('required' => false, 'placeholder' => 'placeholder.telephone')))
                ->add('emailPromoteur', 'email', array('required' => false))

        //->add('isAguipe', 'checkbox', array('attr' => array('checked'   => 'checked'),))

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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemande'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'banquemondialebundle_dossierDemandeDepot';
    }

}
