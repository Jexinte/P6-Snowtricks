<?php
/**
 * Handle user status
 *
 * PHP version 8
 *
 * @category Enumeration
 * @package  UserStatus
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
namespace App\Enumeration;

/**
 * Handle user status
 *
 * PHP version 8
 *
 * @category Enumeration
 * @package  UserStatus
 * @author   Yokke <mdembelepro@gmail.com>
 * @license  ISC License
 * @link     https://github.com/Jexinte/P6-Snowtricks
 */
enum UserStatus: int
{
    public const ACCOUNT_NOT_ACTIVATE = false;
    public const ACCOUNT_ACTIVATE = true;

}
