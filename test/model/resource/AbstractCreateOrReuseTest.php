<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\generis\test\model\resource;

use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\persistence\smoothsql\search\GateWay;
use oat\generis\model\kernel\persistence\smoothsql\search\TaoResultSet;
use oat\generis\model\resource\AbstractCreateOrReuse;
use oat\generis\model\resource\CreateOrReuseInterface;
use oat\generis\model\resource\exception\DuplicateResourceException;
use oat\oatbox\service\ServiceManager;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\ResultSetInterface;
use oat\search\Query;
use oat\search\QueryCriterion;
use oat\tao\test\TaoPhpUnitTestRunner;

/**
 * Description of AbstractCreateOrReuseTest
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class AbstractCreateOrReuseTest extends TaoPhpUnitTestRunner {
    
    public function testGetSearchService() {
        
        $instance       = $this->getMockForAbstractClass(
                                AbstractCreateOrReuse::class,
                                [],
                                '',
                                false,
                                false,
                                true,
                                ['getServiceManager']
                            );
        
        $searchMock     = $this->prophesize(ComplexSearchService::class)->reveal();
        
        $modelProphet = $this->prophesize(\core_kernel_persistence_smoothsql_SmoothModel::class);
        $modelProphet->getSearchInterface()
                ->willReturn($searchMock);
        
        $modelMock = $modelProphet->reveal();
        
        $instance->expects($this->once())->method('getModel')
                ->willReturn($modelMock);
        
        $this->assertSame($searchMock, $this->invokeProtectedMethod($instance, 'getSearchService'));
    }
    
    public function testSearchResource() {
        
        $instance       = $this->getMockForAbstractClass(
                                AbstractCreateOrReuse::class,
                                [],
                                '',
                                false,
                                false,
                                true,
                                ['getGateway' , 'getSearchService']
                            );
        
        $fixtureValues    = 
                [
                    'http://www.w3.org/2000/01/rdf-schema#label'                     => 'media 1',
                    'http://www.tao.lu/Ontologies/TAOMedia.rdf#IdentificationString' => 'm0123456789'
                ];
        
        $fixtureType      = 'http://www.tao.lu/Ontologies/TAOMedia.rdf#Media';
        
        $fixturePredicate = 
                [
                    'http://www.w3.org/2000/01/rdf-schema#label',
                    'http://www.tao.lu/Ontologies/TAOMedia.rdf#IdentificationString'
                ];
        
        $resultSetmock = $this->prophesize(ResultSetInterface::class)->reveal();
        
        $criterionMock = $this->getMock(QueryCriterion::class , ['equals']);
        
        $criterionMock->expects($this->exactly(count($fixturePredicate)))
                ->method('equals')
                ->withConsecutive(['media 1'] , ['m0123456789'])
                ->willReturn($criterionMock);
        
        $queryProphet     = $this->prophesize(Query::class);
        $queryProphet->add('http://www.w3.org/2000/01/rdf-schema#label')->willReturn($criterionMock);
        $queryProphet->add('http://www.tao.lu/Ontologies/TAOMedia.rdf#IdentificationString')->willReturn($criterionMock);
        $queryMock        = $queryProphet->reveal();
        
        $builderProphet   = $this->prophesize(QueryBuilderInterface::class);
        
        $builderProphet->newQuery()->willReturn($queryMock);
        $builderProphet->setCriteria($queryMock)->willReturn($builderProphet);
        $builderProphet->setLimit(1)->willReturn($builderProphet);
        $builderMock      = $builderProphet->reveal();
        
        $gateWayProphet   = $this->prophesize(GateWay::class);
        $gateWayProphet->query()->willReturn($builderMock);
        $gateWayProphet->search($builderMock)->willReturn($resultSetmock);
        $gateWayMock      = $gateWayProphet->reveal();
        
        $searchProphet    = $this->prophesize(ComplexSearchService::class);
        $searchProphet->getGateway()->willReturn($gateWayMock);
        $searchProphet->searchType($builderMock , $fixtureType , true)
                ->willReturn($builderMock);
        $searchMock       = $searchProphet->reveal();
        
        $this->setInaccessibleProperty($instance, 'type', $fixtureType);
        $this->setInaccessibleProperty($instance, 'uniquePredicate', $fixturePredicate);
        
        $instance->expects($this->once())
                ->method('getSearchService')
                ->willReturn($searchMock);
        
        
        
        $this->assertSame($resultSetmock, $this->invokeProtectedMethod($instance , 'searchResource' , [$fixtureValues]));
    }
    
    public function hasResourceProvider() {
        
        return 
        [
            [0 , false, false],
            [1 , true , false],
            [3 , true , true],
        ];
    }

    /**
     * @dataProvider hasResourceProvider
     * @param int $count
     * @param boolean $expected
     * @param boolean $exception
     */
    public function testHasResource($count, $expected , $exception) {
        
        $fixtureValues    = 
                [
                    'http://www.w3.org/2000/01/rdf-schema#label'                     => 'media 1',
                    'http://www.tao.lu/Ontologies/TAOMedia.rdf#IdentificationString' => 'm0123456789'
                ];
        
        $instance       = $this->getMockForAbstractClass(
                                AbstractCreateOrReuse::class,
                                [],
                                '',
                                false,
                                false,
                                true,
                                ['searchResource']
                            );
        
        if($exception) {
            $this->setExpectedException(DuplicateResourceException::class);
        }
        
        $resultSetProphet = $this->prophesize(TaoResultSet::class);
        $resultSetProphet->getTotalCount()->willReturn($count);
        
        $resultMock       = $resultSetProphet->reveal();
        
        $instance->expects($this->once())->method('searchResource')
                ->with($fixtureValues)
                ->willReturn($resultMock);

        $this->assertSame($expected, $instance->hasResource($fixtureValues));
        
    }
    
    public function getResourceProvider() {
        
        return 
        [
            [0 , false],
            [1 , false],
            [3 , true],
        ];
    }

    /**
     * @dataProvider getResourceProvider
     * @param int $count
     * @param boolean $expected
     * @param boolean $exception
     */
    public function testGetResource($count , $exception) {
        
        $fixtureValues    = 
                [
                    'http://www.w3.org/2000/01/rdf-schema#label'                     => 'media 1',
                    'http://www.tao.lu/Ontologies/TAOMedia.rdf#IdentificationString' => 'm0123456789'
                ];
        
        $instance       = $this->getMockForAbstractClass(
                                AbstractCreateOrReuse::class,
                                [],
                                '',
                                false,
                                false,
                                true,
                                ['searchResource' , 'createResource' ]
                            );
        
        $prophetResource = $this->prophesize(\core_kernel_classes_Resource::class);
        $expected = $prophetResource->reveal();
        
        if($exception) {
            $this->setExpectedException(DuplicateResourceException::class);
        }
        
        $resultSetProphet = $this->prophesize(TaoResultSet::class);
        $resultSetProphet->getTotalCount()->willReturn($count);
        
        
        if($count === 1 ) {
            $resultSetProphet->current()->willReturn($expected);
        }
        
        if($count === 0 ) {
            $instance->expects($this->once())->method('createResource')
                ->with($fixtureValues)
                ->willReturn($expected);
        }
        
        $resultMock       = $resultSetProphet->reveal();
        
        $instance->expects($this->once())->method('searchResource')
                ->with($fixtureValues)
                ->willReturn($resultMock);

        $this->assertSame($expected, $instance->getResource($fixtureValues));
        
    }
    
}
