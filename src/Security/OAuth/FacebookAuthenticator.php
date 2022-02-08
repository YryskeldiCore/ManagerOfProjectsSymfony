<?php
declare(strict_types=1);

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FacebookAuthenticator extends SocialAuthenticator
{
    private UrlGeneratorInterface $urlGenerator;
    private ClientRegistry $clients;
    private Handler $handler;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ClientRegistry $clients,
        Handler $handler
    ){
        $this->urlGenerator = $urlGenerator;
        $this->clients = $clients;
        $this->handler = $handler;
    }

    public function supports(Request $request):bool
    {
        return $request->attributes->get('_route') === 'connect_facebook_check';
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getFacebookClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var FacebookUser $facebookUser */
        $facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);

        $network = 'facebook';
        $id = $facebookUser->getId();
        $username = $network . ':' . $id;

        try {
            return $userProvider->loadUserByUsername($username);
        } catch (UsernameNotFoundException $e){
            $this->handler->handler(new Command($network, $id));
            return $userProvider->loadUserByUsername($username);
        }
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getFacebookClient(): OAuth2ClientInterface
    {
        return $this->clients->getClient('facebook_main');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}