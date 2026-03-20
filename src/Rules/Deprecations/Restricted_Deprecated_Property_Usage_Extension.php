<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Extended_Property_Reflection;
use Php_Stan\Rules\Restricted_Usage\Restricted_Property_Usage_Extension;
use Php_Stan\Rules\Restricted_Usage\Restricted_Usage;
use function sprintf;
use function strtolower;
class Restricted_Deprecated_Property_Usage_Extension implements Restricted_Property_Usage_Extension
{
    private Deprecated_Scope_Helper $deprecated_scope_helper;
    public function __construct(Deprecated_Scope_Helper $deprecated_scope_helper)
    {
        $this->deprecated_scope_helper = $deprecated_scope_helper;
    }
    public function is_restricted_property_usage(Extended_Property_Reflection $property_reflection, Scope $scope): ?Restricted_Usage
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return null;
        }
        if ($property_reflection->get_declaring_class()->is_deprecated()) {
            $class = $property_reflection->get_declaring_class();
            $class_description = $class->get_deprecated_description();
            if ($class_description === null) {
                return Restricted_Usage::create(sprintf('Access to %sproperty $%s of deprecated %s %s.', $property_reflection->is_static() ? 'static ' : '', $property_reflection->get_name(), strtolower($property_reflection->get_declaring_class()->get_class_type_description()), $property_reflection->get_declaring_class()->get_name()), sprintf('%s.deprecated%s', $property_reflection->is_static() ? 'staticProperty' : 'property', $property_reflection->get_declaring_class()->get_class_type_description()));
            }
            return Restricted_Usage::create(sprintf("Access to %sproperty \$%s of deprecated %s %s:\n%s", $property_reflection->is_static() ? 'static ' : '', $property_reflection->get_name(), strtolower($property_reflection->get_declaring_class()->get_class_type_description()), $property_reflection->get_declaring_class()->get_name(), $class_description), sprintf('%s.deprecated%s', $property_reflection->is_static() ? 'staticProperty' : 'property', $property_reflection->get_declaring_class()->get_class_type_description()));
        }
        if (!$property_reflection->is_deprecated()->yes()) {
            return null;
        }
        $description = $property_reflection->get_deprecated_description();
        if ($description === null) {
            return Restricted_Usage::create(sprintf('Access to deprecated %sproperty $%s of %s %s.', $property_reflection->is_static() ? 'static ' : '', $property_reflection->get_name(), strtolower($property_reflection->get_declaring_class()->get_class_type_description()), $property_reflection->get_declaring_class()->get_name()), sprintf('%s.deprecated', $property_reflection->is_static() ? 'staticProperty' : 'property'));
        }
        return Restricted_Usage::create(sprintf("Access to deprecated %sproperty \$%s of %s %s:\n%s", $property_reflection->is_static() ? 'static ' : '', $property_reflection->get_name(), strtolower($property_reflection->get_declaring_class()->get_class_type_description()), $property_reflection->get_declaring_class()->get_name(), $description), sprintf('%s.deprecated', $property_reflection->is_static() ? 'staticProperty' : 'property'));
    }
}