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
use ZF\Console\Route;

class GenerateKeys extends AbstractConsole
{
    public function __invoke(Route $route, Console $console): int
    {
        if (!extension_loaded('openssl')) {
            fwrite(STDERR, 'Extension \'openssl\' is not available' . PHP_EOL);
            exit(1);
        }

        $keysPath = trim($route->getMatchedParam('keys-path'), '/');
        $configPath = realpath(getcwd()) . DIRECTORY_SEPARATOR . ltrim($route->getMatchedParam('config-path'), '/');

        // see if there's a data dir of the parent application
        if (file_exists($keysPath)) {
            $console->writeLine(sprintf("Found path for keys:\n%s\n\n", $keysPath), Color::GREEN);
        } else {
            if (! is_dir($keysPath)) {
                $console->writeLine(sprintf("Path '%s' for generated keys does not exist.\n", $keysPath), Color::RED);
                return 1;
            }
        }

        if (!is_writable($keysPath)) {
            $console->writeLine(sprintf("Directory '%s' is not writable.\n", $keysPath), Color::RED);
            return 1;
        }

        $configPathTemplate = realpath(__DIR__ . '/../../config/oauth2-config.php.dist');
        $oauth2Config = file_get_contents($configPathTemplate);

        $params = [
            'authCodeExpiry' => $route->getMatchedParam('authCodeExpiry'),
            'accessTokenExpiry' => $route->getMatchedParam('accessTokenExpiry'),
            'refreshTokenExpiry' => $route->getMatchedParam('refreshTokenExpiry'),
            'privateKey' => $keysPath . '/private.key',
            'publicKey' => $keysPath . '/public.key',
            'encryptionKey' => $keysPath . '/encryption.key',
        ];

        foreach ($params as $index => $param) {
            $value = $params[$index];
            $pattern = strtoupper($this->camelCaseToUnderscore($index));
            $oauth2Config = str_replace($pattern, $value, $oauth2Config);
        }

        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        // Private key
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        file_put_contents($params['privateKey'], $privateKey);
        chmod($params['privateKey'], 600);
        $this->displayMessage($console, "Private key stored in:", $params['privateKey']);

        // Public key
        $publicKey = openssl_pkey_get_details($res);
        file_put_contents($params['publicKey'], $publicKey["key"]);
        chmod($params['publicKey'], 600);
        $this->displayMessage($console, "Public key stored in:", $params['publicKey']);

        // Encryption key
        $encKey = base64_encode(random_bytes(32));
        file_put_contents($params['encryptionKey'], sprintf("<?php return '%s';", $encKey));
        chmod($params['encryptionKey'], 600);
        $this->displayMessage($console, "Encryption key stored in:", $params['encryptionKey']);

        file_put_contents(sprintf('%s/oauth2.php', $configPath), $oauth2Config);

        return 0;
    }
}
