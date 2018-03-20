<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends Controller {

    public function menuAction() {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Category');
        $categories = $repository->findAll();

        return $this->render('category/menu.html.twig', [
                    'categories' => $categories
        ]);
    }

    /**
     * @Route("/category/{id}",requirements={"id": "\d+"})
     * @param int $id
     */
    public function displayAction(/* $id */ Category $category) {
        /* $repository = $this->getDoctrine()->getRepository('AppBundle:Category');
          $category = $repository->find($id);

          if (is_null($category)) {
          throw new NotFoundHttpException();
          } */
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findLatest(2, $category);
        return $this->render('category/display.html.twig', [
                    'category' => $category,
                    'articles' => $articles
        ]);
    }

}
