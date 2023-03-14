<?php declare(strict_types=1);

namespace Laser\Core\Framework\Api\Response;

use Laser\Core\Framework\Log\Package;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Package('core')]
class JsonApiResponse extends JsonResponse
{
    public function update(): static
    {
        parent::update();

        $this->headers->set('Content-Type', 'application/vnd.api+json');

        return $this;
    }
}
