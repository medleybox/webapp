<?php

namespace App\Command;

use App\Entity\LocalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputDefinition,InputInterface,InputOption};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command
{
    /**
    * {@inheritdoc}
    * @var string
    */
    protected static $defaultName = 'app:create-user';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Create a new admin user')
            ->addOption(
                'username',
                'u',
                InputOption::VALUE_REQUIRED,
                'Set the name of the new user',
                'admin'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $io->ask('username', $input->getOption('username'));
        $password = $io->askHidden('password');

        $user = new LocalUser;
        $user->setUsername($username);

        $user->setPassword($this->encoder->encodePassword(
            $user,
            $password
        ));

        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->em->persist($user);
        $this->em->flush();

        $io->success('Success! Use has been created. Now login via the browser');

        return 1;
    }
}
