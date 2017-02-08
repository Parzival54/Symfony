<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdvertController extends Controller {

    public function indexAction($page) {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }

        $nbPerPage = 3;

        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdverts($page, $nbPerPage);

        $nbPages = ceil(count($listAdverts) / $nbPerPage);

        if ($page > $nbPages) {
            throw new NotFoundHttpException('Page "' . $page . '" inexistante.');
        }

        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
                    'listAdverts' => $listAdverts,
                    'nbPages' => $nbPages,
                    'page' => $page
        ));
    }

    public function viewAction($id) {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // On avait déjà récupéré la liste des candidatures
        $listApplications = $em
                ->getRepository('OCPlatformBundle:Application')
                ->findBy(array('advert' => $advert))
        ;
        // On récupère maintenant la liste des AdvertSkill
        $listAdvertSkills = $em
                ->getRepository('OCPlatformBundle:AdvertSkill')
                ->findBy(array('advert' => $advert))
        ;
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
                    'advert' => $advert,
                    'listApplications' => $listApplications,
                    'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request) {

        $advert = new Advert();

        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
                        ->add('date', DateType::class)
                        ->add('title', TextType::class)
                        ->add('content', TextareaType::class)
                        ->add('author', TextType::class)
                        ->add('published', CheckboxType::class, array('required' => false))
                        ->add('save', SubmitType::class)->getForm();

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }

        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
                    'form' => $form->createView()));
    }

    public function editAction($id, Request $request) {

        // On récupère l'annonce $id
        $advert = $this->getDoctrine()->getManager()->getRepository('OCPlatformBundle:Advert')->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }

        $form = $this->get('form.factory')->createBuilder(FormType::class, $advert)
                        ->add('date', DateType::class)
                        ->add('title', TextType::class)
                        ->add('content', TextareaType::class)
                        ->add('author', TextType::class)
                        ->add('published', CheckboxType::class, array('required' => false))
                        ->add('save', SubmitType::class)->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->persist($advert)->flush();

                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }
        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
                    'advert' => $advert,'form' => $form->createView()));
    }

    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }
        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }
        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
        // On déclenche la modification
        $em->flush();

        return $this->render('OCPlatformBundle:Advert:delete.html.twig');
    }

    public function menuAction($limit) {

        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')
                ->findBy(array(), array('date' => 'DESC'), $limit, 0);

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
                    'listAdverts' => $listAdverts
        ));
    }

}
