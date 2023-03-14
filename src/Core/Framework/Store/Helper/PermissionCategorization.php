<?php declare(strict_types=1);

namespace Laser\Core\Framework\Store\Helper;

use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroupRegistrationSalesChannel\CustomerGroupRegistrationSalesChannelDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroupTranslation\CustomerGroupTranslationDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerRecovery\CustomerRecoveryDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerTag\CustomerTagDefinition;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Document\Aggregate\DocumentBaseConfig\DocumentBaseConfigDefinition;
use Laser\Core\Checkout\Document\Aggregate\DocumentBaseConfigSalesChannel\DocumentBaseConfigSalesChannelDefinition;
use Laser\Core\Checkout\Document\Aggregate\DocumentType\DocumentTypeDefinition;
use Laser\Core\Checkout\Document\Aggregate\DocumentTypeTranslation\DocumentTypeTranslationDefinition;
use Laser\Core\Checkout\Document\DocumentDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderDeliveryPosition\OrderDeliveryPositionDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTag\OrderTagDefinition;
use Laser\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionDefinition;
use Laser\Core\Checkout\Order\OrderDefinition;
use Laser\Core\Checkout\Payment\Aggregate\PaymentMethodTranslation\PaymentMethodTranslationDefinition;
use Laser\Core\Checkout\Payment\PaymentMethodDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionCartRule\PromotionCartRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscount\PromotionDiscountDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscountPrice\PromotionDiscountPriceDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionDiscountRule\PromotionDiscountRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionIndividualCode\PromotionIndividualCodeDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionOrderRule\PromotionOrderRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionPersonaCustomer\PromotionPersonaCustomerDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionPersonaRule\PromotionPersonaRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionSalesChannel\PromotionSalesChannelDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionSetGroup\PromotionSetGroupDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionSetGroupRule\PromotionSetGroupRuleDefinition;
use Laser\Core\Checkout\Promotion\Aggregate\PromotionTranslation\PromotionTranslationDefinition;
use Laser\Core\Checkout\Promotion\PromotionDefinition;
use Laser\Core\Content\Category\Aggregate\CategoryTag\CategoryTagDefinition;
use Laser\Core\Content\Category\Aggregate\CategoryTranslation\CategoryTranslationDefinition;
use Laser\Core\Content\Category\CategoryDefinition;
use Laser\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockDefinition;
use Laser\Core\Content\Cms\Aggregate\CmsPageTranslation\CmsPageTranslationDefinition;
use Laser\Core\Content\Cms\Aggregate\CmsSection\CmsSectionDefinition;
use Laser\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotDefinition;
use Laser\Core\Content\Cms\Aggregate\CmsSlotTranslation\CmsSlotTranslationDefinition;
use Laser\Core\Content\Cms\CmsPageDefinition;
use Laser\Core\Content\ImportExport\Aggregate\ImportExportFile\ImportExportFileDefinition;
use Laser\Core\Content\ImportExport\Aggregate\ImportExportLog\ImportExportLogDefinition;
use Laser\Core\Content\ImportExport\ImportExportProfileDefinition;
use Laser\Core\Content\ImportExport\ImportExportProfileTranslationDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailHeaderFooter\MailHeaderFooterDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailHeaderFooterTranslation\MailHeaderFooterTranslationDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailTemplateMedia\MailTemplateMediaDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailTemplateTranslation\MailTemplateTranslationDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeDefinition;
use Laser\Core\Content\MailTemplate\Aggregate\MailTemplateTypeTranslation\MailTemplateTypeTranslationDefinition;
use Laser\Core\Content\MailTemplate\MailTemplateDefinition;
use Laser\Core\Content\Media\Aggregate\MediaDefaultFolder\MediaDefaultFolderDefinition;
use Laser\Core\Content\Media\Aggregate\MediaFolder\MediaFolderDefinition;
use Laser\Core\Content\Media\Aggregate\MediaFolderConfiguration\MediaFolderConfigurationDefinition;
use Laser\Core\Content\Media\Aggregate\MediaFolderConfigurationMediaThumbnailSize\MediaFolderConfigurationMediaThumbnailSizeDefinition;
use Laser\Core\Content\Media\Aggregate\MediaTag\MediaTagDefinition;
use Laser\Core\Content\Media\Aggregate\MediaThumbnail\MediaThumbnailDefinition;
use Laser\Core\Content\Media\Aggregate\MediaThumbnailSize\MediaThumbnailSizeDefinition;
use Laser\Core\Content\Media\Aggregate\MediaTranslation\MediaTranslationDefinition;
use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientDefinition;
use Laser\Core\Content\Newsletter\Aggregate\NewsletterRecipientTag\NewsletterRecipientTagDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCategory\ProductCategoryDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCategoryTree\ProductCategoryTreeDefinition;
use Laser\Core\Content\Product\Aggregate\ProductConfiguratorSetting\ProductConfiguratorSettingDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCrossSellingAssignedProducts\ProductCrossSellingAssignedProductsDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCrossSellingTranslation\ProductCrossSellingTranslationDefinition;
use Laser\Core\Content\Product\Aggregate\ProductCustomFieldSet\ProductCustomFieldSetDefinition;
use Laser\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetDefinition;
use Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation\ProductFeatureSetTranslationDefinition;
use Laser\Core\Content\Product\Aggregate\ProductKeywordDictionary\ProductKeywordDictionaryDefinition;
use Laser\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Laser\Core\Content\Product\Aggregate\ProductManufacturerTranslation\ProductManufacturerTranslationDefinition;
use Laser\Core\Content\Product\Aggregate\ProductMedia\ProductMediaDefinition;
use Laser\Core\Content\Product\Aggregate\ProductOption\ProductOptionDefinition;
use Laser\Core\Content\Product\Aggregate\ProductPrice\ProductPriceDefinition;
use Laser\Core\Content\Product\Aggregate\ProductProperty\ProductPropertyDefinition;
use Laser\Core\Content\Product\Aggregate\ProductReview\ProductReviewDefinition;
use Laser\Core\Content\Product\Aggregate\ProductSearchKeyword\ProductSearchKeywordDefinition;
use Laser\Core\Content\Product\Aggregate\ProductTag\ProductTagDefinition;
use Laser\Core\Content\Product\Aggregate\ProductTranslation\ProductTranslationDefinition;
use Laser\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Product\SalesChannel\Sorting\ProductSortingDefinition;
use Laser\Core\Content\ProductExport\ProductExportDefinition;
use Laser\Core\Content\ProductStream\Aggregate\ProductStreamFilter\ProductStreamFilterDefinition;
use Laser\Core\Content\ProductStream\Aggregate\ProductStreamTranslation\ProductStreamTranslationDefinition;
use Laser\Core\Content\ProductStream\ProductStreamDefinition;
use Laser\Core\Content\Rule\Aggregate\RuleCondition\RuleConditionDefinition;
use Laser\Core\Content\Rule\RuleDefinition;
use Laser\Core\Content\Seo\SeoUrl\SeoUrlDefinition;
use Laser\Core\Content\Seo\SeoUrlTemplate\SeoUrlTemplateDefinition;
use Laser\Core\Framework\App\Aggregate\ActionButton\ActionButtonDefinition;
use Laser\Core\Framework\App\Aggregate\ActionButtonTranslation\ActionButtonTranslationDefinition;
use Laser\Core\Framework\App\Aggregate\AppTranslation\AppTranslationDefinition;
use Laser\Core\Framework\App\AppDefinition;
use Laser\Core\Framework\App\Template\TemplateDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Version\VersionDefinition;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Country\Aggregate\CountryState\CountryStateDefinition;
use Laser\Core\System\Country\CountryDefinition;
use Laser\Core\System\Currency\CurrencyDefinition;
use Laser\Core\System\CustomField\Aggregate\CustomFieldSet\CustomFieldSetDefinition;
use Laser\Core\System\CustomField\Aggregate\CustomFieldSetRelation\CustomFieldSetRelationDefinition;
use Laser\Core\System\CustomField\CustomFieldDefinition;
use Laser\Core\System\DeliveryTime\DeliveryTimeDefinition;
use Laser\Core\System\Integration\IntegrationDefinition;
use Laser\Core\System\Language\LanguageDefinition;
use Laser\Core\System\Locale\Aggregate\LocaleTranslation\LocaleTranslationDefinition;
use Laser\Core\System\Locale\LocaleDefinition;
use Laser\Core\System\NumberRange\Aggregate\NumberRangeSalesChannel\NumberRangeSalesChannelDefinition;
use Laser\Core\System\NumberRange\Aggregate\NumberRangeState\NumberRangeStateDefinition;
use Laser\Core\System\NumberRange\Aggregate\NumberRangeType\NumberRangeTypeDefinition;
use Laser\Core\System\NumberRange\NumberRangeDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelAnalytics\SalesChannelAnalyticsDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelCountry\SalesChannelCountryDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelCurrency\SalesChannelCurrencyDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelLanguage\SalesChannelLanguageDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelPaymentMethod\SalesChannelPaymentMethodDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelShippingMethod\SalesChannelShippingMethodDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelTranslation\SalesChannelTranslationDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelType\SalesChannelTypeDefinition;
use Laser\Core\System\SalesChannel\Aggregate\SalesChannelTypeTranslation\SalesChannelTypeTranslationDefinition;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;
use Laser\Core\System\Salutation\Aggregate\SalutationTranslation\SalutationTranslationDefinition;
use Laser\Core\System\Salutation\SalutationDefinition;
use Laser\Core\System\StateMachine\Aggregation\StateMachineHistory\StateMachineHistoryDefinition;
use Laser\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateDefinition;
use Laser\Core\System\StateMachine\Aggregation\StateMachineState\StateMachineStateTranslationDefinition;
use Laser\Core\System\StateMachine\Aggregation\StateMachineTransition\StateMachineTransitionDefinition;
use Laser\Core\System\StateMachine\StateMachineDefinition;
use Laser\Core\System\StateMachine\StateMachineTranslationDefinition;
use Laser\Core\System\SystemConfig\SystemConfigDefinition;
use Laser\Core\System\Tag\TagDefinition;
use Laser\Core\System\Tax\Aggregate\TaxRule\TaxRuleDefinition;
use Laser\Core\System\Tax\Aggregate\TaxRuleType\TaxRuleTypeDefinition;
use Laser\Core\System\Tax\TaxDefinition;
use Laser\Core\System\Unit\UnitDefinition;
use Laser\Core\System\User\Aggregate\UserAccessKey\UserAccessKeyDefinition;
use Laser\Core\System\User\Aggregate\UserRecovery\UserRecoveryDefinition;
use Laser\Core\System\User\UserDefinition;

/**
 * @internal
 */
#[Package('merchant-services')]
class PermissionCategorization
{
    private const CATEGORY_APP = 'app';
    private const CATEGORY_ADMIN_USER = 'admin_user';
    private const CATEGORY_CATEGORY = 'category';
    private const CATEGORY_CMS = 'cms';
    private const CATEGORY_CUSTOMER = 'customer';
    private const CATEGORY_CUSTOM_FIELDS = 'custom_fields';
    private const CATEGORY_DOCUMENTS = 'documents';
    private const CATEGORY_GOOGLE_SHOPPING = 'google_shopping';
    private const CATEGORY_IMPORT_EXPORT = 'import_export';
    private const CATEGORY_MAIL_TEMPLATES = 'mail_templates';
    private const CATEGORY_MEDIA = 'media';
    private const CATEGORY_NEWSLETTER = 'newsletter';
    private const CATEGORY_ORDER = 'order';
    private const CATEGORY_OTHER = 'other';
    private const CATEGORY_PAYMENT = 'payment';
    private const CATEGORY_PRODUCT = 'product';
    private const CATEGORY_PROMOTION = 'promotion';
    private const CATEGORY_RULES = 'rules';
    private const CATEGORY_SALES_CHANNEL = 'sales_channel';
    private const CATEGORY_SETTINGS = 'settings';
    private const CATEGORY_SOCIAL_SHOPPING = 'social_shopping';
    private const CATEGORY_TAG = 'tag';
    private const CATEGORY_THEME = 'theme';
    private const CATEGORY_ADDITIONAL_PRIVILEGES = 'additional_privileges';

    /**
     * @see \Laser\Storefront\Theme\ThemeDefinition::ENTITY_NAME
     */
    private const THEME_ENTITY_NAME = 'theme';
    /**
     * @see \Laser\Storefront\Theme\Aggregate\ThemeTranslationDefinition::ENTITY_NAME
     */
    private const THEME_TRANSLATION_ENTITY_NAME = 'theme_translation';
    /**
     * @see \Laser\Storefront\Theme\Aggregate\ThemeMediaDefinition::ENTITY_NAME
     */
    private const THEME_MEDIA_ENTITY_NAME = 'theme_media';
    /**
     * @see \Laser\Storefront\Theme\Aggregate\ThemeSalesChannelDefinition::ENTITY_NAME
     */
    private const THEME_SALES_CHANNEL_ENTITY_NAME = 'theme_sales_channel';

    private const PERMISSION_CATEGORIES = [
        self::CATEGORY_ADMIN_USER => [
            IntegrationDefinition::ENTITY_NAME,
            UserDefinition::ENTITY_NAME,
            UserAccessKeyDefinition::ENTITY_NAME,
            UserRecoveryDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_APP => [
            TemplateDefinition::ENTITY_NAME,
            AppDefinition::ENTITY_NAME,
            AppTranslationDefinition::ENTITY_NAME,
            ActionButtonDefinition::ENTITY_NAME,
            ActionButtonTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_CATEGORY => [
            CategoryDefinition::ENTITY_NAME,
            CategoryTranslationDefinition::ENTITY_NAME,
            CategoryTagDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_CMS => [
            CmsBlockDefinition::ENTITY_NAME,
            CmsPageDefinition::ENTITY_NAME,
            CmsPageTranslationDefinition::ENTITY_NAME,
            CmsSectionDefinition::ENTITY_NAME,
            CmsSlotDefinition::ENTITY_NAME,
            CmsSlotTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_CUSTOMER => [
            CustomerDefinition::ENTITY_NAME,
            CustomerAddressDefinition::ENTITY_NAME,
            CustomerGroupDefinition::ENTITY_NAME,
            CustomerGroupTranslationDefinition::ENTITY_NAME,
            CustomerGroupRegistrationSalesChannelDefinition::ENTITY_NAME,
            CustomerRecoveryDefinition::ENTITY_NAME,
            CustomerTagDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_CUSTOM_FIELDS => [
            CustomFieldDefinition::ENTITY_NAME,
            CustomFieldSetDefinition::ENTITY_NAME,
            CustomFieldSetRelationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_DOCUMENTS => [
            DocumentDefinition::ENTITY_NAME,
            DocumentBaseConfigDefinition::ENTITY_NAME,
            DocumentBaseConfigSalesChannelDefinition::ENTITY_NAME,
            DocumentTypeDefinition::ENTITY_NAME,
            DocumentTypeTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_GOOGLE_SHOPPING => [
            'swag_google_shopping_account',
            'swag_google_shopping_ads_account',
            'swag_google_shopping_list_ads_account',
            'swag_google_shopping_category',
            'swag_google_shopping_merchant_account',
        ],
        self::CATEGORY_IMPORT_EXPORT => [
            ImportExportFileDefinition::ENTITY_NAME,
            ImportExportLogDefinition::ENTITY_NAME,
            ImportExportProfileDefinition::ENTITY_NAME,
            ImportExportProfileTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_MAIL_TEMPLATES => [
            MailHeaderFooterDefinition::ENTITY_NAME,
            MailHeaderFooterTranslationDefinition::ENTITY_NAME,
            MailTemplateDefinition::ENTITY_NAME,
            MailTemplateTranslationDefinition::ENTITY_NAME,
            MailTemplateMediaDefinition::ENTITY_NAME,
            MailTemplateTypeDefinition::ENTITY_NAME,
            MailTemplateTypeTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_MEDIA => [
            MediaDefinition::ENTITY_NAME,
            MediaTranslationDefinition::ENTITY_NAME,
            MediaDefaultFolderDefinition::ENTITY_NAME,
            MediaFolderDefinition::ENTITY_NAME,
            MediaFolderConfigurationDefinition::ENTITY_NAME,
            MediaFolderConfigurationMediaThumbnailSizeDefinition::ENTITY_NAME,
            MediaTagDefinition::ENTITY_NAME,
            MediaThumbnailDefinition::ENTITY_NAME,
            MediaThumbnailSizeDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_NEWSLETTER => [
            NewsletterRecipientDefinition::ENTITY_NAME,
            NewsletterRecipientTagDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_ORDER => [
            OrderDefinition::ENTITY_NAME,
            OrderAddressDefinition::ENTITY_NAME,
            OrderCustomerDefinition::ENTITY_NAME,
            OrderDeliveryDefinition::ENTITY_NAME,
            OrderDeliveryPositionDefinition::ENTITY_NAME,
            OrderLineItemDefinition::ENTITY_NAME,
            OrderTagDefinition::ENTITY_NAME,
            OrderTransactionDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_PAYMENT => [
            PaymentMethodDefinition::ENTITY_NAME,
            PaymentMethodTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_PRODUCT => [
            ProductDefinition::ENTITY_NAME,
            ProductCategoryDefinition::ENTITY_NAME,
            ProductCategoryTreeDefinition::ENTITY_NAME,
            ProductConfiguratorSettingDefinition::ENTITY_NAME,
            ProductCrossSellingDefinition::ENTITY_NAME,
            ProductCrossSellingAssignedProductsDefinition::ENTITY_NAME,
            ProductCrossSellingTranslationDefinition::ENTITY_NAME,
            ProductExportDefinition::ENTITY_NAME,
            ProductKeywordDictionaryDefinition::ENTITY_NAME,
            ProductManufacturerDefinition::ENTITY_NAME,
            ProductManufacturerTranslationDefinition::ENTITY_NAME,
            ProductMediaDefinition::ENTITY_NAME,
            ProductOptionDefinition::ENTITY_NAME,
            ProductPriceDefinition::ENTITY_NAME,
            ProductPropertyDefinition::ENTITY_NAME,
            ProductReviewDefinition::ENTITY_NAME,
            ProductSearchKeywordDefinition::ENTITY_NAME,
            ProductStreamDefinition::ENTITY_NAME,
            ProductStreamFilterDefinition::ENTITY_NAME,
            ProductStreamTranslationDefinition::ENTITY_NAME,
            ProductTagDefinition::ENTITY_NAME,
            ProductVisibilityDefinition::ENTITY_NAME,
            ProductSortingDefinition::ENTITY_NAME,
            ProductTranslationDefinition::ENTITY_NAME,
            ProductFeatureSetDefinition::ENTITY_NAME,
            ProductFeatureSetTranslationDefinition::ENTITY_NAME,
            ProductCustomFieldSetDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_PROMOTION => [
            PromotionDefinition::ENTITY_NAME,
            PromotionTranslationDefinition::ENTITY_NAME,
            PromotionCartRuleDefinition::ENTITY_NAME,
            PromotionDiscountDefinition::ENTITY_NAME,
            PromotionDiscountPriceDefinition::ENTITY_NAME,
            PromotionDiscountRuleDefinition::ENTITY_NAME,
            PromotionIndividualCodeDefinition::ENTITY_NAME,
            PromotionOrderRuleDefinition::ENTITY_NAME,
            PromotionPersonaCustomerDefinition::ENTITY_NAME,
            PromotionPersonaRuleDefinition::ENTITY_NAME,
            PromotionSalesChannelDefinition::ENTITY_NAME,
            PromotionSetGroupDefinition::ENTITY_NAME,
            PromotionSetGroupRuleDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_RULES => [
            RuleDefinition::ENTITY_NAME,
            RuleConditionDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_SALES_CHANNEL => [
            SalesChannelDefinition::ENTITY_NAME,
            SalesChannelAnalyticsDefinition::ENTITY_NAME,
            SalesChannelCountryDefinition::ENTITY_NAME,
            SalesChannelCurrencyDefinition::ENTITY_NAME,
            SalesChannelDomainDefinition::ENTITY_NAME,
            SalesChannelLanguageDefinition::ENTITY_NAME,
            SalesChannelPaymentMethodDefinition::ENTITY_NAME,
            SalesChannelShippingMethodDefinition::ENTITY_NAME,
            SalesChannelTranslationDefinition::ENTITY_NAME,
            SalesChannelTypeDefinition::ENTITY_NAME,
            SalesChannelTypeTranslationDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_SETTINGS => [
            CountryDefinition::ENTITY_NAME,
            CountryStateDefinition::ENTITY_NAME,
            CurrencyDefinition::ENTITY_NAME,
            DeliveryTimeDefinition::ENTITY_NAME,
            LanguageDefinition::ENTITY_NAME,
            LocaleDefinition::ENTITY_NAME,
            LocaleTranslationDefinition::ENTITY_NAME,
            NumberRangeDefinition::ENTITY_NAME,
            NumberRangeSalesChannelDefinition::ENTITY_NAME,
            NumberRangeStateDefinition::ENTITY_NAME,
            NumberRangeTypeDefinition::ENTITY_NAME,
            SalutationDefinition::ENTITY_NAME,
            SalutationTranslationDefinition::ENTITY_NAME,
            SeoUrlDefinition::ENTITY_NAME,
            SeoUrlTemplateDefinition::ENTITY_NAME,
            StateMachineDefinition::ENTITY_NAME,
            StateMachineHistoryDefinition::ENTITY_NAME,
            StateMachineStateDefinition::ENTITY_NAME,
            StateMachineStateTranslationDefinition::ENTITY_NAME,
            StateMachineTransitionDefinition::ENTITY_NAME,
            StateMachineTranslationDefinition::ENTITY_NAME,
            SystemConfigDefinition::ENTITY_NAME,
            TaxDefinition::ENTITY_NAME,
            TaxRuleDefinition::ENTITY_NAME,
            TaxRuleTypeDefinition::ENTITY_NAME,
            UnitDefinition::ENTITY_NAME,
            VersionDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_SOCIAL_SHOPPING => [
            'swag_social_shopping_sales_channel',
            'swag_social_shopping_product_error',
        ],
        self::CATEGORY_TAG => [
            TagDefinition::ENTITY_NAME,
        ],
        self::CATEGORY_THEME => [
            self::THEME_ENTITY_NAME,
            self::THEME_TRANSLATION_ENTITY_NAME,
            self::THEME_MEDIA_ENTITY_NAME,
            self::THEME_SALES_CHANNEL_ENTITY_NAME,
        ],
        self::CATEGORY_ADDITIONAL_PRIVILEGES => [
            'additional_privileges',
        ],
    ];

    public static function isInCategory(string $entity, string $category): bool
    {
        if ($category === self::CATEGORY_OTHER) {
            $allCategories = array_merge(...array_values(self::PERMISSION_CATEGORIES));

            return !\in_array($entity, $allCategories, true);
        }

        return \in_array($entity, self::PERMISSION_CATEGORIES[$category], true);
    }

    /**
     * @return string[]
     */
    public static function getCategoryNames(): array
    {
        $categories = array_keys(self::PERMISSION_CATEGORIES);
        $categories[] = self::CATEGORY_OTHER;

        return $categories;
    }
}
