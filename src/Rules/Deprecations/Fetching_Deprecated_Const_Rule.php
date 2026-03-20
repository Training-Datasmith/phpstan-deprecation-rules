<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use Php_Parser\Node;
use Php_Parser\Node\Expr\Const_Fetch;
use Php_Stan\Analyser\Scope;
use Php_Stan\Reflection\Reflection_Provider;
use Php_Stan\Rules\Rule;
use Php_Stan\Rules\Rule_Error_Builder;
use function sprintf;
/**
 * @implements Rule<ConstFetch>
 */
class Fetching_Deprecated_Const_Rule implements Rule
{
    private Reflection_Provider $reflection_provider;
    private Deprecated_Scope_Helper $deprecated_scope_helper;

    /**
     * @param Reflection_Provider   $reflection_provider   PHPStan's reflection provider for resolving constants
     * @param Deprecated_Scope_Helper $deprecated_scope_helper Helper that determines whether the current scope is itself deprecated
     */
    public function __construct(Reflection_Provider $reflection_provider, Deprecated_Scope_Helper $deprecated_scope_helper)
    {
        $this->reflection_provider = $reflection_provider;
        $this->deprecated_scope_helper = $deprecated_scope_helper;
    }

    /**
     * Returns the AST node type this rule applies to.
     *
     * @return class-string<ConstFetch> The fully qualified class name of the node type
     */
    public function get_node_type(): string
    {
        return Const_Fetch::class;
    }

    /**
     * Analyzes a constant fetch expression and reports if the fetched constant is deprecated.
     *
     * Reports an error when a global constant marked with `@deprecated` is used outside
     * a deprecated scope. Uses the constant's own deprecation description if present,
     * otherwise falls back to a generic message.
     *
     * @param Node  $node  The constant fetch AST node being analyzed
     * @param Scope $scope The current analysis scope
     *
     * @return \PHPStan\Rules\RuleError[] Array of errors; empty if no violation found
     */
    public function process_node(Node $node, Scope $scope): array
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return [];
        }
        if (!$this->reflection_provider->has_constant($node->name, $scope)) {
            return [];
        }
        $constant_reflection = $this->reflection_provider->get_constant($node->name, $scope);
        if ($constant_reflection->is_deprecated()->yes()) {
            return [Rule_Error_Builder::message(sprintf($constant_reflection->get_deprecated_description() ?? 'Use of constant %s is deprecated.', $constant_reflection->get_name()))->identifier('constant.deprecated')->build()];
        }
        return [];
    }
}