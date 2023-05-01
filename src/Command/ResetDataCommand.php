<?php

namespace App\Command;

use App\Repository\MediaFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reset-data',
    description: 'Remove all media uploaded',
)]
class ResetDataCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private MediaFileRepository $media
    ) {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        foreach ($this->media->findAll() as $media) {
            $io->text("Removed: '{$media->getTitle()}'");
            $this->em->remove($media);
        }

        $this->em->flush();
        $io->success('Success! Removed all MediaFile entries from system');

        return Command::SUCCESS;
    }
}
