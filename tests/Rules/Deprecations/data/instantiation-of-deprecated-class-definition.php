<?php

declare(strict_types=1);

namespace InstantiationOfDeprecatedClass;

class Foo
{
}

/**
 * @deprecated
 */
class DeprecatedFoo
{
}

/**
 * @deprecated Do not instantiate.
 */
class DeprecatedWithDescription
{
}
