<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
final class Default_Deprecated_Scope_Resolver implements Deprecated_Scope_Resolver
{
    public function is_scope_deprecated(Scope $scope): bool
    {
        $class = $scope->get_class_reflection();
        if ($class !== null && $class->is_deprecated()) {
            return true;
        }
        $trait = $scope->get_trait_reflection();
        if ($trait !== null && $trait->is_deprecated()) {
            return true;
        }
        $function = $scope->get_function();
        if ($function !== null && $function->is_deprecated()->yes()) {
            return true;
        }
        return false;
    }
}