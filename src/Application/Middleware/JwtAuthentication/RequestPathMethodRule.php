<?php

declare(strict_types=1);

namespace PetWatcher\Application\Middleware\JwtAuthentication;

use Psr\Http\Message\ServerRequestInterface;
use Tuupola\Middleware\JwtAuthentication\RuleInterface;

/**
 * Rule to decide by request path and HTTP verb whether the request should be authenticated or not.
 */
class RequestPathMethodRule implements RuleInterface
{
    /**
     * Stores all the options passed to the rule
     */
    private $options = [
        'passthrough' => [],
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        $uri = '/' . $request->getUri()->getPath();
        $uri = preg_replace('#/+#', '/', $uri);

        // If request path matches a passthrough path and its HTTP method, skip authentication
        foreach ((array)$this->options['passthrough'] as $path => $methods) {
            $ignore = rtrim($path, '/');
            if (!!preg_match("@^{$ignore}$@", $uri) && in_array($request->getMethod(), $methods)) {
                return false;
            }
        }

        return true;
    }
}
