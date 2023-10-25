<?php

/**
 * Handle auth checks
 *
 * PHP version 8
 *
 * @category Security
 * @package  UserChecker
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Handle auth checks
 *
 * PHP version 8
 *
 * @category Security
 * @package  UserChecker
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class UserChecker implements UserCheckerInterface
{

    /**
     * Summary of __construct
     *
     * @param UrlGeneratorInterface $urlGenerator Object
     */
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Summary of checkPreAuth
     *
     * @param UserInterface $user Object
     *
     * @return RedirectResponse|null
     */
    public function checkPreAuth(UserInterface $user): ?RedirectResponse
    {
        if (!$user->getStatus()) {
            throw new CustomUserMessageAccountStatusException();
        }
        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    /**
     * Summary of checkPostAuth
     *
     * @param UserInterface $user Object
     *
     * @return RedirectResponse|null
     */
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
