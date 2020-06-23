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
use BanquemondialeBundle\Repository\GenreTraductionRepository;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;

/**
 * Description of EmployeType
 *
 * @author DELL
 */
class EmployeType extends AbstractType {

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
        $this->langue = $opts['langue'];
        $this->definedSexeTraduit=$opts['definedSexeTraduit'];
        $this->definedPaysTraduit=$opts['definedPaysTraduit'];
        $builder
                ->add('nom')
                ->add('prenom')
                ->add('sexe', 'entity', array('class' => 'BanquemondialeBundle:GenreTraduction', 'query_builder' => function (GenreTraductionRepository $formJ) {
                        $langue = $this->langue;
                        return $formJ->getListGenreByLanque($langue->getId());
                    }, 'property' => 'libelle', 'placeholder' => 'message.selectionner', 'multiple' => false))
                ->add('dateNaissance', 'datetime', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false))
                ->add('matricule')
                ->add('dateEmbauche', 'datetime', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false))
                ->add('formation')
                ->add('dernierSalaire')
                ->add('dernierDiplome')
                ->add('emploiOccupe')
                ->add('categorieProfessionnel')
                ->add('nationalite', 'entity', array('class' => 'BanquemondialeBundle:PaysTraduction', 'query_builder' => function (PaysTraductionRepository $formJ) {
                        $langue = $this->langue;
                        return $formJ->getListPaysByLanque($langue->getId());
                    }, 'property' => 'libelle', 'placeholder' => 'message.selectionner', 'multiple' => false))

        ;
        $builder->get('sexe')->addModelTransformer(new CallbackTransformer(
                // genre vers genreTraduction							
                function ($object) {
            return $this->definedSexeTraduit;
        },
                // genreTraduction vers genre
                function ($objectTraduction) {
            return $objectTraduction->getGenre();
        }
        ));
        $builder->get('nationalite')->addModelTransformer(new CallbackTransformer(
                // pays vers paysTraduction							
                function ($object) {
            return $this->definedPaysTraduit;
        },
                // paysTraduction vers pays
                function ($objectTraduction) {
            if ($objectTraduction) {
                return $objectTraduction->getPays();
            } else {
                return null;
            }
        }
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Employe'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'frmEmploye';
    }

}
