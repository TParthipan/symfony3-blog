<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Article;
use AppBundle\Entity\Comment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use function dump;

/**
 * @Route("/comment")
 */
class CommentController extends Controller {

    /**
     * @Route("/article/{id}")
     */
    public function listAction(Request $request, Article $article) {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->find('AppBundle:Comment', $article->getId());
        return $this->render('admin/comment/comment.html.twig', [
                    'article' => $article,
                    'comments' => $comments
        ]);
    }

    /**
     * @Route("/delete/{id}")
     * 
     */
    public function deleteAction(Comment $comment) {
        $em = $this->getDoctrine()->getManager();
        $articleId=$comment->getArticle()->getId();
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Le commentaire a été supprimée'); //ajoute msg flash;*}
        return $this->redirectToRoute('app_admin_comment_list',["id"=>$articleId]); // redirige vers list
    }

}
