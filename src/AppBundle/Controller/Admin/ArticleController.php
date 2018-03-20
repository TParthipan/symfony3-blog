<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/article")
 */
class ArticleController extends Controller {

    /**
     * @Route("/")
     */
    public function listAction() {

        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findAll();
        return $this->render('admin/article/list.html.twig', [
                    'articles' => $articles
        ]);
    }

    /**
     * @Route("/edit/{id}", defaults={"id":null})
     * @param int $id
     */
    public function editAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $originalImage = null;
        if (is_null($id)) {
            $article = new Article();
            $article->setAuthor($this->getUser());
        } else {//modif
            $article = $em->find('AppBundle:Article', $id);
            if (is_null($article)) {
                return $this->redirectToRoute('app_admin_article_edit');
            }

            if (!is_null($article->getImage())) {
                $originalImage = $article->getImage();
                $imagePath = $this->getParameter('upload_dir') . '/' . $article->getImage();
                $article->setImage(new File($imagePath));
            }
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        //si le formulaire a été envoyer
        if ($form->isSubmitted()) {
            //si il n'y a pas d'erreurs de validation du formulaire
            if ($form->isValid()) {
                /** @var UploadedFile */
                $image = $article->getImage();
                if (!is_null($image)) {
                    //nom du fichier que l'on va enregistrer
                    $filename = uniqid() . '.' . $image->guessExtension();
                    //equivalent move_uploaded_file()
                    $image->move(
                            // repertoire de destination
                            $this->getParameter('upload_dir'), $filename
                    );
                    $article->setImage($filename);
                } else {
                    //getData() sur une checkbox = true si cochée, false sinon
                    if ($form->has('remove_image') && $form->get('remove_image')->getData()) {
                        $article->setImage(null);
                        unlink($this->getParameter('upload_dir') . '/' . $originalImage);
                    } else {

                        $article->setImage($originalImage);
                    }
                }
                $em->persist($article); //prepare l'enregistrement
                $em->flush(); //fait l'enregistrement en bdd

                $msg = 'L\'article a été enregistrée';
                $this->addFlash('success', $msg); //ajoute msg flash;
                return $this->redirectToRoute('app_admin_article_list'); // redirige vers list
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render('admin/article/edit.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}")
     * @param int $id
     */
    public function deleteAction($id) {
        $em = $this->getDoctrine()->getManager();
        $article = $em->find('AppBundle:Article', $id);
        if (is_null($article)) {
            return $this->redirectToRoute('app_admin_article_list');
        }
        //prépare,la suppression de la catégorie
        $em->remove($article);
        //suppression
        $em->flush();
        $this->addFlash('warning', 'L\'article a été supprimée'); //ajoute msg flash;
        return $this->redirectToRoute('app_admin_article_list'); // redirige vers list
    }

}
