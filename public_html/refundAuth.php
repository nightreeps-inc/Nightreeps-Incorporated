<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
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
        You’re in luck! Early testing for this can be done in Sandbox. Most resources point to some changes coming to our API beyond what is available in sandbox at this time, but the following flow can be used to get a basic idea of how this may look in the future.
      </p>

    <script>var client_token = "<?php echo($gateway->clientToken()->generate(
      ['customerId' => 242967293,
      'merchantAccountId' => 'MindSapling-CAD']
    ));?>"
    </script>
    <form method="post" id="details" action="/sale.php">
      <label for="amount" class="hosted-fields--label">Amount <a href="https://developers.braintreepayments.com/reference/general/testing/php#transaction-amounts" target="_blank">(use testing values)</a></label>
      <input type="number" class="hosted-field" id="amount" name="amount" placeholder="100.00"required>

      <label for="name" class="hosted-fields--label">Name</label>
      <input type="text" class="hosted-field" id="name" name="name" placeholder="Johnny Utah"required>

      <label for="postalCode" class="hosted-fields--label">Postal Code</label>
      <input type="text" class="hosted-field" id="postalCode" name="postalCode" placeholder="60410"required>

      <label for="card-number" class="hosted-fields--label">Card Number</label>
      <div id="card-number" class="hosted-field"></div>

      <label for="cvv" class="hosted-fields--label">CVV</label>
      <div id="cvv" class="hosted-field"></div>

      <label for="expiration-date" class="hosted-fields--label">Expiration Date</label>
      <div id="expiration-date" class="hosted-field"></div>

      <input class="button" type="submit" value="Request Payment Method" disabled />
      <p><div>Fill out the form and click the button to get a nonce! Click it again to make a transaction.</div></p>
      <div id="nonce-display" name="nonce-display" hidden></div>
      <input type="hidden" id="nonce" name="nonce" />
    </form>
    <script>
      var form = document.querySelector('#details');
      var submit = document.querySelector('input[type="submit"]');
      var nonce = document.querySelector('#nonce-display');
      var gate = 0;
      braintree.client.create({
        authorization: client_token
      }, function (clientErr, clientInstance) {
        if (clientErr) {
          console.error(clientErr);
          return;
        }

            // This example shows Hosted Fields, but you can also use this
            // client instance to create additional components here, such as
            // PayPal or Data Collector.

            braintree.hostedFields.create({
              client: clientInstance,
              styles: {
                'input': {
                  'font-size': '14px'
                },
                'input.invalid': {
                  'color': 'red'
                },
                'input.valid': {
                  'color': 'green'
                }
              },
              fields: {
                number: {
                  selector: '#card-number',
                  placeholder: '4111 1111 1111 1111'
                },
                cvv: {
                  selector: '#cvv',
                  placeholder: '123'
                },
                expirationDate: {
                  selector: '#expiration-date',
                  placeholder: '10/2019'
                }
              }
            }, function (hostedFieldsErr, hostedFieldsInstance) {
              if (hostedFieldsErr) {
                console.error(hostedFieldsErr);
                return;
              }

              submit.removeAttribute('disabled');
              nonce.removeAttribute('hidden');
              form.addEventListener('submit', function (event) {
                event.preventDefault();
              if (gate == 0){
                hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
                  if (tokenizeErr) {
                    console.error(tokenizeErr);
                    return;
                  }
                  var lenonce = payload.nonce;

                  document.getElementById("nonce-display").innerHTML = "<p><pre><code>" + lenonce + "</code></pre></p>";
                  console.log('Got a nonce: ' + lenonce);
                  submit.value ='Click again to full send!';
                  gate = 1
                  console.log(gate)
                  document.querySelector('#nonce').value = payload.nonce;
                });
              };
                if (gate == 1) {
                  console.log("seinding!!")
                  form.submit();
                };
              }, false);
            });
          });
    </script>

  </div>

  <div class="right">
    <!-- empty div for possible content. -->
    <p></p>
  </div>
</div>

<div style="text-align:center;padding:10px;margin-top:7px;"> <p>The Demos on this page use the following merchant ID: tt8srtpp8yfgfghp</p><p>{•̃̾_•̃̾}</p> </div>
</html>
