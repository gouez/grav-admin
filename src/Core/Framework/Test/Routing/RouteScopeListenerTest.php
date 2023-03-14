<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Routing;

use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Api\Context\AdminApiSource;
use Laser\Core\Framework\Api\Context\ContextSource;
use Laser\Core\Framework\Api\Context\SalesChannelApiSource;
use Laser\Core\Framework\Api\Controller\ApiController;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Routing\Exception\InvalidRouteScopeException;
use Laser\Core\Framework\Routing\RouteScopeListener;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\PlatformRequest;
use Symfony\Bundle\WebProfilerBundle\Controller\ProfilerController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 */
class RouteScopeListenerTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testRouteScopeListenerFailsHardWithoutMasterRequest(): void
    {
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $request = $this->createRequest('/api', 'api', new AdminApiSource(null, null));

        $event = $this->createEvent($request);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to check the request scope without master request');
        $listener->checkScope($event);
    }

    public function testRouteScopeListenerIgnoresSymfonyControllers(): void
    {
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $request = $this->createRequest('/api', 'api', new AdminApiSource(null, null));

        $event = $this->createEvent($request);
        /** @var ProfilerController $profilerController */
        $profilerController = $this->getContainer()->get('web_profiler.controller.profiler');
        $event->setController($profilerController->panelAction(...));

        $listener->checkScope($event);
    }

    public function testRouteScopeListenerFailsHardWithoutAnnotation(): void
    {
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $request = $this->createRequest('/api', 'api', new AdminApiSource(null, null));
        $request->attributes->remove(PlatformRequest::ATTRIBUTE_ROUTE_SCOPE);

        $event = $this->createEvent($request);

        $this->expectException(InvalidRouteScopeException::class);
        $listener->checkScope($event);
    }

    public function testRouteScopeListenerHandlesValidAdminRequests(): void
    {
        $stack = $this->getContainer()->get(RequestStack::class);
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $request = $this->createRequest('/api', 'api', new AdminApiSource(null, null));

        $stack->push($request);
        $event = $this->createEvent($request);

        $listener->checkScope($event);
    }

    public function testRouteScopeListenerDeniesInvalidAdminRequest(): void
    {
        $stack = $this->getContainer()->get(RequestStack::class);
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $request = $this->createRequest('/api', 'api', new SalesChannelApiSource(Uuid::randomHex()));

        $stack->push($request);
        $event = $this->createEvent($request);

        $this->expectException(InvalidRouteScopeException::class);
        $listener->checkScope($event);
    }

    public function testSubrequestsAreValidatedAgainstTheMasterScope(): void
    {
        $stack = $this->getContainer()->get(RequestStack::class);
        $listener = $this->getContainer()->get(RouteScopeListener::class);

        $requestMaster = $this->createRequest('/api', 'api', new AdminApiSource(null, null));
        $requestSub = $this->createRequest('/api', 'api', new SalesChannelApiSource(Uuid::randomHex()));

        $stack->push($requestMaster);
        $stack->push($requestSub);

        $event = $this->createEvent($requestSub);

        $listener->checkScope($event);
    }

    private function createEvent(Request $request): ControllerEvent
    {
        return new ControllerEvent(
            $this->getContainer()->get('kernel'),
            [$this->getContainer()->get(ApiController::class), 'clone'],
            $request,
            HttpKernelInterface::SUB_REQUEST
        );
    }

    private function createRequest(string $route, string $scopeName, ContextSource $source): Request
    {
        $request = Request::create($route);

        $request->attributes->set(PlatformRequest::ATTRIBUTE_ROUTE_SCOPE, [$scopeName]);
        $request->attributes->set(PlatformRequest::ATTRIBUTE_CONTEXT_OBJECT, Context::createDefaultContext($source));
        $request->attributes->set('_route', 'test.it');

        return $request;
    }
}
