<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\Rest;

use PHPUnit_Framework_TestCase as TestCase;
use ReflectionMethod;
use ReflectionObject;
use stdClass;
use Zend\EventManager\EventManager;
use Zend\EventManager\SharedEventManager;
use Zend\Http\Response;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\Segment;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Router\SimpleRouteStack;
use Zend\Paginator\Adapter\ArrayAdapter as ArrayPaginator;
use Zend\Paginator\Paginator;
use Zend\Stdlib\Parameters;
use Zend\View\Helper\ServerUrl as ServerUrlHelper;
use Zend\View\Helper\Url as UrlHelper;
use ZF\ApiProblem\ApiProblem;
use ZF\ContentNegotiation\ControllerPlugin\BodyParams;
use ZF\ContentNegotiation\ParameterDataContainer;
use ZF\Hal\Collection as HalCollection;
use ZF\Hal\Entity as HalEntity;
use ZF\Hal\Link\Link;
use ZF\Hal\Plugin\Hal as HalHelper;
use ZF\Rest\Exception;
use ZF\Rest\Resource;
use ZF\Rest\RestController;

/**
 * @subpackage UnitTest
 */
class RestControllerTest extends TestCase
{
    public function setUp()
    {
        $this->controller = $controller = new RestController();

        $this->router = $router = new SimpleRouteStack();
        $route = new Segment('/resource[/[:id]]');
        $router->addRoute('resource', $route);
        $this->event = $event = new MvcEvent();
        $event->setRouter($router);
        $event->setRouteMatch(new RouteMatch([]));
        $controller->setEvent($event);
        $controller->setRoute('resource');

        $pluginManager = new PluginManager();
        $pluginManager->setService('bodyParams', new BodyParams());
        $controller->setPluginManager($pluginManager);

        $urlHelper = new UrlHelper();
        $urlHelper->setRouter($this->router);

        $serverUrlHelper = new ServerUrlHelper();
        $serverUrlHelper->setScheme('http');
        $serverUrlHelper->setHost('localhost.localdomain');

        $linksHelper = new HalHelper();
        $linksHelper->setUrlHelper($urlHelper);
        $linksHelper->setServerUrlHelper($serverUrlHelper);

        $pluginManager->setService('Hal', $linksHelper);
        $linksHelper->setController($controller);

        $this->resource = $resource = new Resource();
        $controller->setResource($resource);
    }

    public function testReturnsErrorResponseWhenPageSizeExceedsMax()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
            ['id' => 'baz', 'bar' => 'baz'],
        ];
        $adapter   = new ArrayPaginator($items);
        $paginator = new Paginator($adapter);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($paginator) {
            return $paginator;
        });

        $this->controller->setPageSizeParam('page_size');
        $this->controller->setMaxPageSize(2);
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters([
            'page'      => 1,
            'page_size' => 3,
        ]));

        $result = $this->controller->getList();
        $this->assertProblemApiResult(416, "Page size is out of range, maximum page size is 2", $result);
    }

    public function testReturnsErrorResponseWhenPageSizeBelowMin()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
            ['id' => 'baz', 'bar' => 'baz'],
        ];
        $adapter   = new ArrayPaginator($items);
        $paginator = new Paginator($adapter);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($paginator) {
            return $paginator;
        });

        $this->controller->setPageSizeParam('page_size');
        $this->controller->setMinPageSize(2);
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters([
            'page'      => 1,
            'page_size' => 1,
        ]));

        $result = $this->controller->getList();
        $this->assertProblemApiResult(416, "Page size is out of range, minimum page size is 2", $result);
    }

    /**
     * @group hotfix/77
     */
    public function testReturnsErrorResponseWhenPageNonInteger()
    {
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters([
            'page'      => '1/',
        ]));

        $result = $this->controller->getList();
        $this->assertProblemApiResult(400, 'Page must be an integer; received "string"', $result);
    }

    public function assertProblemApiResult($expectedStatus, $expectedDetail, $result)
    {
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblem', $result);
        $problem = $result->toArray();
        $this->assertEquals($expectedStatus, $problem['status']);
        $this->assertContains($expectedDetail, $problem['detail']);
    }

    public function testCreateReturnsProblemResultOnCreationException()
    {
        $this->resource->getEventManager()->attach('create', function ($e) {
            throw new Exception\CreationException('failed');
        });

        $result = $this->controller->create([]);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    /**
     * Addresses zfcampus/zf-hal#51
     *
     * @group 43
     */
    public function testCreateDoesNotSetLocationHeaderOnMissingSelfRelationalLink()
    {
        $this->resource->getEventManager()->attach('create', function ($e) {
            return new HalEntity(['foo' => 'bar']);
        });

        $result = $this->controller->create([]);
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $response = $this->controller->getResponse();
        $headers  = $response->getHeaders();
        $this->assertFalse($headers->has('Location'));
    }

    public function testCreateReturnsHalEntityOnSuccess()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('create', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->create([]);
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertEquals($entity, $result->entity);
        return $this->controller->getResponse();
    }

    /**
     * @depends testCreateReturnsHalEntityOnSuccess
     */
    public function testSuccessfulCreationWithEntityIdentifierSetsResponseLocationheader($response)
    {
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
    }

    public function testFalseFromDeleteEntityReturnsProblemApiResult()
    {
        $this->resource->getEventManager()->attach('delete', function ($e) {
            return false;
        });

        $result = $this->controller->delete('foo');
        $this->assertProblemApiResult(422, 'delete', $result);
    }

    public function testTrueFromDeleteEntityReturnsResponseWithNoContent()
    {
        $this->resource->getEventManager()->attach('delete', function ($e) {
            return true;
        });

        $result = $this->controller->delete('foo');
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    public function testFalseFromDeleteCollectionReturnsProblemApiResult()
    {
        $this->resource->getEventManager()->attach('deleteList', function ($e) {
            return false;
        });

        $result = $this->controller->deleteList(null);
        $this->assertProblemApiResult(422, 'delete collection', $result);
    }

    public function testTrueFromDeleteCollectionReturnsResponseWithNoContent()
    {
        $this->resource->getEventManager()->attach('deleteList', function ($e) {
            return true;
        });

        $result = $this->controller->deleteList([1, 2, 3]);
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    public function testDeleteCollectionBackwardsCompatibleWithNoData()
    {
        $this->resource->getEventManager()->attach('deleteList', function ($e) {
            return true;
        });

        $result = $this->controller->deleteList(null);
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    public function testReturningEmptyResultFromGetReturnsProblemApiResult()
    {
        $this->resource->getEventManager()->attach('fetch', function ($e) {
            return false;
        });

        $result = $this->controller->get('foo');
        $this->assertProblemApiResult(404, 'not found', $result);
    }

    public function testReturningEntityFromGetReturnsExpectedHalEntity()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->get('foo');
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertEquals($entity, $result->entity);
    }

    public function testReturnsHalCollectionForNonPaginatedList()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz']
        ];
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($items) {
            return $items;
        });

        $result = $this->controller->getList();
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        $this->assertEquals($items, $result->getCollection());
        return $result;
    }

    public function testReturnsHalCollectionForPaginatedList()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
            ['id' => 'baz', 'bar' => 'baz'],
        ];
        $adapter   = new ArrayPaginator($items);
        $paginator = new Paginator($adapter);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($paginator) {
            return $paginator;
        });

        $this->controller->setPageSize(1);
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters(['page' => 2]));

        $result = $this->controller->getList();
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        $this->assertSame($paginator, $result->getCollection());
        $this->assertEquals(2, $result->getPage());
        $this->assertEquals(1, $result->getPageSize());
    }

    public function testReturnsHalCollectionForPaginatedListUsingPassedPageSizeParameter()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
            ['id' => 'baz', 'bar' => 'baz'],
        ];
        $adapter   = new ArrayPaginator($items);
        $paginator = new Paginator($adapter);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($paginator) {
            return $paginator;
        });

        $this->controller->setPageSizeParam('page_size');
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters([
            'page'      => 2,
            'page_size' => 1,
        ]));

        $result = $this->controller->getList();
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        $this->assertSame($paginator, $result->getCollection());
        $this->assertEquals(2, $result->getPage());
        $this->assertEquals(1, $result->getPageSize());
    }

    /**
     * @depends testReturnsHalCollectionForNonPaginatedList
     */
    public function testHalCollectionReturnedIncludesRoutes($collection)
    {
        $this->assertEquals('resource', $collection->getCollectionRoute());
        $this->assertEquals('resource', $collection->getEntityRoute());
    }

    public function testHeadReturnsListResponseWhenNoIdProvided()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
            ['id' => 'baz', 'bar' => 'baz'],
        ];
        $adapter   = new ArrayPaginator($items);
        $paginator = new Paginator($adapter);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($paginator) {
            return $paginator;
        });

        $this->controller->setPageSize(1);
        $request = $this->controller->getRequest();
        $request->setQuery(new Parameters(['page' => 2]));

        $result = $this->controller->head();
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        $this->assertSame($paginator, $result->getCollection());
    }

    public function testHeadReturnsEntityResponseWhenIdProvided()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->head('foo');
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertEquals($entity, $result->entity);
    }

    public function testOptionsReturnsEmptyResponseWithAllowHeaderPopulatedForCollection()
    {
        $r = new ReflectionObject($this->controller);
        $httpMethodsProp = $r->getProperty('collectionHttpMethods');
        $httpMethodsProp->setAccessible(true);
        $httpMethods = $httpMethodsProp->getValue($this->controller);
        sort($httpMethods);

        $result = $this->controller->options();
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
        $headers = $result->getHeaders();
        $this->assertTrue($headers->has('allow'));
        $allow = $headers->get('allow');
        $test  = $allow->getFieldValue();
        $test  = explode(', ', $test);
        sort($test);
        $this->assertEquals($httpMethods, $test);
    }

    public function testOptionsReturnsEmptyResponseWithAllowHeaderPopulatedForEntity()
    {
        $r = new ReflectionObject($this->controller);
        $httpMethodsProp = $r->getProperty('entityHttpMethods');
        $httpMethodsProp->setAccessible(true);
        $httpMethods = $httpMethodsProp->getValue($this->controller);
        sort($httpMethods);

        $this->event->getRouteMatch()->setParam('id', 'foo');

        $result = $this->controller->options();
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
        $headers = $result->getHeaders();
        $this->assertTrue($headers->has('allow'));
        $allow = $headers->get('allow');
        $test  = $allow->getFieldValue();
        $test  = explode(', ', $test);
        sort($test);
        $this->assertEquals($httpMethods, $test);
    }

    public function testOptionsReturnsEmptyResponseWithAllowHeaderPopulatedForEntityWhenRouteIdentifierIsCustomized()
    {
        $this->controller->setIdentifierName('user_id');

        $r = new ReflectionObject($this->controller);
        $httpMethodsProp = $r->getProperty('entityHttpMethods');
        $httpMethodsProp->setAccessible(true);
        $httpMethods = $httpMethodsProp->getValue($this->controller);
        sort($httpMethods);

        $this->event->getRouteMatch()->setParam('user_id', 'foo');

        $result = $this->controller->options();
        $this->assertInstanceOf('Zend\Http\Response', $result);
        $this->assertEquals(204, $result->getStatusCode());
        $headers = $result->getHeaders();
        $this->assertTrue($headers->has('allow'));
        $allow = $headers->get('allow');
        $test  = $allow->getFieldValue();
        $test  = explode(', ', $test);
        sort($test);
        $this->assertEquals($httpMethods, $test);
    }

    public function testPatchReturnsProblemResultOnPatchException()
    {
        $this->resource->getEventManager()->attach('patch', function ($e) {
            throw new Exception\PatchException('failed');
        });

        $result = $this->controller->patch('foo', []);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    public function testPatchReturnsHalEntityOnSuccess()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('patch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->patch('foo', $entity);
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertEquals($entity, $result->entity);
    }

    public function testUpdateReturnsProblemResultOnUpdateException()
    {
        $this->resource->getEventManager()->attach('update', function ($e) {
            throw new Exception\UpdateException('failed');
        });

        $result = $this->controller->update('foo', []);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    public function testUpdateReturnsHalEntityOnSuccess()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('update', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->update('foo', $entity);
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertEquals($entity, $result->entity);
    }

    public function testReplaceListReturnsProblemResultOnUpdateException()
    {
        $this->resource->getEventManager()->attach('replaceList', function ($e) {
            throw new Exception\UpdateException('failed');
        });

        $result = $this->controller->replaceList([]);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    public function testReplaceListReturnsHalCollectionOnSuccess()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz']];
        $this->resource->getEventManager()->attach('replaceList', function ($e) use ($items) {
            return $items;
        });

        $result = $this->controller->replaceList($items);
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        return $result;
    }

    /**
     * @depends testReplaceListReturnsHalCollectionOnSuccess
     */
    public function testReplaceListReturnsHalCollectionWithRoutesInjected($collection)
    {
        $this->assertEquals('resource', $collection->getCollectionRoute());
        $this->assertEquals('resource', $collection->getEntityRoute());
    }

    public function testOnDispatchRaisesDomainExceptionOnMissingEntity()
    {
        $controller = new RestController();
        $this->setExpectedException('ZF\ApiProblem\Exception\DomainException', 'No resource');
        $controller->onDispatch($this->event);
    }

    public function testOnDispatchRaisesDomainExceptionOnMissingRoute()
    {
        $controller = new RestController();
        $controller->setResource($this->resource);
        $this->setExpectedException('ZF\ApiProblem\Exception\DomainException', 'route');
        $controller->onDispatch($this->event);
    }

    public function testValidMethodReturningHalOrApiValueIsCastToViewModel()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $this->controller->setEntityHttpMethods(['GET']);

        $request = $this->controller->getRequest();
        $request->setMethod('GET');
        $this->event->setRequest($request);
        $this->event->getRouteMatch()->setParam('id', 'foo');

        $result = $this->controller->onDispatch($this->event);
        $this->assertInstanceof('Zend\View\Model\ModelInterface', $result);
    }

    public function testValidMethodReturningHalOrApiValueCastsReturnToContentNegotiationViewModel()
    {
        $entity = ['id' => 'foo', 'bar' => 'baz'];
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $this->controller->setEntityHttpMethods(['GET']);

        $request = $this->controller->getRequest();
        $request->setMethod('GET');
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');
        $this->event->setRequest($request);
        $this->event->getRouteMatch()->setParam('id', 'foo');

        $result = $this->controller->onDispatch($this->event);
        $this->assertInstanceof('ZF\ContentNegotiation\ViewModel', $result);
    }

    public function testPassingIdentifierToConstructorAllowsListeningOnThatIdentifier()
    {
        $controller   = new RestController('MyNamespace\Controller\Foo');
        $events       = new EventManager();
        $sharedEvents = new SharedEventManager();
        $events->setSharedManager($sharedEvents);
        $controller->setEventManager($events);

        $test = new stdClass;
        $test->flag = false;
        $sharedEvents->attach('MyNamespace\Controller\Foo', 'test', function ($e) use ($test) {
            $test->flag = true;
        });

        $events->trigger('test', $controller, []);
        $this->assertTrue($test->flag);
    }

    public function testHalCollectionUsesControllerCollectionName()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz']
        ];
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($items) {
            return $items;
        });

        $this->controller->setCollectionName('resources');

        $result = $this->controller->getList();
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        $this->assertEquals('resources', $result->getCollectionName());
    }

    public function testCreateUsesHalEntityReturnedByResource()
    {
        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('create', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->create($data);
        $this->assertSame($entity, $result);
    }

    public function testGetUsesHalEntityReturnedByResource()
    {
        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->get('foo');
        $this->assertSame($entity, $result);
    }

    public function testGetListUsesHalCollectionReturnedByResource()
    {
        $collection = new HalCollection([]);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->getList();
        $this->assertSame($collection, $result);
    }

    public function testPatchUsesHalEntityReturnedByResource()
    {
        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('patch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->patch('foo', $data);
        $this->assertSame($entity, $result);
    }

    public function testUpdateUsesHalEntityReturnedByResource()
    {
        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('update', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->update('foo', $data);
        $this->assertSame($entity, $result);
    }

    public function testReplaceListUsesHalCollectionReturnedByResource()
    {
        $collection = new HalCollection([]);
        $this->resource->getEventManager()->attach('replaceList', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->replaceList([]);
        $this->assertSame($collection, $result);
    }

    public function testCreateTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'pre_data'  => false,
            'post'      => false,
            'post_data' => false,
            'entity'    => false,
        ];

        $this->controller->getEventManager()->attach('create.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_data = $e->getParam('data');
        });
        $this->controller->getEventManager()->attach('create.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_data = $e->getParam('data');
            $test->entity = $e->getParam('entity');
        });

        $data   = ['id' => 'foo', 'data' => 'bar'];
        $entity = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('create', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->create($data);
        $this->assertTrue($test->pre);
        $this->assertEquals($data, $test->pre_data);
        $this->assertTrue($test->post);
        $this->assertEquals($data, $test->post_data);
        $this->assertSame($entity, $test->entity);
    }

    public function testDeleteTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'pre_id'  => false,
            'post'      => false,
            'post_id' => false,
        ];

        $this->controller->getEventManager()->attach('delete.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_id = $e->getParam('id');
        });
        $this->controller->getEventManager()->attach('delete.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_id = $e->getParam('id');
        });

        $this->resource->getEventManager()->attach('delete', function ($e) {
            return true;
        });

        $result = $this->controller->delete('foo');
        $this->assertTrue($test->pre);
        $this->assertEquals('foo', $test->pre_id);
        $this->assertTrue($test->post);
        $this->assertEquals('foo', $test->post_id);
    }

    public function testDeleteListTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'post'      => false,
        ];

        $this->controller->getEventManager()->attach('deleteList.pre', function ($e) use ($test) {
            $test->pre      = true;
        });
        $this->controller->getEventManager()->attach('deleteList.post', function ($e) use ($test) {
            $test->post = true;
        });

        $this->resource->getEventManager()->attach('deleteList', function ($e) {
            return true;
        });

        $result = $this->controller->deleteList(null);
        $this->assertTrue($test->pre);
        $this->assertTrue($test->post);
    }

    public function testGetTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'pre_id'    => false,
            'post'      => false,
            'post_id'   => false,
            'entity'    => false,
        ];

        $this->controller->getEventManager()->attach('get.pre', function ($e) use ($test) {
            $test->pre    = true;
            $test->pre_id = $e->getParam('id');
        });
        $this->controller->getEventManager()->attach('get.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_id = $e->getParam('id');
            $test->entity = $e->getParam('entity');
        });

        $data   = ['id' => 'foo', 'data' => 'bar'];
        $entity = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->get('foo');
        $this->assertTrue($test->pre);
        $this->assertEquals('foo', $test->pre_id);
        $this->assertTrue($test->post);
        $this->assertEquals('foo', $test->post_id);
        $this->assertSame($entity, $test->entity);
    }

    public function testOptionsTriggersPreAndPostEventsForCollection()
    {
        $methods = ['GET', 'POST'];
        $this->controller->setCollectionHttpMethods($methods);

        $test = (object) [
            'pre'          => false,
            'post'         => false,
            'pre_options'  => false,
            'post_options' => false,
        ];

        $this->controller->getEventManager()->attach('options.pre', function ($e) use ($test) {
            $test->pre = true;
            $test->pre_options = $e->getParam('options');
        });
        $this->controller->getEventManager()->attach('options.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_options = $e->getParam('options');
        });

        $this->controller->options();
        $this->assertTrue($test->pre);
        $this->assertEquals($methods, $test->pre_options);
        $this->assertTrue($test->post);
        $this->assertEquals($methods, $test->post_options);
    }

    public function testOptionsTriggersPreAndPostEventsForEntity()
    {
        $methods = ['GET', 'PUT', 'PATCH'];
        $this->controller->setEntityHttpMethods($methods);

        $test = (object) [
            'pre'          => false,
            'post'         => false,
            'pre_options'  => false,
            'post_options' => false,
        ];

        $this->controller->getEventManager()->attach('options.pre', function ($e) use ($test) {
            $test->pre = true;
            $test->pre_options = $e->getParam('options');
        });
        $this->controller->getEventManager()->attach('options.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_options = $e->getParam('options');
        });

        $this->event->getRouteMatch()->setParam('id', 'foo');

        $this->controller->options();
        $this->assertTrue($test->pre);
        $this->assertEquals($methods, $test->pre_options);
        $this->assertTrue($test->post);
        $this->assertEquals($methods, $test->post_options);
    }

    public function testGetListTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'        => false,
            'post'       => false,
            'collection' => false,
        ];

        $this->controller->getEventManager()->attach('getList.pre', function ($e) use ($test) {
            $test->pre    = true;
        });
        $this->controller->getEventManager()->attach('getList.post', function ($e) use ($test) {
            $test->post = true;
            $test->collection = $e->getParam('collection');
        });

        $collection = new HalCollection([]);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->getList();
        $this->assertTrue($test->pre);
        $this->assertTrue($test->post);
        $this->assertSame(
            $collection,
            $test->collection,
            'Expected ' . get_class($collection) . ', received ' . get_class($test->collection)
        );
    }

    public function testPatchTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'pre_id'    => false,
            'pre_data'  => false,
            'post'      => false,
            'post_id'   => false,
            'post_data' => false,
            'entity'    => false,
        ];

        $this->controller->getEventManager()->attach('patch.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_id   = $e->getParam('id');
            $test->pre_data = $e->getParam('data');
        });
        $this->controller->getEventManager()->attach('patch.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_id   = $e->getParam('id');
            $test->post_data = $e->getParam('data');
            $test->entity    = $e->getParam('entity');
        });

        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('patch', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->patch('foo', $data);
        $this->assertTrue($test->pre);
        $this->assertEquals('foo', $test->pre_id);
        $this->assertEquals($data, $test->pre_data);
        $this->assertTrue($test->post);
        $this->assertEquals('foo', $test->post_id);
        $this->assertEquals($data, $test->post_data);
        $this->assertSame($entity, $test->entity);
    }

    public function testUpdateTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'       => false,
            'pre_id'    => false,
            'pre_data'  => false,
            'post'      => false,
            'post_id'   => false,
            'post_data' => false,
            'entity'    => false,
        ];

        $this->controller->getEventManager()->attach('update.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_id   = $e->getParam('id');
            $test->pre_data = $e->getParam('data');
        });
        $this->controller->getEventManager()->attach('update.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_id   = $e->getParam('id');
            $test->post_data = $e->getParam('data');
            $test->entity    = $e->getParam('entity');
        });

        $data     = ['id' => 'foo', 'data' => 'bar'];
        $entity   = new HalEntity($data, 'foo', 'resource');
        $this->resource->getEventManager()->attach('update', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->update('foo', $data);
        $this->assertTrue($test->pre);
        $this->assertEquals('foo', $test->pre_id);
        $this->assertEquals($data, $test->pre_data);
        $this->assertTrue($test->post);
        $this->assertEquals('foo', $test->post_id);
        $this->assertEquals($data, $test->post_data);
        $this->assertSame($entity, $test->entity);
    }

    public function testReplaceListTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'        => false,
            'pre_data'   => false,
            'post'       => false,
            'post_data'  => false,
            'collection' => false,
        ];

        $this->controller->getEventManager()->attach('replaceList.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_data = $e->getParam('data');
        });
        $this->controller->getEventManager()->attach('replaceList.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_data = $e->getParam('data');
            $test->collection = $e->getParam('collection');
        });

        $data       = ['foo' => ['id' => 'bar']];
        $collection = new HalCollection($data);
        $this->resource->getEventManager()->attach('replaceList', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->replaceList($data);
        $this->assertTrue($test->pre);
        $this->assertEquals($data, $test->pre_data);
        $this->assertTrue($test->post);
        $this->assertEquals($data, $test->post_data);
        $this->assertSame($collection, $test->collection);
    }

    public function testDispatchReturnsEarlyIfApiProblemReturnedFromListener()
    {
        $problem  = new ApiProblem(500, 'got an error');
        $listener = function ($e) use ($problem) {
            $e->setParam('api-problem', $problem);
            return $problem;
        };
        $this->controller->getEventManager()->attach('dispatch', $listener, 100);

        $request = $this->controller->getRequest();
        $request->getHeaders()->addHeaderLine('Accept', 'application/json');

        $result = $this->controller->dispatch($request, $this->controller->getResponse());

        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $result);
        $this->assertSame($problem, $result->getApiProblem());
    }

    /**
     * @expectedException \ZF\ApiProblem\Exception\DomainException
     */
    public function testGetResourceThrowsExceptionOnMissingResource()
    {
        $controller = new RestController();
        $controller->getResource();
    }

    public function testGetResourceReturnsSameInstance()
    {
        $this->assertEquals($this->resource, $this->controller->getResource());
    }

    public function eventsProducingApiProblems()
    {
        return [
            'delete' => [
                'delete', 'delete', 'foo',
            ],
            'deleteList' => [
                'deleteList', 'deleteList', null,
            ],
            'get' => [
                'fetch', 'get', 'foo',
            ],
            'getList' => [
                'fetchAll', 'getList', null,
            ],
        ];
    }

    /**
     * @group 36
     * @dataProvider eventsProducingApiProblems
     */
    public function testExceptionDuringDeleteReturnsApiProblem($event, $method, $args)
    {
        $this->resource->getEventManager()->attach($event, function ($e) {
            throw new \Exception('failed');
        });

        $result = $this->controller->$method($args);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    public function testIdentifierNameHasSaneDefault()
    {
        $this->assertEquals('id', $this->controller->getIdentifierName());
    }

    public function testCanSetIdentifierName()
    {
        $this->controller->setIdentifierName('name');
        $this->assertEquals('name', $this->controller->getIdentifierName());
    }

    public function testUsesConfiguredIdentifierNameToGetIdentifier()
    {
        $r = new ReflectionObject($this->controller);
        $getIdentifier = $r->getMethod('getIdentifier');
        $getIdentifier->setAccessible(true);

        $this->controller->setIdentifierName('name');

        $routeMatch = $this->event->getRouteMatch();
        $request    = $this->controller->getRequest();

        $routeMatch->setParam('name', 'foo');
        $result = $getIdentifier->invoke($this->controller, $routeMatch, $request);
        $this->assertEquals('foo', $result);

        // Queries should not be used as identifiers, identifiers are route information.
        $routeMatch->setParam('name', false);
        $request->getQuery()->set('name', 'bar');
        $this->assertFalse($getIdentifier->invoke($this->controller, $routeMatch, $request));
    }

    /**
     * @group 44
     */
    public function testCreateAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Validation error',
            null,
            null,
            ['email' => 'Invalid email address provided']
        );
        $this->resource->getEventManager()->attach('create', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->create([]);
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testDeleteAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Invalid identifier',
            null,
            null,
            ['delete' => 'Invalid identifier provided']
        );
        $this->resource->getEventManager()->attach('delete', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->delete('foo');
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testDeleteListAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(400, 'Invalid list', null, null, ['delete' => 'Invalid collection']);
        $this->resource->getEventManager()->attach('deleteList', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->deleteList(null);
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testGetAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(400, 'Invalid identifier', null, null, ['get' => 'Invalid identifier provided']);
        $this->resource->getEventManager()->attach('fetch', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->get('foo');
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testGetListAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(400, 'Invalid collection', null, null, ['fetchAll' => 'Invalid collection']);
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->getList();
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testPatchAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Validation error',
            null,
            null,
            ['email' => 'Invalid email address provided']
        );
        $this->resource->getEventManager()->attach('patch', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->patch('foo', []);
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testUpdateAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Validation error',
            null,
            null,
            ['email' => 'Invalid email address provided']
        );
        $this->resource->getEventManager()->attach('update', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->update('foo', []);
        $this->assertSame($problem, $result);
    }

    /**
     * @group 44
     */
    public function testReplaceListAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Validation error',
            null,
            null,
            ['email' => 'Invalid email address provided']
        );
        $this->resource->getEventManager()->attach('replaceList', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->replaceList([]);
        $this->assertSame($problem, $result);
    }

    public function testPatchListReturnsProblemResultOnUpdateException()
    {
        $this->resource->getEventManager()->attach('patchList', function ($e) {
            throw new Exception\UpdateException('failed');
        });

        $result = $this->controller->patchList([]);
        $this->assertProblemApiResult(500, 'failed', $result);
    }

    public function testPatchListReturnsHalCollectionOnSuccess()
    {
        $items = [
            ['id' => 'foo', 'bar' => 'baz'],
            ['id' => 'bar', 'bar' => 'baz'],
        ];
        $this->resource->getEventManager()->attach('patchList', function ($e) use ($items) {
            return $items;
        });

        $result = $this->controller->patchList($items);
        $this->assertInstanceOf('ZF\Hal\Collection', $result);
        return $result;
    }

    /**
     * @depends testPatchListReturnsHalCollectionOnSuccess
     */
    public function testPatchListReturnsHalCollectionWithRoutesInjected($collection)
    {
        $this->assertEquals('resource', $collection->getCollectionRoute());
        $this->assertEquals('resource', $collection->getEntityRoute());
    }

    public function testPatchListUsesHalCollectionReturnedByResource()
    {
        $collection = new HalCollection([]);
        $this->resource->getEventManager()->attach('patchList', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->patchList([]);
        $this->assertSame($collection, $result);
    }

    public function testPatchListTriggersPreAndPostEvents()
    {
        $test = (object) [
            'pre'        => false,
            'pre_data'   => false,
            'post'       => false,
            'post_data'  => false,
            'collection' => false,
        ];

        $this->controller->getEventManager()->attach('patchList.pre', function ($e) use ($test) {
            $test->pre      = true;
            $test->pre_data = $e->getParam('data');
        });
        $this->controller->getEventManager()->attach('patchList.post', function ($e) use ($test) {
            $test->post = true;
            $test->post_data = $e->getParam('data');
            $test->collection = $e->getParam('collection');
        });

        $data       = ['foo' => ['id' => 'bar']];
        $collection = new HalCollection($data);
        $this->resource->getEventManager()->attach('patchList', function ($e) use ($collection) {
            return $collection;
        });

        $result = $this->controller->patchList($data);
        $this->assertTrue($test->pre);
        $this->assertEquals($data, $test->pre_data);
        $this->assertTrue($test->post);
        $this->assertEquals($data, $test->post_data);
        $this->assertSame($collection, $test->collection);
    }

    /**
     * @group 44
     */
    public function testPatchListAllowsReturningApiProblemFromResource()
    {
        $problem = new ApiProblem(
            400,
            'Validation error',
            null,
            null,
            ['email' => 'Invalid email address provided']
        );
        $this->resource->getEventManager()->attach('patchList', function ($e) use ($problem) {
            return $problem;
        });

        $result = $this->controller->patchList([]);
        $this->assertSame($problem, $result);
    }

    public function validResourcePayloads()
    {
        return [
            'GET_collection' => [
                'GET',
                'fetchAll',
                null,
                null,
                [],
            ],
            'GET_item' => [
                'GET',
                'fetch',
                'foo',
                null,
                ['id' => 'foo', 'bar' => 'baz'],
            ],
            'POST' => [
                'POST',
                'create',
                null,
                ['bar' => 'baz'],
                ['id' => 'foo', 'bar' => 'baz'],
            ],
            'PUT_collection' => [
                'PUT',
                'replaceList',
                null,
                [['id' => 'foo', 'bar' => 'bat']],
                [['id' => 'foo', 'bar' => 'bat']],
            ],
            'PUT_item' => [
                'PUT',
                'update',
                'foo',
                ['bar' => 'bat'],
                ['id' => 'foo', 'bar' => 'bat'],
            ],
            'PATCH_collection' => [
                'PATCH',
                'patchList',
                null,
                ['foo' => ['bar' => 'bat']],
                [['id' => 'foo', 'bar' => 'bat']],
            ],
            'PATCH_item' => [
                'PATCH',
                'patch',
                'foo',
                ['bar' => 'bat'],
                ['id' => 'foo', 'bar' => 'bat'],
            ],
            'DELETE_collection' => [
                'DELETE',
                'deleteList',
                null,
                null,
                true,
            ],
            'DELETE_item' => [
                'DELETE',
                'delete',
                'foo',
                null,
                true,
            ],
        ];
    }

    /**
     * @dataProvider validResourcePayloads
     */
    public function testInjectsContentValidationInputFilterFromMvcEventIntoResourceEvent(
        $method,
        $event,
        $id,
        $data,
        $returnValue
    ) {
        $resourceEvent = null;
        $this->resource->getEventManager()->attach($event, function ($e) use ($returnValue, & $resourceEvent) {
            $resourceEvent = $e;
            return $resource;
        });

        $this->controller->setCollectionHttpMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);
        $this->controller->setEntityHttpMethods(['GET', 'PUT', 'PATCH', 'DELETE']);

        $request = $this->controller->getRequest();
        $request->setMethod($method);
        $this->event->setRequest($request);

        $container = new ParameterDataContainer();
        $container->setBodyParams((null === $data) ? [] : $data);
        $this->event->setParam('ZFContentNegotiationParameterData', $container);

        if ($id) {
            $this->event->getRouteMatch()->setParam('id', $id);
        }

        $inputFilter = new InputFilter();
        $this->event->setParam('ZF\ContentValidation\InputFilter', $inputFilter);

        $result = $this->controller->onDispatch($this->event);

        $this->assertInstanceOf('ZF\Rest\ResourceEvent', $resourceEvent);
        $this->assertSame($inputFilter, $resourceEvent->getInputFilter());
    }


    /**
     * @group zf-mvc-auth-20
     */
    public function testInjectsIdentityFromMvcEventIntoResourceEvent()
    {
        $identity = $this->getMock('ZF\MvcAuth\Identity\IdentityInterface');
        $this->event->setParam('ZF\MvcAuth\Identity', $identity);
        $resource = $this->controller->getResource();
        $this->assertSame($identity, $resource->getIdentity());
    }

    public function testInjectsRequestFromControllerIntoResourceEvent()
    {
        $request = $this->controller->getRequest();
        $resource = $this->controller->getResource();

        $r = new ReflectionObject($resource);
        $m = $r->getMethod('prepareEvent');
        $m->setAccessible(true);
        $event = $m->invoke($resource, 'fetch', []);
        $this->assertSame($request, $event->getRequest());
    }

    public function entitiesReturnedForCollections()
    {
        return [
            'with-identifier' => [(object) ['id' => 'foo', 'foo' => 'bar']],
            'no-identifier'   => [(object) ['foo' => 'bar']],
        ];
    }

    /**
     * @dataProvider entitiesReturnedForCollections
     * @group 31
     */
    public function testGetListAllowsReturningEntitiesInsteadOfCollections($entity)
    {
        $this->resource->getEventManager()->attach('fetchAll', function ($e) use ($entity) {
            return $entity;
        });

        $result = $this->controller->getList();
        $this->assertInstanceOf('ZF\Hal\Entity', $result);
        $this->assertSame($entity, $result->entity);
    }

    public function methods()
    {
        return [
            'get-list'    => ['getList', 'fetchAll', [null]],
            'get'         => ['get', 'fetch', [1]],
            'post'        => ['create', 'create', [[]]],
            'put-list'    => ['replaceList', 'replaceList', [[]]],
            'put'         => ['update', 'update', [1, []]],
            'patch-list'  => ['patchList', 'patchList', [[]]],
            'patch'       => ['patch', 'patch', [1, []]],
            'delete-list' => ['deleteList', 'deleteList', [[]]],
            'delete'      => ['delete', 'delete', [1]],
        ];
    }

    /**
     * @group 68
     * @dataProvider methods
     */
    public function testAllowsReturningResponsesReturnedFromResources($method, $event, $argv)
    {
        $response = new Response();
        $response->setStatusCode(418);

        $events = $this->resource->getEventManager();
        $events->attach($event, function ($e) use ($response) {
            return $response;
        });

        $result = call_user_func_array([$this->controller, $method], $argv);
        $this->assertSame($response, $result);
    }

    /**
     * @group 74
     */
    public function testNonArrayToReplaceListReturnsApiProblem()
    {
        $response = $this->controller->replaceList(new stdClass());
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblem', $response);
        $details = $response->toArray();
        $this->assertEquals(400, $details['status']);
    }

    /**
     * @group 79
     */
    public function testAllowsReturningHalCollectionFromCreate()
    {
        $collection = [
            [
                'id'  => 1,
                'foo' => 'bar',
            ],
            [
                'id'  => 2,
                'foo' => 'bar',
            ],
            [
                'id'  => 3,
                'foo' => 'bar',
            ],
        ];
        $halCollection = new HalCollection($collection);

        $resource = $this->getMockBuilder('ZF\Rest\Resource')->getMock();
        $resource
            ->expects($this->once())
            ->method('create')
            ->with($this->anything())
            ->willReturn($halCollection);

        $this->controller->setResource($resource);
        $this->controller->setCollectionName('foo');
        $this->controller->setPageSize(5);

        $request = $this->controller->getRequest();
        $request->getQuery()->set('page', 3);

        $result = $this->controller->create([]);

        $this->assertSame($halCollection, $result);
        $this->assertEquals('resource', $halCollection->getCollectionRoute());
        $this->assertEquals('resource', $halCollection->getEntityRoute());
        $this->assertEquals($this->controller->getRouteIdentifierName(), $halCollection->getRouteIdentifierName());
        $this->assertEquals('foo', $halCollection->getCollectionName());
        $this->assertEquals(5, $halCollection->getPageSize());
        $this->assertEquals(3, $halCollection->getPage());
        $this->assertTrue($halCollection->getLinks()->has('self'));
    }

    /**
     * @group 79
     */
    public function testAllowsReturningHalEntityFromCreate()
    {
        $entity = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $halEntity = new HalEntity($entity, 1);

        $resource = $this->getMockBuilder('ZF\Rest\Resource')->getMock();
        $resource
            ->expects($this->once())
            ->method('create')
            ->with($this->anything())
            ->willReturn($halEntity);

        $this->controller->setResource($resource);

        $result = $this->controller->create([]);

        $this->assertSame($halEntity, $result);
        $this->assertTrue($halEntity->getLinks()->has('self'));
    }

    /**
     * @group 79
     */
    public function testCreateHalEntityInjectsExistingEntityWithSelfRelationalLinkIfNotPresent()
    {
        $entity = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $halEntity = new HalEntity($entity, 1);

        $r = new ReflectionMethod($this->controller, 'createHalEntity');
        $r->setAccessible(true);

        $result = $r->invoke($this->controller, $halEntity);
        $this->assertSame($result, $halEntity);
        $this->assertTrue($result->getLinks()->has('self'));
    }

    /**
     * @group 79
     */
    public function testCreateHalEntityDoesNotInjectExistingEntityWithSelfRelationalLinkIfAlreadyPresent()
    {
        $entity = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $halEntity = new HalEntity($entity, 1);
        $self = Link::factory([
            'rel' => 'self',
            'url' => 'http://example.com/foo/1',
        ]);
        $halEntity->getLinks()->add($self);

        $r = new ReflectionMethod($this->controller, 'createHalEntity');
        $r->setAccessible(true);

        $result = $r->invoke($this->controller, $halEntity);
        $this->assertSame($result, $halEntity);
        $this->assertTrue($result->getLinks()->has('self'));
        $this->assertSame($self, $result->getLinks()->get('self'));
    }

    /**
     * @group 79
     */
    public function testCreateHalCollectionInjectsExistingCollectionWithSelfRelationalLinkIfNotPresent()
    {
        $collection = [
            [
                'id'  => 1,
                'foo' => 'bar',
            ],
            [
                'id'  => 2,
                'foo' => 'bar',
            ],
            [
                'id'  => 3,
                'foo' => 'bar',
            ],
        ];
        $halCollection = new HalCollection($collection);

        $r = new ReflectionMethod($this->controller, 'createHalCollection');
        $r->setAccessible(true);

        $result = $r->invoke($this->controller, $halCollection);
        $this->assertSame($halCollection, $result);
        $this->assertTrue($result->getLinks()->has('self'));
    }

    /**
     * @group 79
     */
    public function testCreateHalCollectionInjectsExistingCollectionWithMetadataIfMissing()
    {
        $collection = [
            [
                'id'  => 1,
                'foo' => 'bar',
            ],
            [
                'id'  => 2,
                'foo' => 'bar',
            ],
            [
                'id'  => 3,
                'foo' => 'bar',
            ],
        ];
        $halCollection = new HalCollection($collection);

        $this->controller->setCollectionName('foo');
        $this->controller->setPageSize(5);

        $request = $this->controller->getRequest();
        $request->getQuery()->set('page', 3);

        $r = new ReflectionMethod($this->controller, 'createHalCollection');
        $r->setAccessible(true);

        $result = $r->invoke($this->controller, $halCollection);
        $this->assertSame($halCollection, $result);
        $this->assertEquals('resource', $halCollection->getCollectionRoute());
        $this->assertEquals('resource', $halCollection->getEntityRoute());
        $this->assertEquals($this->controller->getRouteIdentifierName(), $halCollection->getRouteIdentifierName());
        $this->assertEquals('foo', $halCollection->getCollectionName());
        $this->assertEquals(5, $halCollection->getPageSize());
        $this->assertEquals(3, $halCollection->getPage());
    }
}
