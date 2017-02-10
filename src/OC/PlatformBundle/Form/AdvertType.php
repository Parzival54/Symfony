<?php

namespace OC\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use OC\PlatformBundle\Form\ImageType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdvertType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->add('date', DateTimeType::class)
                ->add('title', TextType::class)
                ->add('author', TextType::class)
                ->add('content', TextareaType::class)
                ->add('image', ImageType::class)
                ->add('categories', EntityType::class, array(
                    'class' => 'OCPlatformBundle:Category',
                    'choice_label' => 'name',
                    'multiple' => true
                ))
                ->add('save', SubmitType::class);

        $builder->addEventListener(
                FormEvents::PRE_SET_DATA, // 1er argument : L'évènement qui nous intéresse : ici, PRE_SET_DATA
                function(FormEvent $event) { // 2e argument : La fonction à exécuter lorsque l'évènement est déclenché
// On récupère notre objet Advert sous-jacent
            $advert = $event->getData();

            if (null == $advert) {
                return;
            }
            if (!$advert->getPublished() || null === $advert->getId()) {
                // Alors on ajoute le champ published
                $event->getForm()->add('published', CheckboxType::class, array(
                    'required' => false,
                    'label' => 'Publié'));
            } else {
                // Sinon, on le supprime
                $event->getForm()->remove('published');
            }
        }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'OC\PlatformBundle\Entity\Advert'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'oc_platformbundle_advert';
    }

}
