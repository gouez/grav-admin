<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Shipping;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Shipping\ShippingMethodCollection;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Write\WriteException;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\Framework\Validation\WriteConstraintViolationException;
use Laser\Core\System\DeliveryTime\DeliveryTimeEntity;

/**
 * @internal
 */
#[Package('checkout')]
class ShippingMethodRepositoryTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository
     */
    private $shippingRepository;

    private string $shippingMethodId;

    private string $ruleId;

    public function setUp(): void
    {
        $this->shippingRepository = $this->getContainer()->get('shipping_method.repository');
        $this->shippingMethodId = Uuid::randomHex();
        $this->ruleId = Uuid::randomHex();
    }

    public function testCreateShippingMethod(): void
    {
        $defaultContext = Context::createDefaultContext();

        $shippingMethod = $this->createShippingMethodDummyArray();

        $this->shippingRepository->create($shippingMethod, $defaultContext);

        $criteria = new Criteria([$this->shippingMethodId]);
        $criteria->addAssociation('availabilityRule');

        /** @var ShippingMethodCollection $resultSet */
        $resultSet = $this->shippingRepository->search($criteria, $defaultContext);

        static::assertSame($this->shippingMethodId, $resultSet->first()->getId());
        static::assertSame($this->ruleId, $resultSet->first()->getAvailabilityRule()->getId());
        static::assertSame($this->ruleId, $resultSet->first()->getAvailabilityRuleId());
    }

    public function testUpdateShippingMethod(): void
    {
        $defaultContext = Context::createDefaultContext();

        $shippingMethod = $this->createShippingMethodDummyArray();

        $this->shippingRepository->create($shippingMethod, $defaultContext);

        $updateParameter = [
            'id' => $this->shippingMethodId,
            'availabilityRule' => [
                'id' => Uuid::randomHex(),
                'name' => 'test update',
                'priority' => 5,
                'created_at' => new \DateTime(),
            ],
        ];

        $this->shippingRepository->update([$updateParameter], $defaultContext);

        $criteria = new Criteria([$this->shippingMethodId]);
        $criteria->addAssociation('availabilityRule');

        /** @var ShippingMethodCollection $resultSet */
        $resultSet = $this->shippingRepository->search($criteria, $defaultContext);

        static::assertSame('test update', $resultSet->first()->getAvailabilityRule()->getName());
    }

    public function testShippingMethodCanBeDeleted(): void
    {
        $defaultContext = Context::createDefaultContext();

        $shippingMethod = $this->createShippingMethodDummyArray();

        $this->shippingRepository->create($shippingMethod, $defaultContext);

        $primaryKey = [
            'id' => $this->shippingMethodId,
        ];

        $this->shippingRepository->delete([$primaryKey], $defaultContext);

        $criteria = new Criteria([$this->shippingMethodId]);

        /** @var ShippingMethodCollection $resultSet */
        $resultSet = $this->shippingRepository->search($criteria, $defaultContext);

        static::assertCount(0, $resultSet);
    }

    public function testThrowsExceptionIfNotAllRequiredValuesAreGiven(): void
    {
        $defaultContext = Context::createDefaultContext();
        $shippingMethod = $this->createShippingMethodDummyArray();

        unset($shippingMethod[0]['name']);

        try {
            $this->shippingRepository->create($shippingMethod, $defaultContext);

            static::fail('The type should always be required!');
        } catch (WriteException $e) {
            /** @var WriteConstraintViolationException $constraintViolation */
            $constraintViolation = $e->getExceptions()[0];
            static::assertInstanceOf(WriteConstraintViolationException::class, $constraintViolation);
            static::assertEquals('/name', $constraintViolation->getViolations()[0]->getPropertyPath());
        }
    }

    public function testSearchWithoutEntriesWillBeEmpty(): void
    {
        $defaultContext = Context::createDefaultContext();

        $result = $this->shippingRepository->search(new Criteria([$this->shippingMethodId]), $defaultContext);

        static::assertEmpty($result);
    }

    private function createShippingMethodDummyArray(): array
    {
        return [
            [
                'id' => $this->shippingMethodId,
                'bindShippingfree' => false,
                'name' => 'test',
                'tax_type' => null,
                'availabilityRule' => [
                    'id' => $this->ruleId,
                    'name' => 'asd',
                    'priority' => 2,
                ],
                'deliveryTime' => $this->createDeliveryTimeData(),
            ],
        ];
    }

    private function createDeliveryTimeData(): array
    {
        return [
            'id' => Uuid::randomHex(),
            'name' => 'testDeliveryTime',
            'min' => 1,
            'max' => 90,
            'unit' => DeliveryTimeEntity::DELIVERY_TIME_DAY,
        ];
    }
}
