<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserPasswordReset;
use App\Repository\UserPlayHistoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Security;
use Exception;

class UserHistoryController extends AbstractController
{
    /**
     * @var \Symfony\Component\Security\Core\Security
     */
    private $security;

    /**
     * @var \App\Repository\UserPlayHistoryRepository
     */
    private $history;

    /**
     * @var \App\Entity\LocalUser
     */
    private $user;

    public function __construct(UserPlayHistoryRepository $history, Security $security)
    {
        $this->history = $history;
        $this->security = $security;
        $this->user = $this->security->getUser();
    }

    /**
     * @Route("/history/list", name="history_list", methods={"GET"})
     */
    public function history(): Response
    {
        $history = $this->history->getAll($this->user);

        return $this->json($history);
    }

    /**
     * @Route("/history/clear", name="history_clear", methods={"GET"})
     */
    public function clear(): Response
    {
        $this->history->clearHistory($this->user);

        return $this->json(true);
    }
}
