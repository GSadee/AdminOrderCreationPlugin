<?php

namespace Sylius\AdminOrderCreationPlugin\Factory;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class OrderFactory implements OrderFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $currencyRepository;

    /** @var RepositoryInterface */
    private $localeRepository;

    public function __construct(
        FactoryInterface $decoratedFactory,
        CustomerRepositoryInterface $customerRepository,
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $currencyRepository,
        RepositoryInterface $localeRepository
    ) {
        $this->decoratedFactory = $decoratedFactory;
        $this->customerRepository = $customerRepository;
        $this->channelRepository = $channelRepository;
        $this->currencyRepository = $currencyRepository;
        $this->localeRepository = $localeRepository;
    }

    public function createNew(): OrderInterface
    {
        return $this->decoratedFactory->createNew();
    }

    public function createForCustomer(string $customerEmail): OrderInterface
    {
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);
        Assert::notNull($customer);

        /** @var OrderInterface $order */
        $order = $this->decoratedFactory->createNew();
        $order->setCustomer($customer);
        $order->setChannel($this->channelRepository->findOneBy(['enabled' => true]));
        $order->setCurrencyCode($this->currencyRepository->findOneBy([])->getCode());
        $order->setLocaleCode($this->localeRepository->findOneBy([])->getCode());

        return $order;
    }
}
