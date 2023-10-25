<?php

/**
 * Handle security
 *
 * PHP version 8
 *
 * @category Security
 * @package  AuthenticationEntryPoint
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Handle security
 *
 * PHP version 8
 *
 * @category Security
 * @package  AuthenticationEntryPoint
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{

    /**
     * Summary of __construct
     *
     * @param UrlGeneratorInterface $urlGenerator Object
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }


    /**
     * Summary of start
     *
     * @param Request                      $request       Object
     * @param AuthenticationException|null $authException Object
     * 
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('unauthorized'));
    }
}
