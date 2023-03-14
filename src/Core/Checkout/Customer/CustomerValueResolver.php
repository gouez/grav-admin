<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer;

use Laser\Core\Framework\Log\Package;
use Laser\Core\PlatformRequest;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[Package('customer-order')]
class CustomerValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        if ($argument->getType() !== CustomerEntity::class) {
            return;
        }

        $loginRequired = $request->attributes->get(PlatformRequest::ATTRIBUTE_LOGIN_REQUIRED);

        if ($loginRequired !== true) {
            $route = $request->attributes->get('_route');

            throw new \RuntimeException('Missing @LoginRequired annotation for route: ' . $route);
        }

        $context = $request->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
        if (!$context instanceof SalesChannelContext) {
            $route = $request->attributes->get('_route');

            throw new \RuntimeException('Missing sales channel context for route ' . $route);
        }

        yield $context->getCustomer();
    }
}
