<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<!-- Sale Call -->
<?php $result = $gateway->transaction()->sale([
  'amount' => '3001',
  'paymentMethodNonce' => 'fake-valid-nonce',
  'options' => [
    'submitForSettlement' => True
  ]
]);
$txnID = $result->transaction->id;
// sleep(1.5);
?>
<?php
sleep(1.5);
$transaction = $gateway->testing()->settle($result->transaction->id);
get_class($transaction)?>
<body style="font-family:Verdana;">
  <div style="overflow:auto;">
    <div class="menu">
      <?php require_once("../includes/nav.html"); ?>
    </div>
    <div class="main">

      <h2>Updates to Braintree Refund Processes</h2>
      <h3>Overview</h3>
      <p>
        To keep up with industry standards in transaction processing, Braintree is adjusting the manner in which refunds are processed. Non-refund transactions follow the Transaction Lifecycle (Authorized, submitted for settlement, etc.) but at this time refunds do not, at least not in the same way.
      </p>
      <p>
        When this new process is fully implemented, merchants can expect refunds to follow a similar lifecycle. Refunds will be preceded by authorizations to allow the card issuer to weigh in before you send a refund to a cardholder. This means that, much like a transaction, refunds can be declined by the issuer.
      </p>
      <p>
        This change should affect the following card brands:
      </p>
      <ul>
        <li>Visa</li>
        <li>Mastercard</li>
        <li>Discover</li>
      </ul>
      <p>
        You can find more fine grained information about this project, the timeline, etc. in the excellent <a href="https://internal.braintreepayments.com/display/DD/Refund+Authorizations">Refund Authorizations</a> wiki page.
      </p>
      <h3>Why is this important to know?</h3>
      <p>
        Beyond it being a mandate by these card brands, our merchants can enjoy several key benefits of such a process.
      </p>
      <ul>
        <li>Improved customer experience due to pending refunds showing up on their statement in real time</li>
        <li>Merchants have instant access to the bank’s decision to approve or decline the refund</li>
        <li>Less back and forth regarding ARNs and refund investigation in general</li>
      </ul>
      <h3>How can I get prepared early?</h3>
      <p>
        You’re in luck! Early testing for this can be done in the Braintree Sandbox using the Control Panel or the API. Most resources point to some changes coming to our API beyond what is available in sandbox at this time, but the following flow can be used to get a basic idea of how this may look in the future. Your server must be using the latest Braintree SDK to take advantage of this feature.
      </p>
      <ol>
        <li>Create a successful transaction with an amount between $3001 and $4000.99.</li>
        <li>Settle the transaction by either submitting it for settlement and waiting, or auto-setting it via the API. </li>
        <li>Refund the transaction for a decline amount, such as 2004.</li>
      </ol>
      <h3>Demonstration</h3>
      <p>Want to test in your own sandbox? Plug the following script into your PHP Polyglot!</p>
        <button data-toggle="collapse" data-target="#c2" class="button">Show/Hide Polyglot example</button>
        <div id="c2" class="collapse">
    <pre class="code">
      <code id="decode" class="prettyprint">$result = $gateway->transaction()->sale([
  'amount' => '3001',
  'paymentMethodNonce' => 'fake-valid-nonce',
  'options' => [
    'submitForSettlement' => True
  ]
]);
if ($result->success) {
  echo "Transaction Successful for ID ";
  echo $result->transaction->id . "\n";
  $settledResult = $gateway->testing()->settle($result->transaction->id);
  echo "Refunding transaction . . ." . "\n";
  $result = $gateway->transaction()->refund($result->transaction->id, 2004);
  echo $result->message . "\n";
} else {
  echo "Error";
  print_r($result->message);
};
      </code>
    </pre>
    <br>
        </div>
      <p>This demonstration uses the following transaction call when the page is loaded:</p>
      <pre class="code">
        <code class="prettyprint">//Create the sale
$result = $gateway->transaction()->sale([
  'amount' => '3001',
  'paymentMethodNonce' => 'fake-valid-nonce',
  'options' => [
    'submitForSettlement' => True
  ]
]);
//define the transaction ID
$transactionId = $result->transaction->id;
//Fast settle the transaction
$settledResult = $gateway->testing()->settle($transactionId);</code></pre>
    <p>And the following to initiate a refund when the amount is entered, and the button is clicked:</p>
    <pre class="code">
      <code class="prettyprint">$refund = $gateway->transaction()->refund($transactionId, USER_DEFINED_AMOUNT);</code></pre>
      <br>
      <div align="center">
      <form action="/refund.php?id=<?php echo $txnID ?>" method="post">
        <input onchange="statusFind()"class="field" id="amount" name="amount" required=True placeholder="amount">
        <input class="button" type="submit" value="Refund">
      </form>
      </div>
      <div align="center">
        This amount will yield the status:
        <p id="futureStatus"></p>
      </div>
      <script>

      function statusFind(){
        var num = document.getElementById("amount").value;
        var prediction = document.getElementById("futureStatus").innerHTML;
        var status = "nothin"
        console.log(num);
        if (num>0 && num<1999.99) {
          status = "success"
          console.log(status);
        }
        else {
          status = "failure"
          console.log(status);
        }
        if (status == "failure") {
          document.getElementById("futureStatus").innerHTML = "Failed Refund";
        }
        else {
          document.getElementById("futureStatus").innerHTML = "Successful refund";
        }
      }
      </script>
    </div>
    <div class="right">
      <!-- empty div for possible content. -->
      <p></p>
    </div>
  </div>

  <div style="text-align:center;padding:10px;margin-top:7px;"> <p><br><br><br><br>The Demos on this page use the following merchant ID: tt8srtpp8yfgfghp</p><p>{•̃̾_•̃̾}</p> </div>
  </html>
