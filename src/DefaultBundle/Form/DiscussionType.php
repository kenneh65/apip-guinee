<?php

namespace DefaultBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscussionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('objet','text',array('label'=>'discussion.objet','attr'=>array('class'=>'form-control')))
            ->add('numeroDossier','text',array('required' => true,'mapped'=>false)) 
			->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole','property' => 'nom','multiple' => false,'required' => true,'mapped'=>false))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DefaultBundle\Entity\Discussion'
        ));
    }
}
