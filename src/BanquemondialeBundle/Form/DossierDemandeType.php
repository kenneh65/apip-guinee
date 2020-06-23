<?php

namespace BanquemondialeBundle\Form;

use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use BanquemondialeBundle\Repository\PrefectureRepository;
use BanquemondialeBundle\Repository\QuartierRepository;
use BanquemondialeBundle\Repository\RegionRepository;
use BanquemondialeBundle\Repository\SousPrefectureCommuneRepository;
use BanquemondialeBundle\Repository\TypeDossierRepository;
use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use ParametrageBundle\Repository\CategorieActiviteTraductionRepository;
use ParametrageBundle\Repository\SecteurActiviteTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DossierDemandeType extends AbstractType
{

    protected $options;

    //protected $pays;

    public function __construct($options = null)
    {
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
        $this->typOpTraduit = $opts['typOpTraduit'];
        $this->formeJTraduit = $opts['formeJTraduit'];
        $this->secteurTraduit = $opts['secteurTraduit'];
        $this->secteurTraduit2 = $opts['secteurTraduit2'];
        $this->secteurTraduit3 = $opts['secteurTraduit3'];
        $this->categorieTraduit = $opts['categorieTraduit'];
        $this->paysTraduit = $opts['paysTraduit'];
        $this->pref = $opts['pref'];
        $this->sousP = $opts['sousP'];
        //$this->qrt=$opts['qrt'];
        //$pays = $this->pays;
        $builder
            ->add('typeOperation', 'entity', array(
                'class' => 'BanquemondialeBundle:TypeOperationTraduction',
                'query_builder' => function (TypeOperationTraductionRepository $typOp) use (&$opts) {
                    $langue = $opts['langue'];
                    return $typOp->getListOperationByLanque($langue->getId());
                }, 'property' => 'libelle', 'multiple' => false))
            ->add('typeDossier', 'entity', array('class' => 'BanquemondialeBundle:TypeDossier',
                'query_builder' => function (TypeDossierRepository $typDoss) use (&$opts) {
                    $idP = $opts['idP'];
                    $sgleP = $opts['sgleP'];

                    return $typDoss->getListTypeDossier($idP, $sgleP);
                },
                //'placeholder' => 'message.selectionner',
                'property' => 'libelle', 'required' => true, 'choice_translation_domain' => true))
            ->add('formeJuridique', 'entity', array(
                'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                'query_builder' => function (FormeJuridiqueTraductionRepository $formJ) use (&$opts) {

                    $langue = $opts['langue'];
                    return $formJ->getListeFormeJuridiqueByLanque($langue->getId(), $opts['sgleP']);
                }, 'property' => 'libelle', //'choice_value' => 'formeJuridique.id',
                'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
            ))
            ->add('denominationSociale')
            ->add('nomCommercial', null, array('required' => true))
            ->add('categorie', 'entity', array('class' => 'ParametrageBundle:CategorieActiviteTraduction',
                'query_builder' => function (CategorieActiviteTraductionRepository $rep) use (&$opts) {
                    $langue = $opts['langue'];
                    $idl = $langue->getId();
                    return $rep->getListCategoriesByLangue($idl);
                }, 'property' => 'libelle',// 'choice_value' => 'categorieActivite.id',
                'multiple' => false, 'placeholder' => 'message.selectionner.categorie'
            ))
            ->add('secteurActivite', 'entity', array('class' => 'ParametrageBundle:SecteurActiviteTraduction',
                'query_builder' => function (SecteurActiviteTraductionRepository $rep) use (&$opts) {
                    $langue = $opts['langue'];
                    $idl = $langue->getId();
                    return $rep->getListSecteursByLangue($idl);
                }, 'property' => 'libelle',
                'multiple' => false, 'placeholder' => 'message.selectionner.activite'
            ))
            ->add('activiteSecondaire', 'entity', array('class' => 'ParametrageBundle:SecteurActiviteTraduction',
                'query_builder' => function (SecteurActiviteTraductionRepository $rep) use (&$opts) {
                    $langue = $opts['langue'];
                    $idl = $langue->getId();
                    return $rep->getListSecteursByLangue($idl);
                }, 'property' => 'libelle', 'required' => false,
                'multiple' => false, 'placeholder' => 'message.selectionner.activite'
            ))
            ->add('activiteSecondaire2', 'entity', array('class' => 'ParametrageBundle:SecteurActiviteTraduction',
                'query_builder' => function (SecteurActiviteTraductionRepository $rep) use (&$opts) {
                    $langue = $opts['langue'];
                    $idl = $langue->getId();
                    return $rep->getListSecteursByLangue($idl);
                }, 'property' => 'libelle', 'required' => false,
                'multiple' => false, 'placeholder' => 'message.selectionner.activite'
            ))
            //->add('activiteSecondaire')
            //->add('activiteSecondaire2')
            ->add('activiteSociale')
            ->add('sigle')
            ->add('enseigne')
            ->add('adresseSiege', 'textarea')
            ->add('adresseEtablissement', 'text', array('required' => true))
            ->add('rccmSiege', 'text', array('required' => false))
            ->add('pays', 'entity', array(
                'class' => 'BanquemondialeBundle:PaysTraduction',
                'query_builder' => function (PaysTraductionRepository $formJ) use (&$opts) {
                    $langue = $opts['langue'];
                    $idl = $langue->getId();
                    return $formJ->getListPaysResidenceByLanque($idl);
                }, 'property' => 'libelle',
                'multiple' => false
            ))
            ->add('region', 'entity', array(
                'class' => 'BanquemondialeBundle:Region',
                'query_builder' => function (RegionRepository $formJ) use (&$options) {
                    return $formJ->getRegionByPaysdeResidence();
                }, 'property' => 'libelle', 'multiple' => false,
                'placeholder' => 'message.selectionner'
            ))
            ->add('prefecture', 'entity', array(
                'class' => 'BanquemondialeBundle:Prefecture', 'query_builder' => function (PrefectureRepository $pref) use (&$options) {
                    return $pref->getListPrefecture($this->pref);
                },
                'property' => 'libelle', 'multiple' => false, 'required' => true,
                'placeholder' => 'message.selectionner'
            ))
            ->add('sousPrefecture', 'entity', array(
                'class' => 'BanquemondialeBundle:SousPrefectureCommune', 'query_builder' => function (SousPrefectureCommuneRepository $pref) use (&$options) {
                    return $pref->getListOfSousPrefecture($this->pref);
                },
                'property' => 'libelle', 'multiple' => false, 'required' => true,
                'placeholder' => 'message.selectionner'
            ))
            ->add('boitePostale')
            ->add('telephone', null, array('attr' => array('required' => true, 'placeholder' => '(+224) 999-99-99-99')))
            ->add('telephone2', null, array('attr' => array('required' => false, 'placeholder' => '(+224) 999-99-99-99')))
            ->add('email', EmailType::class, array('required' => true))
            ->add('duree', null, array('required' => true))
            ->add('nombreSalariePrevu', null, array('required' => true))
            ->add('dateDebut', DateType::class, array('widget' => 'single_text', 'format' => 'yyyy-MM-dd', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => true))
            ->add('activiteComplementaire', 'textarea', array('required' => false))
            ->add('ville', null, array('required' => true))
            ->add('enActivite', 'choice', array('choices' => array(true => "Oui", false => "Non"),
                    'multiple' => false, 'required' => true, 'placeholder' => 'select')
            )
            ->add('soussigne', null, array('required' => true))
            ->add('fonctionSoussigne', null, array('required' => true))
            ->add('quartierCodifie', 'entity', array(
                'class' => 'BanquemondialeBundle:Quartier', 'query_builder' => function (QuartierRepository $pref) use (&$options) {
                    return $pref->getListOfQuartier($this->sousP);
                },
                'property' => 'libelle', 'multiple' => false, 'required' => true,
                'placeholder' => 'message.selectionner'
            ))
            ->add('telephonePromoteur', null, array('attr' => array('required' => false, 'placeholder' => '(+224) 999-99-99-99')))
            ->add('emailPromoteur', EmailType::class, array('required' => false));
//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            // ... adding the name field if needed
//            $data = $event->getData();
//            $form = $event->getForm();
//
//            if (!$data || null === $data->getFormeJuridique()) {
//                $form->add('adresseEtablissement', 'textarea');
//            }
//        });
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
        $builder->get('pays')->addModelTransformer(new CallbackTransformer(
        // objet vers string
            function ($formeAsObject) {
                return $this->paysTraduit;
            },
            // string vers objet
            function ($formAsString) {
                return $formAsString->getPays();
            }
        ));


        $builder->get('categorie')->addModelTransformer(new CallbackTransformer(
        // objet vers string
            function ($categorieAsObject) {
                return $this->categorieTraduit;
            },
            function ($categorieAsString) {
                return $categorieAsString->getCategorieActivite();
            }
        ));

        $builder->get('secteurActivite')->addModelTransformer(new CallbackTransformer(
        // objet vers string
            function ($secteurAsObject) {
                return $this->secteurTraduit;
            },
            // string vers objet
            function ($secteurAsString) {
                return $secteurAsString->getSecteurActivite();
            }
        ));

        $builder->get('activiteSecondaire')->addModelTransformer(new CallbackTransformer(
        // objet vers string
            function ($secteurAsObject) {
                return $this->secteurTraduit2;
            },
            // string vers objet
            function ($secteurAsString) {
                if ($secteurAsString) {
                    return $secteurAsString->getSecteurActivite();
                } else {
                    return null;
                }
            }
        ));

        $builder->get('activiteSecondaire2')->addModelTransformer(new CallbackTransformer(
        // objet vers string
            function ($secteurAsObject) {
                return $this->secteurTraduit3;
            },
            // string vers objet
            function ($secteurAsString) {
                if ($secteurAsString) {
                    return $secteurAsString->getSecteurActivite();
                } else {
                    return null;
                }
            }
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemande'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banquemondialebundle_dossierDemande';
    }

}
