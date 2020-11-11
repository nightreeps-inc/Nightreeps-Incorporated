<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<?php
$result = $gateway->customer()->create([
    'firstName' => 'ThreeD',
    'lastName' => 'Dude'
]);
$cxID = $result->customer->id;
?>
<body style="font-family:Verdana;">
<!-- generating a client token -->
  <script>var client_token = "<?php echo($clientToken = $gateway->clientToken()->generate([
    "customerId" => $cxID,
    // Nick customer "customerId" => 591852991
    // "merchantAccountId" => "WrongDude"
    'merchantAccountId' => 'MindSapling-CAD'
]));?>"
console.log(client_token);
console.log("client token made for " + <?php echo($cxID)?>)
  </script>
<div style="overflow:auto">
  <div class="menu">
    <!-- navigation will go here -->
    <?php require_once("../includes/nav.html"); ?>
  </div>
<div class="main">
  <h2>3D Secure + Vault + Drop-in UI</h2>
  <h3>A streamlined approach</h3>
  <p>Did you know that the Drop-in UI&rsquo;s auto-vaulting feature allows for a more streamlined 3D Secure flow for Vaulting cards? Well now you do! Let me explain. Currently we are recommending flows to all merchants that require them to pass the nonce to their server, create a payment method, then pass it back to their client for 3D Secure authentication only to then pass it <em>again&nbsp;</em>back to their server to make a subscription/transaction.</p>
  <p>Sounds a little inefficient right? If only there was a way to vault the payment method on the client side . . . WELL THERE IS! Using the Drop-in UI auto-vault capabilities the customer gets their card Vaulted AND authenticated with 3D Secure in <strong>one step</strong>.
  </p>
  <h3>At a glance</h3>
  <p>Here is the general flow:</p>
  <ol style="list-style-type: decimal;">
      <li>When the customer lands on the page, the merchant creates a customer ID for them (can be empty and updated later with customer details).</li>
      <li>A client token using this customer ID is created</li>
      <li>That client token is used to build the Drop-in UI (much akin to Vault Manager setup)</li>
      <li>The customer enters their payment information</li>
      <li>Upon submission, the requestPaymentMethod function in the Drop-in UI vaults the card, and authenticates it with 3D Secure &ndash; returning a 3D Secure enriched nonce that represents a payment method token</li>
  </ol>
  <p>This nonce can then be used to do whatever the merchant desires! Update a subscription, create a new one, create a transaction, the only limit is your imagination! Lets go over these steps in a little more detail with some code examples and a live demo below.</p>
  <h3>In action</h3>
  <!-- <p>When you landed on this page, a customer ID was created with no payment method. Just an empty ol customer ID.</p>
  <pre class="code"><code class="prettyprint lang-php">$result = $gateway->customer()->create([
      'firstName' => 'ThreeD',
      'lastName' => 'Dude'
  ]);</code></pre>
  <p>This customer ID was then used to help generate a client token. These steps are the same as setting up Vault Manager. Indeed, the end result is the same even if an existing customer ID is entered below!</p>
  <pre class="code"><code class="prettyprint lang-javascript">$clientToken = $gateway->clientToken()->generate([
    "customerId" => CUSTOMER ID
]));</code></pre>
  <p>Then the client token is used to build the Drop-in UI. The Drop-in UI doesn&rsquo;t have anything special in it besides the necessary 3D Secure info as described in our 3D Secure Client-side docs.</p> -->
  <p>Go ahead and use the Drop-in UI below to see this in action! </p>
  <div>
  <p>Here are some handy values to test with:</p>
<ul>
  <li>Successful No Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001000</span></li>
  <li>Successful Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001091</span></li>
  <li>Failed No Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001018</span></li>
  <li>Error on Lookup: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001067</span></li>
</ul>
  </div>
<!-- Drop-in UI form. -->
      <div id="content">

      </div>
      <form method="post" id="details" action="/customerCreate.php">
        <input type="hidden" id="nonce" name="nonce" />
      </form>
    <div id="dropin-container" style="display: table; float: left; width:100%;"></div>
      <button id="submit-button" class="button">Pay</button>
      <script>
        var button = document.querySelector('#submit-button');
        var form = document.querySelector('#details');
        var submit = document.querySelector('input[type="submit"]');
        var btn = document.getElementById('submit-button');
        var content = document.getElementById('content');
        var customerID = '<?php echo $cxID; ?>'
        console.log(customerID);
          var threeDSecureParameters = {
            amount: "1",
            email: "test@nightreeps.inc",
            billingAddress: {
              givenName: "Johnny", // ASCII-printable characters required, else will throw a validation error
              surname: "Utah", // ASCII-printable characters required, else will throw a validation error
              phoneNumber: "8158288282",
              streetAddress: "24123 Green Herron Drive",
              locality: "Channahon",
              region: "IL",
              postalCode: "60410",
              countryCodeAlpha2: "US"
            }
          };
          console.log(threeDSecureParameters.email);
          braintree.dropin.create({
            vaultManager: true,
            //7:15.30
            authorization: client_token,
            container: '#dropin-container',
            threeDSecure: true
          }, function (createErr, instance) {
            button.addEventListener('click', function () {
              instance.requestPaymentMethod({
                threeDSecure: threeDSecureParameters
              },function (requestPaymentMethodErr, payload) {
                document.querySelector('#nonce').value = payload.nonce;
                var lenonce = payload.nonce;
                console.log(lenonce);
//communicate with the server for the newly created token (for demonstration purposes)
                $.ajax({
                    url:"customerFind.php",    //the page containing php script
                    type: "post",    //request type,
                    dataType: 'json',
                    data: {theID: customerID},
                    success:function(response){
                        console.log(response);
                        var newToken = response;
                        instance.teardown();
                        btn.remove();
                        content.innerHTML += '<h3>Success!</h3> <p>You just created a nonce that has both 3D Secure information on it, AND represents a vaulted token -- all on the client side!</p> <p> Now go use this nonce to make a subscription, or transaction, or anything!</p> <ul><li>nonce: ' + lenonce + '</li><li>customer ID: ' + customerID + '<li>token:' + newToken; + '</li> </ul> <p> Now go use this nonce to make a subscription, or transaction, or anything!</p>';
                    }
                });
                // form.submit()
              });
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
