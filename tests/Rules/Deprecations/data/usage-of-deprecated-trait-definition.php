<?php

declare(strict_types=1);

namespace UsageOfDeprecatedTrait;

trait FooTrait
{
}

/**
 * @deprecated
 */
trait DeprecatedFooTrait
{
}

/**
 * @deprecated Do not use traits.
 */
trait DeprecatedTraitWithDescription
{
}
