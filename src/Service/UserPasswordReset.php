<?php

namespace App\Service;

use App\Repository\{LocalUserRepository, UserPasswordResetRepository};
use App\Entity\UserPasswordReset as ResetEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\{Address, Email};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserPasswordReset
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var \App\Repository\LocalUserRepository
     */
    private LocalUserRepository $users;

    /**
     * @var \App\Repository\UserPasswordResetRepository
     */
    private UserPasswordResetRepository $passwordResets;

    /**
     * @var \Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface
     */
    private PasswordHasherFactoryInterface $passwordHasherFactory;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    /**
     * @var \Symfony\Component\Mailer\MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * @var \Symfony\Component\Routing\UrlGeneratorInterface
     */
    private UrlGeneratorInterface $router;

    public function __construct(EntityManagerInterface $em, LocalUserRepository $users, UserPasswordResetRepository $passwordResets, PasswordHasherFactoryInterface $passwordHasherFactory, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->em = $em;
        $this->users = $users;
        $this->passwordResets = $passwordResets;
        $this->passwordHasherFactory = $passwordHasherFactory;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function updatePassword(ResetEntity $reset, string $password): bool
    {
        $user = $reset->getLocaluser();
        $user->setPassword($this->encoder->encodePassword(
            $user,
            $password
        ));
        $reset->setActive(false);

        $this->em->flush();

        return true;
    }
    
    public function tryReset(string $email, Request $request = null): string
    {
        $user = $this->users->findOneBy(['email' => $email]);
        if (null === $user) {
            throw new \Exception('Unable to find account with that email');
        }

        $hash = bin2hex(random_bytes(24));
        $encode = $this->encode($hash);

        $reset = (new ResetEntity())
            ->setHash($encode)
            ->setLocaluser($user)
            ->setActive(true)
        ;

        $this->em->persist($reset);
        $this->em->flush();

        return $hash;
    }

    public function sendEmail(string $hash): bool
    {
        $reset = $this->validate($hash);
        if (null === $reset) {
            return false;
        }

        // Fix routing with https using ppm
        $this->router->getContext()->setScheme('https');
        $link = $this->router->generate(
            'security_forgotten_password_reset',
            ['hash' => $hash],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $user = $reset->getLocaluser();

        $email = (new Email())
            ->from('no-reply@medleybox')
            ->to((new Address($user->getEmail(), $user->getUsername())))
            ->subject('User Password Reset | Medleybox')
            ->text("Click the link to reset your password - ${link}")
            ->html("<p>Click this <a href='${link}'>link</a> to reset your password</p>");

        $this->mailer->send($email);

        return true;
    }

    public function validate(string $hash, Request $request = null): ?ResetEntity
    {
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(self::class);
        foreach ($this->passwordResets->findBy(['active' => true]) as $reset) {
            if ($passwordHasher->verify($reset->getHash(), $hash)) {
                return $reset;
            }
        }

        return null;
    }

    private function encode(string $hash): string
    {
        // Configure different password hashers via the factory
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher(self::class);

        // Hash a plain password
        $encoded = $passwordHasher->hash($hash);

        // Verify that a given plain password matches the hash
        //dump($passwordHasher->verify($hash, 'wrong')); // returns false
        //dump($passwordHasher->verify($hash, $reset->getHash())); // returns true (valid)

        return $encoded;
    }
}
