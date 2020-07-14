<html>
<!-- server-side transaction sale call -->
<?php
require_once("../includes/head.php");
require_once("../includes/braintree_init.php");
$txnID = $_GET["id"];
$amount = $_POST["amount"];
echo $amount;
echo $txnID;
$title = "title";
$message = "Message: ";
//refund call
$result = $gateway->transaction()->refund($txnID, $amount);
//if customer was created, create a transaction
// then if transaction sale was success, print the info
  if ($result->success) {
    $title = "Success!";
    $message = "<th>Transaction ID</th>
    <th>Status</th>
    <th>Amount</th>
    <tr>
      <td> {$result->transaction->id} </td>
      <td> {$result->transaction->status} </td>
      <td> {$result->transaction->amount} </td>
    </tr>";

  } elseif (empty($result->errors->deepAll())) {
// But if the transaction was created and failed, print the failed info
    $title = "Failure :(";
      $message = "
      <th>Transaction ID</th>
      <th>Status</th>
      <th>Amount</th>
      <tr>
        <td> {$result->transaction->id} </td>
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
  </div>
</div>
    </body>
<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
