<?php

declare(strict_types=1);

/**
 * Example: Basic usage of phpstan-deprecation-rules.
 *
 * This file demonstrates the kinds of deprecated symbol usages that the extension
 * detects. Running PHPStan with rules.neon included will produce errors on the
 * marked lines below.
 *
 * Setup in phpstan.neon:
 *
 *   includes:
 *     - vendor/phpstan/phpstan-deprecation-rules/rules.neon
 *
 * Or install phpstan/extension-installer to have it loaded automatically.
 */

// --- Deprecated class ---

/**
 * @deprecated since 2.0 — Use NewService instead
 */
class OldService
{
    /**
     * @deprecated since 2.0 — Use NewService::process() instead
     */
    public function process(): void {}
}

class NewService
{
    public function process(): void {}
}

// PHPStan error: Instantiation of deprecated class OldService.
$old = new OldService();

// PHPStan error: Call to method process() of deprecated class OldService.
$old->process();


// --- Deprecated function ---

/**
 * @deprecated since 1.5 — Use newHelper() instead
 */
function oldHelper(): string
{
    return 'old';
}

function newHelper(): string
{
    return 'new';
}

// PHPStan error: Call to deprecated function oldHelper().
$result = oldHelper();


// --- Deprecated constant ---

/**
 * @deprecated since 3.0 — Use NEW_STATUS instead
 */
const OLD_STATUS = 'inactive';

const NEW_STATUS = 'active';

// PHPStan error: Use of constant OLD_STATUS is deprecated.
echo OLD_STATUS;


// --- Usage inside deprecated scope (no error reported) ---

/**
 * @deprecated since 2.0 — LegacyAdapter wraps deprecated APIs intentionally
 */
class LegacyAdapter
{
    public function wrap(): void
    {
        // No PHPStan error here — this class is itself deprecated.
        $svc = new OldService();
        $svc->process();
    }
}
