<?php

namespace App\Command;

use App\Entity\LocalUser;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';

    private $em;

    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Create a new user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask('username');
        $password = $io->askHidden('password');

        $user = new LocalUser;
        $user->setUsername($username);

        $user->setPassword($this->encoder->encodePassword(
            $user,
            $password
        ));

        $this->em->persist($user);
        $this->em->flush();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
