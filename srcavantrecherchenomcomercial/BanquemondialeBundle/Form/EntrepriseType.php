<?php

namespace BanquemondialeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use BanquemondialeBundle\Repository\FormeJuridiqueTraductionRepository;
use BanquemondialeBundle\Repository\RegionRepository;
use BanquemondialeBundle\Repository\PrefectureRepository;
use Symfony\Component\Form\CallbackTransformer;
use ParametrageBundle\Repository\PoleRepository;
class EntrepriseType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->pref = null;
        $builder
                ->add('denomination', 'text', array('label' => 'entreprise.denomination', 'required' => true))
               // ->add('ninea', 'text', array('label' => 'ninea', 'required' => false))
                ->add('telephone', 'text', array('label' => 'contact.telephone', 'required' => false))
                ->add('email', 'text', array('label' => 'message_email', 'required' => false))
                ->add('adresse', 'text', array('label' => 'message_adresse', 'required' => false))
//                ->add('formeJuridique', 'entity', array(
//                    'class' => 'BanquemondialeBundle:FormeJuridiqueTraduction',
//                    'query_builder' => function (FormeJuridiqueTraductionRepository $formJ) use(&$options) {
//                        return $formJ->getListFormes($options['locale']);
//                    }, 'property' => 'libelle', 'multiple' => false,
//                    'label' => 'entreprise.forme'
//                ))
                ->add('pole', 'entity', array('class' => 'ParametrageBundle:Pole', 'query_builder' => function (PoleRepository $pole)use(&$opts) {
                        return $pole->getListeDesPoleAPIP_Traitant();
                    },
                    'required' => true, 'property' => 'nom', 'multiple' => false, 'label' => "pole",
                    'placeholder' => 'message.selectionner', 'required' => 'required'))
                ->add('region', 'entity', array(
                    'class' => 'BanquemondialeBundle:Region',
                    'query_builder' => function (RegionRepository $formJ) use(&$options) {
                        return $formJ->getRegionByPaysdeResidence();
                    }, 'property' => 'libelle', 'multiple' => false,
                    'placeholder' => 'message.selectionner'
                ))
                ->add('prefecture', 'entity', array(
                    'class' => 'BanquemondialeBundle:Prefecture', 'query_builder' => function (PrefectureRepository $pref) use(&$options) {
                        return $pref->getListPrefecture($this->pref);
                    },
                    'property' => 'libelle', 'multiple' => false, 'required' => true,
                    'placeholder' => 'message.selectionner'
                ))
                ->add('sousPrefecture', 'entity', array('class' => 'BanquemondialeBundle:SousPrefectureCommune', 'required' => true, 'property' => 'libelle', 'multiple' => false, 'label' => "sousPrefecture",
                    'placeholder' => 'message.selectionner'))

        //->add('utilisateur')
        ;
//        $builder->get('formeJuridique')->addModelTransformer(new CallbackTransformer(
//                // objet vers string
//                function ($formeAsObject) {
//            return (String) $formeAsObject;
//        },
//                // string vers objet
//                function ($formAsString) {
//            if ($formAsString)
//                return $formAsString->getFormeJuridique();
//            else
//                return null;
//        }
//        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'BanquemondialeBundle\Entity\Entreprise',
            'locale' => 'fr'
        ));
    }

}
