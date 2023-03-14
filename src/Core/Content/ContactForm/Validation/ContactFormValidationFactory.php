<?php declare(strict_types=1);

namespace Laser\Core\Content\ContactForm\Validation;

use Laser\Core\Framework\DataAbstractionLayer\Validation\EntityExists;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Validation\BuildValidationEvent;
use Laser\Core\Framework\Validation\DataBag\DataBag;
use Laser\Core\Framework\Validation\DataValidationDefinition;
use Laser\Core\Framework\Validation\DataValidationFactoryInterface;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Laser\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[Package('content')]
class ContactFormValidationFactory implements DataValidationFactoryInterface
{
    /**
     * The regex to check if string contains an url
     */
    final public const DOMAIN_NAME_REGEX = '/((https?:\/\/))/';

    /**
     * @internal
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly SystemConfigService $systemConfigService
    ) {
    }

    public function create(SalesChannelContext $context): DataValidationDefinition
    {
        return $this->createContactFormValidation('contact_form.create', $context);
    }

    public function update(SalesChannelContext $context): DataValidationDefinition
    {
        return $this->createContactFormValidation('contact_form.update', $context);
    }

    private function createContactFormValidation(string $validationName, SalesChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition($validationName);

        $definition
            ->add('salutationId', new NotBlank(), new EntityExists(['entity' => 'salutation', 'context' => $context->getContext()]))
            ->add('email', new NotBlank(), new Email())
            ->add('subject', new NotBlank())
            ->add('comment', new NotBlank())
            ->add('firstName', new Regex(['pattern' => self::DOMAIN_NAME_REGEX, 'match' => false]))
            ->add('lastName', new Regex(['pattern' => self::DOMAIN_NAME_REGEX, 'match' => false]));

        $required = $this->systemConfigService->get('core.basicInformation.firstNameFieldRequired', $context->getSalesChannel()->getId());
        if ($required) {
            $definition->set('firstName', new NotBlank(), new Regex([
                'pattern' => self::DOMAIN_NAME_REGEX,
                'match' => false,
            ]));
        }

        $required = $this->systemConfigService->get('core.basicInformation.lastNameFieldRequired', $context->getSalesChannel()->getId());
        if ($required) {
            $definition->set('lastName', new NotBlank(), new Regex([
                'pattern' => self::DOMAIN_NAME_REGEX,
                'match' => false,
            ]));
        }

        $required = $this->systemConfigService->get('core.basicInformation.phoneNumberFieldRequired', $context->getSalesChannel()->getId());
        if ($required) {
            $definition->add('phone', new NotBlank());
        }

        $validationEvent = new BuildValidationEvent($definition, new DataBag(), $context->getContext());
        $this->eventDispatcher->dispatch($validationEvent, $validationEvent->getName());

        return $definition;
    }
}
