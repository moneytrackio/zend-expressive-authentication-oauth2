<?php
/**
 * @see       https://github.com/zendframework/zend-expressive-authentication-oauth2 for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive-authentication-oauth2/blob/master/LICENSE.md
 *     New BSD License
 */

declare(strict_types=1);

namespace Zend\Expressive\Authentication\OAuth2;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;

use function strtoupper;

class TokenRevokeHandler implements RequestHandlerInterface
{
    /**
     * @var AuthorizationServer
     */
    protected $server;

    /**
     * @var callable
     */
    protected $responseFactory;

    /**
     * TokenRevokeHandler constructor.
     * @param AuthorizationServer $server
     * @param callable $responseFactory
     */
    public function __construct(AuthorizationServer $server, callable $responseFactory)
    {
        $this->server = $server;
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var ResponseInterface $response */
        $response = ($this->responseFactory)();

        if (strtoupper($request->getMethod()) !== 'POST') {
            return $response->withStatus(501);
        }

        try {
            return $this->server->respondToRevokeTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            return new EmptyResponse(200);
        } catch (\Exception $exception) {
            return new EmptyResponse(200);
            /*return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error', 500))
                ->generateHttpResponse($response);*/
        }
    }
}
