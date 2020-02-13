<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<?php require_once("../includes/braintree_init.php"); ?>
<body style="font-family:Verdana;">
<!-- generating a client token -->
  <script>var client_token = "<?php echo($gateway->clientToken()->generate());?>"
  </script>
<div style="overflow:auto">
  <div class="menu">
    <!-- navigation will go here -->
    <?php require_once("../includes/nav.html"); ?>
  </div>
<div class="main">
  <h2>3D Secure Verifications</h2>
<!-- all the stuff you need for 3D Secure. -->
  <h3>Notes on 3D Secure Parameters</h3>
    <p>We recommend passing as much information into your <span style="background-color: #E6E6E6;font-weight: bold;">threeDSecureParameters</span> as possible. The following details are recommended to be collected from your customer upfront and passed into the <span style="background-color: #E6E6E6;font-weight: bold;">requestPaymentMethod</span> options, and any of the options in the <a href="https://braintree.github.io/braintree-web/3.57.0/ThreeDSecure.html#verifyCard">Braintree 3D Secure client reference</a> except for <span style="background-color: #E6E6E6;font-weight: bold;">nonce</span>, <span style="background-color: #E6E6E6;font-weight: bold;">bin</span>, and <span style="background-color: #E6E6E6;font-weight: bold;">onLookupComplete</span> can be included. </p>
    <p>Currently, the <span style="background-color: #E6E6E6;font-weight: bold;">amount</span> passed when authenticating a card with 3D Secure must be a non-zero value. This may change in the future, but for the time being, it would be best to ensure that the credit card verification amount is also non-zero.</p>
    <p>These parameters are used for authentication only at this point. If you wish for these customer details to be included in the transaction, you must also pass these details to your server-side integration.</p>
  <div>
    <pre class="code"><code class="prettyprint lang-javascript">var threeDSecureParameters = {
  amount: "1",
  email: "test@nightreeps.inc",
  billingAddress: {
    givenName: "Johnny", // ASCII-printable characters required
    surname: "Utah", // ASCII-printable characters required
    phoneNumber: "8158288282",
    streetAddress: "24123 Green Herron Drive",
    locality: "Channahon",
    region: "Illinois",
    postalCode: "60410",
    countryCodeAlpha2: "US"
  }
};</code></pre>
<h3>Creating a verification on the server-side</h3>
<p>Using the 3D Secure enriched nonce, a payment method can be verified and stored much like any other nonce. After the verification is created, the information can be accessed via the CreditCardVerification response object.</p>
<pre class="code"><code class="prettyprint">$result = $gateway->customer()->create(array(
    'paymentMethodNonce' => THREE_D_SECURE_ENRICHED_NONCE
));
if ($result->success) {
$threeDSDetails = $result->customer->paymentMethods[0]->verification->threeDSecureInfo};</code></pre>
<p>Use the below example integration to see what kind of information can be accessed from this object! This information is also in the Control Panel.<p>
  <p>Here are some handy values to test with:</p>
<ul>
  <li>Successful No Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001000</span></li>
  <li>Successful Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001091</span></li>
  <li>Failed No Challenge: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001018</span></li>
  <li>Error on Lookup: <span style="background-color: #E6E6E6;font-weight: bold;">4000000000001067</span></li>
</ul>
<!-- If you wanna have user inputs for this. -->
  <!-- <form id="3DSInfo" onsubmit="retrn false;">
    <input type="text" id="amount" placeholder="amount" class="other-input" onchange="getParam();"></input>
    <input type="text" id="email" placeholder="email address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="givenName" placeholder="first name" class="other-input" onchange="getParam();"></input>
    <input type="text" id="surname" placeholder="last name" class="other-input" onchange="getParam();"></input>
    <input type="text" id="phoneNumber" placeholder="phone number" class="other-input" onchange="getParam();"></input>
    <input type="text" id="streetAddress" placeholder="address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="extendedAddress" placeholder="extended address" class="other-input" onchange="getParam();"></input>
    <input type="text" id="locality" placeholder="city" class="other-input" onchange="getParam();"></input>
    <input type="text" id="region" placeholder="region/state" class="other-input" onchange="getParam();"></input>
    <input type="text" id="postalCode" placeholder="zip" class="other-input" onchange="getParam();"></input>
    <input type="text" id="countryCodeAlpha2" placeholder="Country Code" class="other-input" onchange="getParam();"></input>
  </form> -->
  </div>
<!-- Drop-in UI form. -->
      <form method="post" id="details" action="/customerCreate.php">
        <input type="hidden" id="nonce" name="nonce" />
      </form>
    <div id="dropin-container" style="display: table; float: left; width:100%;"></div>
      <button id="submit-button" class="button">Pay</button>
      <script>
        var button = document.querySelector('#submit-button');
        var form = document.querySelector('#details');
        var submit = document.querySelector('input[type="submit"]');
          var threeDSecureParameters = {
            amount: "1",
            email: "test@nightreeps.inc",
            billingAddress: {
              givenName: "Johnny", // ASCII-printable characters required, else will throw a validation error
              surname: "Utah", // ASCII-printable characters required, else will throw a validation error
              phoneNumber: "8158288282",
              streetAddress: "24123 Green Herron Drive",
              locality: "Channahon",
              region: "Illinois",
              postalCode: "60410",
              countryCodeAlpha2: "US"
            }
          };
          console.log(threeDSecureParameters.email);
          braintree.dropin.create({
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
                form.submit()
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

<div style="text-align:center;padding:10px;margin-top:7px;"> {•̃̾_•̃̾} </div>
</html>
