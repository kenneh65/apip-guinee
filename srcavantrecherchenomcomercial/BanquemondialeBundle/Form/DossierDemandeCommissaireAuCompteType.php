<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Form;

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\TypeFonctionCommissaireTraductionRepository;
use BanquemondialeBundle\Repository\CommissionnaireAuCompteRepository;

/**
 * Description of DossierDemandeCommissaireAuCompteType
 *
 * @author mthioye
 */
class DossierDemandeCommissaireAuCompteType extends AbstractType {

    //put your code here
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
        $this->definedTypeFonctionCommissaire = $opts['definedTypeFonctionCommissaire'];
        $builder
//                ->add('commissionnaireAuCompte')
                /*->add('commissionnaireAuCompte', 'entity', array(
                    'class' => 'BanquemondialeBundle:CommissionnaireAuCompte',
                    'property' => 'fullName',
                    'multiple' => false, 'placeholder' => 'message.selectionner.nom'
                ))*/
				 ->add('commissionnaireAuCompte', 'entity', array(
                    'class' => 'BanquemondialeBundle:CommissionnaireAuCompte',
                    'query_builder' => function (CommissionnaireAuCompteRepository $formJ) {
                        return $formJ->getListCommissaire();
                    }, 'property' => 'fullName',
                    'multiple' => false, 'placeholder' => 'message.selectionner.nom'
                ))
                ->add('prenom','text',array('read_only' =>'true','required'=>false))
                ->add('adresse','text',array('read_only' =>'true','required'=>false))
                ->add('fonction', 'entity', array(
                    'class' => 'BanquemondialeBundle:TypeFonctionCommissaireTraduction',
                    'query_builder' => function (TypeFonctionCommissaireTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];					
                        $idl = $langue->getId(); //$langue->getId()
                        return $formJ->getListTypeFonctionCommissaireByLanque($idl);
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner.fonction'
                ))

        ;

        $builder->get('fonction')->addModelTransformer(new CallbackTransformer(
				// fonction vers fonctionTraduction							
                function ($object) {return $this->definedTypeFonctionCommissaire;;},
				// fonctionTraduction vers fonction
                function ($objectTraduction) {return $objectTraduction->getTypeFonctionCommissaire();}
		));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemandeCommissionnaireAuCompte'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'banquemondialebundle_commissionnaireAuCompte';
    }

//    protected function buildChoiceNomPrenom() {
//        $choices = [];
//        $commissaireCpteRepository = $this->getDoctrine()->getRepository('BanquemondialeBundle:CommissionnaireAuCompte');
//        $commissaires = $commissaireCpteRepository->findAll();
//
//        foreach ($commissaires as $commissaire) {
//            $choices[$commissaire->getId()] = $commissaire->getPrenom() . ' - ' . $commissaire->getName();
//        }
//
//        return $choices;
//    }

}
