<?php

namespace ParametrageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use ParametrageBundle\Repository\Regle;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RegleType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
		 $this->formeTraduit = $options['formeTraduit'];
        $builder
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$options) {
                        return $formJ->getListFormeJuridiqueByCodeLanque($options['locale']);
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'message.selectionner.formejuridique'
                ))  
				->add('commissaireRequis', CheckboxType::class, array('label' => 'commissaire','required' => false))					
        ;
		
		$builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($formeAsObject) {
				//die(dump($this->formeTraduit));
				return  $this->formeTraduit;
        },
                // string vers objet
                function ($formAsString) {
					if($formAsString){return $formAsString->getFormeJuridique();}
				else return null;
            
        }
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'ParametrageBundle\Entity\Regle',
			'locale'=>'fr',
			'formeTraduit' => 'BanquemondialeBundle\Entity\FormeJuridiqueTraduction',
        ));
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'form_regle';
    }

}
