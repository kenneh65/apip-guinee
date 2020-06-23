<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use BanquemondialeBundle\Form\MediaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LiensutilesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','text',array('label'=>'lien.titre','required'=>true))
           
            ->add('description','textarea',array('attr'=>array('class' => 'ckeditor'),'label'=>'lien.description'))
            ->add('url','url',array('label'=>'URL (*)','required'=>true));


           

        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Liensutiles'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Banquemondialebundle_liensutiles';
    }
}
