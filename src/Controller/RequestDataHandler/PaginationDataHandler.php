<?php

namespace App\Controller\RequestDataHandler;

class PaginationDataHandler
{
    public const PAGE_INDEX = 'page';
    public const LIMIT_INDEX = 'limit';
    private ?int $defaultLimit = 9;

    public function retrieveData(array $requestData): array
    {
        $data = [];

        $this->resolvePage($requestData, $data);
        $this->resolveLimit($requestData, $data);

        return $data;
    }

    private function resolvePage(array $requestData, array &$data): void
    {
        $page = 1;

        if (isset($requestData[self::PAGE_INDEX])) {
            $page = (int) $requestData[self::PAGE_INDEX];
        }

        $data[self::PAGE_INDEX] = $page;
    }

    private function resolveLimit(array $requestData, array &$data): void
    {
        $limit = $this->defaultLimit;

        if (isset($requestData[self::LIMIT_INDEX])) {
            $limit = (int) $requestData[self::LIMIT_INDEX];
        }

        $data[self::LIMIT_INDEX] = $limit;
    }
}