<?php

namespace Springbot\Queue\Cron;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Springbot\Queue\Model\Queue;

/**
 * Class ProcessQueueCommand
 *
 * @package Springbot\Queue\Console\Command
 */
class Process
{

    private $_queue;
    private $_state;

    /**
     * @param State $state
     * @param Queue $queue
     */
    public function __construct(State $state, Queue $queue)
    {
        $this->_state = $state;
        $this->_queue = $queue;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return string
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        $success = $this->_queue->runNextJob();
        if ($success === true) {
            $output->writeln("Queue Processed.");
        } elseif ($success === false) {
            $output->writeln("Job failed.");
        } else {
            $output->writeln("No jobs in queue.");
        }
    }
}