<?php declare(strict_types=1);

namespace Laser\Core\System\Tax;

use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class TaxEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const TAX_WRITTEN_EVENT = 'tax.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const TAX_DELETED_EVENT = 'tax.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const TAX_LOADED_EVENT = 'tax.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const TAX_SEARCH_RESULT_LOADED_EVENT = 'tax.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const TAX_AGGREGATION_LOADED_EVENT = 'tax.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const TAX_ID_SEARCH_RESULT_LOADED_EVENT = 'tax.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const TAX_AREA_RULE_WRITTEN_EVENT = 'tax_area_rule.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const TAX_AREA_RULE_DELETED_EVENT = 'tax_area_rule.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const TAX_AREA_RULE_LOADED_EVENT = 'tax_area_rule.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_SEARCH_RESULT_LOADED_EVENT = 'tax_area_rule.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_AGGREGATION_LOADED_EVENT = 'tax_area_rule.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_ID_SEARCH_RESULT_LOADED_EVENT = 'tax_area_rule.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_WRITTEN_EVENT = 'tax_area_rule_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_DELETED_EVENT = 'tax_area_rule_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_LOADED_EVENT = 'tax_area_rule_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'tax_area_rule_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_AGGREGATION_LOADED_EVENT = 'tax_area_rule_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const TAX_AREA_RULE_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'tax_area_rule_translation.id.search.result.loaded';
}
