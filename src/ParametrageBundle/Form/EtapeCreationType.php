<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use ParametrageBundle\Repository\FonctionnaliteTraductionRepository;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EtapeCreationType extends AbstractType {

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
        $this->fonctionnaliteTraduit = $opts['fonctionnaliteTraduit'];
        $this->formeJTraduit = $opts['formeJTraduit'];
        $builder
                /* ->add('etape', EntityType::class, array(
                  'class' => 'ParametrageBundle:Fonctionnalite',
                  'choice_label' => 'titre',
                  'placeholder' => 'message.selectionner.etape'
                  )) */
                ->add('etape', 'entity', array(
                    'class' => 'ParametrageBundle:FonctionnaliteTraduction',
                    'query_builder' => function (FonctionnaliteTraductionRepository $fonctJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $fonctJ->getListFonctionnaliteByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner.etape'
                ))
                ->add('ordre')
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle', //'choice_value' => 'formeJuridique.id',
                    'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
                ))
        ;

        $builder->get('etape')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($formeAsObject) {
            return $this->fonctionnaliteTraduit;
        },
                // string vers objet
                function ($formAsString) {
            return $formAsString->getFonctionnalite();
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
            'data_class' => 'ParametrageBundle\Entity\EtapeCreation'
        ));
    }

}
