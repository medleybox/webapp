<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\LocalUser;
use App\Service\UserPasswordReset;
use App\Repository\UserSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Bundle\SecurityBundle\Security;
use Exception;

class UserController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/user/settings', name: 'user_settings_json', methods: ['GET'])]
    public function settingsJson(): Response
    {
        /**
         * null or UserInterface if request has valid session
         * @var \App\Entity\LocalUser
         */
        $user = $this->security->getUser();
        $settings = $user->getSettings();

        return $this->json([
            'autoPlay' => $settings->isAutoPlay(),
            'random' => $settings->isRandom(),
            'openVlc' => $settings->isOpenVlc()
        ]);
    }

    #[Route('/user/update-settings', name: 'user_update_settings', methods: ['POST'])]
    public function updateSettings(Request $request, UserSettingsRepository $repo): Response
    {
        try {
            $user = $this->security->getUser();
            assert($user instanceof LocalUser);
            $settings = $user->getSettings()
                ->setAutoPlay("1" === $request->request->get('autoPlay'))
                ->setRandom("1" === $request->request->get('random'))
                ->setOpenVlc("1" === $request->request->get('openVlc'))
            ;
            $repo->save($settings, $user);
        } catch (\Exception $e) {
            return $this->json(['save' => false, 'attempt' => true, 'error' => $e->getMessage()]);
        }

        return $this->json(['save' => true, 'attempt' => false]);
    }

    #[Route('/user/update-password', name: 'user_update_password', methods: ['POST'])]
    public function updatePassword(Request $request, UserPasswordReset $reset): Response
    {
        $password = $request->request->get('password');
        if (null === $password) {
            return $this->json(['updated' => false]);
        }

        try {
            $user = $this->security->getUser();
            assert($user instanceof LocalUser);
            $reset->updatePassword($user, $password);
        } catch (\Exception $e) {
            return $this->json(['updated' => false, 'error' => $e->getMessage()]);
        }

        return $this->json(['updated' => true, 'attempt' => true]);
    }
}
