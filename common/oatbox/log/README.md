# Log

Here you can find help on how to log information in TAO.

More docs [here](./logger/readme.md).

## Advanced Log

The **advanced log** implements `PSR-3` and is useful to add extra information to the log, such as:

- User data.
- Exception trace.
- The HTTP method and endpoint called.
- ...and more.

Check the [AdvancedLogger](./logger/AdvancedLogger.php) for more details.

**IMPORTANT**: The AdvancedLogger is already available from the DI container. 

### Example:

It will log the exception details based on custom `context parameters`.

```php
use oat\oatbox\log\logger\AdvancedLogger;
use \oat\oatbox\log\logger\extender\ContextExtenderInterface;

$someExceptionCaught = new Exception(
    'Error 2',
    2,
    new Exception(
        'Error 1',
        1
    )
);

/** 
 * You can also add extra context information for your logs by providing context extenders.
 * 
 * @var ContextExtenderInterface $extraContextExtender
 * @var AdvancedLogger $logger 
 */
$logger->addContextExtender($extraContextExtender);
$logger->critical(
    'My messaged',
    [
        ContextExtenderInterface::CONTEXT_EXCEPTION => $someExceptionCaught
    ]
);
```
