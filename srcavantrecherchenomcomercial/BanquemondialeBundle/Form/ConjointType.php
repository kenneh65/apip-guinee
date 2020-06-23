<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConjointType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')            
            ->add('lieuMariage', 'text', array('required' => false))
			->add('dateMariage', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy','required' => false, 'attr'=>array('placeholder' => 'message.placeholder.date')))
			->add('optionMatrimoniale', 'text', array('required' => false))
			->add('regimeMatrimonial', 'entity', array('class' => 'BanquemondialeBundle:RegimeMatrimonial', 'property' => 'libelle', 'multiple' => false,'placeholder'=>'selectionnez','required' => false))
			->add('clausesRestrictives', 'text', array('required' => false))
			->add('demandeSeparationBiens', 'text', array('required' => false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Conjoint'
        ));
    }
}
