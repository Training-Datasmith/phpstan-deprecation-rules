<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Function_Reflection;
use Php_Stan\Rules\Restricted_Usage\Restricted_Function_Usage_Extension;
use Php_Stan\Rules\Restricted_Usage\Restricted_Usage;
use function sprintf;
class Restricted_Deprecated_Function_Usage_Extension implements Restricted_Function_Usage_Extension
{
    private Deprecated_Scope_Helper $deprecated_scope_helper;
    public function __construct(Deprecated_Scope_Helper $deprecated_scope_helper)
    {
        $this->deprecated_scope_helper = $deprecated_scope_helper;
    }
    public function is_restricted_function_usage(Function_Reflection $function_reflection, Scope $scope): ?Restricted_Usage
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return null;
        }
        if (!$function_reflection->is_deprecated()->yes()) {
            return null;
        }
        $description = $function_reflection->get_deprecated_description();
        if ($description === null) {
            return Restricted_Usage::create(sprintf('Call to deprecated function %s().', $function_reflection->get_name()), 'function.deprecated');
        }
        return Restricted_Usage::create(sprintf("Call to deprecated function %s():\n%s", $function_reflection->get_name(), $description), 'function.deprecated');
    }
}