<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\AbstractType;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\EntrepriseRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;




use Doctrine\ORM\EntityManager; 

class RepartitionQuittanceSearchType extends AbstractType
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

				
		->add('debutPeriode', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy','attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => false,'mapped'=>false))
		->add('finPeriode', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy','attr'=>array('placeholder' => 'message.placeholder.date'), 'required' => false,'mapped'=>false))
		->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole','property' => 'nom','multiple' => false, 'placeholder' => '', 'required' =>false))
		
		->add('formeJuridique', 'entity', array('class' => 'BanquemondialeBundle:FormeJuridiqueTraduction','required' => false, 'query_builder' => function (		FormeJuridiqueTraductionRepository $formJ)use(&$opts) {
				$langue = $opts['langue'];
                $idl = $langue->getId();
				return $formJ->getListFormeJuridiqueByLanque($idl);
				},'choice_value'=>'formeJuridique.id', 'property' => 'libelle','placeholder' => 'message.selectionner.formejuridique','multiple' => false))

		->add('entreprise', 'entity', array('class' => 'BanquemondialeBundle:Entreprise',
                    'query_builder' => function (EntrepriseRepository $entreprise) {
                        return $entreprise->getListCaisse();
                    },'property' => 'denomination','placeholder' => 'message.selectionner', 'required' => false))
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
			'csrf_protection'   => false,
            'data_class' => 'BanquemondialeBundle\Entity\RepartitionQuittance'
        ));
    }
	
	public function getName()
	{
		return 'repartitionType';
	}
	
	
}
