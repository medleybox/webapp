<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\LocalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputDefinition,InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new admin user',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $encoder
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption(
            'username',
            'u',
            InputOption::VALUE_REQUIRED,
            'Set the name of the new user',
            'admin'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getOption('username');
        $username = $io->ask('username', $username);
        $email = $io->ask('email', '');
        $password = $io->askHidden('password');

        $user = new LocalUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setActive(true);

        $user->setPassword($this->encoder->hashPassword(
            $user,
            $password
        ));

        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Success! Use has been created. Now login via the browser');

        return Command::SUCCESS;
    }
}
