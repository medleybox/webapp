<?php

namespace App\Command;

use App\Repository\{LocalUserRepository, MediaFileRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AssignOrphanedMediaFilesCommand extends Command
{
    /**
    * {@inheritdoc}
    * @var string
    */
    protected static $defaultName = 'app:assign-orphaned-media-files';

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \App\Repository\MediaFileRepository
     */
    private $media;

     /**
     * @var \App\Repository\LocalUserRepository
     */
    private $users;

    public function __construct(EntityManagerInterface $em, MediaFileRepository $media, LocalUserRepository $users)
    {
        $this->em = $em;
        $this->media = $media;
        $this->users = $users;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setDescription('Assign users to media files without importUser set');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Looking for meida files with no import user');
        $mediaFiles = $this->media->findBy(['importUser' => null]);
        if ([] === $mediaFiles) {
            $io->info('No files found without import user 🎉');

            return 0;
        }

        $localUsers = [];
        $io->note('Loading local users');
        foreach ($this->users->findAll() as $user) {
            $localUsers[$user->getId()] = $user->getUsername();
        }

        $lastUser = array_key_first($localUsers);
        foreach ($mediaFiles as $media) {
            $username = $io->choice("{$media->getTitle()}", $localUsers, $lastUser);
            $localUser = $this->users->findOneBy(['username' => $username]);
            $media->setImportUser($localUser);
        }

        $this->em->flush();
        $io->success('Success! Flished changes to database');

        return 0;
    }
}
