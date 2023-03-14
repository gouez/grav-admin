<?php declare(strict_types=1);

namespace Laser\Core\Content\Mail\Service;

use Laser\Core\Framework\Log\Package;
use Symfony\Component\Mime\Email;

#[Package('system-settings')]
abstract class AbstractMailFactory
{
    /**
     * @param array $sender         e.g. ['laser@example.com' => 'Laser AG']
     * @param array $recipients     e.g. ['laser@example.com' => 'Laser AG', 'symfony@example.com' => 'Symfony']
     * @param array $contents       e.g. ['text/plain' => 'Foo', 'text/html' => '&lt;h1&gt;Bar&lt;/h1&gt;']
     * @param array $additionalData e.g. [
     *                              'recipientsCc' => 'laser &lt;laser@example.com&gt;,
     *                              'recipientsBcc' => 'laser@example.com',
     *                              'replyTo' => 'reply@example.com',
     *                              'returnPath' => 'bounce@example.com'
     *                              ]
     */
    abstract public function create(
        string $subject,
        array $sender,
        array $recipients,
        array $contents,
        array $attachments,
        array $additionalData,
        ?array $binAttachments = null
    ): Email;

    abstract public function getDecorated(): AbstractMailFactory;
}
