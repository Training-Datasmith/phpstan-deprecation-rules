<?php

declare(strict_types=1);

/**
 * Example: Custom deprecated scope resolver.
 *
 * By default, usages of deprecated symbols inside deprecated classes/functions are
 * suppressed. This example shows how to register a custom resolver to suppress warnings
 * in additional contexts — for example, inside any class tagged with a custom attribute.
 *
 * 1. Implement Deprecated_Scope_Resolver (PHPStan\Rules\Deprecations\DeprecatedScopeResolver).
 * 2. Register it in your phpstan.neon with the correct service tag.
 */

// phpstan.neon:
//
// services:
//   -
//     class: App\PHPStan\MigrationScopeResolver
//     tags:
//       - phpstan.deprecations.deprecatedScopeResolver

namespace App\PHPStan;

use PHPStan\Analyser\Scope;
use PHPStan\Rules\Deprecations\DeprecatedScopeResolver;

/**
 * Suppresses deprecation warnings inside classes annotated with @migration.
 *
 * Migration classes intentionally bridge old and new APIs, so they are expected
 * to reference deprecated symbols. Marking their containing class with @migration
 * is a project-level convention; this resolver enforces that convention in PHPStan.
 */
class MigrationScopeResolver implements DeprecatedScopeResolver
{
    public function isDeprecatedScope(Scope $scope): bool
    {
        $class = $scope->getClassReflection();
        if ($class === null) {
            return false;
        }

        // Suppress warnings for any class whose doc comment contains @migration.
        $docComment = $class->getNativeReflection()->getDocComment();
        if ($docComment === false) {
            return false;
        }

        return str_contains($docComment, '@migration');
    }
}
