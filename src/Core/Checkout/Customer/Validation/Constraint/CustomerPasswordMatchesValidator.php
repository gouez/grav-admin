<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Validation\Constraint;

use Laser\Core\Checkout\Customer\Exception\BadCredentialsException;
use Laser\Core\Checkout\Customer\SalesChannel\AccountService;
use Laser\Core\Framework\Log\Package;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

#[Package('customer-order')]
class CustomerPasswordMatchesValidator extends ConstraintValidator
{
    /**
     * @internal
     */
    public function __construct(private readonly AccountService $accountService)
    {
    }

    public function validate(mixed $password, Constraint $constraint): void
    {
        if (!$constraint instanceof CustomerPasswordMatches) {
            return;
        }

        $context = $constraint->getContext();

        try {
            $email = $context->getCustomer()->getEmail();

            $this->accountService->getCustomerByLogin($email, (string) $password, $constraint->getContext());

            return;
        } catch (BadCredentialsException) {
            $this->context->buildViolation($constraint->message)
                ->setCode(CustomerPasswordMatches::CUSTOMER_PASSWORD_NOT_CORRECT)
                ->addViolation();
        }
    }
}
