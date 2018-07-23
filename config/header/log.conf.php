<?php
/**
 *
 * To set the platform logger use the logger service to specify a PSR3 compliant logger
 *
 * To use monolog logger, taoMonolog wrapper can be set as following
 *
 * `return new oat\oatbox\log\LoggerService(array(
 *     'logger' => array(
 *         'class' => \oat\oatbox\log\logger\TaoMonolog::class,
 *         'options' => array(
 *             'name' => 'tao',
 *             'handlers' => array(
 *                  [...]
 *             ),
 *         )
 *     )
 * ));`
 *
 * Examples :
 *
`return new oat\oatbox\log\LoggerService(array(
    'logger' => array(
        'class' => \oat\oatbox\log\logger\TaoMonolog::class,
        'options' => array(
            'name' => 'tao',
            'handlers' => array(

                // Send log to a stream, could be a file or a daemon
                array(
                    'class' => \Monolog\Handler\StreamHandler::class,
                    'options' => array(
                        '/var/www/tao/package-tao/test-log.log',
                        \Monolog\Logger::DEBUG
                    ),
                ),

                // Send log to web console
                array(
                    'class' => \Monolog\Handler\BrowserConsoleHandler::class,
                    'options' => array(
                        \Monolog\Logger::INFO
                    ),
                ),

                // Send log to Slack channel
                array(
                    'class' => \Monolog\Handler\SlackWebhookHandler::class,
                    'options' => array(
                        'https://hooks.slack.com/services/XXXXXX/XXXXXX/XXXXXX',
                        '#test',
                        'tao-bot',
                    ),
                ),

                // Send log to UDP port
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
                                \Monolog\Logger::WARNING
                            )
                        ),
                        array(
                            'class' => \Monolog\Processor\MemoryUsageProcessor::class,
                        ),
                        array(
                            'class' => \Monolog\Processor\MemoryPeakUsageProcessor::class,
                        )
                    )
                )
            ),

            // Processors at logger level
            'processors' => array(

                // Apply PSR3 rules to message
                array(
                    'class' => \Monolog\Processor\PsrLogMessageProcessor::class
                ),

                // Add UID to logger to identify same logs to different handlers
                array(
                    'class' => \Monolog\Processor\UidProcessor::class,
                    'options' => array (
                        24
                    )
                ),
            )

        )
    )
));`
 *
 * To use the old logger, a wrapper exists:
 *
 * Examples:
`
return new oat\oatbox\log\LoggerService(array(
    'logger' => new oat\oatbox\log\logger\TaoLog(array(
        'appenders' => array(

            //Example of a UDP Appender
            array(
                'class' => 'UDPAppender',
                'host' => '127.0.0.1',
                'port' => 5775,
                'threshold' => 1,
                'prefix' => 'tao'
            ),

            // Example of a Single File Appender
            array(
                'class' => 'SingleFileAppender',
                'threshold' => 4,
                'max_file_size' => 1048576, // 1Mb
                'rotation-ratio' => .5,
                'file' => dirname(__FILE__) . '/../../log/error.txt',
                'format' => '%m',
                'prefix' => '[dev]'
            ),

            // Example of a Multiple File Appender with archiving
            array(
                'class' => 'ArchiveFileAppender',
                'mask' => 62, // 111110
                'tags' => array('GENERIS', 'TAO'),
                'file' => '/var/log/tao/debug.txt',
                'directory' => '/var/log/tao/',
                'max_file_size' => 10000000,
                'prefix' => '[dev]'
            ),

        )
    ))
));
`
 *
 * Old and new logger can be used in same time with LoggerAggregator object
 * Example:
`
return new oat\oatbox\log\LoggerService(array(
    'logger' => new \oat\oatbox\log\LoggerAggregator(
        array(

            new oat\oatbox\log\logger\TaoLog(array(
                'appenders' => array(
                    array(
                        'class' => 'UDPAppender',
                        'host' => '127.0.0.1',
                        'port' => 5775,
                        'threshold' => 1,
                        'prefix' => 'tao'
                    )
                )
            )),

            new \oat\oatbox\log\logger\TaoMonolog(array(
                'name' => 'tao',
                'handlers' => array(

                    // Send log to a stream, could be a file or a daemon
                    array(
                        'class' => \Monolog\Handler\StreamHandler::class,
                        'options' => array(
                            '/var/www/tao/package-tao/test-log.log',
                            \Monolog\Logger::DEBUG
                        ),
                    ),

                )
            ))

        )
    )
));
`
 *
 * TAO Fluentd Logger config with backtrace on error
 * Example:
`
return new oat\oatbox\log\LoggerService(array(
    'logger' => array(
        'class' => 'oat\\oatbox\\log\\logger\\TaoMonolog',
        'options' => array(
            'name' => 'tao',
            'handlers' => array(
                array(
                    'class' => \oat\oatbox\log\logger\handler\FluentdHandler::class,
                    'options' => [
                        new \Fluent\Logger\FluentLogger(
                            \Fluent\Logger\FluentLogger::DEFAULT_ADDRESS,
                            \Fluent\Logger\FluentLogger::DEFAULT_LISTEN_PORT
                        )
                    ],
                    'processors' => array(
                        array(
                            'class' => \oat\oatbox\log\logger\processor\EnvironmentProcessor::class,
                            'options' => array(
                                100, // Monolog level e.q. debug
                            )
                        ),
                        array(
                            'class' => \oat\oatbox\log\logger\processor\BacktraceProcessor::class,
                            'options' => array(
                                300, // Monolog level e.q. error
                                true,
                            )
                        )
                    ),
                    'formatter' => array(
                        'class' => \oat\oatbox\log\logger\formatter\TaoJsonLogFormatter::class,
                    ),
                ),
            ),
        )
    )
));
`
 *
 **/