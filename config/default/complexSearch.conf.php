<?php
/**
 * Default config header
 *
 * To replace this add a file /home/christophe/Projects/tao/generis/config/header/complexSearch.conf.php
 */

return new oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService(array(
    'shared' => array(
        'search.query.query' => false,
        'search.query.builder' => false,
        'search.query.criterion' => false,
        'search.tao.serialyser' => false,
        'search.tao.result' => false
    ),
    'invokables' => array(
        'search.query.query' => '\\oat\\search\\Query',
        'search.query.builder' => '\\oat\\search\\QueryBuilder',
        'search.query.criterion' => '\\oat\\search\\QueryCriterion',
        'search.driver.postgres' => '\\oat\\search\\DbSql\\Driver\\PostgreSQL',
        'search.driver.mysql' => '\\oat\\search\\DbSql\\Driver\\MySQL',
        'search.driver.tao' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\driver\\TaoSearchDriver',
        'search.tao.serialyser' => '\\oat\\search\\DbSql\\TaoRdf\\UnionQuerySerialyser',
        'search.factory.query' => '\\oat\\search\\factory\\QueryFactory',
        'search.factory.builder' => '\\oat\\search\\factory\\QueryBuilderFactory',
        'search.factory.criterion' => '\\oat\\search\\factory\\QueryCriterionFactory',
        'search.tao.gateway' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\GateWay',
        'search.tao.result' => '\\oat\\generis\\model\\kernel\\persistence\\smoothsql\\search\\TaoResultSet'
    ),
    'abstract_factories' => array(
        '\\oat\\search\\Command\\OperatorAbstractfactory'
    ),
    'services' => array(
        'search.options' => array(
            'table' => 'statements',
            'driver' => 'taoRdf'
        )
    )
));
