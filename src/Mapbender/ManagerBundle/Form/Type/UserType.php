<?php

namespace Mapbender\ManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType {
    public function getName() {
        return 'user';
    }

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
            ->add('username', 'text', array(
                'label' => 'Username'))
            ->add('email', 'email', array(
                'label' => 'E-Mail'))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array(
                    'required' => $options['requirePassword'],
                    'label' => 'Password')))
            ->add('roleObjects', 'entity', array(
                'class' =>  'MapbenderCoreBundle:Role',
                'query_builder' => function(EntityRepository $er) {
                    $qb = $er->createQueryBuilder('r')
                        ->add('where', 'r.mpttLeft != 1')
                        ->orderBy('r.title', 'ASC');

                    return $qb;
                },
                'expanded' => true,
                'multiple' => true,
                'property' => 'title',
                'label' => 'Roles'));

    }

    public function getDefaultOptions(array $options) {
        return array('requirePassword' => true);
    }
}

