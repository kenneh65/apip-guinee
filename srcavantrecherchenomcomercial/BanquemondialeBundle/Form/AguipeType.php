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
use BanquemondialeBundle\Entity\Aguipe;

/**
 * Description of AssocieType
 *
 * @author DELL
 */
class AguipeType extends AbstractType {

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
        $builder
                //->add('personnel')
                ->add('en_activite', 'choice', array('choices' => array(true => "Oui  ", false => "Non  "),
                    'multiple' => false,
                    'required' => true,
                    'placeholder' => 'select'
                        )
                )
                ->add('dateDebutActivite', 'datetime', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'attr' => array('placeholder' => 'message.placeholder.date'), 'required' => false))
                ->add('nombreEmployeActuel', 'number', array('read_only' => 'true'))
                ->add('nombreEmployeGuineen', 'integer', array('attr' => array('min' => 0)))
                ->add('nombreEmployeEtranger', 'integer', array('attr' => array('min' => 0)))
                ->add('nombreEmployePrevisionnel', 'integer', array('attr' => array('min' => 0)))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Aguipe'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'frmAguipe';
    }

}
