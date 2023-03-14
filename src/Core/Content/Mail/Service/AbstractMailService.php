<?php declare(strict_types=1);

namespace Laser\Core\Content\Mail\Service;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Mime\Email;

#[Package('system-settings')]
abstract class AbstractMailService
{
    abstract public function getDecorated(): AbstractMailService;

    abstract public function send(array $data, Context $context, array $templateData = []): ?Email;
}
