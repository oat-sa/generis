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