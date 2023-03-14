<?php declare(strict_types=1);

namespace Laser\Core\Content\Media;

use Laser\Core\Content\Media\Event\MediaFolderConfigurationIndexerEvent;
use Laser\Core\Content\Media\Event\MediaFolderIndexerEvent;
use Laser\Core\Content\Media\Event\MediaIndexerEvent;
use Laser\Core\Content\Media\Event\MediaUploadedEvent;
use Laser\Core\Framework\Log\Package;

#[Package('content')]
class MediaEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const MEDIA_WRITTEN_EVENT = 'media.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const MEDIA_DELETED_EVENT = 'media.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const MEDIA_LOADED_EVENT = 'media.loaded';

    /**
     * @Event("Laser\Core\Content\Media\Event\MediaUploadedEvent")
     */
    public const MEDIA_UPDATED_EVENT = MediaUploadedEvent::EVENT_NAME;

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const MEDIA_SEARCH_RESULT_LOADED_EVENT = 'media.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const MEDIA_AGGREGATION_LOADED_EVENT = 'media.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Content\Media\Event\MediaIndexerEvent")
     */
    final public const MEDIA_INDEXER_EVENT = MediaIndexerEvent::class;

    /**
     * @Event("Laser\Core\Content\Media\Event\MediaFolderConfigurationIndexerEvent")
     */
    final public const MEDIA_FOLDER_CONFIGURATION_INDEXER_EVENT = MediaFolderConfigurationIndexerEvent::class;

    /**
     * @Event("Laser\Core\Content\Media\Event\MediaFolderIndexerEvent")
     */
    final public const MEDIA_FOLDER_INDEXER_EVENT = MediaFolderIndexerEvent::class;

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const MEDIA_ID_SEARCH_RESULT_LOADED_EVENT = 'media.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const MEDIA_TRANSLATION_WRITTEN_EVENT = 'media_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const MEDIA_TRANSLATION_DELETED_EVENT = 'media_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const MEDIA_TRANSLATION_LOADED_EVENT = 'media_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const MEDIA_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'media_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const MEDIA_TRANSLATION_AGGREGATION_LOADED_EVENT = 'media_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const MEDIA_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'media_translation.id.search.result.loaded';
}
