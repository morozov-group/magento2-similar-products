<?php

namespace Morozov\Similarity\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Generates url keys.
 */
class ReindexAll extends Command
{
    /**
     * @var \Morozov\Similarity\Helper\Data
     */
    private $defaultHelper;

    /**
     * @var \Morozov\Similarity\Helper\Api
     */
    private $apiHelper;


    /**
     * @param \Morozov\Similarity\Helper\Api $apiHelper
     */
    public function __construct(
        \Morozov\Similarity\Helper\Data $defaultHelper,
        \Morozov\Similarity\Helper\Api $apiHelper
    ) {
        $this->defaultHelper = $defaultHelper;
        $this->apiHelper = $apiHelper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('morozov-similarity:reindexall');
        $this->setDescription('Push all Products to the service');
        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->setDecorated(true);
        foreach($this->defaultHelper->getStores() as $store) {
            try {
                $msg = "Pushing Products to the service (Store ID = {$store->getId()}): ";
                $output->write($msg);
                $this->defaultHelper->log('');
                $this->defaultHelper->log($msg);
                $this->defaultHelper->setStore($store);
                $this->apiHelper->setAllProducts();
                $msg = 'Done.';
                $output->writeln($msg);
                $this->defaultHelper->log($msg);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                $this->defaultHelper->log($e->getMessage());
            }
        }
    }
}
