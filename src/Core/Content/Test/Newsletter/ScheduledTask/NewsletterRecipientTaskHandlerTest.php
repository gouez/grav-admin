<?php declare(strict_types=1);

namespace Laser\Core\Content\Test\Newsletter\ScheduledTask;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Laser\Core\Content\Newsletter\ScheduledTask\NewsletterRecipientTaskHandler;
use Laser\Core\Defaults;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseHelper\ReflectionHelper;

/**
 * @internal
 */
#[Package('customer-order')]
class NewsletterRecipientTaskHandlerTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGetExpiredNewsletterRecipientCriteria(): void
    {
        $taskHandler = $this->getTaskHandler();
        $method = ReflectionHelper::getMethod(NewsletterRecipientTaskHandler::class, 'getExpiredNewsletterRecipientCriteria');

        /** @var Criteria $criteria */
        $criteria = $method->invoke($taskHandler);

        $filters = $criteria->getFilters();
        $dateFilter = array_shift($filters);
        $equalsFilter = array_shift($filters);

        static::assertInstanceOf(RangeFilter::class, $dateFilter);
        static::assertInstanceOf(EqualsFilter::class, $equalsFilter);

        static::assertSame('createdAt', $dateFilter->getField());
        static::assertNotEmpty($dateFilter->getParameter(RangeFilter::LTE));

        static::assertSame('status', $equalsFilter->getField());
        static::assertSame('notSet', $equalsFilter->getValue());
    }

    public function testRun(): void
    {
        $this->installTestData();

        $taskHandler = $this->getTaskHandler();
        $taskHandler->run();

        /** @var EntityRepository $repository */
        $repository = $this->getContainer()->get('newsletter_recipient.repository');
        $result = $repository->searchIds(new Criteria(), Context::createDefaultContext());

        $expectedResult = [
            '7912f4de72aa43d792bcebae4eb45c5c',
            'b4b45f58088d41289490db956ca19af7',
        ];

        foreach ($expectedResult as $id) {
            static::assertContains($id, array_keys($result->getData()), print_r(array_keys($result->getData()), true));
        }
    }

    private function installTestData(): void
    {
        $salutationSql = file_get_contents(__DIR__ . '/../fixtures/salutation.sql');
        static::assertIsString($salutationSql);
        $this->getContainer()->get(Connection::class)->executeStatement($salutationSql);

        $recipientSql = file_get_contents(__DIR__ . '/../fixtures/recipient.sql');
        static::assertIsString($recipientSql);
        $recipientSql = str_replace(':createdAt', (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT), $recipientSql);
        $this->getContainer()->get(Connection::class)->executeStatement($recipientSql);
    }

    private function getTaskHandler(): NewsletterRecipientTaskHandler
    {
        return new NewsletterRecipientTaskHandler(
            $this->getContainer()->get('scheduled_task.repository'),
            $this->getContainer()->get('newsletter_recipient.repository')
        );
    }
}
