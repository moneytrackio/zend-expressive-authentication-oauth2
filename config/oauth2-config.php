<?php
/**
 * To generate a private key run this command:
 * openssl genrsa -out private.key 1024
 *
 * To generate the encryption key use this command:
 * php -r 'echo base64_encode(random_bytes(32)), PHP_EOL;'
 *
 * The expire values must be a valid DateInterval format
 * @see http://php.net/manual/en/class.dateinterval.php
 */
return [
    'authentication' => [
        'private_key' => __DIR__ . '/../../../PRIVATE_KEY',
        'public_key' => __DIR__ . '/../../../PUBLIC_KEY',
        'encryption_key' => require __DIR__ . '/../../../ENCRYPTION_KEY',
        'access_token_expire' => 'ACCESS_TOKEN_EXPIRY',
        'refresh_token_expire' => 'REFRESH_TOKEN_EXPIRY',
        'auth_code_expire' => 'AUTH_CODE_EXPIRY',

        'grants' => [
            League\OAuth2\Server\Grant\ClientCredentialsGrant::class
            => League\OAuth2\Server\Grant\ClientCredentialsGrant::class,
            League\OAuth2\Server\Grant\PasswordGrant::class
            => League\OAuth2\Server\Grant\PasswordGrant::class,
            League\OAuth2\Server\Grant\AuthCodeGrant::class
            => League\OAuth2\Server\Grant\AuthCodeGrant::class,
            League\OAuth2\Server\Grant\ImplicitGrant::class
            => League\OAuth2\Server\Grant\ImplicitGrant::class,
            League\OAuth2\Server\Grant\RefreshTokenGrant::class
            => League\OAuth2\Server\Grant\RefreshTokenGrant::class
        ],
    ],
];
