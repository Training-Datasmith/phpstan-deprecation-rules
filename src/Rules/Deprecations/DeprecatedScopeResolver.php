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
    public function is_scope_deprecated(Scope $scope): bool;
}