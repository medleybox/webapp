<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\{Address, Email};
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validation;

class TestEmailDeliverabilityCommand extends Command
{
    /**
    * {@inheritdoc}
    * @var string
    */
    protected static $defaultName = 'app:test-email-deliverability';

    /**
     * @var \Symfony\Component\Mailer\MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Send an email to test deliverability');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $validation = Validation::createCallable(new EmailConstraint([
            'message' => 'Email address not valid',
        ]));
        $question = (new Question('Email: ', ''))->setValidator($validation);
        $address = $this->getHelper('question')->ask($input, $output, $question);

        $email = (new Email())
            ->from("hello@medleybox.live")
            ->to((new Address($address)))
            ->subject('Deliverability Test | Medleybox')
            ->text("This email was sent to test email deliverability.")
            ->html("<p>This email was sent to test email deliverability.</p>")
        ;

        $this->mailer->send($email);

        $io = new SymfonyStyle($input, $output);
        $io->success("Success! Sent email to ${address}");

        return Command::SUCCESS;
    }
}
