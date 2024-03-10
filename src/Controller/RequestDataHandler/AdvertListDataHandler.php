<?php

namespace App\Controller\RequestDataHandler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdvertListDataHandler
{
    private ?string $categoryPropertyPrefix;
    private ?string $typePropertyPrefix;
    private ?string $cityPropertyPrefix;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->categoryPropertyPrefix = $parameterBag->get('app_category_property_prefix');
        $this->typePropertyPrefix = $parameterBag->get('app_type_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
    }

    public function retrieveData(array $requestData): array
    {
        $data[$this->categoryPropertyPrefix] = (string) $requestData[$this->categoryPropertyPrefix];
        $data[$this->typePropertyPrefix] = $requestData[$this->typePropertyPrefix];
        $data[$this->cityPropertyPrefix] = (string) $requestData[$this->cityPropertyPrefix];

        return array_merge($data, $requestData['price']);
    }
}
