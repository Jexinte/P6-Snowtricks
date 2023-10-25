<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function checkPreAuth(UserInterface $user): ?RedirectResponse
    {
        if (!$user->getStatus()) {
            throw new CustomUserMessageAccountStatusException();
        }
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    public function checkPostAuth(UserInterface $user): ?RedirectResponse
    {
        if ($user->getStatus()) {
            return new RedirectResponse(
                $this->urlGenerator->generate('homepage')
            );
        }
        return null;
    }
}
