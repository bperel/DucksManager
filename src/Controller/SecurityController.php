<?php

namespace App\Controller;

use App\Security\User;
use App\Security\UserAuthenticator;
use App\Service\UserService;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends PageSiteController
{
    /**
     * @Route({
     *     "en": "/login",
     *     "fr": "/connexion"
     * },
     *     name="app_login"
     * )
     */
    public function login(AuthenticationUtils $authenticationUtils, UserService $userService): Response
    {
        if (!empty($userService->getCurrentUserId())) {
            return $this->redirectToRoute('app_collection_show');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.twig', [
            'vueProps' => [
                'component' => 'Site',
                'page' => 'Login',
                'last-username' => $lastUsername,
            ] + compact('error')
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     path="/demo"
     * )
     */
    public function demo(Request $request, UserAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler): Response
    {
        $demoUser = new User(999, 'demo', sha1($_ENV['DEMO_PASSWORD']), ['ROLE_USER']);
        return $guardHandler->authenticateUserAndHandleSuccess(
            $demoUser,
            $request,
            $authenticator,
            'main'
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
