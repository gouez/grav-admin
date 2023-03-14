<?php declare(strict_types=1);

namespace Laser\Core\Content\Seo\Hreflang;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('sales-channel')]
class HreflangStruct extends Struct
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $locale;

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getApiAlias(): string
    {
        return 'seo_hreflang';
    }
}
