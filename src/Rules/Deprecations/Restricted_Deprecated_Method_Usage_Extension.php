<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Extended_Method_Reflection;
use Php_Stan\Rules\Restricted_Usage\Restricted_Method_Usage_Extension;
use Php_Stan\Rules\Restricted_Usage\Restricted_Usage;
use function sprintf;
use function strtolower;
class Restricted_Deprecated_Method_Usage_Extension implements Restricted_Method_Usage_Extension
{
    private Deprecated_Scope_Helper $deprecated_scope_helper;

    /**
     * @param Deprecated_Scope_Helper $deprecated_scope_helper Determines whether the calling scope is itself deprecated
     */
    public function __construct(Deprecated_Scope_Helper $deprecated_scope_helper)
    {
        $this->deprecated_scope_helper = $deprecated_scope_helper;
    }

    /**
     * Checks whether a method call should be reported as a restricted (deprecated) usage.
     *
     * Returns null if the call is allowed (either scope is deprecated, or the method/class
     * is not deprecated). Returns a {@see Restricted_Usage} descriptor when a violation is
     * detected, containing a human-readable message and an error identifier.
     *
     * Handles two distinct cases:
     * - The declaring class itself is deprecated (method call on deprecated class)
     * - The specific method is deprecated (direct method deprecation)
     *
     * Special handling for `__toString` produces a "casting to string is deprecated" message.
     *
     * @param Extended_Method_Reflection $method_reflection Reflection of the method being called
     * @param Scope                      $scope             The analysis scope of the call site
     *
     * @return Restricted_Usage|null Violation descriptor, or null if no violation
     */
    public function is_restricted_method_usage(Extended_Method_Reflection $method_reflection, Scope $scope): ?Restricted_Usage
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return null;
        }
        if ($method_reflection->get_declaring_class()->is_deprecated()) {
            $class = $method_reflection->get_declaring_class();
            $class_description = $class->get_deprecated_description();
            if ($class_description === null) {
                return Restricted_Usage::create(sprintf('Call to method %s() of deprecated %s %s.', $method_reflection->get_name(), strtolower($method_reflection->get_declaring_class()->get_class_type_description()), $method_reflection->get_declaring_class()->get_name()), sprintf('%s.deprecated%s', $method_reflection->is_static() ? 'staticMethod' : 'method', $method_reflection->get_declaring_class()->get_class_type_description()));
            }
            return Restricted_Usage::create(sprintf("Call to method %s() of deprecated %s %s:\n%s", $method_reflection->get_name(), strtolower($method_reflection->get_declaring_class()->get_class_type_description()), $method_reflection->get_declaring_class()->get_name(), $class_description), sprintf('%s.deprecated%s', $method_reflection->is_static() ? 'staticMethod' : 'method', $method_reflection->get_declaring_class()->get_class_type_description()));
        }
        if (!$method_reflection->is_deprecated()->yes()) {
            return null;
        }
        $description = $method_reflection->get_deprecated_description();
        if (strtolower($method_reflection->get_name()) === '__tostring') {
            if ($description === null) {
                return Restricted_Usage::create(sprintf('Casting class %s to string is deprecated.', $method_reflection->get_declaring_class()->get_name()), 'class.toStringDeprecated');
            }
            return Restricted_Usage::create(sprintf("Casting class %s to string is deprecated.:\n%s", $method_reflection->get_declaring_class()->get_name(), $description), 'class.toStringDeprecated');
        }
        if ($description === null) {
            return Restricted_Usage::create(sprintf('Call to deprecated method %s() of %s %s.', $method_reflection->get_name(), strtolower($method_reflection->get_declaring_class()->get_class_type_description()), $method_reflection->get_declaring_class()->get_name()), sprintf('%s.deprecated', $method_reflection->is_static() ? 'staticMethod' : 'method'));
        }
        return Restricted_Usage::create(sprintf("Call to deprecated method %s() of %s %s:\n%s", $method_reflection->get_name(), strtolower($method_reflection->get_declaring_class()->get_class_type_description()), $method_reflection->get_declaring_class()->get_name(), $description), sprintf('%s.deprecated', $method_reflection->is_static() ? 'staticMethod' : 'method'));
    }
}