<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CollectionPieceJointeType extends AbstractType
{

 protected $options;

	
/**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('file', FileType::class)
			->add('file')	
		
			->add('f', 'collection', array(
        'type'         => new CategoryType(),
        'allow_add'    => true,
        'allow_delete' => true
      ))

			
        ;
    }
	
	
	 /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            
        ));
    }
	
    public function getName()
    {
        return 'banquemondialebundle_collectionpiecejointe';
    }
}

