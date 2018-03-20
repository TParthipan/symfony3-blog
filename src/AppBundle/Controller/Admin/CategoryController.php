<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/categorie")
 */
class CategoryController extends Controller {

    /**
     * @Route("/")
     */
    public function listAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Category');
        $categories = $repository->findAll();
        return $this->render('admin/category/list.html.twig', [
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/edit/{id}", defaults={"id":null})
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        if (is_null($id)) {
            $category = new Category();
        } else {
            //raccourcie pour $em->getRepository('AppBundle:Category')->find($id)
            $category = $em->find('AppBundle:Category', $id);
            //si id n'existe pas en bdd
            if (is_null($category)) {
                return $this->redirectToRoute('app_admin_category_list');
            }
        }
        // creation du formulaire relié à la catégorie
        $form = $this->createForm(\AppBundle\Form\CategoryType::class, $category);
        //le formulaire traite la requête HTTP
        $form->handleRequest($request);

        //si le formulaire a été envoyer
        if ($form->isSubmitted()) {
            //si il n'y a pas d'erreurs de validation du formulaire
            if ($form->isValid()) {
                $em->persist($category); //prepare l'enregistrement
                $em->flush(); //fait l'enregistrement en bdd

                $msg = 'La rubrique a été enregistrée';
                $this->addFlash('success', $msg); //ajoute msg flash;
                return $this->redirectToRoute('app_admin_category_list'); // redirige vers list
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render('admin/category/edit.html.twig', [
                    'form' => $form->createView()
                        ]
        );
    }

    /**
     * @Route("/delete/{id}")
     * @param int $id
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $category = $em->find('AppBundle:Category', $id);
        if (is_null($category)) {
            return $this->redirectToRoute('app_admin_category_list');
        }
        if (!$category->hasArticles()) {
            //prépare,la suppression de la catégorie
            $em->remove($category);
            //suppression
            $em->flush();
            $this->addFlash('success', 'La catégorie a été supprimée'); //ajoute msg flash;*}
        } else {
            $this->addFlash('success', 'La catégorie ne peut pas être supprimée');
        }
        return $this->redirectToRoute('app_admin_category_list'); // redirige vers list
    }

}
