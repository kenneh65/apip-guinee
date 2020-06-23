<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use BanquemondialeBundle\Form\MediaType;


class DocumentationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','text',array('label'=>'guide.titre','attr'=>array('class'=>'form-control')))
           
            ->add('description','textarea')
          
            ->add('fichier', new MediaType(),array('label'=>'guide.fichier','required'=>false));


           

        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Documentation',
             'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Banquemondialebundle_documentation';
    }
}
