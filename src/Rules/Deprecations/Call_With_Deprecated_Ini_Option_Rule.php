<?php

declare (strict_types=1);
namespace Php_Stan\Rules\Deprecations;

use function array_key_exists;
use function count;
use function in_array;
use Php_Parser\Node;
use Php_Parser\Node\Expr\Func_Call;
use Php_Parser\Node\Name;
use Php_Stan\Analyser\Scope;
use Php_Stan\Broker\Function_Not_Found_Exception;
use Php_Stan\Php\Php_Version;
use Php_Stan\Reflection\Reflection_Provider;
use Php_Stan\Rules\Rule;
use Php_Stan\Rules\Rule_Error_Builder;
use function sprintf;
use function strtolower;
/**
 * @implements Rule<FuncCall>
 */
class Call_With_Deprecated_Ini_Option_Rule implements Rule
{
    private const INI_FUNCTIONS = ['ini_get', 'ini_set', 'ini_alter', 'ini_restore', 'get_cfg_var'];
    private const DEPRECATED_OPTIONS = [
        // deprecated since unknown version
        'mbstring.http_input' => 0,
        'mbstring.http_output' => 0,
        'mbstring.internal_encoding' => 0,
        'pdo_odbc.db2_instance_name' => 0,
        'enable_dl' => 0,
        'iconv.input_encoding' => 50600,
        'iconv.output_encoding' => 50600,
        'iconv.internal_encoding' => 50600,
        'mbstring.func_overload' => 70200,
        'track_errors' => 70200,
        'allow_url_include' => 70400,
        'assert.quiet_eval' => 80000,
        'filter.default' => 80100,
        'oci8.old_oci_close_semantics' => 80100,
        'assert.active' => 80300,
        'assert.exception' => 80300,
        'assert.bail' => 80300,
        'assert.warning' => 80300,
        'session.sid_length' => 80400,
        'session.sid_bits_per_character' => 80400,
    ];
    private Reflection_Provider $reflection_provider;
    private Deprecated_Scope_Helper $deprecated_scope_helper;
    private Php_Version $php_version;
    /**
     * @param Reflection_Provider     $reflection_provider     PHPStan's reflection provider for resolving function names
     * @param Deprecated_Scope_Helper $deprecated_scope_helper Helper to skip reporting when current scope is deprecated
     * @param Php_Version             $php_version             The configured PHP version, used to filter version-gated deprecations
     */
    public function __construct(Reflection_Provider $reflection_provider, Deprecated_Scope_Helper $deprecated_scope_helper, Php_Version $php_version)
    {
        $this->reflection_provider = $reflection_provider;
        $this->deprecated_scope_helper = $deprecated_scope_helper;
        $this->php_version = $php_version;
    }

    /**
     * Returns the AST node type this rule applies to.
     *
     * @return class-string<FuncCall> Fully qualified class name of the node type
     */
    public function get_node_type(): string
    {
        return Func_Call::class;
    }

    /**
     * Analyzes a function call and reports if a deprecated INI option name is passed.
     *
     * Checks calls to ini_get(), ini_set(), ini_alter(), ini_restore(), and get_cfg_var()
     * for known deprecated option names. The deprecation is only reported when the current
     * PHP version meets or exceeds the version when the option was deprecated.
     *
     * @param Node  $node  The function call AST node being analyzed
     * @param Scope $scope The current analysis scope
     *
     * @return \PHPStan\Rules\RuleError[] Array of errors; empty if no violation found
     *
     * @complexity O(n) where n is the number of constant string types of the first argument
     */
    public function process_node(Node $node, Scope $scope): array
    {
        if ($this->deprecated_scope_helper->is_scope_deprecated($scope)) {
            return [];
        }
        if (!$node->name instanceof Name) {
            return [];
        }
        if (count($node->get_args()) < 1) {
            return [];
        }
        try {
            $function = $this->reflection_provider->get_function($node->name, $scope);
        } catch (Function_Not_Found_Exception $e) {
            // Other rules will notify if the function is not found
            return [];
        }
        if (!in_array(strtolower($function->get_name()), self::INI_FUNCTIONS, true)) {
            return [];
        }
        $php_version_id = $this->php_version->get_version_id();
        $ini_type = $scope->get_type($node->get_args()[0]->value);
        foreach ($ini_type->get_constant_strings() as $string) {
            if (!array_key_exists($string->get_value(), self::DEPRECATED_OPTIONS)) {
                continue;
            }
            if ($php_version_id < self::DEPRECATED_OPTIONS[$string->get_value()]) {
                continue;
            }
            return [Rule_Error_Builder::message(sprintf("Call to function %s() with deprecated option '%s'.", $function->get_name(), $string->get_value()))->identifier('function.deprecated')->build()];
        }
        return [];
    }
}