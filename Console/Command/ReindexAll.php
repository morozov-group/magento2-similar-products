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
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @var Actions
     */
    private $actions;

    /**
     * @param \Magento\Framework\App\State $appState
     * @param Actions $actions
     */
    public function __construct(
        \Magento\Framework\App\State $appState
    ) {
        $this->appState = $appState;
        //$this->actions = $actions;
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
        $output->writeln("");
        $output->writeln("111111111");

    }
}
