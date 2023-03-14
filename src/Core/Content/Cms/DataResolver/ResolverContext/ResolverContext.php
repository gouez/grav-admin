<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\DataResolver\ResolverContext;

use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

#[Package('content')]
class ResolverContext
{
    public function __construct(
        private readonly SalesChannelContext $context,
        private readonly Request $request
    ) {
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->context;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
