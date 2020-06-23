<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MessagerieType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mailerHost','text',array('label'=>'mailer.hote'))
            ->add('expediteurEmail','text',array('label'=>'mailer.expediteur'))	
            ->add('expediteurName','text',array('label'=>'mailer.nom_expediteur'))	
            ->add('mailerPort','number',array('label'=>'mailer.port'))			
            ->add('mailerUser','email',array('label'=>'mailer.identifiant','required'=>false))
            ->add('mailerPassword','password',array('label'=>'mailer.password','required'=>false, 'always_empty' => false))
			->add('encryption','hidden')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\Messagerie'
        ));
    }
}
