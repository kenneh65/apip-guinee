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

class RegistrationForm2Type extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->remove('type', ChoiceType::class, array(
                    'choices' => array(
                        'entreprise' => 'utilisateur.entreprise',
                        'particulier' => 'utilisateur.particulier')))
                ->add('nom')
                ->add('prenom')
                ->add('genre', ChoiceType::class, array(
                    'choices' => array(
                        'M' => 'Masculin',
                        'F' => 'Feminin')))
                ->add('typeIdentification',new ChoiceType(),array('choices'=>array('cni'=>'utilisateur.type_cni','passport'=>'utilisateur.type_passport','carte consulaire'=>'utilisateur.type_carte_consulaire','extrait naissance'=>'utilisateur.type_extrait_naissance'),'label'=>'utilisateur.type_identiifcation','attr'=>array('class'=>'form-control')))
                ->add('cni')
              ->add('dateNaissance', 'date', array('widget' => 'single_text', 'label' => 'utilisateur.dateNaissance'))
                ->add('lieuNaissance','text',array('label'=>'utilisateur.lieu'))
                ->add('adresse','textarea',array('label'=>'utilisateur.adresse'))
                ->add('telephone','text',array('label'=>'utilisateur.telephone'))
                ->remove('paysResidence', 'entity', array(
                    'class' => 'BanquemondialeBundle:PaysTraduction',
                    'query_builder' => function (PaysTraductionRepository $formJ) use(&$options) {
                        return $formJ->getListPays($options['locale']);
                    },
                    'property' => 'libelle', 'multiple' => false)
                )
                ->remove('regionResidence', 'entity', array('class' => 'BanquemondialeBundle:Region', 'property' => 'libelle', 'multiple' => false))
                //->remove('departement', 'entity', array('class' => 'BanquemondialeBundle:Departement', 'property' => 'libelle', 'multiple' => false))
				->remove('prefecture', 'entity', array('class' => 'BanquemondialeBundle:Prefecture', 'property' => 'libelle', 'multiple' => false))
				->remove('sousPrefecture', 'entity', array('class' => 'BanquemondialeBundle:SousPrefectureCommune', 'property' => 'libelle', 'multiple' => false))
				
                ->remove('entreprise', new EntrepriseType(),array('required'=>false))
                ->remove('particulier', new ParticulierType(),array('required'=>false))
                ->add('email', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\EmailType'), array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
                ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
                ->add('plainPassword', LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\RepeatedType'), array(
                    'type' => LegacyFormHelper::getType('Symfony\Component\Form\Extension\Core\Type\PasswordType'),
                    'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
        ));
    }

    public function getParent() {
        return new RegistrationFormType();
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
