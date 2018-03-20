<?php

namespace AppBundle\Controller;

use AppBundle\Form\ContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    /**
     * @Route("/")
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('AppBundle:Article')->findLatest(3);
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
                    'articles' => $articles
        ]);
    }

    /**
     * @Route("/contact")
     * @param Request $request
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer, \Twig_Environment $twig) {
        $form = $this->createForm(ContactType::class);
        if ($this->getUser()) {
            $form->get('name')->setData($this->getUser()->getFullname());
            $form->get('email')->setData($this->getUser()->getEmail());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $mail = $mailer->createMessage();
                /*
                  $mailBody='<h3>Nouveau message de '.$data['name'].'('.$data['email'].')</h3>'
                  .'<p><strong>'.$data['subject'].'</strong></p>'
                  .'<p>'.nl2br($data['body']).'</p>';
                 */
                $mailBody=$twig->render('default/mail_body.html.twig',['data'=>$data]);
                $mail
                        ->setSubject('Nouveau message sur votre blog')
                        ->setFrom($this->getParameter('contact_email'))
                        ->setTo($this->getParameter('contact_email'))
                        ->setBody($mailBody)
                        ->setReplyTo($data['email']);
                $mailer->send($mail);
                $this->addFlash('success', 'Votre message est envoyé');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }
        return $this->render('default/contact.html.twig', [
                    'form' => $form->createView()
        ]);
    }

}
