<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
class Deprecated_Scope_Helper
{
    /** @var DeprecatedScopeResolver[]  */
    private array $resolvers;
    /**
     * @param DeprecatedScopeResolver[] $checkers
     */
    public function __construct(array $checkers)
    {
        $this->resolvers = $checkers;
    }
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