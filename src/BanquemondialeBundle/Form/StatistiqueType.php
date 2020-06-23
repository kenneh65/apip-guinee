<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;




use Doctrine\ORM\EntityManager; 

class StatistiqueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	 
	  protected $options;



    public function __construct($options = null) {
        $this->options = $options;

    }
	 
	 
 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	  $opts = $this->options;
	 $this->langue = $options['langue'];
	 
	 $builder

				
				->add('dateCreationDebut', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy','attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => true,'mapped'=>false))
				->add('dateCreationFin', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy','attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => true,'mapped'=>false))
	
		;

	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(					
			'langue'=>'BanquemondialeBundle\Entity\Langue',
			'csrf_protection'   => false,
            'data_class' => 'BanquemondialeBundle\Entity\DossierDemande'
        ));
    }
	
	public function getName()
	{
		return 'statType';
	}
	
	
}
