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
                    'class' => \Monolog\Handler\BrowserConsoleHandler::class,
                    'options' => array(
                        \Monolog\Logger::DEBUG
                    ),
                ),

                array(
                    'class' => \Monolog\Handler\SlackHandler::class,
                    'options' => array(
                        'xoxp-5156076911-5156636951-6084570483-7b4fb8',
                        '#general',
                        'ChhiwatBot',
                        null,
                        null,
                        \Monolog\Logger::DEBUG
                    ),
                ),

                array(
                    'class' => \Monolog\Handler\SlackHandler::class,
                    'options' => array(
                        'https://hooks.slack.com/services/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',
                        '#test',
                        'ChhiwatBot',
                    ),
                ),

                array(
                    'class' => \Monolog\Handler\SyslogUdpHandler::class,
                    'options' => array(
                        '127.0.0.1',
                        '5775'
                    ),
//                    'processors' => array(
//                        array(
//                            'class' => \oat\oatbox\log\logger\processor\BacktraceProcessor::class,
//                            'options' => array(
//                                \Monolog\Logger::DEBUG
//                            )
//                        )
//                    ),
//                    'formatter' => array(
//                        'class' => \Monolog\Formatter\FluentdFormatter::class,
//                        'options' => true
//                    )
                )
            )
        )
    )
));