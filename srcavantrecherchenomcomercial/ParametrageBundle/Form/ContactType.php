<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text',array('label'=>'contact.nom'))
            ->add('fonction','text',array('label'=>'contact.fonction', 'required'   => false))
            ->add('telephone','text',array('label'=>'contact.telephone', 'required'   => false))
			->add('telephone2','text',array('label'=>'contact.telephone', 'required'   => false))
            ->add('email','email',array('label'=>'contact.email', 'required'   => false))
            ->add('siteWeb','text',array('label'=>'contact.siteWeb', 'required'   => false))
            ->add('adresse','textarea',array('label'=>'contact.adresse', 'required'   => false))
               ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\Contact'
        ));
    }
}
