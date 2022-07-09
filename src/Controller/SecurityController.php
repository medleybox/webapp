<?php

namespace App\Controller;

use App\Service\UserPasswordReset;
use App\Entity\LocalUser;
use App\Form\{UserForgottenPasswordType, UserResetPasswordType, UserSignUpType};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/sign-up", name="security_signup")
     */
    public function signup(UserPasswordHasherInterface $encoder, EntityManagerInterface $em, Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        $user = new LocalUser();
        $form = $this->createForm(UserSignUpType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setActive(true);
            $user->setPassword($encoder->hashPassword(
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
     * @Route("/forgotten-password", name="security_forgotten_password")
     */
    public function forgottenPassword(UserPasswordReset $reset, Request $request): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        $form = $this->createForm(UserForgottenPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];
            try {
                $hash = $reset->tryReset($email, $request);
                $reset->sendEmail($hash);
                $this->addFlash('success', 'Email sent, please confirm before continuing');

                return $this->redirectToRoute('security_login');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            // Once the form has been sent reset all values with a redirect
            return $this->redirect($request->getUri());
        }

        return $this->render('security/forgotten-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgotten-password/{hash}", name="security_forgotten_password_reset")
     */
    public function forgottenPasswordReset(UserPasswordReset $reset, Request $request, string $hash): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('index_index');
        }

        $validate = $reset->validate($hash, $request);
        if (null === $validate) {
            return $this->redirectToRoute('security_forgotten_password');
        }

        $form = $this->createForm(UserResetPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData()['password'];
            try {
                $reset->updatePasswordWithReset($validate, $password);
                $this->addFlash('success', 'Password has been updated');

                return $this->redirectToRoute('security_login');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }

            // Once the form has been sent reset all values with a redirect
            return $this->redirect($request->getUri());
        }

        return $this->render('security/reset-password.html.twig', [
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
