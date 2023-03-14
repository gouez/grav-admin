<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Test\Validation;

use PHPUnit\Framework\TestCase;
use Laser\Core\Checkout\Customer\Validation\Constraint\CustomerVatIdentification;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Laser\Core\Framework\Test\TestCaseBase\SalesChannelApiTestBehaviour;
use Laser\Core\Framework\Validation\DataValidationDefinition;
use Laser\Core\Framework\Validation\DataValidator;
use Laser\Core\Framework\Validation\Exception\ConstraintViolationException;

/**
 * @internal
 */
#[Package('checkout')]
class CustomerVatIdentificationValidatorTest extends TestCase
{
    use IntegrationTestBehaviour;
    use SalesChannelApiTestBehaviour;

    public function testValidateVatIds(): void
    {
        $vatIds = [
            '123546',
        ];

        $constraint = new CustomerVatIdentification([
            'countryId' => $this->getValidCountryId(),
        ]);

        $validation = new DataValidationDefinition('customer.create');

        $validation
            ->add('vatIds', $constraint);

        $validator = $this->getContainer()->get(DataValidator::class);

        try {
            static::assertEmpty($validator->validate([
                'vatIds' => $vatIds,
            ], $validation));
        } catch (\Throwable $exception) {
            static::assertInstanceOf(ConstraintViolationException::class, $exception);
            $violations = $exception->getViolations();
            $violation = $violations->get(1);

            static::assertNotEmpty($violation);
            static::assertEquals($constraint->message, $violation->getMessageTemplate());
        }
    }
}
