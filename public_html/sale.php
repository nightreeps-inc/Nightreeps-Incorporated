<html>
<!-- server-side transaction sale call -->
<?php
require_once("../includes/head.php");
require_once("../includes/braintree_init.php");
//declare variables from le post
$nonce = $_POST["nonce"];
$amount = $_POST["amount"];
$name = $_POST["name"];
$postalCode = $_POST["postalCode"];
$firstLast = explode(" ", $name);
$title = "title";
$message = "Message: ";
$result = $gateway->customer()->create([
  'firstName' => $firstLast[0],
  'lastName' => $firstLast[1],
  'paymentMethodNonce' => $nonce,
]);
//if customer was created, create a transaction
if ($result->success){
  $customerID = $result->customer->id;
  foreach ($result->customer->creditCards as $card){
    $expiry = $card->expirationDate;
  };
  $result = $gateway->transaction()->sale([
    'amount' => $amount,
    'merchantAccountId' => 'MindSapling-CAD',
    'options' => [
      'submitForSettlement' => True
    ],
    'customerId'=> $customerID,
    'customer' => [
      'firstName' => $firstLast[0],
      'lastName' => $firstLast[1]
    ],
    'billing' => [
      'postalCode' => $postalCode
    ]
  ]);

// then if transaction sale was success, print the info
  if ($result->success) {
    $transaction = $gateway->testing()->settle($result->transaction->id);
    $txnID = $result->transaction->id;
    $title = "Success!";
    $message = "<th>Transaction ID</th>
    <th>Customer ID</th>
    <th>Status</th>
    <th>Amount</th>
    <th>Expiration Date</th>
    <tr>
      <td> {$result->transaction->id} </td>
      <td> {$customerID} </td>
      <td> {$result->transaction->status} </td>
      <td> {$result->transaction->amount} </td>
      <td> {$expiry} </td>
    </tr>";

  } elseif (empty($result->errors->deepAll())) {
// But if the transaction was created and failed, print the failed info
    $title = "Failure :(";
      $message = "
      <th>Transaction ID</th>
      <th>Customer ID</th>
      <th>Status</th>
      <th>Amount</th>
      <tr>
        <td> {$result->transaction->id} </td>
        <td> {$customerID} </td>
        <td> {$result->transaction->status} </td>
        <td> {$result->transaction->amount} </td>
      </tr>";
    }else{
//otherwise no transaction was created, which means there were errors to display. Iterate through those errors.
      $title = "Validation error(s)";
      $message = "<th>Attribute</th>
            <th>Code</th>
            <th>Message</th>";
      foreach($result->errors->deepAll() AS $error) {
        $message .= "<tr><td>" . $error->attribute . "</td><td>" . $error->code . "</td><td>" . $error->message . "</td></tr>";
    }
  }
} else{
//If none of the above happened, verification failed
  $title = "Failed Verification";
  $message = "<th>Verification ID</th>
  <th>Status</th>
  <th>Message</th>
  <th>Verification ID</th>
  <tr>
    <td> {$result->verification->amount} </td>
    <td> {$result->verification->status} </td>
    <td> {$result->message} </td>
    <td> {$result->verification->id} </td>
  </tr>";
}
?>

<body style="font-family:Verdana;">
<div style="overflow:auto">
<div class="menu">
    <?php require_once("../includes/nav.html"); ?>
</div>
  <div class="main">
    <body>
      <h2><?php echo $title;?></h2>
      <table><?php echo $message;?></table>
      <div align="center">
        <br>
        <form action="/refund.php?id=<?php echo $txnID ?>" method="post">
          <input class="button" type="submit" value="Refund">
          <input id="amount" name="amount" required=True placeholder="amount">
        </form>
      </div>
  </div>
</div>
    </body>
<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
