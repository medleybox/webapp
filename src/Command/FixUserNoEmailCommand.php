<?php

namespace App\Command;

use App\Repository\{LocalUserRepository, MediaFileRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:fix-user-email',
    description: 'Upgrade users that have no email set',
)]
class FixUserNoEmailCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private LocalUserRepository $users
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->users->findAll() as $user) {
            if (null !== $user->getEmail()) {
                $user->setEmail('noreply@localhost');
                $user->setActive(true);
            }
        }

        $this->em->flush();
        return Command::SUCCESS;
    }
}
