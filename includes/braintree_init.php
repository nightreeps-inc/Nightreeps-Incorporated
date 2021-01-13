<?php
require_once("../vendor/autoload.php");

$gateway = new Braintree\Gateway([
  'environment' => 'sandbox',
  'merchantId' => 'tt8srtpp8yfgfghp',
  'publicKey' => 't93rv9zd344vn2td',
  'privateKey' => '43b01fc0041eac2b913a2f251d3e1e48'
]);

// Nicks AIB Sandbox
// $gateway = new Braintree\Gateway([
//   'environment' => 'sandbox',
//   'merchantId' => 'cdrsbfyxsb24hz4v',
//   'publicKey' => 'xtyrsdhg765vs253',
//   'privateKey' => '7ec92acf803134f45848e6562377d58c'
// ]);
//Nicks wells
// $gateway = new Braintree\Gateway([
//   'environment' => 'sandbox',
//   'merchantId' => 'rmcvst6f8dwrgxch',
//   'publicKey' => '9hgnp4tnndvyq2sn',
//   'privateKey' => '0e6bc1e594be1172aa1ea9e89d5cd735'
// ]);
