<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BanquemondialeBundle\Form;

/**
 * Description of DocumentToBeCollectedType
 *
 * @author fgueye
 */
class DocumentToBeCollectedType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('document');
    }
}
