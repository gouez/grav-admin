<?php declare(strict_types=1);

namespace Laser\Core\Content\Media\Cms;

use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Laser\Core\Content\Cms\DataResolver\CriteriaCollection;
use Laser\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Laser\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Laser\Core\Content\Cms\DataResolver\FieldConfig;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Laser\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Laser\Core\Content\Cms\SalesChannel\Struct\ImageStruct;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Media\MediaEntity;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class YoutubeVideoCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'youtube-video';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $mediaConfig = $slot->getFieldConfig()->get('previewMedia');
        if ($mediaConfig === null || $mediaConfig->isMapped() || $mediaConfig->getValue() === null) {
            return null;
        }

        $criteria = new Criteria([$mediaConfig->getStringValue()]);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $image = new ImageStruct();
        $slot->setData($image);

        $mediaConfig = $config->get('previewMedia');
        if ($mediaConfig && $mediaConfig->getValue()) {
            $this->addMediaEntity($slot, $image, $result, $mediaConfig, $resolverContext);
        }
    }

    private function addMediaEntity(CmsSlotEntity $slot, ImageStruct $image, ElementDataCollection $result, FieldConfig $config, ResolverContext $resolverContext): void
    {
        if ($config->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $media = $this->resolveEntityValue($resolverContext->getEntity(), $config->getStringValue());

            if ($media instanceof MediaEntity) {
                $image->setMediaId($media->getUniqueIdentifier());
                $image->setMedia($media);
            }
        }

        if ($config->isStatic()) {
            $image->setMediaId($config->getStringValue());

            $searchResult = $result->get('media_' . $slot->getUniqueIdentifier());
            if (!$searchResult) {
                return;
            }

            $media = $searchResult->get($config->getStringValue());
            if (!$media instanceof MediaEntity) {
                return;
            }

            $image->setMedia($media);
        }
    }
}
