<?php

namespace App\Form;

use App\Entity\Opinion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpinionForm extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('autor',TextType::class, array('attr' => array('placeholder' => 'Juan Rolo', 'label'=>'Autor')));
    $builder->add('ciudad',ChoiceType::class,['choices'=>['Madrid'=>'madrid','Sevilla'=>'sevilla','Bacerlona'=>'barcelona'],'placeholder'=>'Elige tu ciudad', 'label'=>'Ciudad']);
    $builder->add('comentario',TextareaType::class, array('attr' => array('placeholder' => 'Los usuarios de Madrid son majos.', 'label'=>'Comentario')));
  }

  public function configureOptions(OptionsResolver $resolver)
  {        
    $resolver->setDefaults(['data_class'=>Opinion::class]);
  }
}