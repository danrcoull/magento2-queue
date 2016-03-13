<?php

namespace Springbot\Queue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Springbot\Queue\Helper\Data as SpringbotHelper;

/**
 * Class ProductDeleteObserver
 * @package Springbot\Queue\Observer
 */
class ProductDeleteObserver implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @var SpringbotHelper
     */
    private $_springbotHelper;

    /**
     * HarvestProductObserver constructor.
     *
     * @param LoggerInterface $loggerInterface
     * @param SpringbotHelper $springbotHelper
     */
    public function __construct(
        LoggerInterface $loggerInterface,
        SpringbotHelper $springbotHelper
    ) {
        $this->_logger = $loggerInterface;
        $this->_springbotHelper = $springbotHelper;
    }

    /**
     * Queue up product changes
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Grab the product from the event
         */
        $product = $observer->getEvent()->getProduct();

        /**
         * Get the store Id
         */
        $storeId = $product->getStoreId();

        /**
         * Grab the product Id
         */
        $productId = $product->getId();

        /**
         * Schedule the job
         */
        $this->_springbotHelper->scheduleJob('deleteProduct', [$storeId, $productId],
            'Springbot\Main\Helper\QueueProductChanges', 'listener', 5);
    }
}
