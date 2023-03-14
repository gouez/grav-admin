<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Customer\SalesChannel;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Checkout\Customer\SalesChannel\ChangeCustomerProfileRoute;
use Laser\Core\Checkout\Customer\Validation\CustomerValidationFactory;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\DataBag\RequestDataBag;
use Laser\Core\Framework\Validation\DataValidator;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SalesChannel\StoreApiCustomFieldMapper;
use Laser\Core\System\SalesChannel\SuccessResponse;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @internal
 *
 * @covers \Laser\Core\Checkout\Customer\SalesChannel\ChangeCustomerProfileRoute
 */
#[Package('customer-order')]
class ChangeCustomerProfileRouteTest extends TestCase
{
    public function testCustomFieldsGetPassed(): void
    {
        $customFields = new RequestDataBag(['test1' => '1', 'test2' => '2']);

        $customerRepository = $this->createMock(EntityRepository::class);
        $customerRepository
            ->method('update')
            ->with([
                ['id' => 'customer1', 'company' => '', 'customFields' => ['test1' => '1']],
            ]);

        $storeApiCustomFieldMapper = $this->createMock(StoreApiCustomFieldMapper::class);
        $storeApiCustomFieldMapper
            ->expects(static::once())
            ->method('map')
            ->with('customer', $customFields)
            ->willReturn(['test1' => '1']);

        $change = new ChangeCustomerProfileRoute(
            $customerRepository,
            new EventDispatcher(),
            $this->createMock(DataValidator::class),
            $this->createMock(CustomerValidationFactory::class),
            $storeApiCustomFieldMapper
        );

        $customer = new CustomerEntity();
        $customer->setId('customer1');
        $data = new RequestDataBag([
            'customFields' => $customFields,
        ]);
        $response = $change->change($data, $this->createMock(SalesChannelContext::class), $customer);
        static::assertInstanceOf(SuccessResponse::class, $response);
    }
}
