<?php declare(strict_types=1);

namespace Laser\Core\Content\Flow\Dispatching\Storer;

use Laser\Core\Content\Flow\Dispatching\Aware\ReviewFormDataAware;
use Laser\Core\Content\Flow\Dispatching\StorableFlow;
use Laser\Core\Framework\Event\FlowEventAware;
use Laser\Core\Framework\Log\Package;

#[Package('business-ops')]
class ReviewFormDataStorer extends FlowStorer
{
    /**
     * @param array<string, mixed> $stored
     *
     * @return array<string, mixed>
     */
    public function store(FlowEventAware $event, array $stored): array
    {
        if (!$event instanceof ReviewFormDataAware || isset($stored[ReviewFormDataAware::REVIEW_FORM_DATA])) {
            return $stored;
        }

        $stored[ReviewFormDataAware::REVIEW_FORM_DATA] = $event->getReviewFormData();

        return $stored;
    }

    public function restore(StorableFlow $storable): void
    {
        if (!$storable->hasStore(ReviewFormDataAware::REVIEW_FORM_DATA)) {
            return;
        }

        $storable->setData(ReviewFormDataAware::REVIEW_FORM_DATA, $storable->getStore(ReviewFormDataAware::REVIEW_FORM_DATA));
    }
}
