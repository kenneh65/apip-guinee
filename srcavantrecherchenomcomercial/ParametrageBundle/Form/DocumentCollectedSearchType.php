<?php

namespace ParametrageBundle\Form;

use BanquemondialeBundle\Repository\TypeOperationTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use BanquemondialeBundle\Repository\RegionRepository;
use BanquemondialeBundle\Repository\StatutTraitementTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use BanquemondialeBundle\Entity\Pays;



use Doctrine\ORM\EntityManager; 

class DocumentCollectedSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	 
	  protected $options;

    //protected $pays;

    public function __construct($options = null) {
        $this->options = $options;
        //$this->pays=$pays;
    }
	 
	 
 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	  $opts = $this->options;
	 $this->langue = $options['langue'];
	 
	 //die(print_r(array_values($listeDenominationSociale)));
	 
	 $builder

				->add('id','integer',array('required' => false))
				//->add('dossierDemande','text',array('required' => false))
				
				//->add('denominationSociale','text',array('required' => false, 'mapped'=>false))
				/*
				->add('denominationSociale', 'text', array('required' => false, 'query_builder' => function (		DossierDemandeRepository $formJ)use(&$opts) {	
				$langue = $opts['langue'];
                $idl = $langue->getId();			
				return $formJ->getListDenominationSocialeByLangue($idl);
				}, 'property' => 'libelle','placeholder' => '-- selectionner denomination sociale --','multiple' => false, 'mapped'=>false))
*/

				->add('denominationSociale', 'text', array('required' => false, 'mapped'=>false ))				
			
			
				->add('typeOperation', 'entity', array('class' => 'BanquemondialeBundle:TypeOperationTraduction','required' => false, 'query_builder' => function (		TypeOperationTraductionRepository $formJ)use(&$opts) {	
				$langue = $opts['langue'];
                $idl = $langue->getId();			
				return $formJ->getListOperationByLangue($idl);
				}, 'property' => 'libelle','placeholder' => '-- selectionner type operation --','multiple' => false, 'mapped'=>false)) 
		
				
				->add('formeJuridique', 'entity', array('class' => 'BanquemondialeBundle:FormeJuridiqueTraduction','required' => false, 'query_builder' => function (		FormeJuridiqueTraductionRepository $formJ)use(&$opts) {
				$langue = $opts['langue'];
                $idl = $langue->getId();
				return $formJ->getListFormeJuridiqueByLangue($idl);
				}, 'property' => 'libelle','placeholder' => '-- selectionner forme juridique --','multiple' => false, 'mapped'=>false)) 
				
				
				
				->add('statutTraitement', 'entity', array('class' => 'BanquemondialeBundle:StatutTraitementTraduction','required' => false, 'query_builder' => function (		StatutTraitementTraductionRepository $formJ)use(&$opts) {	
				$langue = $opts['langue'];
                $idl = $langue->getId();			
				return $formJ->getListStatutTraitementByLangue($idl);
				}, 'property' => 'libelle','placeholder' => '-- selectionner statut traitement --','multiple' => false)) 		
		;
		
		$builder->get('statutTraitement')->addModelTransformer(new CallbackTransformer(
				// statutTraitement vers statutTraitementTraduction							
                function ($object) {return null;},
				// statutTraitementTraduction vers statutTraitement
                function ($objectTraduction) {
				if($objectTraduction){return $objectTraduction->getStatutTraitement();}
				else return null;					
				}
		));
		
		$builder->get('typeOperation')->addModelTransformer(new CallbackTransformer(
				// typeOperation vers typeOperationTraduction							
                function ($object) {return null;},
				// typeOperationTraduction vers typeOperation
                function ($objectTraduction) {
				if($objectTraduction){return $objectTraduction->getTypeOperation();}
				else return null;
					
					}
		));
		
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
			'langue'=>'ParametrageBundle\Entity\Langue',
			'csrf_protection'   => false,
            'data_class' => 'BanquemondialeBundle\Entity\DocumentCollected'
        ));
    }
	
	public function getName()
	{
		return 'listDossierPole';
	}
	
	
}
