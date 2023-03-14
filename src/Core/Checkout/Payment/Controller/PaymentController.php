<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Payment\Controller;

use Laser\Core\Checkout\Cart\Order\OrderConverter;
use Laser\Core\Checkout\Order\OrderEntity;
use Laser\Core\Checkout\Payment\Cart\Token\TokenFactoryInterfaceV2;
use Laser\Core\Checkout\Payment\Cart\Token\TokenStruct;
use Laser\Core\Checkout\Payment\Exception\InvalidTokenException;
use Laser\Core\Checkout\Payment\PaymentService;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Laser\Core\Framework\LaserException;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Package('checkout')]
class PaymentController extends AbstractController
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly OrderConverter $orderConverter,
        private readonly TokenFactoryInterfaceV2 $tokenFactoryInterfaceV2,
        private readonly EntityRepository $orderRepository
    ) {
    }

    #[Route(path: '/payment/finalize-transaction', name: 'payment.finalize.transaction', methods: ['GET', 'POST'])]
    public function finalizeTransaction(Request $request): Response
    {
        $paymentToken = $request->get('_sw_payment_token');

        if ($paymentToken === null) {
            throw new MissingRequestParameterException('_sw_payment_token');
        }

        $salesChannelContext = $this->assembleSalesChannelContext($paymentToken);

        $result = $this->paymentService->finalizeTransaction(
            $paymentToken,
            $request,
            $salesChannelContext
        );

        $response = $this->handleException($result);
        if ($response !== null) {
            return $response;
        }

        $finishUrl = $result->getFinishUrl();
        if ($finishUrl) {
            return new RedirectResponse($finishUrl);
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function handleException(TokenStruct $token): ?Response
    {
        if ($token->getException() === null) {
            return null;
        }

        if ($token->getErrorUrl() === null) {
            return null;
        }

        $url = $token->getErrorUrl();

        $exception = $token->getException();
        if ($exception instanceof LaserException) {
            return new RedirectResponse(
                $url . (parse_url($url, \PHP_URL_QUERY) ? '&' : '?') . 'error-code=' . $exception->getErrorCode()
            );
        }

        return new RedirectResponse($url);
    }

    private function assembleSalesChannelContext(string $paymentToken): SalesChannelContext
    {
        $context = Context::createDefaultContext();

        $transactionId = $this->tokenFactoryInterfaceV2->parseToken($paymentToken)->getTransactionId();
        if ($transactionId === null) {
            throw new InvalidTokenException($paymentToken);
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('transactions.id', $transactionId));
        $criteria->addAssociation('transactions');
        $criteria->addAssociation('orderCustomer');

        /** @var OrderEntity|null $order */
        $order = $this->orderRepository->search($criteria, $context)->first();

        if ($order === null) {
            throw new InvalidTokenException($paymentToken);
        }

        return $this->orderConverter->assembleSalesChannelContext($order, $context);
    }
}
