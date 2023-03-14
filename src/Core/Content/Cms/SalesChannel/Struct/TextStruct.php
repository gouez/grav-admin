<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\SalesChannel\Struct;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('content')]
class TextStruct extends Struct
{
    /**
     * @var string|null
     */
    protected $content;

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getApiAlias(): string
    {
        return 'cms_text';
    }
}
