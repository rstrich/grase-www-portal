<?php

namespace App\Controller;

use App\Util\SettingsUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This controller is for all the UAM related functions (login for the client side of the hotspot)
 */
class UamController extends AbstractController
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var SettingsUtils */
    protected $settingsUtils;

    /**
     * UserController constructor.
     *
     * @param TranslatorInterface $translator
     * @param SettingsUtils       $settingsUtils
     */
    public function __construct(TranslatorInterface $translator, SettingsUtils $settingsUtils)
    {
        $this->translator = $translator;
        $this->settingsUtils = $settingsUtils;
    }

    /**
     * The basic uam login page, just displays the login forms etc (customised based on settings)
     *
     * @param Request $request
     *
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function uamAction(Request $request)
    {
        $result = $request->query->get('res');
        $userUrl = $this->cleanUserUrl($request->query->get('userurl'));
        $challenge = $request->query->get('challenge');

        // Check if we have a result or if we need to redirect through the prelogin
        if (!in_array($result, ['notyet', 'already', 'failed', 'success', 'logoff'])) {
            return new RedirectResponse('http://' . $request->server->get('HTTP_HOST') . ':3990/prelogin');
        }

        return $this->render(
            'uamLogin.html.twig',
            []
        );
    }

    /**
     * Ensure if a logout url is the last url used, that we don't use it as the userUrl
     *
     * @param $userUrl
     *
     * @return string
     */
    private function cleanUserUrl($userUrl)
    {
        if ('http://logout/' === $userUrl || 'http://1.0.0.0/' === $userUrl) {
            return '';
        }

        return $userUrl;
    }
}