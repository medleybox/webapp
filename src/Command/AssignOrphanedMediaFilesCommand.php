<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\{LocalUserRepository, MediaFileRepository};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputArgument,InputInterface};
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:assign-orphaned-media-files',
    description: 'Assign users to media files without importUser set',
)]
class AssignOrphanedMediaFilesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private MediaFileRepository $media,
        private LocalUserRepository $users
    ) {
        parent::__construct();
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

            return Command::SUCCESS;
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

        return Command::SUCCESS;
    }
}
