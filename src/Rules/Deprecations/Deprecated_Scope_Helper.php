<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
class Deprecated_Scope_Helper
{
    /** @var DeprecatedScopeResolver[]  */
    private array $resolvers;

    /**
     * Constructs the helper with a list of scope resolver implementations.
     *
     * Each resolver is consulted in order; the first one to return true short-circuits
     * the remaining checks. Resolvers are typically registered via the DI container tag
     * `phpstan.deprecations.deprecatedScopeResolver`.
     *
     * @param Deprecated_Scope_Resolver[] $checkers One or more scope resolver implementations
     */
    public function __construct(array $checkers)
    {
        $this->resolvers = $checkers;
    }

    /**
     * Checks whether the given analysis scope is deprecated according to any registered resolver.
     *
     * Iterates through all registered {@see Deprecated_Scope_Resolver} instances and returns
     * true as soon as one confirms the scope is deprecated. Returns false if none do.
     *
     * @param Scope $scope The PHPStan analysis scope to evaluate
     *
     * @return bool True if any resolver considers the scope deprecated
     */
    public function is_scope_deprecated(Scope $scope): bool
    {
        foreach ($this->resolvers as $checker) {
            if ($checker->is_scope_deprecated($scope)) {
                return true;
            }
        }
        return false;
    }
}