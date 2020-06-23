<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\ModePaiementTraductionRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;

class QuittanceType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected $options;

    public function __construct($options = null) {
        $this->options = $options;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $opts = $this->options;
        $this->langue = $options['langue'];
        $this->formeJTraduit = $opts['formeJTraduit'];
        $this->modeTraduit = $opts['modeTraduit'];
        $builder
                ->add('numeroDossier', 'text', array('required' => false))
                ->add('denominationSociale', 'text', array('required' => false))
                ->add('serie', 'text', array('required' => true))
                ->add('numeroVolume', 'text', array('required' => false))
                ->add('refTitreRecette', 'text', array('required' => false))
                ->add('montantTotalFacture', 'number', array('required' => false))
                ->add('montantVerse', 'number', array('required' => false))
                ->add('numeroQuittance', 'text', array('required' => true))
                //->add('formeJuridiqueTraduction','text',array('required' => false,'mapped'=>false))
                ->add('formeJuridique', 'entity', array(
                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ)use(&$opts) {
                        $langue = $opts['langue'];
                        return $formJ->getListFormeJuridiqueByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => ''
                ))
                ->add('modePaiement', 'entity', array(
                    'class' => 'BanquemondialeBundle:ModePaiementTraduction',
                    'query_builder' => function (ModePaiementTraductionRepository $formJ)use(&$opts) {

                        $langue = $opts['langue'];
                        return $formJ->getListModePaiementByLanque($langue->getId());
                    }, 'property' => 'libelle',
                    'multiple' => false, 'placeholder' => 'select', 'required' => true
                ))
                ->add('datePaiement', 'date', array('widget' => 'single_text', 'format' => 'dd-MM-yyyy', 'required' => false))
                ->add('typeDossier', 'entity', array('class' => 'BanquemondialeBundle:TypeDossier', 'multiple' => false, 'property' => 'libelle', 'placeholder' => 'select'))
                ->add('natureRecette', 'entity', array('class' => 'BanquemondialeBundle:NatureRecette', 'multiple' => false, 'property' => 'libelle', 'required' => true))

        ;


        $builder->get('modePaiement')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($modeAsObject) {
            return $this->modeTraduit;
        },
                // string vers objet
                function ($modeAsString) {
            return $modeAsString->getModePaiement();
        }
        ));

		
		 $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
			// formeJuridique vers formeJuridiqueTraduction							
			function ($object) {return $this->formeJTraduit;},
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
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'langue' => 'BanquemondialeBundle\Entity\Langue',
            'csrf_protection' => false,
            'data_class' => 'BanquemondialeBundle\Entity\Quittance'
        ));
    }

    public function getName() {
        return 'quittance';
    }

}
