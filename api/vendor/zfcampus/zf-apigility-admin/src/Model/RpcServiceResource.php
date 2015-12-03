<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\Apigility\Admin\Model;

use RuntimeException;
use Zend\Mvc\Controller\ControllerManager;
use ZF\ApiProblem\ApiProblem;
use ZF\Hal\Collection as HalCollection;
use ZF\Hal\Link\Link;
use ZF\Hal\Entity as HalEntity;
use ZF\Rest\AbstractResourceListener;
use ZF\Rest\Exception\CreationException;
use ZF\Rest\Exception\PatchException;

class RpcServiceResource extends AbstractResourceListener
{
    /**
     * @var ControllerManager
     */
    protected $controllerManager;

    /**
     * @var InputFilterModel
     */
    protected $inputFilterModel;

    /**
     * @var DocumentationModel
     */
    protected $documentationModel;

    /**
     * @var RpcServiceModel
     */
    protected $model;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var RpcServiceModelFactory
     */
    protected $rpcFactory;

    /**
     * @param  RpcServiceModelFactory $rpcFactory
     * @param  InputFilterModel $inputFilterModel
     */
    public function __construct(
        RpcServiceModelFactory $rpcFactory,
        InputFilterModel $inputFilterModel,
        ControllerManager $controllerManager,
        DocumentationModel $documentationModel
    ) {
        $this->rpcFactory = $rpcFactory;
        $this->inputFilterModel = $inputFilterModel;
        $this->controllerManager = $controllerManager;
        $this->documentationModel = $documentationModel;
    }

    /**
     * @return string
     * @throws RuntimeException if module name is not present in route matches
     */
    public function getModuleName()
    {
        if (null !== $this->moduleName) {
            return $this->moduleName;
        }

        $moduleName = $this->getEvent()->getRouteParam('name', false);
        if (!$moduleName) {
            throw new RuntimeException(sprintf(
                '%s cannot operate correctly without a "name" segment in the route matches',
                __CLASS__
            ));
        }
        $this->moduleName = $moduleName;
        return $moduleName;
    }

    /**
     * @return RpcServiceModel
     */
    public function getModel()
    {
        if ($this->model instanceof RpcServiceModel) {
            return $this->model;
        }
        $moduleName = $this->getModuleName();
        $this->model = $this->rpcFactory->factory($moduleName);
        return $this->model;
    }

    /**
     * Create a new RPC service
     *
     * @param  array|object $data
     * @return RpcServiceEntity
     * @throws CreationException
     */
    public function create($data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        $creationData = [
            'http_methods' => ['GET'],
            'selector'     => null,
        ];

        if (!isset($data['service_name'])
            || !is_string($data['service_name'])
            || empty($data['service_name'])
        ) {
            throw new CreationException('Unable to create RPC service; missing service_name');
        }
        $creationData['service_name'] = $data['service_name'];

        $model = $this->getModel();
        if ($model->fetch($creationData['service_name'])) {
            throw new CreationException('Service by that name already exists', 409);
        }

        if (!isset($data['route_match'])
            || !is_string($data['route_match'])
            || empty($data['route_match'])
        ) {
            throw new CreationException('Unable to create RPC service; missing route');
        }
        $creationData['route_match'] = $data['route_match'];

        if (isset($data['http_methods'])
            && (is_string($data['http_methods']) || is_array($data['http_methods']))
            && !empty($data['http_methods'])
        ) {
            $creationData['http_methods'] = $data['http_methods'];
        }

        if (isset($data['selector'])
            && is_string($data['selector'])
            && !empty($data['selector'])
        ) {
            $creationData['selector'] = $data['selector'];
        }

        try {
            $service = $model->createService(
                $creationData['service_name'],
                $creationData['route_match'],
                $creationData['http_methods'],
                $creationData['selector']
            );
        } catch (\Exception $e) {
            if ($e->getCode() !== 500) {
                return new ApiProblem($e->getCode(), $e->getMessage());
            }
            return new ApiProblem(500, 'Unable to create RPC service');
        }

        return $service;
    }

    /**
     * Fetch RPC metadata
     *
     * @param  string $id
     * @return RpcServiceEntity|ApiProblem
     */
    public function fetch($id)
    {
        $service = $this->getModel()->fetch($id);
        if (!$service instanceof RpcServiceEntity) {
            return new ApiProblem(404, 'RPC service not found');
        }
        $this->injectInputFilters($service);
        $this->injectDocumentation($service);
        $this->injectControllerClass($service);
        return $service;
    }

    /**
     * Fetch metadata for all RPC services
     *
     * @param  array $params
     * @return RpcServiceEntity[]
     */
    public function fetchAll($params = [])
    {
        $version  = $this->getEvent()->getQueryParam('version', null);
        $services = $this->getModel()->fetchAll($version ?: null);

        foreach ($services as $service) {
            $this->injectInputFilters($service);
            $this->injectDocumentation($service);
            $this->injectControllerClass($service);
        }

        return $services;
    }

    /**
     * Update an existing RPC service
     *
     * @param  string $id
     * @param  object|array $data
     * @return ApiProblem|RpcServiceEntity
     * @throws PatchException if unable to update configuration
     */
    public function patch($id, $data)
    {
        if (is_object($data)) {
            $data = (array) $data;
        }

        if (!is_array($data)) {
            return new ApiProblem(400, 'Invalid data provided for update');
        }

        if (empty($data)) {
            return new ApiProblem(400, 'No data provided for update');
        }

        $model = $this->getModel();
        foreach ($data as $key => $value) {
            try {
                switch (strtolower($key)) {
                    case 'httpmethods':
                    case 'http_methods':
                        $model->updateHttpMethods($id, $value);
                        break;
                    case 'routematch':
                    case 'route_match':
                        $model->updateRoute($id, $value);
                        break;
                    case 'selector':
                        $model->updateSelector($id, $value);
                        break;
                    case 'accept_whitelist':
                        $model->updateContentNegotiationWhitelist($id, 'accept', $value);
                        break;
                    case 'content_type_whitelist':
                        $model->updateContentNegotiationWhitelist($id, 'content_type', $value);
                        break;
                    default:
                        break;
                }
            } catch (\Exception $e) {
                if ($e->getCode() !== 500) {
                    return new ApiProblem($e->getCode(), $e->getMessage());
                }
                return new ApiProblem(500, 'Unable to update RPC service');
            }
        }

        return $model->fetch($id);
    }

    /**
     * Delete an RPC service
     *
     * @param  string $id
     * @return true
     */
    public function delete($id)
    {
        $entity = $this->fetch($id);
        if ($entity instanceof ApiProblem) {
            return $entity;
        }
        $request   = $this->getEvent()->getRequest();
        $recursive = $request->getQuery('recursive', false);

        return $this->getModel()->deleteService($entity, $recursive);
    }

    /**
     * Inject the input filters collection, if any, as an embedded collection
     *
     * @param RpcServiceEntity $service
     */
    protected function injectInputFilters(RpcServiceEntity $service)
    {
        $inputFilters = $this->inputFilterModel->fetch($this->moduleName, $service->controllerServiceName);
        if (!$inputFilters instanceof InputFilterCollection
            || !count($inputFilters)
        ) {
            return;
        }

        $collection = [];
        $parentName = str_replace('\\', '-', $service->controllerServiceName);
        foreach ($inputFilters as $inputFilter) {
            $inputFilter['input_filter_name'] = str_replace('\\', '-', $inputFilter['input_filter_name']);
            $entity   = new HalEntity($inputFilter, $inputFilter['input_filter_name']);
            $links    = $entity->getLinks();
            $links->add(Link::factory([
                'rel' => 'self',
                'route' => [
                    'name' => 'zf-apigility/api/module/rpc-service/input-filter',
                    'params' => [
                        'name' => $this->moduleName,
                        'controller_service_name' => $parentName,
                        'input_filter_name' => $inputFilter['input_filter_name'],
                    ],
                ],
            ]));
            $collection[] = $entity;
        }

        $collection = new HalCollection($collection);
        $collection->setCollectionName('input_filter');
        $collection->setCollectionRoute('zf-apigility/module/rpc-service/input-filter');
        $collection->setCollectionRouteParams([
            'name' => $this->moduleName,
            'controller_service_name' => $service->controllerServiceName,
        ]);

        $service->exchangeArray([
            'input_filters' => $collection,
        ]);
    }

    protected function injectDocumentation(RpcServiceEntity $service)
    {
        $documentation = $this->documentationModel->fetchDocumentation(
            $this->moduleName,
            $service->controllerServiceName
        );
        if (!$documentation) {
            return;
        }
        $entity = new HalEntity($documentation, 'documentation');

        $service->exchangeArray(['documentation' => $entity]);
    }

    /**
     * Inject the class name of the controller, if it can be resolved.
     *
     * @param RpcServiceEntity $service
     */
    protected function injectControllerClass(RpcServiceEntity $service)
    {
        $controllerServiceName = $service->controllerServiceName;
        if (!$this->controllerManager->has($controllerServiceName)) {
            return;
        }

        $controller = $this->controllerManager->get($controllerServiceName);
        $service->exchangeArray([
            'controller_class' => get_class($controller),
        ]);
    }
}
