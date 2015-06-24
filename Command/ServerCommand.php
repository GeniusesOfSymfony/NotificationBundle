<?php

namespace Gos\Bundle\NotificationBundle\Command;

use Gos\Bundle\NotificationBundle\Server\PubSubServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    /**
     * @var PubSubServer
     */
    protected $server;

    /**
     * @param PubSubServer $server
     */
    public function __construct(PubSubServer $server)
    {
        $this->server = $server;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('gos:notification:server')
            ->setDescription('Starts the notification server')
            ->addOption('profile', 'p', InputOption::VALUE_NONE, 'Profiling server');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->server->launch($input->getOption('profile'));
    }
}
