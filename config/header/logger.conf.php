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
*/