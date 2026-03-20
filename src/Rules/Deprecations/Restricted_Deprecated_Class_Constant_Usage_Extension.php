<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Class_Constant_Reflection;
use Php_Stan\Rules\Restricted_Usage\Restricted_Class_Constant_Usage_Extension;
use Php_Stan\Rules\Restricted_Usage\Restricted_Usage;
use function sprintf;
use function strtolower;
class Restricted_Deprecated_Class_Constant_Usage_Extension implements Restricted_Class_Constant_Usage_Extension
{
    private Deprecated_Scope_Helper $deprecated_scope_helper;
    public function __construct(Deprecated_Scope_Helper $deprecated_scope_helper)
    {
        $this->deprecated_scope_helper = $deprecated_scope_helper;
    }
    public function is_restricted_class_constant_usage(Class_Constant_Reflection $constant_reflection, Scope $scope): ?Restricted_Usage
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return null;
        }
        if ($constant_reflection->get_declaring_class()->is_deprecated()) {
            $class = $constant_reflection->get_declaring_class();
            $class_description = $class->get_deprecated_description();
            if ($class_description === null) {
                return Restricted_Usage::create(sprintf('Fetching class constant %s of deprecated %s %s.', $constant_reflection->get_name(), strtolower($constant_reflection->get_declaring_class()->get_class_type_description()), $constant_reflection->get_declaring_class()->get_name()), sprintf('classConstant.deprecated%s', $constant_reflection->get_declaring_class()->get_class_type_description()));
            }
            return Restricted_Usage::create(sprintf("Fetching class constant %s of deprecated %s %s:\n%s", $constant_reflection->get_name(), strtolower($constant_reflection->get_declaring_class()->get_class_type_description()), $constant_reflection->get_declaring_class()->get_name(), $class_description), sprintf('classConstant.deprecated%s', $constant_reflection->get_declaring_class()->get_class_type_description()));
        }
        if (!$constant_reflection->is_deprecated()->yes()) {
            return null;
        }
        $description = $constant_reflection->get_deprecated_description();
        if ($description === null) {
            return Restricted_Usage::create(sprintf('Fetching deprecated class constant %s of %s %s.', $constant_reflection->get_name(), strtolower($constant_reflection->get_declaring_class()->get_class_type_description()), $constant_reflection->get_declaring_class()->get_name()), 'classConstant.deprecated');
        }
        return Restricted_Usage::create(sprintf("Fetching deprecated class constant %s of %s %s:\n%s", $constant_reflection->get_name(), strtolower($constant_reflection->get_declaring_class()->get_class_type_description()), $constant_reflection->get_declaring_class()->get_name(), $description), 'classConstant.deprecated');
    }
}