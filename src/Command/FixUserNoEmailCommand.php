<?php

namespace App\Command;

use App\Repository\{LocalUserRepository, MediaFileRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixUserNoEmailCommand extends Command
{
    /**
    * {@inheritdoc}
    * @var string
    */
    protected static $defaultName = 'app:fix-user-email';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

     /**
     * @var \App\Repository\LocalUserRepository
     */
    private $users;

    public function __construct(EntityManagerInterface $em, LocalUserRepository $users)
    {
        $this->em = $em;
        $this->users = $users;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Upgrade users that have no email set');
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
