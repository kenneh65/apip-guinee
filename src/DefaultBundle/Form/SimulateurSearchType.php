<?php

namespace DefaultBundle\Form;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use BanquemondialeBundle\Entity\FormeJuridique;



use Doctrine\ORM\EntityManager; 

class SimulateurSearchType extends AbstractType
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
			->add('typeDossier', 'entity', array('class' => 'BanquemondialeBundle:TypeDossier','choice_value'=>'id', 'property' => 'libelle','placeholder' => 'message.selectionner','multiple' => false, 'mapped'=>false, 'required'=>true, 'choice_translation_domain' => true))
			->add('formeJuridique', 'entity', array('class' => 'BanquemondialeBundle:FormeJuridiqueTraduction','required' => false, 'query_builder' => function (		FormeJuridiqueTraductionRepository $formJ)use(&$opts) {
				$langue = $opts['langue'];
                $idl = $langue->getId();
				return $formJ->getListFormeJuridiqueByLanque($idl);
				},'choice_value'=>'formeJuridique.id', 'property' => 'fullLibelle','placeholder' => 'message.selectionner.formejuridique','multiple' => false, 'mapped'=>false, 'required'=>true)) 				
			
			;

		$builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
				// formeJuridique vers formeJuridiqueTraduction							
                function ($object) {return null;},
				// formeJuridiqueTraduction vers formeJuridique
                function ($objectTraduction) {
				if($objectTraduction){return $objectTraduction->getFormeJuridique();}
				else return null;
					
					}
		));		
		

	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(					
			'langue'=>'BanquemondialeBundle\Entity\Langue',
			'csrf_protection'   => false
        ));
    }
	
	public function getName()
	{
		return 'simulation';
	}
	
	
}
