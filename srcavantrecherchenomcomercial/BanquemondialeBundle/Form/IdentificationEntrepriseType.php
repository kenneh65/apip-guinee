<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class IdentificationEntrepriseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('denominationSociale')
            ->add('nomcommercial')
            ->add('datedebutactivite','date', array('widget' =>'single_text', 'format' => 'yyyy-MM-dd'))



            ->add('exercicesocial','date', array('widget' =>'single_text', 'format' => 'yyyy-MM-dd'))
            ->add('aciviteexercee','textarea',array('attr' => array('rows' => '3','col' => '250', 'max_length' => 20)))
            ->add('activiteprincipale','textarea',array('attr' => array('rows' => '3','col' => '250', 'max_length' => 20)))
            ->add('sigle')
            ->add('enseigne')
            ->add('duree')
            ->add('datesignaturestatus','date', array('widget' =>'single_text', 'format' => 'yyyy-MM-dd'))
            ->add('adressesiege')
            ->add('boitepostale')
            ->add('telephone')
            ->add('email','email')
            //->add('fax')






        ;


        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\IdentificationEntreprise'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'banquemondialebundle_identificationEntreprise';
    }
}
