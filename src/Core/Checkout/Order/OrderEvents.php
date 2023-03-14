<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Order;

use Laser\Core\Checkout\Order\Event\OrderPaymentMethodChangedEvent;
use Laser\Core\Framework\Log\Package;

#[Package('customer-order')]
class OrderEvents
{
    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_WRITTEN_EVENT = 'order.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_DELETED_EVENT = 'order.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_LOADED_EVENT = 'order.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_SEARCH_RESULT_LOADED_EVENT = 'order.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_AGGREGATION_LOADED_EVENT = 'order.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_ID_SEARCH_RESULT_LOADED_EVENT = 'order.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_ADDRESS_WRITTEN_EVENT = 'order_address.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_ADDRESS_DELETED_EVENT = 'order_address.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_ADDRESS_LOADED_EVENT = 'order_address.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_ADDRESS_SEARCH_RESULT_LOADED_EVENT = 'order_address.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_ADDRESS_AGGREGATION_LOADED_EVENT = 'order_address.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_ADDRESS_ID_SEARCH_RESULT_LOADED_EVENT = 'order_address.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_DELIVERY_WRITTEN_EVENT = 'order_delivery.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_DELIVERY_DELETED_EVENT = 'order_delivery.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_DELIVERY_LOADED_EVENT = 'order_delivery.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_SEARCH_RESULT_LOADED_EVENT = 'order_delivery.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_AGGREGATION_LOADED_EVENT = 'order_delivery.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_ID_SEARCH_RESULT_LOADED_EVENT = 'order_delivery.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_DELIVERY_POSITION_WRITTEN_EVENT = 'order_delivery_position.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_DELIVERY_POSITION_DELETED_EVENT = 'order_delivery_position.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_DELIVERY_POSITION_LOADED_EVENT = 'order_delivery_position.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_POSITION_SEARCH_RESULT_LOADED_EVENT = 'order_delivery_position.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_POSITION_AGGREGATION_LOADED_EVENT = 'order_delivery_position.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_DELIVERY_POSITION_ID_SEARCH_RESULT_LOADED_EVENT = 'order_delivery_position.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_LINE_ITEM_WRITTEN_EVENT = 'order_line_item.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_LINE_ITEM_DELETED_EVENT = 'order_line_item.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_LINE_ITEM_LOADED_EVENT = 'order_line_item.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_LINE_ITEM_SEARCH_RESULT_LOADED_EVENT = 'order_line_item.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_LINE_ITEM_AGGREGATION_LOADED_EVENT = 'order_line_item.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_LINE_ITEM_ID_SEARCH_RESULT_LOADED_EVENT = 'order_line_item.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_STATE_WRITTEN_EVENT = 'order_state.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_STATE_DELETED_EVENT = 'order_state.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_STATE_LOADED_EVENT = 'order_state.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_STATE_SEARCH_RESULT_LOADED_EVENT = 'order_state.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_STATE_AGGREGATION_LOADED_EVENT = 'order_state.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_STATE_ID_SEARCH_RESULT_LOADED_EVENT = 'order_state.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_STATE_TRANSLATION_WRITTEN_EVENT = 'order_state_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_STATE_TRANSLATION_DELETED_EVENT = 'order_state_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_STATE_TRANSLATION_LOADED_EVENT = 'order_state_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_STATE_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'order_state_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_STATE_TRANSLATION_AGGREGATION_LOADED_EVENT = 'order_state_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_STATE_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'order_state_translation.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_TRANSACTION_WRITTEN_EVENT = 'order_transaction.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_TRANSACTION_DELETED_EVENT = 'order_transaction.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_TRANSACTION_LOADED_EVENT = 'order_transaction.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_SEARCH_RESULT_LOADED_EVENT = 'order_transaction.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_AGGREGATION_LOADED_EVENT = 'order_transaction.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_ID_SEARCH_RESULT_LOADED_EVENT = 'order_transaction.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_TRANSACTION_STATE_WRITTEN_EVENT = 'order_transaction_state.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_DELETED_EVENT = 'order_transaction_state.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_LOADED_EVENT = 'order_transaction_state.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_SEARCH_RESULT_LOADED_EVENT = 'order_transaction_state.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_AGGREGATION_LOADED_EVENT = 'order_transaction_state.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_ID_SEARCH_RESULT_LOADED_EVENT = 'order_transaction_state.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_WRITTEN_EVENT = 'order_transaction_state_translation.written';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_DELETED_EVENT = 'order_transaction_state_translation.deleted';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_LOADED_EVENT = 'order_transaction_state_translation.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntitySearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_SEARCH_RESULT_LOADED_EVENT = 'order_transaction_state_translation.search.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityAggregationResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_AGGREGATION_LOADED_EVENT = 'order_transaction_state_translation.aggregation.result.loaded';

    /**
     * @Event("Laser\Core\Framework\DataAbstractionLayer\Event\EntityIdSearchResultLoadedEvent")
     */
    final public const ORDER_TRANSACTION_STATE_TRANSLATION_ID_SEARCH_RESULT_LOADED_EVENT = 'order_transaction_state_translation.id.search.result.loaded';

    /**
     * @Event("Laser\Core\Checkout\Order\Event\OrderPaymentMethodChangedEvent")
     */
    final public const ORDER_PAYMENT_METHOD_CHANGED = OrderPaymentMethodChangedEvent::EVENT_NAME;
}
