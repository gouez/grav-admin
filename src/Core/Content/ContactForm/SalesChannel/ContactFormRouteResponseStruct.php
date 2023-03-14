<?php declare(strict_types=1);

namespace Laser\Core\Content\ContactForm\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('content')]
class ContactFormRouteResponseStruct extends Struct
{
    /**
     * @var string
     */
    protected $individualSuccessMessage;

    public function getApiAlias(): string
    {
        return 'contact_form_result';
    }

    public function getIndividualSuccessMessage(): string
    {
        return $this->individualSuccessMessage;
    }
}
