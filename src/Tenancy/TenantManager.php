<?php

declare(strict_types=1);

namespace Palet\Framework\Tenancy;

use Palet\Framework\Contracts\Tenancy\TenantManagerInterface;
use Palet\Framework\Contracts\Tenancy\TenantResolverInterface;
use Palet\Framework\Contracts\Tenancy\TenantContextInterface;
use Palet\Framework\Contracts\Tenancy\TenantBootstrapInterface;
use Palet\Framework\Contracts\Http\Message\RequestInterface;
use Palet\Framework\Contracts\Events\EventDispatcherInterface;
use Palet\Framework\Tenancy\Events\TenantResolving;
use Palet\Framework\Tenancy\Events\TenantResolved;
use Palet\Framework\Tenancy\Events\TenantBootstrapping;
use Palet\Framework\Tenancy\Events\TenantBootstrapped;
use Palet\Framework\Tenancy\Events\TenantContextDestroyed;
use RuntimeException;

class TenantManager implements TenantManagerInterface
{
    protected TenantContextInterface $context;
    protected TenantLocator $locator;
    protected TenantBootstrapInterface $bootstrapper;
    /** @var TenantResolverInterface[] */
    protected array $resolvers = [];
    protected ?EventDispatcherInterface $events = null;

    public function __construct(
        TenantContextInterface $context,
        TenantLocator $locator,
        TenantBootstrapInterface $bootstrapper
    ) {
        $this->context = $context;
        $this->locator = $locator;
        $this->bootstrapper = $bootstrapper;
    }

    public function setEventDispatcher(EventDispatcherInterface $events): void
    {
        $this->events = $events;
    }

    public function addResolver(TenantResolverInterface $resolver): void
    {
        $this->resolvers[] = $resolver;
    }

    public function initialize(RequestInterface $request): void
    {
        if ($this->events) {
            $this->events->dispatch(new TenantResolving($request));
        }

        $tenantId = null;
        foreach ($this->resolvers as $resolver) {
            $tenantId = $resolver->resolve($request);
            if ($tenantId !== null) {
                break;
            }
        }

        if ($tenantId === null) {
            throw new RuntimeException("Could not resolve tenant from request.");
        }

        $tenant = $this->locator->findById($tenantId);
        
        if ($tenant === null) {
            throw new RuntimeException("Tenant with ID [{$tenantId}] not found.");
        }

        if ($this->events) {
            $this->events->dispatch(new TenantResolved($tenant));
        }

        $this->context->setTenant($tenant);

        if ($this->events) {
            $this->events->dispatch(new TenantBootstrapping($tenant));
        }

        $this->bootstrapper->bootstrap($tenant);

        if ($this->events) {
            $this->events->dispatch(new TenantBootstrapped($tenant));
        }
    }

    public function endContext(): void
    {
        $tenant = $this->context->getTenant();
        
        $this->bootstrapper->revert();
        $this->context->setTenant(null);

        if ($this->events && $tenant) {
            $this->events->dispatch(new TenantContextDestroyed($tenant));
        }
    }
}
