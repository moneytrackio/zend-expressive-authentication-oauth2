<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2017 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication\OAuth2\Console;

use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\Console\ColorInterface as Color;
use Zend\Stdlib\StringUtils;

abstract class AbstractConsole
{
    /**
     * Method extracted from Zend\Filter\CamelcaseToUnderscore
     *
     * @param $value
     * @return string
     */
    protected function camelCaseToUnderscore($value): string
    {
        $separator = '_';
        if (StringUtils::hasPcreUnicodeSupport()) {
            $pattern     = ['#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#', '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'];
            $replacement = [$separator . '\1', $separator . '\1'];
        } else {
            $pattern     = ['#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#'];
            $replacement = ['\1' . $separator . '\2', $separator . '\1'];
        }

        return preg_replace($pattern, $replacement, $value);
    }

    /**
     * Format output message to console
     *
     * @param Console $console
     * @param string $title
     * @param string $message
     */
    protected function displayMessage(Console $console, string $title, string $message): void
    {
        $console->write($title . "\n", Color::WHITE);
        $console->write($message . "\n", Color::BLUE);
        $console->write("\n");
    }
}
