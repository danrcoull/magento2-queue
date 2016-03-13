<?php

namespace Springbot\Queue\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Springbot\Queue\Helper\Data as SpringbotHelper;

/**
 * Class ProductSaveObserver
 * @package Springbot\Queue\Observer
 */
class ProductSaveObserver implements ObserverInterface
{
    /**
     * Attributes we care about
     *
     * @var array
     */
    private $_attributes = [
        'entity_id',
        'sku',
        'attribute_set_id',
        'description',
        'full_description',
        'short_description',
        'image',
        'url_key',
        'small_image',
        'thumbnail',
        'status',
        'visibility',
        'price',
        'special_price',
        'image_label',
        'name',
    ];

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
         * Schedule the job to be processed
         */
        $changedAttributes = [];

        // Check to see if the data we care about has changed. If so, add it to the array for hasing purposes.
        if ($product->hasDataChanges()) {
            foreach ($this->_attributes as $attribute) {
                if ($product->dataHasChangedFor($attribute)) {
                    $changedAttributes[] = $attribute;
                }
            }

            // Queue the job to send to the ETL
            if (count($changedAttributes) !== 0) {
                $this->_springbotHelper->scheduleJob('updateProduct', [$storeId, $productId, $changedAttributes],
                    'Springbot\Main\Helper\QueueProductChanges', 'listener', 5);
            }
        }
    }
}
