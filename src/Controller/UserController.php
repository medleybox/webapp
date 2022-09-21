<?php

namespace App\Controller;

use App\Service\UserPasswordReset;
use App\Repository\UserSettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Security\Core\Security;
use \Exception;

class UserController extends AbstractController
{
    /**
     * @var \Symfony\Component\Security\Core\Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/user/settings", name="user_settings_json", methods={"GET"})
     */
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

    /**
     * @Route("/user/update-settings", name="user_update_settings", methods={"POST"})
     */
    public function updateSettings(Request $request, UserSettingsRepository $repo): Response
    {
        if ($request->isMethod('POST')) {
            /**
             * null or UserInterface if request has valid session
             * @var \App\Entity\LocalUser
             */
            $user = $this->security->getUser();
            $settings = $user->getSettings();
            $settings->setAutoPlay($request->request->get('autoPlay'));
            $settings->setRandom($request->request->get('random'));
            $settings->setOpenVlc($request->request->get('openVlc'));

            try {
                $repo->save($settings, $user);
            } catch (\Exception $e) {
                return $this->json(['save' => false, 'attempt' => true, 'error' => $e->getMessage()]);
            }

            return $this->json(['save' => true, 'attempt' => true]);
        }

        return $this->json(['save' => false, 'attempt' => true]);
    }

    /**
     * @Route("/user/update-password", name="user_update_password", methods={"POST"})
     */
    public function updatePassword(Request $request, UserPasswordReset $reset): Response
    {
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            if (null === $password) {
                return $this->json(['import' => false, 'attempt' => false]);
            }

            try {
                /**
                 * null or UserInterface if request has valid session
                 * @var \App\Entity\LocalUser
                 */
                $user = $this->security->getUser();
                $reset->updatePassword($user, $password);
            } catch (\Exception $e) {
                return $this->json(['import' => false, 'attempt' => true, 'error' => $e->getMessage()]);
            }

            return $this->json(['updated' => true, 'attempt' => true]);
        }

        return $this->json(['updated' => false, 'attempt' => true]);
    }
}
