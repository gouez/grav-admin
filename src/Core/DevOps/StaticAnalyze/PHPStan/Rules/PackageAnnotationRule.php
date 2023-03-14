<?php declare(strict_types=1);

namespace Laser\Core\DevOps\StaticAnalyze\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPUnit\Framework\TestCase;
use Laser\Core\Framework\Log\Package;

/**
 * @implements Rule<InClassNode>
 *
 * @internal
 */
#[Package('core')]
class PackageAnnotationRule implements Rule
{
    /**
     * @internal
     */
    public const PRODUCT_AREA_MAPPING = [
        'business-ops' => [
            '/Laser\\\\.*\\\\(Rule|Flow|ProductStream)\\\\/',
            '/Laser\\\\Core\\\\Framework\\\\(Event)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(Tag)\\\\/',
        ],
        'inventory' => [
            '/Laser\\\\Core\\\\Content\\\\(Product|ProductExport|Property)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(Currency|Unit)\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Product\\\\/',
        ],
        'content' => [
            '/Laser\\\\Core\\\\Content\\\\(Media|Category|Cms|ContactForm|LandingPage)\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Cms\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\LandingPage\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Contact\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Navigation\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Menu\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Footer\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Header\\\\/',
        ],
        'system-settings' => [
            '/Laser\\\\Core\\\\Content\\\\(ImportExport|Mail)\\\\/',
            '/Laser\\\\Core\\\\Framework\\\\(Update)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(Country|CustomField|Integration|Language|Locale|Snippet|User)\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Country\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Suggest\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Search\\\\/',
        ],
        'sales-channel' => [
            '/Laser\\\\Core\\\\Content\\\\(MailTemplate|Seo|Sitemap)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(SalesChannel)\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Sitemap\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Captcha\\\\/',
        ],
        'customer-order' => [
            '/Laser\\\\Core\\\\Content\\\\(Newsletter)\\\\/',
            '/Laser\\\\Core\\\\Checkout\\\\(Customer|Document|Order)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(DeliveryTime|Salutation|Tax)\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Newsletter\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Newsletter\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Maintenance\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Address\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Account\\\\/',
        ],
        'checkout' => [
            '/Laser\\\\Core\\\\Checkout\\\\(Cart|Payment|Promotion|Shipping)\\\\/',
            '/Laser\\\\Core\\\\System\\\\(DeliveryTime|NumberRange|StateMachine)\\\\/',
            '/Laser\\\\Storefront\\\\Checkout\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Wishlist\\\\/',
            '/Laser\\\\Storefront\\\\Pagelet\\\\Wishlist\\\\/',
            '/Laser\\\\Storefront\\\\Page\\\\Checkout\\\\/',
        ],
        'merchant-services' => [
            '/Laser\\\\Core\\\\Framework\\\\Store\\\\/',
        ],
        'storefront' => [
            '/Laser\\\\Storefront\\\\Theme\\\\/',
            '/Laser\\\\Storefront\\\\Controller\\\\/',
            '/Laser\\\\Storefront\\\\(DependencyInjection|Migration|Event|Exception|Framework|Test)\\\\/',
        ],
        'core' => [
            '/Laser\\\\Core\\\\Framework\\\\(Adapter|Api|App|Changelog|DataAbstractionLayer|Demodata|DependencyInjection)\\\\/',
            '/Laser\\\\Core\\\\Framework\\\\(Increment|Log|MessageQueue|Migration|Parameter|Plugin|RateLimiter|Script|Routing|Struct|Util|Uuid|Validation|Webhook)\\\\/',
            '/Laser\\\\Core\\\\DevOps\\\\/',
            '/Laser\\\\Core\\\\Installer\\\\/',
            '/Laser\\\\Core\\\\Maintenance\\\\/',
            '/Laser\\\\Core\\\\Migration\\\\/',
            '/Laser\\\\Core\\\\Profiling\\\\/',
            '/Laser\\\\Elasticsearch\\\\/',
            '/Laser\\\\Docs\\\\/',
            '/Laser\\\\Core\\\\System\\\\(Annotation|CustomEntity|DependencyInjection|SystemConfig)\\\\/',
            '/Laser\\\\.*\\\\(DataAbstractionLayer)\\\\/',
        ],
        'administration' => [
            '/Laser\\\\Administration\\\\/',
        ],
    ];

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     *
     * @return array<array-key, RuleError|string>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->isTestClass($node)) {
            return [];
        }

        $area = $this->getProductArea($node);

        if ($this->hasPackageAnnotation($node)) {
            return [];
        }

        return [sprintf('This class is missing the "@package" annotation (recommendation: %s)', $area ?? 'unknown')];
    }

    private function getProductArea(InClassNode $node): ?string
    {
        $namespace = $node->getClassReflection()->getName();

        foreach (self::PRODUCT_AREA_MAPPING as $area => $regexes) {
            foreach ($regexes as $regex) {
                if (preg_match($regex, $namespace)) {
                    return $area;
                }
            }
        }

        return null;
    }

    private function hasPackageAnnotation(InClassNode $class): bool
    {
        foreach ($class->getOriginalNode()->attrGroups as $group) {
            $attribute = $group->attrs[0];

            /** @var Node\Name\FullyQualified $name */
            $name = $attribute->name;

            if ($name->toString() === Package::class) {
                return true;
            }
        }

        return false;
    }

    private function isTestClass(InClassNode $node): bool
    {
        $namespace = $node->getClassReflection()->getName();

        if (\str_contains($namespace, '\\Tests\\') || \str_contains($namespace, '\\Test\\')) {
            return true;
        }

        $file = (string) $node->getClassReflection()->getFileName();
        if (\str_contains($file, '/tests/') || \str_contains($file, '/Tests/') || \str_contains($file, '/Test/')) {
            return true;
        }

        if ($node->getClassReflection()->getParentClass() === null) {
            return false;
        }

        return $node->getClassReflection()->getParentClass()->getName() === TestCase::class;
    }
}
