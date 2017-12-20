<?php
/**
 * The logger service is used to set a default logger for the platform
 * It takes option to set logger
 *
 *
 * return new \oat\oatbox\log\LoggerService(array(
 *     'logger' => new Psr3LoggerInterface()
 * ));
 *
 * return new \oat\oatbox\log\LoggerService(array(
 *     'logger' => new \oat\oatbox\log\LoggerAggregator(array(
 *          new Psr3LoggerInterface(),
 *          new AnotherPsr3LoggerInterface()
 *      ))
 * ));
 *
 *
 * ));
 */
return new oat\oatbox\log\LoggerService(array(
    'logger' => array(
        'class' => \oat\oatbox\log\logger\TaoMonolog::class,
        'options' => array(
            'name' => 'tao',
            'handlers' => array(

                array(
                    'class' => \Monolog\Handler\StreamHandler::class,
                    'options' => array(
                        '/var/www/html/tao/package-tao/test-log.log',
                        \Monolog\Logger::DEBUG
                    ),
                ),

                array(
                    'class' => \Monolog\Handler\SyslogUdpHandler::class,
                    'options' => array(
                        '127.0.0.1',
                        '5775'
                    ),
                    'processors' => array(
                        array(
                            'class' => \oat\oatbox\log\logger\processor\BacktraceProcessor::class,
                            'options' => array(
                                \Monolog\Logger::DEBUG
                            )
                        )
                    ),
                    'formatter' => array(
                        'class' => \Monolog\Formatter\FluentdFormatter::class,
                        'options' => true
                    )
                )
            )
        )
    )
));