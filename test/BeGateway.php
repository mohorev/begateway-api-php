<?php

date_default_timezone_set('UTC');

echo "Running the BeGateway PHP bindings test suite.\n" .
    "If you're trying to use the PHP bindings you'll probably want " .
    "to require('src/BeGateway.php'); instead of this file\n\n" .
    "Setup the env variable LOG_LEVEL=DEBUG for more verbose output\n";

$ok = @include_once(dirname(__FILE__) . '/simpletest/autorun.php');
if (!$ok) {
    echo "MISSING DEPENDENCY: The BeGateway API test cases depend on SimpleTest. " .
        "Download it at <http://www.simpletest.org/>, and either install it " .
        "in your PHP include_path or put it in the test/ directory.\n";
    exit(1);
}

require_once(dirname(__FILE__) . '/../src/BeGateway.php');
// Throw an exception on any error
function exception_error_handler($errno, $errstr, $errfile, $errline)
{
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}

set_error_handler('exception_error_handler');
error_reporting(E_ALL | E_STRICT);

require_once(dirname(__FILE__) . '/../src/BeGateway.php');


$log_level = getenv('LOG_LEVEL');

//if ($log_level == 'DEBUG') {
//    \BeGateway\Logger::getInstance()->setLogLevel(\BeGateway\Logger::DEBUG);
//} else {
//    \BeGateway\Logger::getInstance()->setLogLevel(\BeGateway\Logger::INFO);
//}

require_once(dirname(__FILE__) . '/BeGateway/TestCase.php');
require_once(dirname(__FILE__) . '/BeGateway/CustomerTest.php');

require_once(dirname(__FILE__) . '/BeGateway/AuthorizationOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/PaymentOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/CaptureOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/VoidOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/RefundOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/CreditOperationTest.php');
require_once(dirname(__FILE__) . '/BeGateway/GetPaymentTokenTest.php');
require_once(dirname(__FILE__) . '/BeGateway/QueryByUidTest.php');
require_once(dirname(__FILE__) . '/BeGateway/QueryByTrackingIdTest.php');
require_once(dirname(__FILE__) . '/BeGateway/QueryByPaymentTokenTest.php');
require_once(dirname(__FILE__) . '/BeGateway/WebhookTest.php');
require_once(dirname(__FILE__) . '/BeGateway/GatewayExceptionTest.php');
require_once(dirname(__FILE__) . '/BeGateway/CreditCardTokenizationTest.php');
