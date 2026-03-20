# Architecture: phpstan-deprecation-rules

## Purpose

A PHPStan extension that detects usages of deprecated PHP code. It reports errors when
code references classes, methods, functions, properties, constants, traits, or INI options
annotated with `@deprecated` (or custom deprecation markers).

## Directory Structure

```
src/
  DependencyInjection/
    Lazy_Deprecated_Scope_Resolver_Provider.php  # Lazy DI provider; collects tagged resolvers on first use
  Rules/Deprecations/
    Call_With_Deprecated_Ini_Option_Rule.php      # Rule: reports deprecated INI option names in ini_*() calls
    Default_Deprecated_Scope_Resolver.php         # Default resolver: checks @deprecated on class/trait/function
    Deprecated_Scope_Helper.php                   # Aggregates multiple resolvers; short-circuits on first match
    Deprecated_Scope_Resolver.php                 # @api interface for custom scope resolvers
    Fetching_Deprecated_Const_Rule.php            # Rule: reports access to deprecated global constants
    Restricted_Deprecated_Class_Constant_Usage_Extension.php
    Restricted_Deprecated_Class_Name_Usage_Extension.php
    Restricted_Deprecated_Function_Usage_Extension.php
    Restricted_Deprecated_Method_Usage_Extension.php
    Restricted_Deprecated_Property_Usage_Extension.php
tests/
  Rules/Deprecations/
    *_Test.php       # PHPUnit tests using PHPStan's RuleTestCase
    data/            # PHP fixture files: *-definition.php (symbols) and *-usage.php (references)
  bootstrap.php
rules.neon           # Extension registration: services, rules, restricted-usage extensions
```

## Key Design Decisions

### Restricted Usage Extensions vs. Rules

Most deprecation checks are implemented as PHPStan `Restricted*UsageExtension` interfaces
rather than standalone `Rule` implementations. This approach gives PHPStan's core the
ability to integrate the errors at the right abstraction level and deduplicate them with
any overlapping type-level rules. Only two cases require standalone rules:

- **Global constants** — no `RestrictedConstantUsageExtension` interface exists
- **INI options** — requires inspecting the string value of the first argument

### Deprecated Scope Suppression

Code that is itself deprecated is allowed to use other deprecated symbols without warnings.
This is implemented via the `Deprecated_Scope_Resolver` interface and aggregated through
`Deprecated_Scope_Helper`. The default resolver checks `@deprecated` on the surrounding
class, trait, and function/method. Users may register custom resolvers via the DI tag
`phpstan.deprecations.deprecatedScopeResolver`.

### Lazy Initialization

`Lazy_Deprecated_Scope_Resolver_Provider` defers collection of tagged services until the
first analysis run, avoiding circular dependency issues during container construction.

## Extension Points

- **`Deprecated_Scope_Resolver`** (`@api`) — implement and tag with
  `phpstan.deprecations.deprecatedScopeResolver` to define custom contexts where deprecated
  usage should be suppressed (e.g., migration layers, compatibility shims).

## Dependency Flow

```
rules.neon
  └─ Lazy_Deprecated_Scope_Resolver_Provider
       └─ Deprecated_Scope_Helper
            └─ Deprecated_Scope_Resolver[] (default + custom via DI tag)

Restricted_Deprecated_*_Extension
  └─ Deprecated_Scope_Helper (via constructor injection)

Call_With_Deprecated_Ini_Option_Rule
  └─ Reflection_Provider + Deprecated_Scope_Helper + Php_Version

Fetching_Deprecated_Const_Rule
  └─ Reflection_Provider + Deprecated_Scope_Helper
```
