<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\ContentValidation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\Request as StdlibRequest;
use ZF\ContentNegotiation\ParameterDataContainer;
use ZF\ContentValidation\ContentValidationListener;
use Zend\InputFilter\InputFilterInterface;
use ZF\ApiProblem\ApiProblemResponse;
use ZF\ApiProblem\ApiProblem;

class ContentValidationListenerTest extends TestCase
{
    public function testAttachesToRouteEventAtLowPriority()
    {
        $listener = new ContentValidationListener();
        $events = $this->getMock('Zend\EventManager\EventManagerInterface');
        $events->expects($this->once())
            ->method('attach')
            ->with(
                $this->equalTo(MvcEvent::EVENT_ROUTE),
                $this->equalTo([$listener, 'onRoute']),
                $this->lessThan(-99)
            );
        $listener->attach($events);
    }

    public function testReturnsEarlyIfRequestIsNonHttp()
    {
        $listener = new ContentValidationListener();

        $request = new StdlibRequest();
        $event   = new MvcEvent();
        $event->setRequest($request);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    public function nonBodyMethods()
    {
        return [
            'get'     => ['GET'],
            'head'    => ['HEAD'],
            'options' => ['OPTIONS'],
            'delete'  => ['DELETE'],
        ];
    }

    public function testAddCustomMethods()
    {
        $className = 'ZF\ContentValidation\ContentValidationListener';
        $listener = $this->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->getMock();

        $listener->expects($this->at(0))->method('addMethodWithoutBody')->with('LINK');
        $listener->expects($this->at(1))->method('addMethodWithoutBody')->with('UNLINK');

        $reflectedClass = new \ReflectionClass($className);
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($listener, [
            'methods_without_bodies' => [
                'LINK',
                'UNLINK',
            ],
        ]);
    }

    /**
     * @dataProvider nonBodyMethods
     */
    public function testReturnsEarlyIfRequestMethodWillNotContainRequestBody($method)
    {
        $listener = new ContentValidationListener();

        $request = new HttpRequest();
        $request->setMethod($method);
        $event   = new MvcEvent();
        $event->setRequest($request);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    public function testReturnsEarlyIfNoRouteMatchesPresent()
    {
        $listener = new ContentValidationListener();

        $request = new HttpRequest();
        $request->setMethod('POST');
        $event   = new MvcEvent();
        $event->setRequest($request);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    public function testReturnsEarlyIfRouteMatchesDoNotContainControllerService()
    {
        $listener = new ContentValidationListener();

        $request = new HttpRequest();
        $request->setMethod('POST');
        $matches = new RouteMatch([]);
        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    public function testReturnsEarlyIfControllerServiceIsNotInConfig()
    {
        $listener = new ContentValidationListener();

        $request = new HttpRequest();
        $request->setMethod('POST');
        $matches = new RouteMatch(['controller' => 'Foo']);
        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    public function testReturnsApiProblemResponseIfContentNegotiationBodyDataIsMissing()
    {
        $services = new ServiceManager();
        $services->setService('FooValidator', new InputFilter());
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');
        $matches = new RouteMatch(['controller' => 'Foo']);
        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        return $response;
    }

    /**
     * @depends testReturnsApiProblemResponseIfContentNegotiationBodyDataIsMissing
     */
    public function testMissingContentNegotiationDataHas500Response($response)
    {
        $this->assertEquals(500, $response->getApiProblem()->status);
    }

    public function testReturnsApiProblemResponseIfInputFilterServiceIsInvalid()
    {
        $services = new ServiceManager();
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(500, $response->getApiProblem()->status);
    }

    public function testReturnsNothingIfContentIsValid()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 123,
            'bar' => 'abc',
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
        return $event;
    }

    public function testReturnsApiProblemResponseIfContentIsInvalid()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 'abc',
            'bar' => 123,
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        return $response;
    }

    /**
     * @depends testReturnsApiProblemResponseIfContentIsInvalid
     */
    public function testApiProblemResponseFromInvalidContentHas422Status($response)
    {
        $this->assertEquals(422, $response->getApiProblem()->status);
    }

    /**
     * @depends testReturnsApiProblemResponseIfContentIsInvalid
     */
    public function testApiProblemResponseFromInvalidContentContainsValidationErrorMessages($response)
    {
        $problem = $response->getApiProblem();
        $asArray = $problem->toArray();
        $this->assertArrayHasKey('validation_messages', $asArray);
        $this->assertCount(2, $asArray['validation_messages']);
        $this->assertArrayHasKey('foo', $asArray['validation_messages']);
        $this->assertInternalType('array', $asArray['validation_messages']['foo']);
        $this->assertArrayHasKey('bar', $asArray['validation_messages']);
        $this->assertInternalType('array', $asArray['validation_messages']['bar']);
    }

    public function testReturnsApiProblemResponseIfParametersAreMissing()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 123,
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        return $response;
    }

    public function testAllowsValidationOfPartialSetsForPatchRequests()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('PATCH');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 123,
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
    }

    public function testFailsValidationOfPartialSetsForPatchRequestsThatIncludeUnknownInputs()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('PATCH');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 123,
            'baz' => 'who cares?',
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        return $response;
    }

    /**
     * @depends testFailsValidationOfPartialSetsForPatchRequestsThatIncludeUnknownInputs
     */
    public function testInvalidValidationGroupIs400Response($response)
    {
        $this->assertEquals(400, $response->getApiProblem()->status);
    }

    /**
     * @depends testReturnsNothingIfContentIsValid
     */
    public function testInputFilterIsInjectedIntoMvcEvent($event)
    {
        $inputFilter = $event->getParam('ZF\ContentValidation\InputFilter');
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
    }

    /**
     * @group zf-apigility-skeleton-43
     */
    public function testPassingOnlyDataNotInInputFilterShouldInvalidateRequest()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'first_name' => [
                'name' => 'first_name',
                'required' => true,
                'validators' => [
                    [
                        'name' => 'Zend\Validator\NotEmpty',
                        'options' => ['breakchainonfailure' => true],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([
            'foo' => 'abc',
            'bar' => 123,
        ]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        return $response;
    }

    public function httpMethodSpecificInputFilters()
    {
        return [
            'post-valid' => [
                'POST',
                ['post' => 123],
                true,
                'PostValidator',
            ],
            'post-invalid' => [
                'POST',
                ['post' => 'abc'],
                false,
                'PostValidator',
            ],
            'post-invalid-property' => [
                'POST',
                ['foo' => 123],
                false,
                'PostValidator',
            ],
            'patch-valid' => [
                'PATCH',
                ['patch' => 123],
                true,
                'PatchValidator',
            ],
            'patch-invalid' => [
                'PATCH',
                ['patch' => 'abc'],
                false,
                'PatchValidator',
            ],
            'patch-invalid-property' => [
                'PATCH',
                ['foo' => 123],
                false,
                'PatchValidator',
            ],
            'put-valid' => [
                'PUT',
                ['put' => 123],
                true,
                'PutValidator',
            ],
            'put-invalid' => [
                'PUT',
                ['put' => 'abc'],
                false,
                'PutValidator',
            ],
            'put-invalid-property' => [
                'PUT',
                ['foo' => 123],
                false,
                'PutValidator',
            ],
        ];
    }

    public function configureInputFilters($services)
    {
        $inputFilterFactory = new InputFilterFactory();
        $services->setService('PostValidator', $inputFilterFactory->createInputFilter([
            'post' => [
                'name' => 'post',
                'required' => true,
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
        ]));

        $services->setService('PatchValidator', $inputFilterFactory->createInputFilter([
            'patch' => [
                'name' => 'patch',
                'required' => true,
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
        ]));

        $services->setService('PutValidator', $inputFilterFactory->createInputFilter([
            'put' => [
                'name' => 'put',
                'required' => true,
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
        ]));
    }

    /**
     * @group method-specific
     * @dataProvider httpMethodSpecificInputFilters
     */
    public function testCanFetchHttpMethodSpecificInputFilterWhenValidating(
        $method,
        array $data,
        $expectedIsValid,
        $filterName
    ) {
        $services = new ServiceManager();
        $this->configureInputFilters($services);

        $listener = new ContentValidationListener([
            'Foo' => [
                'POST'  => 'PostValidator',
                'PATCH' => 'PatchValidator',
                'PUT'   => 'PutValidator',
            ],
        ], $services);

        $request = new HttpRequest();
        $request->setMethod($method);

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($data);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $result = $listener->onRoute($event);

        // Ensure input filter discovered is the same one we expect
        $inputFilter = $event->getParam('ZF\ContentValidation\InputFilter');
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertSame($services->get($filterName), $inputFilter);

        // Ensure we have a response we expect
        if ($expectedIsValid) {
            $this->assertNull($result);
            $this->assertNull($event->getResponse());
        } else {
            $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $result);
        }
    }

    public function testMergesFilesArrayIntoDataPriorToValidationWhenFilesArrayIsPopulated()
    {
        $validator = $this->getMock('Zend\InputFilter\InputFilterInterface');
        $services = new ServiceManager();
        $services->setService('FooValidator', $validator);

        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services);

        $files = new Parameters([
            'foo' => [
                'name' => 'foo.txt',
                'type' => 'text/plain',
                'size' => 1,
                'tmp_name' => '/tmp/foo.txt',
                'error' => UPLOAD_ERR_OK,
            ],
        ]);
        $data = [
            'bar' => 'baz',
            'quz' => 'quuz',
        ];
        $dataContainer = new ParameterDataContainer();
        $dataContainer->setBodyParams($data);

        $request = new HttpRequest();
        $request->setMethod('POST');
        $request->setFiles($files);

        $matches = new RouteMatch(['controller' => 'Foo']);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataContainer);

        $validator->expects($this->any())
            ->method('has')
            ->with($this->equalTo('FooValidator'))
            ->will($this->returnValue(true));
        $validator->expects($this->once())
            ->method('setData')
            ->with($this->equalTo(array_merge_recursive($data, $files->toArray())));
        $validator->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));

        $this->assertNull($listener->onRoute($event));
    }

    public function listMethods()
    {
        return [
            'PUT'    => ['PUT'],
            'PATCH'  => ['PATCH'],
        ];
    }

    /**
     * @dataProvider listMethods
     * @group 3
     */
    public function testCanValidateCollections($method)
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));

        // Create ContentValidationListener with rest controllers populated
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod($method);

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();

        $params = array_fill(0, 10, [
            'foo' => 123,
            'bar' => 'abc',
        ]);

        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    /**
     * @group 3
     * @dataProvider listMethods
     */
    public function testReturnsApiProblemResponseForCollectionIfAnyFieldsAreInvalid($method)
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod($method);

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => '123a',
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
    }

    /**
     * @group 3
     */
    public function testValidatesPatchToCollectionWhenFieldMissing()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('PATCH');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => 123,
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertNull($response);
    }

    /**
     * @group 3
     */
    public function testCanValidatePostedCollections()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => 123,
            'bar' => 'abc',
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertNull($response);
    }

    /**
     * @group 3
     */
    public function testReportsValidationFailureForPostedCollection()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => 'abc',
            'bar' => 123,
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(422, $response->getApiProblem()->status);
    }

    /**
     * @group 3
     */
    public function testValidatesPostedEntityWhenCollectionIsPossibleForService()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => 123,
            'bar' => 'abc',
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertNull($response);
    }

    /**
     * @group 3
     */
    public function testIndicatesInvalidPostedEntityWhenCollectionIsPossibleForService()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => 'abc',
            'bar' => 123,
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(422, $response->getApiProblem()->status);
    }

    /**
     * @group 29
     */
    public function testSaveFilteredDataIntoDataContainer()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => [
                'input_filter' => 'FooFilter',
                'use_raw_data' => false,
            ],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
        $this->assertEquals('abc', $dataParams->getBodyParam('foo'));
    }

    /**
     * @group 29
     */
    public function testShouldSaveFilteredDataWhenRequiredEvenIfInputFilterIsNotUnknownInputsCapable()
    {
        $services = new ServiceManager();
        $inputFilter = $this->getMock('Zend\InputFilter\InputFilterInterface');
        $inputFilter->expects($this->any())
            ->method('setData')
            ->willReturn($this->returnValue(null));
        $inputFilter->expects($this->any(''))
            ->method('isValid')
            ->will($this->returnValue(true));
        $inputFilter->expects($this->any(''))
            ->method('getValues')
            ->will($this->returnValue(['foo' => 'abc']));

        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $inputFilter);
        $listener = new ContentValidationListener([
            'Foo' => [
                'input_filter' => 'FooFilter',
                'use_raw_data' => false,
            ],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
        $this->assertEquals('abc', $dataParams->getBodyParam('foo'));
    }


    /**
     * @group 29
     */
    public function testSaveRawDataIntoDataContainer()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooFilter', 'use_raw_data' => true],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $listener->onRoute($event);
        $this->assertEquals(' abc ', $dataParams->getBodyParam('foo'));
    }

    /**
     * @group 29
     */
    public function testTrySaveUnknownData()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => [
                'input_filter' => 'FooFilter',
                'allows_only_fields_in_filter' => true,
                'use_raw_data' => false,
            ],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
            'unknown' => 'value'
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $result = $listener->onRoute($event);

        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $result);
        $apiProblemData = $result->getApiProblem()->toArray();
        $this->assertEquals(422, $apiProblemData['status']);
        $this->assertContains('Unrecognized fields', $apiProblemData['detail']);
    }

    /**
     * @group 29
     */
    public function testUnknownDataMustBeMergedWithFilteredData()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'filters' => [
                    ['name' => 'StringTrim'],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => [
                'input_filter' => 'FooFilter',
                'allows_only_fields_in_filter' => false,
                'use_raw_data' => false,
            ],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
            'unknown' => 'value'
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $result = $listener->onRoute($event);
        $this->assertNotInstanceOf('ZF\ApiProblem\ApiProblemResponse', $result);
        $this->assertEquals('abc', $dataParams->getBodyParam('foo'));
        $this->assertEquals('value', $dataParams->getBodyParam('unknown'));
    }


    /**
     * @group 29
     */
    public function testSaveUnknownDataWhenEmptyInputFilter()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooFilter', $factory->createInputFilter([]));
        $listener = new ContentValidationListener([
            'Foo' => [
                'input_filter' => 'FooFilter',
                'use_raw_data' => false,
            ],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = [
            'foo' => ' abc ',
            'unknown' => 'value'
        ];

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $listener->onRoute($event);

        $this->assertEquals($params, $dataParams->getBodyParams());
    }

    /**
     * @depends testFailsValidationOfPartialSetsForPatchRequestsForCollectionThatIncludeUnknownInputs
     */
    public function testInvalidValidationGroupOnCollectionPatchIs400Response($response)
    {
        $this->assertEquals(400, $response->getApiProblem()->status);
    }

    /**
     * @dataProvider listMethods
     * @group 19
     */
    public function testDoesNotAttemptToValidateAnEntityAsACollection($method)
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));

        // Create ContentValidationListener with rest controllers populated
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod($method);

        $matches = new RouteMatch([
            'controller' => 'Foo',
            'foo_id'     => uniqid(),
        ]);

        $dataParams = new ParameterDataContainer();

        $params = [
            'foo' => 123,
            'bar' => 'abc',
        ];

        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $this->assertNull($listener->onRoute($event));
        $this->assertNull($event->getResponse());
    }

    /**
     * @group 20
     */
    public function testEmptyPostShouldReturnValidationError()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ],
            'bar' => [
                'name' => 'bar',
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '/^[a-z]+/i'],
                    ],
                ],
            ],
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $request = new HttpRequest();
        $request->setMethod('POST');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams([]);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(422, $response->getApiProblem()->status);
    }

    /**
     * @group event
     */
    public function testTriggeredEventBeforeValidate()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ]
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $eventManager = new EventManager();
        $listener->setEventManager($eventManager);

        $runner = $this;
        $hasRun = false;
        $eventManager->attach(
            ContentValidationListener::EVENT_BEFORE_VALIDATE,
            function (MvcEvent $e) use ($runner, &$hasRun) {
                $runner->assertInstanceOf(
                    'Zend\InputFilter\InputFilterInterface',
                    $e->getParam('ZF\ContentValidation\InputFilter')
                );
                $hasRun = true;
            }
        );

        $request = new HttpRequest();
        $request->setMethod('PUT');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => '123',
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertTrue($hasRun);
        $this->assertEmpty($response);
    }

    /**
     * @group event
     */
    public function testTriggeredEventBeforeValidateReturnsApiProblemResponseFromApiProblem()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ]
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $eventManager = new EventManager();
        $listener->setEventManager($eventManager);

        $runner = $this;
        $hasRun = false;
        $eventManager->attach(
            ContentValidationListener::EVENT_BEFORE_VALIDATE,
            function (MvcEvent $e) use ($runner, &$hasRun) {
                $runner->assertInstanceOf(
                    'Zend\InputFilter\InputFilterInterface',
                    $e->getParam('ZF\ContentValidation\InputFilter')
                );
                $hasRun = true;
                return new ApiProblem(422, 'Validation failed');
            }
        );

        $request = new HttpRequest();
        $request->setMethod('PUT');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => '123',
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertTrue($hasRun);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(422, $response->getApiProblem()->status);
        $this->assertEquals('Validation failed', $response->getApiProblem()->detail);
    }


    /**
     * @group event
     */
    public function testTriggeredEventBeforeValidateReturnsApiProblemResponseFromCallback()
    {
        $services = new ServiceManager();
        $factory  = new InputFilterFactory();
        $services->setService('FooValidator', $factory->createInputFilter([
            'foo' => [
                'name' => 'foo',
                'validators' => [
                    ['name' => 'Digits'],
                ],
            ]
        ]));
        $listener = new ContentValidationListener([
            'Foo' => ['input_filter' => 'FooValidator'],
        ], $services, [
            'Foo' => 'foo_id',
        ]);

        $eventManager = new EventManager();
        $listener->setEventManager($eventManager);

        $runner = $this;
        $hasRun = false;
        $eventManager->attach(
            ContentValidationListener::EVENT_BEFORE_VALIDATE,
            function (MvcEvent $e) use ($runner, &$hasRun) {
                $runner->assertInstanceOf(
                    'Zend\InputFilter\InputFilterInterface',
                    $e->getParam('ZF\ContentValidation\InputFilter')
                );
                $hasRun = true;
                return new ApiProblemResponse(new ApiProblem(422, 'Validation failed'));
            }
        );

        $request = new HttpRequest();
        $request->setMethod('PUT');

        $matches = new RouteMatch(['controller' => 'Foo']);

        $params = array_fill(0, 10, [
            'foo' => '123',
        ]);

        $dataParams = new ParameterDataContainer();
        $dataParams->setBodyParams($params);

        $event   = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch($matches);
        $event->setParam('ZFContentNegotiationParameterData', $dataParams);

        $response = $listener->onRoute($event);
        $this->assertTrue($hasRun);
        $this->assertInstanceOf('ZF\ApiProblem\ApiProblemResponse', $response);
        $this->assertEquals(422, $response->getApiProblem()->status);
        $this->assertEquals('Validation failed', $response->getApiProblem()->detail);
    }
}
