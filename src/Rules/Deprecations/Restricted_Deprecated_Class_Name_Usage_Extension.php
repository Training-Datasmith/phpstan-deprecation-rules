<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Class_Reflection;
use Php_Stan\Reflection\Reflection_Provider;
use Php_Stan\Rules\Class_Name_Usage_Location;
use Php_Stan\Rules\Restricted_Usage\Restricted_Class_Name_Usage_Extension;
use Php_Stan\Rules\Restricted_Usage\Restricted_Usage;
use function rtrim;
use function sprintf;
use function strtolower;
class Restricted_Deprecated_Class_Name_Usage_Extension implements Restricted_Class_Name_Usage_Extension
{
    private Deprecated_Scope_Helper $deprecated_scope_helper;
    private Reflection_Provider $reflection_provider;
    private bool $bleeding_edge;
    public function __construct(Deprecated_Scope_Helper $deprecated_scope_helper, Reflection_Provider $reflection_provider, bool $bleeding_edge)
    {
        $this->deprecated_scope_helper = $deprecated_scope_helper;
        $this->reflection_provider = $reflection_provider;
        $this->bleeding_edge = $bleeding_edge;
    }
    public function is_restricted_class_name_usage(Class_Reflection $class_reflection, Scope $scope, Class_Name_Usage_Location $location): ?Restricted_Usage
    {
        if (!$class_reflection->is_deprecated()) {
            return null;
        }
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return null;
        }
        $current_class_name = $location->get_current_class_name();
        if ($current_class_name !== null && $this->reflection_provider->has_class($current_class_name)) {
            $current_class_reflection = $this->reflection_provider->get_class($current_class_name);
            if ($current_class_reflection->is_deprecated()) {
                return null;
            }
        }
        $identifier_part = sprintf('deprecated%s', $class_reflection->get_class_type_description());
        $default_usage = Restricted_Usage::create($this->add_class_description_to_message($class_reflection, $location->create_message(sprintf('deprecated %s %s', strtolower($class_reflection->get_class_type_description()), $class_reflection->get_display_name()))), $location->create_identifier($identifier_part));
        if ($location->value === Class_Name_Usage_Location::CLASS_IMPLEMENTS) {
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::CLASS_EXTENDS) {
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::INTERFACE_EXTENDS) {
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::INSTANTIATION) {
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::TRAIT_USE) {
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::STATIC_METHOD_CALL) {
            $method = $location->get_method();
            if ($method === null) {
                return $default_usage;
            }
            if ($method->is_deprecated()->yes() || $method->get_declaring_class()->is_deprecated()) {
                return null;
            }
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::STATIC_PROPERTY_ACCESS) {
            $property = $location->get_property();
            if ($property === null) {
                return $default_usage;
            }
            if ($property->is_deprecated()->yes() || $property->get_declaring_class()->is_deprecated()) {
                return null;
            }
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::CLASS_CONSTANT_ACCESS) {
            $constant = $location->get_class_constant();
            if ($constant === null) {
                return $default_usage;
            }
            if ($constant->is_deprecated()->yes() || $constant->get_declaring_class()->is_deprecated()) {
                return null;
            }
            return $default_usage;
        }
        if ($location->value === Class_Name_Usage_Location::PARAMETER_TYPE || $location->value === Class_Name_Usage_Location::RETURN_TYPE) {
            return $default_usage;
        }
        if (!$this->bleeding_edge) {
            return null;
        }
        return $default_usage;
    }
    private function add_class_description_to_message(Class_Reflection $class_reflection, string $message): string
    {
        if ($class_reflection->get_deprecated_description() === null) {
            return $message;
        }
        return rtrim($message, '.') . ":\n" . $class_reflection->get_deprecated_description();
    }
}