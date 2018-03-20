<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ForgottenPassword;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController extends Controller {

    /**
     * @Route("/inscription")
     * @param Request $request
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = new User();
        $form = $this->createForm(
                UserType::class, $user, [
            //default: les validation qui n'appartiennent à aucun groupe
            //registration: la validation pour plainPassword
            'validation_groups' => ['Default', 'registration']
                ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Votre compte est créé');
                return $this->redirectToRoute('app_default_index');
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }
        return $this->render('security/register.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login")
     * @param Request $request
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils) {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($request->isMethod('POST') && is_null($error)) {
            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('security/login.html.twig', [
                    'error' => $error,
                    'last_username' => $lastUsername
        ]);
    }

    /**
     * @Route("/profil")
     * @param Request $request
     */
    public function profileAction(Request $request, UserPasswordEncoderInterface $passwordEncoder) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_security_login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if (!empty($user->getPlainPassword())) {
                    $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                    $user->setPassword($password);
                }
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs');
            }
        }

        return $this->render('security/profile.html.twig', [
                    'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/oubli-mdp")
     */
    public function forgottenPasswordAction(Request $request, Swift_Mailer $mailer) {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:User');
        $form = $this->createFormBuilder()
                ->add('email', EmailType::class, [
                    'label' => 'Email',
                    'constraints' =>
                    [
                        new NotBlank(['message' => 'L\'email est obligatoire']),
                        new Email(['message' => 'l\'email n\'est pas valide'])
                    ]
                        ]
                )
                ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                //dump($data['email']);
                $user = $repository->findOneBy($data);
                if (is_null($user)) {
                    $this->addFlash('error', 'Aucun utilisateur est inscrit avec ce mail');
                } else {
                    $repository = $em->getRepository('AppBundle:ForgottenPassword');
                    $mdp = $repository->findOneBy(['user' => $user]);
                    //dump($user->getEmail());
                    //dump($data['email']);
                    if (is_null($mdp)) {
                        $mdp = new ForgottenPassword();
                        $mdp->setUser($user);
                        $em->persist($mdp);
                        $em->flush();
                    }

                    $token = $mdp->getToken();
                    $url = $this->generateUrl('app_security_resetpassword', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                    $mail = $mailer->createMessage();
                    $mailBody = "<h3>Cliquez sur le lien ci-dessous pour créér un nouveau mot de passe </h3>"
                            . "<p><a href='" . $url . "'</a>Cliquer-ici</p>";
                    $mail
                            ->setSubject('Mot de passe oublié')
                            ->setFrom($this->getParameter('contact_email'))
                            ->setTo($user->getEmail())
                            ->setBody($mailBody);
                    $mailer->send($mail);
                    $this->addFlash("success", "Un email a été envoyé pour créer un nouveau mot de passe <a href='$url'>Cliquer-ici</a>");
                }
            }
        }
        return $this->render(
                        'security/forgotten_password.html.twig', [
                    'form' => $form->createView()
                        ]
        );
    }

    /**
     * @Route("/nouveau-mdp/{token}")
     */
    public function resetPasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token) {

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:ForgottenPassword');
        $ForgottenPassword = $repository->findOneBy(['token' => $token]);
        //dump($ForgottenPassword);
        if (is_null($ForgottenPassword)) {
            throw new NotFoundHttpException;
        } else {
            $form = $this->createFormBuilder()
                    ->add('plainPassword', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'first_options' => ['label' => 'Mot de passe'],
                        'second_options' => ['label' => 'Confirmation de mot de passe']
                    ])
                    ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $plainPassword = $form->getData()['plainPassword'];
                $password = $passwordEncoder->encodePassword($ForgottenPassword->getUser(), $plainPassword);
                $ForgottenPassword->getUser()->setPassword($password);
                $em->persist($ForgottenPassword);
                $em->remove($ForgottenPassword);
                $em->flush();

                $this->addFlash("success", "Le mot de passe a été modifié");
                return $this->redirectToRoute('app_security_login');
            }
        }

        return $this->render(
                        'security/reset_password.html.twig', [
                    'token' => $token,
                    'form' => $form->createView()
                        ]
        );
    }

}
