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
     * @var \Morozov\Similarity\Helper\Api
     */
    private $apiHelper;


    /**
     * @param \Morozov\Similarity\Helper\Api $apiHelper
     */
    public function __construct(
        \Morozov\Similarity\Helper\Api $apiHelper
    ) {
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
        try {
            $this->apiHelper->setAllProducts();
            $output->writeln('Products were successfully pushed to the service.');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
