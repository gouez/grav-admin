<?php declare(strict_types=1);

namespace Laser\Core\Framework\Script\Execution\Awareness;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Script\Api\ScriptResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[Package('core')]
trait ScriptResponseAwareTrait
{
    /**
     * @internal
     */
    protected ?ScriptResponse $scriptResponse = null;

    /**
     * @internal
     */
    public function getScriptResponse(): ScriptResponse
    {
        if (!$this->scriptResponse) {
            $this->scriptResponse = new ScriptResponse(null, Response::HTTP_NO_CONTENT);
        }

        return $this->scriptResponse;
    }

    public function setResponse(ScriptResponse $scriptResponse): void
    {
        $this->scriptResponse = $scriptResponse;
    }
}
