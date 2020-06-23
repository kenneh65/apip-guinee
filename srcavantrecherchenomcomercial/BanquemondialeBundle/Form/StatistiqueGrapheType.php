<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use BanquemondialeBundle\Repository\EntrepriseRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



use Doctrine\ORM\EntityManager; 

class StatistiqueGrapheType extends AbstractType
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
		->add('typeGraphe', ChoiceType::class, array(
		'choices' => array(
			'column' => 'histogramme',
			'pie' => 'secteurs',
			'line' => 'courbe',
		),
		 'data' => 1,
		'expanded' => true, 'required' => true,'mapped'=>false))	
                 ->add('entreprise', 'entity', array('class' => 'BanquemondialeBundle:Entreprise',
                    'query_builder' => function (EntrepriseRepository $entreprise) {
                        return $entreprise->getListeDesStructures();
                    }, 'property' => 'denomination', 'placeholder' => 'message.selectionner', 'required' => false))
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
		return 'statGrapheType';
	}
	
	
}
