<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\Rule\Api;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Laser\Core\Checkout\Customer\Rule\CustomerGroupRule;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Rule\RuleConfig;
use Laser\Core\Framework\Test\TestCaseBase\AdminFunctionalTestBehaviour;

/**
 * @internal
 */
#[Package('business-ops')]
class RuleConfigControllerTest extends TestCase
{
    use AdminFunctionalTestBehaviour;

    public function testGetConditionsConfig(): void
    {
        $this->getBrowser()->request(
            'GET',
            '/api/_info/rule-config'
        );
        $response = $this->getBrowser()->getResponse();

        static::assertEquals(200, $this->getBrowser()->getResponse()->getStatusCode());

        $content = json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);

        $customerGroupRuleName = (new CustomerGroupRule())->getName();
        static::assertArrayHasKey($customerGroupRuleName, $content);

        $customerGroupRouleConfig = $content[$customerGroupRuleName];

        static::assertCount(2, $customerGroupRouleConfig['operatorSet']['operators']);
        static::assertEquals(RuleConfig::OPERATOR_SET_STRING, $customerGroupRouleConfig['operatorSet']['operators']);
        static::assertTrue($customerGroupRouleConfig['operatorSet']['isMatchAny']);

        static::assertCount(1, $customerGroupRouleConfig['fields']);

        static::assertEquals('customerGroupIds', $customerGroupRouleConfig['fields'][0]['name']);
        static::assertEquals('multi-entity-id-select', $customerGroupRouleConfig['fields'][0]['type']);
        static::assertEquals(CustomerGroupDefinition::ENTITY_NAME, $customerGroupRouleConfig['fields'][0]['config']['entity']);
    }
}
