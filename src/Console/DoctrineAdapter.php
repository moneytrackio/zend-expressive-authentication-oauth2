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
use ZF\Console\Route;

class DoctrineAdapter extends AbstractConsole
{
    public function __invoke(Route $route, Console $console): int
    {
        return 0;
    }
}
