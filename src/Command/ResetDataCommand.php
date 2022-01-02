<?php

namespace App\Command;

use App\Repository\MediaFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ResetDataCommand extends Command
{
    /**
    * {@inheritdoc}
    * @var string
    */
    protected static $defaultName = 'app:reset-data';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \App\Repository\MediaFileRepository
     */
    private $media;

    public function __construct(EntityManagerInterface $em, MediaFileRepository $media)
    {
        $this->em = $em;
        $this->media = $media;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Remove media uploaded');
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

        return 0;
    }
}
