<?php

namespace App\Controller;

use App\Entity\LocalUser;
use App\Form\{UserSignUpType, UserLoginType};

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(UserLoginType::class);


        $form->handleRequest($request);
        //

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/sign-up", name="security_signup")
     */
    public function signup(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em, Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        $user = new LocalUser();
        $form = $this->createForm(UserSignUpType::class, $user);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($user);
            $user->setActive(true);
            $user->setPassword($encoder->encodePassword(
                $user,
                $user->getPassword()
            ));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/sign-up.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}
