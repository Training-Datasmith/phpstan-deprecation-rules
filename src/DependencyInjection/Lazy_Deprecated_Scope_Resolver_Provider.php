<?php

declare (strict_types=1);
namespace Php_Stan\Dependency_Injection;

use Php_Stan\Rules\Deprecations\Deprecated_Scope_Helper;
final class Lazy_Deprecated_Scope_Resolver_Provider
{
    public const EXTENSION_TAG = 'phpstan.deprecations.deprecatedScopeResolver';

    private Container $container;
    private ?Deprecated_Scope_Helper $scope_helper = null;

    /**
     * @param Container $container The DI container used to resolve tagged scope resolver services
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the lazily-initialized {@see Deprecated_Scope_Helper} instance.
     *
     * On first call, collects all services tagged with {@see EXTENSION_TAG} from the DI container
     * and constructs a {@see Deprecated_Scope_Helper}. Subsequent calls return the cached instance.
     *
     * @return Deprecated_Scope_Helper The aggregated scope helper with all registered resolvers
     */
    public function get(): Deprecated_Scope_Helper
    {
        if ($this->scope_helper === null) {
            $this->scope_helper = new Deprecated_Scope_Helper($this->container->get_services_by_tag(self::EXTENSION_TAG));
        }
        return $this->scope_helper;
    }
}