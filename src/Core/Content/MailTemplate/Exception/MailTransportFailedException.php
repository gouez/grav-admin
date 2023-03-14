<?php declare(strict_types=1);

namespace Laser\Core\Content\MailTemplate\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('sales-channel')]
class MailTransportFailedException extends LaserHttpException
{
    public function __construct(
        array $failedRecipients,
        ?\Throwable $e = null
    ) {
        parent::__construct(
            'Failed sending mail to following recipients: {{ recipients }} with Error: {{ errorMessage }}',
            ['recipients' => $failedRecipients, 'recipientsString' => implode(', ', $failedRecipients), 'errorMessage' => $e ? $e->getMessage() : 'Unknown error'],
            $e
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__MAIL_TRANSPORT_FAILED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
