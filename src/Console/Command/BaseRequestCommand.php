<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Console\Command;

use OpenEuropa\EPoetry\Authentication\AuthenticationInterface;
use OpenEuropa\EPoetry\RequestClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Base class for all request-related commands.
 */
abstract class BaseRequestCommand extends Command
{
    protected string $endpoint;

    protected LoggerInterface $logger;

    protected SerializerInterface $serializer;

    protected EventDispatcherInterface $eventDispatcher;

    protected AuthenticationInterface $authentication;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Symfony\Component\Serializer\SerializerInterface $serializer
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \OpenEuropa\EPoetry\Authentication\AuthenticationInterface $authentication
     */
    public function __construct(string $endpoint, LoggerInterface $logger, SerializerInterface $serializer, EventDispatcherInterface $eventDispatcher, AuthenticationInterface $authentication)
    {
        parent::__construct(null);
        $this->endpoint = $endpoint;
        $this->logger = $logger;
        $this->serializer = $serializer;
        $this->eventDispatcher = $eventDispatcher;
        $this->authentication = $authentication;
    }

    /**
     * @return \OpenEuropa\EPoetry\RequestClientFactory
     */
    protected function getRequestFactory(): RequestClientFactory
    {
        return new RequestClientFactory($this->endpoint, $this->authentication, $this->eventDispatcher, $this->logger);
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param \OpenEuropa\EPoetry\RequestClientFactory $factory
     * @param $response
     *
     * @return void
     */
    protected function outputResponse(OutputInterface $output, RequestClientFactory $factory, $response)
    {
        $this->logger->info('Endpoint: ' . $factory->getEndpoint());
        $this->logger->info('Proxy ticket: ' . $factory->getProxyTicket());
        $output->writeln('Response:');
        $output->writeln($this->serializer->serialize($response, 'json', [
            JsonEncode::OPTIONS => JSON_PRETTY_PRINT,
        ]));
    }
}
