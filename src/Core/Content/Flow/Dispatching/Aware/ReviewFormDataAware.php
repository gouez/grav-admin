<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Aware;

use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
interface ReviewFormDataAware extends FlowEventAware
{
    public const REVIEW_FORM_DATA = 'reviewFormData';

    /**
     * @return array<string, mixed>
     */
    public function getReviewFormData(): array;
}
