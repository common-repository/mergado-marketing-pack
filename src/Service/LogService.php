<?php

namespace Mergado\Service;

use Mergado\Traits\SingletonTrait;

class LogService
{
    use SingletonTrait;

    private $logger;

    public function __construct()
    {
        // Check if already initialized, if not it will be initialized later
        if (function_exists('wc_get_logger')) {
            $this->logger = wc_get_logger();
        }
    }

    public function error($message, string $context = ''): void
    {
        $this->setLoggerIfDoesNotExist();

        $source = $context !== '' ? "mergado-{$context}" : "mergado";

        $this->logger->error($message, ['source' => $source]);
    }

    public function warning($message, string $context = ''): void
    {
        $this->setLoggerIfDoesNotExist();

        $source = $context !== '' ? "mergado-{$context}" : "mergado";

        $this->logger->warning($message, ['source' => $source]);
    }

    public function info($message, string $context = ''): void
    {
        $this->setLoggerIfDoesNotExist();

        $source = $context !== '' ? "mergado-{$context}" : "mergado";

        $this->logger->info($message, ['source' => $source]);
    }

    private function setLoggerIfDoesNotExist(): void
    {
        if (!$this->logger) {
            $this->logger = wc_get_logger();
        }
    }
}
