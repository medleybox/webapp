<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\{Address, Email};
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validation;

#[AsCommand(
    name: 'app:test-email-deliverability',
    description: 'Send an email to test deliverability',
)]
class TestEmailDeliverabilityCommand extends Command
{
    public function __construct(private MailerInterface $mailer)
    {
        parent::__construct();
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
        $io->success("Success! Sent email to {$address}");

        return Command::SUCCESS;
    }
}
