<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use BanquemondialeBundle\Entity\Pays;
use BanquemondialeBundle\Entity\PaysTraduction;

use BanquemondialeBundle\Repository\GenreTraductionRepository;
use BanquemondialeBundle\Entity\Genre;
use BanquemondialeBundle\Entity\GenreTraduction;

use BanquemondialeBundle\Repository\SituationMatrimonialeTraductionRepository;
use BanquemondialeBundle\Entity\SituationMatrimoniale;
use BanquemondialeBundle\Entity\SituationMatrimonialeTraduction;

use BanquemondialeBundle\Repository\FonctionTraductionRepository;
use BanquemondialeBundle\Entity\Fonction;
use BanquemondialeBundle\Entity\FonctionTraduction;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\ORM\EntityManager; 

class RepresentantType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
	 
	 
 
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	 $this->definedPaysTraduction = $options['definedPaysTraduction'];
	 $this->definedGenreTraduction = $options['definedGenreTraduction'];
	 $this->definedFonctionTraduction = $options['definedFonctionTraduction'];
	 $this->definedSituationMatrimonialeTraduction = $options['definedSituationMatrimonialeTraduction'];
	 $this->fonctionTraduit = $options['fonctionTraduit'];
	 $this->langue = $options['langue'];
	 
        $builder
            ->add('nom' )
            ->add('prenom')
            ->add('dateDeNaissance', 'date', array('widget' =>'single_text', 'format' => 'yyyy-MM-dd', 'attr'=>array('placeholder' => 'message.placeholder.date')))
            ->add('adresse')
            ->add('numeroIdentiteNational',null, array( 'attr'=>array('required' => true)))
			->add('typeIdentification',new ChoiceType(),array('choices'=>array('cni'=>'utilisateur.type_cni','passport'=>'utilisateur.type_passport','carte consulaire'=>'utilisateur.type_carte_consulaire','extrait naissance'=>'utilisateur.type_extrait_naissance'),'placeholder' => 'selectionner.type_document_identite','label'=>'utilisateur.type_identiifcation','attr'=>array('class'=>'form-control')))
            ->add('telephone',null, array( 'attr'=>array('required' => false, 'placeholder'=>'(+224) 999-99-99-99')))
            ->add('portable',null, array( 'attr'=>array('required' => false, 'placeholder'=>'(+224) 999-99-99-99')))
            ->add('email',null, array( 'attr'=>array('required' => false)))
			->add('civilite', null, array('placeholder' => 'placeholder.selectionner.civilite', 'property' => 'libelle', 'required'=>false, 'choice_translation_domain' => true))
			->add('ville')
			->add('quartier')
			->add('lieuNaissance')
			
			->add('genre', 'entity', array('class' => 'BanquemondialeBundle:GenreTraduction','query_builder' => function (GenreTraductionRepository $formJ) {
				$langue = $this->langue;
				$idg =$langue->getId();			  
				return $formJ->getListGenreByLanque($langue->getId());
				}, 'property' => 'libelle','placeholder' => 'message.selectionner.genre','multiple' => false))
					

		   ->add('situationMatrimoniale', 'entity', array('class' => 'BanquemondialeBundle:SituationMatrimonialeTraduction','query_builder' => function (SituationMatrimonialeTraductionRepository $formJ) {
				$langue = $this->langue;
				$idg =$langue->getId();			  
				return $formJ->getListSituationMatrimonialeByLanque($langue->getId());
				}, 'property' => 'libelle','placeholder' => 'message.selectionner.situation_matrimoniale','multiple' => false))
					

			 ->add('pays', 'entity', array('class' => 'BanquemondialeBundle:PaysTraduction','query_builder' => function (PaysTraductionRepository $formJ) {
			  $langue = $this->langue;
			  $idp =$langue->getId();
				return $formJ->getListPaysByLanque($langue->getId());
				}, 'property' =>'libelle','placeholder' => 'message.selectionner.pays','multiple' => false))

			 ->add('fonction', 'entity', array('class' => 'BanquemondialeBundle:FonctionTraduction','query_builder' => function (FonctionTraductionRepository $formJ) {
			  $langue = $this->langue;
			  $idp =$langue->getId();			  
				return $formJ->getListFonctionByLangue($langue->getId());
				}, 'property' => 'libelle','placeholder' => 'message.selectionner.fonction','multiple' => false))
					
			
            ->add('dossierDemande','entity', array('class' => 'BanquemondialeBundle:DossierDemande', 'multiple' => false, 'property' => 'denominationSociale'))			
        ;
		
		$builder->get('pays')->addModelTransformer(new CallbackTransformer(
				// pays vers paysTraduction							
                function ($object) {
					return $this->definedPaysTraduction;},
				// paysTraduction vers pays
                function ($objectTraduction) {
					if($objectTraduction)
					{
						return $objectTraduction->getPays();
					}
					else
					{
						return null;
					}
				}		
		));
		
		$builder->get('genre')->addModelTransformer(new CallbackTransformer(
				// genre vers genreTraduction							
                function ($object) {return $this->definedGenreTraduction;},
				// genreTraduction vers genre
                function ($objectTraduction) {return $objectTraduction->getGenre();}
		));
		
		$builder->get('situationMatrimoniale')->addModelTransformer(new CallbackTransformer(
				// situationMatrimoniale vers situationMatrimonialeTraduction							
                function ($object) {return $this->definedSituationMatrimonialeTraduction;},
				// situationMatrimonialeTraduction vers situationMatrimoniale
                function ($objectTraduction) 
				{
					if($objectTraduction)
					{
						return $objectTraduction->getSituationMatrimoniale();
					}
					else
					{
						return null;
					}
				}
		));
		
		$builder->get('fonction')->addModelTransformer(new CallbackTransformer(
				// fonction vers fonctionTraduction							
                function ($object) {return $this->definedFonctionTraduction;},
				// fonctionTraduction vers fonction
                function ($objectTraduction) {
					if($objectTraduction)
					{
						return $objectTraduction->getFonction();
					}
					else
					{
						return null;
					}
				}		
		));
		
		
	}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
			'definedPaysTraduction' => 'BanquemondialeBundle\Entity\PaysTraduction',
			'definedGenreTraduction' => 'BanquemondialeBundle\Entity\GenreTraduction',
			'definedFonctionTraduction' => 'BanquemondialeBundle\Entity\FonctionTraduction',
			'fonctionTraduit' => 'BanquemondialeBundle\Entity\FonctionTraduction',
			'definedSituationMatrimonialeTraduction' => 'BanquemondialeBundle\Entity\SituationMatrimonialeTraduction',
			'langue'=>'BanquemondialeBundle\Entity\Langue',
            'data_class' => 'BanquemondialeBundle\Entity\Representant',			
        ));
    }
	
	
}
