<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\ORM\EntityManager; 
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use BanquemondialeBundle\Entity\Pays;
use BanquemondialeBundle\Entity\PaysTraduction;

class PersonneEngageurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */

	 
 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	 $this->langue = $options['langue'];
	 $this->definedPaysTraduction = $options['definedPaysTraduction'];
	 
        $builder
            ->add('nom' )
            ->add('prenom')
            ->add('dateDeNaissance', 'date', array('widget' =>'single_text', 'format' => 'dd-MM-yyyy', 'attr'=>array('placeholder' => 'message.placeholder.date')))
			->add('lieuDeNaissance')
            ->add('domicile')				
            ->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'denominationSociale'))
//->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'id'))	
			->add('pays', 'entity', array('class' => 'BanquemondialeBundle:PaysTraduction','query_builder' => function (PaysTraductionRepository $formJ) {
			  $langue = $this->langue;
			  $idp =$langue->getId();			  
				return $formJ->getListPaysByLanque($langue->getId());
				}, 'property' => 'libelle','placeholder' => 'message.selectionner.pays','multiple' => false))	
        ;
		
		
		$builder->get('pays')->addModelTransformer(new CallbackTransformer(
				// pays vers paysTraduction							
                function ($object) {
					
					return $this->definedPaysTraduction;
					},
				// paysTraduction vers pays
                function ($objectTraduction) {return $objectTraduction->getPays();}
		));

	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'definedPaysTraduction' => 'BanquemondialeBundle\Entity\PaysTraduction',
			'langue'=>'BanquemondialeBundle\Entity\Langue',
            'data_class' => 'BanquemondialeBundle\Entity\PersonneEngageur',			
        ));
    }
	
	
}
