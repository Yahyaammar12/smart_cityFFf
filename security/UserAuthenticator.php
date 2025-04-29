<?php

namespace App\security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;





class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    private UserRepository $userRepository;
    private UrlGeneratorInterface $urlGenerator;
    private HttpClientInterface $httpClient;
    private $recaptchaSecret;

    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        HttpClientInterface $httpClient,
    ) {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->httpClient = $httpClient;
    }

    public function authenticate(Request $request): Passport
    {
        // Skip authentication for reset password route
        if ($request->get('_route') === 'app_reset_password') {
            return null;  // Skip authentication
        }
        
        $email = $request->request->get('_username', '');
        $password = $request->request->get('_password', '');
        $csrfToken = $request->request->get('_csrf_token');

        if (empty($email)) {
            throw new CustomUserMessageAuthenticationException('Email cannot be empty');
        }

        if (empty($password)) {
            throw new CustomUserMessageAuthenticationException('Password cannot be empty');
        }

        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        if ($this->isRecaptchaValid($recaptchaResponse, $request->getClientIp())) {
            throw new CustomUserMessageAuthenticationException('reCAPTCHA verification failed.');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('No user found with this email');
        }

        if (!$user->isActif()) {
            throw new CustomUserMessageAuthenticationException('Your account is not active');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        $roles = $user->getRoles();

        if (in_array('ADMIN', $roles, true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }

        if (array_intersect($roles, ['PATIENT', 'CLIENT', 'MEDECIN'])) {
            return new RedirectResponse($this->urlGenerator->generate('app_front'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_front'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    private function isRecaptchaValid(string $recaptchaResponse, string $clientIp): bool
    {
        $response = $this->httpClient->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $this->recaptchaSecret,
                'response' => $recaptchaResponse,
                'remoteip' => $clientIp,
            ]
        ]);

        $data = $response->toArray();

        return isset($data['success']) && $data['success'] === true;
    }

    
}
