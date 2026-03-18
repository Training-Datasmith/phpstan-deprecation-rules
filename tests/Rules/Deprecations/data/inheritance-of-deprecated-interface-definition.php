<?php

declare(strict_types=1);

namespace InheritanceOfDeprecatedInterface;

interface Fooable
{
}

/**
 * @deprecated
 */
interface DeprecatedFooable
{
}

/**
 * @deprecated
 */
interface DeprecatedFooable2
{
}

/**
 * @deprecated Implement something else.
 */
interface DeprecatedWithDescription
{
}
