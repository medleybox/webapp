<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserPlayHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;

class UserHistoryController extends AbstractController
{
    /**
     * @var \App\Entity\LocalUser
     */
    private $user;

    public function __construct(private UserPlayHistoryRepository $history, Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/history/list', name: 'history_list', methods: ['GET'])]
    public function history(): Response
    {
        $history = $this->history->getAll($this->user);

        return $this->json($history);
    }

    #[Route('/history/clear', name: 'history_clear', methods: ['GET'])]
    public function clear(): Response
    {
        $this->history->clearHistory($this->user);

        return $this->json(true);
    }
}
