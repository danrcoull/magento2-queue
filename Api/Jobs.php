<?php

namespace Springbot\Queue\Api;

use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Springbot\Queue\Model\Queue;

/**
 * Class Config
 * @package Springbot\Main\Api
 */
class Jobs extends AbstractModel implements JobsInterface
{

    private $queue;

    /**
     * @param Queue $queue
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Queue $queue,
        Context $context,
        Registry $registry
    ) {
        $this->queue = $queue;
        parent::__construct($context, $registry);
    }

    public function viewJobs()
    {
        return [
            'jobs' => $this->queue->getCollection()->toArray()
        ];
    }

    public function process()
    {
        $result = $this->queue->process();
        if ($result === true) {
            $message = "Job(s) run successfully";
        } elseif ($result === false) {
            $message = "Job(s) failed";
        } else {
            $message = "No jobs left to run";
        }
        return new ProcessResponse($message, $this->queue->getCount());
    }
}
