<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilisateursBundle\Form\RegistrationForm2Type;

class AdministrationInstallationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateur',new RegistrationForm2Type());
            //->add('profil');
    }
   
   public function getParent(){
	   return new AdministrationType();
   }
   
   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'locale'=>'fr'
        ));
    }
}
