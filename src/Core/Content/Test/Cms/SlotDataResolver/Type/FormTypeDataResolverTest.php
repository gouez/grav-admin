<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Cms\SlotDataResolver\Type;

use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Laser\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Laser\Core\Content\Cms\DataResolver\Element\FormCmsElementResolver;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Uuid\Uuid;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\Salutation\SalesChannel\AbstractSalutationRoute;
use Laser\Core\System\Salutation\SalesChannel\SalutationRouteResponse;
use Laser\Core\System\Salutation\SalutationCollection;
use Laser\Core\System\Salutation\SalutationDefinition;
use Laser\Core\System\Salutation\SalutationEntity;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
class FormTypeDataResolverTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testType(): void
    {
        $formCmsElementResolver = $this->getContainer()->get(FormCmsElementResolver::class);

        static::assertEquals('form', $formCmsElementResolver->getType());
    }

    public function testResolverUsesAbstractSalutationsRouteToEnrichSlot(): void
    {
        $salutationCollection = $this->getSalutationCollection();
        $formCmsElementResolver = new FormCmsElementResolver($this->getSalutationRoute($salutationCollection));

        $formElement = $this->getCmsFormElement();

        $formCmsElementResolver->enrich(
            $formElement,
            new ResolverContext($this->createMock(SalesChannelContext::class), new Request()),
            new ElementDataCollection()
        );

        static::assertSame($formElement->getData(), $salutationCollection);
    }

    public function testResolverSortsSalutationsBySalutationKeyDesc(): void
    {
        $salutationCollection = $this->getSalutationCollection();
        $formCmsElementResolver = new FormCmsElementResolver($this->getSalutationRoute($salutationCollection));

        $formElement = $this->getCmsFormElement();

        $formCmsElementResolver->enrich(
            $formElement,
            new ResolverContext($this->createMock(SalesChannelContext::class), new Request()),
            new ElementDataCollection()
        );

        /** @var SalutationCollection $enrichedCollection */
        $enrichedCollection = $formElement->getData();

        $sortedKeys = array_values($enrichedCollection->map(static fn (SalutationEntity $salutation) => $salutation->getSalutationKey()));

        static::assertEquals(['d', 'c', 'b', 'a'], $sortedKeys);
    }

    private function getCmsFormElement(): CmsSlotEntity
    {
        $slot = new CmsSlotEntity();
        $slot->setType('form');
        $slot->setUniqueIdentifier('id');

        return $slot;
    }

    private function getSalutationCollection(): SalutationCollection
    {
        return new SalutationCollection([
            $this->createSalutationWithSalutationKey('c'),
            $this->createSalutationWithSalutationKey('a'),
            $this->createSalutationWithSalutationKey('d'),
            $this->createSalutationWithSalutationKey('b'),
        ]);
    }

    private function createSalutationWithSalutationKey(string $salutationKey): SalutationEntity
    {
        return (new SalutationEntity())->assign([
            'id' => Uuid::randomHex(),
            'salutationKey' => $salutationKey,
        ]);
    }

    private function getSalutationRoute(SalutationCollection $salutationCollection): AbstractSalutationRoute
    {
        $salutationRoute = $this->createMock(AbstractSalutationRoute::class);
        $salutationRoute->expects(static::once())
            ->method('load')
            ->willReturn(new SalutationRouteResponse(
                new EntitySearchResult(
                    SalutationDefinition::ENTITY_NAME,
                    $salutationCollection->count(),
                    $salutationCollection,
                    null,
                    new Criteria(),
                    Context::createDefaultContext()
                )
            ));

        return $salutationRoute;
    }
}
