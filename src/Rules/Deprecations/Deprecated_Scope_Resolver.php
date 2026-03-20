<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
/**
 * This is the interface for custom deprecated scope resolvers.
 *
 * To register it in the configuration file use the `phpstan.deprecations.deprecatedScopeResolver` service tag:
 *
 * ```
 * services:
 * 	-
 *		class: App\PHPStan\MyExtension
 *		tags:
 *			- phpstan.deprecations.deprecatedScopeResolver
 * ```
 *
 * @api
 */
interface Deprecated_Scope_Resolver
{
    /**
     * Determines whether the given PHPStan analysis scope should be treated as deprecated.
     *
     * When this returns true, usages of deprecated symbols within the scope are suppressed.
     * Implement this interface to define custom deprecation context detection logic beyond
     * the default `@deprecated` annotation check.
     *
     * @param Scope $scope The PHPStan analysis scope representing the call or usage context
     *
     * @return bool True if the scope is deprecated and usage warnings should be suppressed
     */
    public function is_scope_deprecated(Scope $scope): bool;
}