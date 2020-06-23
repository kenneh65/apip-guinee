<?php

namespace BanquemondialeBundle\Form;

use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\EntrepriseRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DossierPoleSearchType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected $options;

    //protected $pays;

    public function __construct($options = null) {
        $this->options = $options;
        //$this->pays=$pays;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $opts = $this->options;
        $this->langue = $options['langue'];

        $builder
            ->add('statusRetrait', ChoiceType::class, array(
                'choices' => array(1=>'Dossier retiré',0=>'Dossier non retiré'),
                'mapped' => false,
                'required' => false, 'placeholder' => '========== Choisisez un statut =========='
            ))
                ->add('numeroDossier', 'text', array('required' => false))
                ->add('denominationSociale', 'text', array('required' => false))
                ->add('gerant', 'text', array('required' => false))
                ->add('dateCreationDebut', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('dateCreationFin', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('dateDelivranceDebut', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('dateDelivranceFin', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false, 'mapped' => false))
                ->add('typeOperation', 'entity', array('class' => 'BanquemondialeBundle:TypeOperationTraduction', 'required' => false, 'query_builder' => function ( TypeOperationTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        $idl = $langue->getId();
                        return $formJ->getListOperationByLanque($idl);
                    }, 'choice_value' => 'typeOperation', 'property' => 'libelle', 'placeholder' => 'message.selectionner.typeoperation', 'multiple' => false))
                ->add('typeDossier', null, array('placeholder' => 'message.selectionner', 'property' => 'libelle', 'required' => false))
                ->add('formeJuridique', 'entity', array('class' => 'BanquemondialeBundle:FormeJuridiqueTraduction', 'required' => false, 'query_builder' => function ( FormeJuridiqueTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        $idl = $langue->getId();
                        return $formJ->getListFormeJuridiqueByLanque($idl);
                    }, 'choice_value' => 'formeJuridique.id', 'property' => 'libelle', 'placeholder' => 'message.selectionner.formejuridique', 'multiple' => false))
                ->add('entreprise', 'entity', array('class' => 'BanquemondialeBundle:Entreprise',
                    'query_builder' => function (EntrepriseRepository $entreprise) {
                        return $entreprise->getListeDesStructures();
                    }, 'property' => 'denomination', 'placeholder' => 'message.selectionner', 'required' => false))
        ;
        /*
          ->add('statut','entity',array('class'=>'BanquemondialeBundle:StatutTraitementTraduction','required'=>false,'query_builder'=>function(StatutTraitementTraductionRepository $statut)use(&$opts){
          $langue = $opts['langue'];
          $idl = $langue->getId();
          return $statut->getListStatutTraitementByLangue($idl);
          },'choice_value'=>'statutTraitement.id','property'=>'libelle','placeholder'=>'message.selectionner.statuttraitement','multiple'=>false))
         */
        ;






        $builder->get('typeOperation')->addModelTransformer(new CallbackTransformer(
                // typeOperation vers typeOperationTraduction							
                function ($object) {
            return null;
        },
                // typeOperationTraduction vers typeOperation
                function ($objectTraduction) {
            if ($objectTraduction) {
                return $objectTraduction->getTypeOperation();
            } else
                return null;
        }
        ));

        $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
                // formeJuridique vers formeJuridiqueTraduction							
                function ($object) {
            return null;
        },
                // formeJuridiqueTraduction vers formeJuridique
                function ($objectTraduction) {
            if ($objectTraduction) {
                return $objectTraduction->getFormeJuridique();
            } else
                return null;
        }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'langue' => 'BanquemondialeBundle\Entity\Langue',
            'csrf_protection' => false,
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemande'
        ));
    }

    public function getName() {
        return 'dossiersPole';
    }

}
