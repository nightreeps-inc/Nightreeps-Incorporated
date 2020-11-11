<?php
// require_once("../includes/head.php");
require_once("../includes/braintree_init.php");

$customerID = $_POST['theID'];

$customer = $gateway->customer()->find($customerID);
echo json_encode($customer->paymentMethods[0]->token);
?>
