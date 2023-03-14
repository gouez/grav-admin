<?php declare(strict_types=1);

namespace Laser\Core\Content\ProductExport\Event;

use Monolog\Level;
use Laser\Core\Content\Flow\Dispatching\Aware\NameAware;
use Laser\Core\Content\MailTemplate\Exception\MailEventConfigurationException;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\EventData\ScalarValueType;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Log\LogAware;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('sales-channel')]
class ProductExportLoggingEvent extends Event implements LogAware, MailAware, NameAware
{
    final public const NAME = 'product_export.log';

    /**
     * @deprecated tag:v6.6.0 - Property $logLevel will no longer allow integer values
     *
     * @var value-of<Level::VALUES>|Level
     */
    private readonly int|Level $logLevel;

    /**
     * Do not remove initialization, even though the property is set in the constructor.
     * The property is accessed via reflection in some places and is therefore needing a value.
     */
    private string $name = self::NAME;

    /**
     * @internal
     *
     * @deprecated tag:v6.6.0 - Parameter $logLevel will no longer allow integer values
     *
     * @param value-of<Level::VALUES>|Level|null $logLevel
     */
    public function __construct(
        private readonly Context $context,
        ?string $name,
        int|Level|null $logLevel,
        private readonly ?\Throwable $throwable = null
    ) {
        $this->name = $name ?? self::NAME;
        $this->logLevel = $logLevel ?? Level::Debug;
    }

    public function getThrowable(): ?\Throwable
    {
        return $this->throwable;
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * @deprecated tag:v6.6.0 - reason:return-type-change - Return type will change to @see \Monolog\Level
     */
    public function getLogLevel(): int
    {
        if (\is_int($this->logLevel)) {
            return $this->logLevel;
        }

        return $this->logLevel->value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLogData(): array
    {
        $logData = [];

        if ($this->getThrowable()) {
            $throwable = $this->getThrowable();
            $logData['exception'] = (string) $throwable;
        }

        return $logData;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('name', new ScalarValueType(ScalarValueType::TYPE_STRING));
    }

    public function getMailStruct(): MailRecipientStruct
    {
        throw new MailEventConfigurationException('Data for mailRecipientStruct not available.', self::class);
    }

    public function getSalesChannelId(): ?string
    {
        return null;
    }
}
