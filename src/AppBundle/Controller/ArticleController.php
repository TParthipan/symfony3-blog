<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/article")
 */
class ArticleController extends Controller {

    /**
     * @Route("/{id}")
     * @param Article $article
     */
    public function displayAction(Request $request, Article $article) {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $comment
                        ->setArticle($article)
                        ->setUser($this->getUser())
                ;
                $em->persist($comment); //prepare l'enregistrement
                $em->flush(); //fait l'enregistrement en bdd
                $msg = 'Le commentaire a été enregistrée';
                $this->addFlash('success', $msg); //ajoute msg flash;
                return $this->redirectToRoute('app_article_display', ["id" => $article->getId()]);
                //return $this->redirectToRoute($request->get('_route'), ["id" => $article->getId()]);
            }
        }
        return $this->render('article/display.html.twig', [
                    'article' => $article,
                    //'comments' => $article->getComments(),
                    'form' => $form->createView()
        ]);
    }

}
