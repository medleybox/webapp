<?php

namespace App\Service;

use App\Repository\{LocalUserRepository, UserPasswordResetRepository};
use App\Entity\UserPasswordReset as ResetEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPasswordReset
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \App\Repository\LocalUserRepository
     */
    protected $users;

    /**
     * @var \App\Repository\UserPasswordResetRepository
     */
    protected $passwordResets;

    /**
     * @var \Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface
     */
    protected PasswordHasherFactoryInterface $passwordHasherFactory;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(EntityManagerInterface $em, LocalUserRepository $users, UserPasswordResetRepository $passwordResets, PasswordHasherFactoryInterface $passwordHasherFactory, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->users = $users;
        $this->passwordResets = $passwordResets;
        $this->passwordHasherFactory = $passwordHasherFactory;
        $this->encoder = $encoder;
    }

    public function updatePassword(ResetEntity $validate, string $password): bool
    {
        $user = $validate->getLocaluser();
        $user->setPassword($this->encoder->encodePassword(
            $user,
            $password
        ));
        $validate->setActive(false);

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
