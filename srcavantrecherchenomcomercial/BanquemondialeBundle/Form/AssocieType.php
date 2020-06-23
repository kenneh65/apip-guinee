<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use ParametrageBundle\Repository\TypeEntrepriseTraductionRepository;
use BanquemondialeBundle\Repository\FonctionTraductionRepository;
use BanquemondialeBundle\Repository\GenreTraductionRepository;
use BanquemondialeBundle\Repository\SituationMatrimonialeTraductionRepository;
use BanquemondialeBundle\Entity\PaysTraduction;
use ParametrageBundle\Entity\TypeEntrepriseTraduction;
use BanquemondialeBundle\Entity\GenreTraduction;
use BanquemondialeBundle\Entity\FonctionTraduction;
use BanquemondialeBundle\Entity\SituationMatrimonialeTraduction;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Description of AssocieType
 *
 * @author DELL
 */
class AssocieType extends AbstractType {

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
        $this->definedPaysTraduction = $options['definedPaysTraduction'];
        $this->definedTypeEntrepriseTraduction = $options['definedTypeEntrepriseTraduction'];
        /* $this->definedFonctionTraduction = $options['definedFonctionTraduction'];
          $this->definedGenreTraduction = $options['definedGenreTraduction'];
          $this->definedSituationMatrimonialeTraduction = $options['definedSituationMatrimonialeTraduction']; */
        //$this->fonctionTraduit = $options['fonctionTraduit'];
        $opts = $this->options;
        $builder
                //->add('idDossierDemande')
                ->add('nom')
                ->add('prenom', 'text', array('required' => false))
                //->add('genre','entity', array('class' => 'BanquemondialeBundle:Genre', 'multiple' => false, 'property' => 'id'))
                /* ->add('genre', 'entity', array(
                  'class' => 'BanquemondialeBundle:GenreTraduction',
                  'query_builder' => function (GenreTraductionRepository $formJ)use(&$opts) {
                  $langue = $opts['langue'];
                  $idl = $langue->getId(); //$langue->getId()
                  return $formJ->getListGenreByLanque($idl);
                  }, 'property' => 'libelle',
                  'multiple' => false,'placeholder' => 'message.selectionner.genre'
                  )) */
                ->add('dateNaissance', 'datetime', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false))
                //->add('situationMatrimoniale','entity', array('class' => 'BanquemondialeBundle:SituationMatrimoniale', 'multiple' => false, 'property' => 'id'))
                /* ->add('situationMatrimoniale', 'entity', array(
                  'class' => 'BanquemondialeBundle:SituationMatrimonialeTraduction',
                  'query_builder' => function (SituationMatrimonialeTraductionRepository $formJ)use(&$opts) {
                  $langue = $opts['langue'];
                  $idl = $langue->getId(); //$langue->getId()
                  return $formJ->getListSituationMatrimonialeByLanque($idl);
                  }, 'property' => 'libelle',
                  'multiple' => false,'placeholder' => 'message.selectionner.situation_matrimoniale'
                  )) */
//            ->add('nationalite','entity', array('class' => 'BanquemondialeBundle:Pays','query_builder' => function(PaysRepository $er) use ($id) {
//                    return $er->myFindAllPays($id);
//                    }, 'property' => 'code','placeholder' => '-- selectionner le pays --','multiple' => false))
                ->add('pays', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        $idl = $langue->getId(); //$langue->getId()
                        return $formJ->getListPaysByLanque($idl);
                    }, 'property' => 'libelle','required'=>false,
                    'multiple' => false, 'placeholder' => 'message.selectionner.pays'
                ))
                ->add('typeEntreprise', 'entity', array(
                    'class' => 'ParametrageBundle:TypeEntrepriseTraduction',
                    'query_builder' => function (TypeEntrepriseTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        $idl = $langue->getId(); //$langue->getId()
                        return $formJ->getListTypeEntrepriseByLanque($idl);
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner'
                ))
                /* ->add('numeroIdentificationNationale')
                  ->add('typeIdentification',new ChoiceType(),array('choices'=>array('cni'=>'utilisateur.type_cni','passport'=>'utilisateur.type_passport','carte consulaire'=>'utilisateur.type_carte_consulaire','extrait naissance'=>'utilisateur.type_extrait_naissance'),'placeholder' => 'selectionner.type_document_identite','label'=>'utilisateur.type_identiifcation','attr'=>array('class'=>'form-control'))) */
                ->add('adresse')
                ->add('lieuNaissance', 'text', array('required' => false))
//            ->add('fonction','entity', array('class' => 'BanquemondialeBundle:Fonction', 'multiple' => false, 'property' => 'code','placeholder' => '-- selectionner la fonction --'))
                /* ->add('fonction', 'entity', array(
                  'class' => 'BanquemondialeBundle:FonctionTraduction',
                  'query_builder' => function (FonctionTraductionRepository $formJ)use(&$opts) {
                  $langue = $opts['langue'];
                  $idl = $langue->getId(); //$langue->getId()
                  return $formJ->getListFonctionByLangue($idl);
                  }, 'property' => 'libelle',
                  'multiple' => false,'placeholder' => 'message.selectionner.fonction'
                  )) */
                ->add('dossierDemande', 'entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'id'))
        /* ->add('telephone')
          ->add('portable')
          ->add('email','email') */
        ;

        $builder->get('pays')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($paysAsObject) {
            return $this->definedPaysTraduction;
        },
                // string vers objet
                function ($paysAsString) {
             if($paysAsString){return $paysAsString->getPays();}else {return null;}
        }
        ));
        $builder->get('typeEntreprise')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($typeEntrepriseAsObject) {
            return $this->definedTypeEntrepriseTraduction;
        },
                // string vers objet
                function ($typeEntrepriseAsString) {
            return $typeEntrepriseAsString->getTypeEntreprise();
        }
        ));
        /* $builder->get('fonction')->addModelTransformer(new CallbackTransformer(
          // objet vers string
          function ($fonctionAsObject) {return $this->definedFonctionTraduction;},
          // string vers objet
          function ($fonctionAsString) {return $fonctionAsString->getFonction();}
          ));
          $builder->get('genre')->addModelTransformer(new CallbackTransformer(
          // objet vers string
          function ($genreAsObject) {return $this->definedGenreTraduction;},
          // string vers objet
          function ($genreAsString) {return $genreAsString->getGenre();}
          ));
          $builder->get('situationMatrimoniale')->addModelTransformer(new CallbackTransformer(
          // objet vers string
          function ($situationMatrimonialeAsObject) {return $this->definedSituationMatrimonialeTraduction;},
          // string vers objet
          function ($situationMatrimonialeAsString) {return $situationMatrimonialeAsString->getSituationMatrimoniale();}
          )); */
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'definedPaysTraduction' => 'BanquemondialeBundle\Entity\PaysTraduction',
            'definedTypeEntrepriseTraduction' => 'ParametrageBundle\Entity\TypeEntrepriseTraduction',
            /* 'definedGenreTraduction' => 'BanquemondialeBundle\Entity\GenreTraduction',
              'definedFonctionTraduction' => 'BanquemondialeBundle\Entity\FonctionTraduction',
              'fonctionTraduit' => 'BanquemondialeBundle\Entity\FonctionTraduction',
              'definedSituationMatrimonialeTraduction' => 'BanquemondialeBundle\Entity\SituationMatrimonialeTraduction', */
            'langue' => 'BanquemondialeBundle\Entity\Langue',
            'data_class' => 'BanquemondialeBundle\Entity\Associe'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'banquemondialebundle_associe';
    }

}
