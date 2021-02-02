<?php

namespace App\Form;

use App\Entity\ReservationPiercing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class PiercingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add('partieCorps')
	        ->add('allergie', ChoiceType::class,[
		        'placeholder' => 'Des allergies ?',
		        'choices'  => [
			        'oui' => 'oui',
			        'non' => 'non',]
	        ])
	        ->add('message', TextareaType::class)
	        ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReservationPiercing::class,
        ]);
    }
}
