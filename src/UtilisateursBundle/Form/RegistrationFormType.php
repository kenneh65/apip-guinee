<?php

namespace UtilisateursBundle\Form;

use FOS\UserBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use BanquemondialeBundle\Form\AdministrationType;
use BanquemondialeBundle\Form\EntrepriseType;
use BanquemondialeBundle\Form\ParticulierType;
use BanquemondialeBundle\Form\RegionTypeForRegisterType;
use BanquemondialeBundle\Form\RegionType;
use BanquemondialeBundle\Form\DepartementForRegisterType;
use BanquemondialeBundle\Repository\PaysTraductionRepository;
use Symfony\Component\Form\CallbackTransformer;
use BanquemondialeBundle\Repository\RegionRepository;
use BanquemondialeBundle\Repository\PrefectureRepository;

class RegistrationFormType extends AbstractType {

    protected $options;

    //protected $pays;

    public function __construct($options = null) {
        $this->options = $options;
        //$this->pays=$pays;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->locale = $options['locale'];
        $this->pref = null;
        $opts = $this->options;
        $this->paysTraduit = $opts['paysTraduit'];

        //die($options['locale']);

        $builder
                /* ->add('type', ChoiceType::class, array('label' => '
                  civilite_form',
                  'choices' => array(
                  'entreprise' => 'utilisateur.mandataire',
                  'particulier' => 'utilisateur.particulier'))) */
                ->add('nom', 'text', array('label' => 'utilisateur.nom'))
                ->add('prenom', 'text', array('label' => 'utilisateur.prenom'))
                ->add('genre', ChoiceType::class, array('label' => 'civilite_form',
                    'choices' => array(
                        'M' => 'monsieur',
                        'F' => 'madame')))
                ->add('typeIdentification', new ChoiceType(), array('choices' => array('cni' => 'utilisateur.type_cni', 'passport' => 'utilisateur.type_passport', 'carte consulaire' => 'utilisateur.type_carte_consulaire', 'extrait naissance' => 'utilisateur.type_extrait_naissance'), 'label' => 'utilisateur.type_identiifcation', 'attr' => array('class' => 'form-control')))
                ->add('cni')
                //->add('dateNaissance', 'date', array('widget' => 'single_text', 'label' => 'utilisateur.dateNaissance', 'format' => 'dd-MM-yyyy', 'attr'=>array('placeholder' => 'message.placeholder.date')))
                //->add('lieuNaissance', 'text', array('label' => 'utilisateur.lieu'))
                ->add('adresse', 'textarea', array('label' => 'utilisateur.adresse'))
                ->add('telephone', 'text', array('label' => 'utilisateur.telephone', 'attr' => array('placeholder' => 'placeholder.telephone')))
                ->add('paysResidence', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
                        return $formJ->getListPays($options['locale']);
                    },
                    'property' => 'libelle', 'multiple' => false, 'placeholder' => 'message.selectionner')
                )
                ->add('regionResidence', 'entity', array('placeholder' => 'message.selectionner','class' => 'BanquemondialeBundle:Region',
                    'query_builder' => function(RegionRepository $rep) {
                        return $rep->getRegionResidence();
                    },
                    'required' => true, 'property' => 'libelle', 'multiple' => false))
                //->add('departement', 'entity', array('class' => 'BanquemondialeBundle:Departement', 'property' => 'libelle', 'multiple' => false))
                ->add('prefecture', 'entity', array(
                    'class' => 'BanquemondialeBundle:Prefecture', 'query_builder' => function (PrefectureRepository $pref) use(&$options) {
                        return $pref->getListPrefecture($this->pref);
                    },
                    'property' => 'libelle', 'multiple' => false, 'required' => true,
                    'placeholder' => 'message.selectionner'
                ))
                ->add('sousPrefecture', 'entity', array('class' => 'BanquemondialeBundle:SousPrefectureCommune', 
                    'required' => true, 'property' => 'libelle', 'multiple' => false, 'label' => "sousPrefecture",
                    'placeholder' => 'message.selectionner'))
                ->add('entreprise', new EntrepriseType(), array('locale' => $options['locale'], 'required' => false))
                ->add('particulier', new ParticulierType())
                ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
                ->remove('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                    'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ))
                ->add('ville','text',array('required'=>true))
        ;
        $builder->get('paysResidence')->addModelTransformer(new CallbackTransformer(
                // objet vers string
                function ($formeAsObject) {
            return $this->paysTraduit;
        },
                // string vers objet
                function ($formAsString) {
            return $formAsString->getPays();
        }
        ));
    }

    public function getParent() {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    // BC for SF < 2.7
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $this->configureOptions($resolver);
    }

    // BC for SF < 3.0
    public function getName() {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix() {
        return 'app_user_registration';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'locale' => 'fr'
        ));
    }

}
