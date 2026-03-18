<?php

declare(strict_types=1);

namespace CheckDeprecatedFunctionCall;

foo();
\CheckDeprecatedFunctionCall\foo();

deprecated_foo();
\CheckDeprecatedFunctionCall\deprecated_foo();
deprecated_with_description();

/**
 * @deprecated
 */
function deprecated_scope()
{
    deprecated_foo();
    \CheckDeprecatedFunctionCall\deprecated_foo();
}

/**
 * @deprecated
 */
class DeprecatedScope
{
    public function foo()
    {
        deprecated_foo();
        \CheckDeprecatedFunctionCall\deprecated_foo();
    }

}
