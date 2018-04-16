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
        $userClass = $route->getMatchedParam('userClass');
        $userField = $route->getMatchedParam('userField');
        $configPath = realpath(getcwd()) . DIRECTORY_SEPARATOR . ltrim($route->getMatchedParam('config-path'), '/');

        $configPathTemplate = realpath(__DIR__ . '/../../config/oauth2-doctrine.php.dist');
        $oauth2DoctrineConfig = file_get_contents($configPathTemplate);

        $params = compact('userClass', 'userField');

        foreach ($params as $index => $param) {
            $value = $params[$index];
            $pattern = strtoupper($this->camelCaseToUnderscore($index));
            $oauth2DoctrineConfig = str_replace($pattern, $value, $oauth2DoctrineConfig);
        }

        file_put_contents(sprintf('%s/oauth2-doctrine.php', $configPath), $oauth2DoctrineConfig);

        return 0;
    }
}
