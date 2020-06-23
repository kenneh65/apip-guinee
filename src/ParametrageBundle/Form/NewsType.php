<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class NewsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre','text',array('label'=>'news.titre'))
            ->add('contenu', 'textarea',array('attr'=>array('class'=>'ckeditor form-control'),'label'=>'news.contenu'))
            ->add('dateExpiration','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy hh:mm'))
            ->add('datePublication','datetime',array('widget' => 'single_text', 'format' => 'dd-MM-yyyy hh:mm'))
            
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\News'
        ));
    }
}
