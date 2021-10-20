<html>
<!-- Loads the header, for consistency across pages. -->
<?php require_once("../includes/head.php"); ?>
<!-- Braintree init, the file which creates the gateway using api keys -->
<body style="font-family:Verdana;">
<!-- generating a client token -->
<div style="overflow:auto">
  <div class="menu">
    <!-- navigation will go here -->
    <?php require_once("../includes/nav.html"); ?>
  </div>
<div class="main">
  <h2>PHP Tester</h2>
<!-- all the stuff you need for 3D Secure. -->
  <h3>Type your script below</h3>
  <form>
    <input type="text" id="code" placeholder="code" name="code" onchange="runIt()">
  </form>
  <script>
    function runIt(){
      form = document.getElementById('code');
      userCode = form.value;
      console.log(userCode);
      console.log("junk");
      $.ajax({
          url:"eval.php",    //the page containing php script
          type: "post",    //request type,
          dataType: 'json',
          data: {itsTheCode: userCode},
          success:function(response){
              console.log(response);
          }
      });
    }

  </script>


</div>

  <div class="right">
    <!-- empty div for possible content. -->
    <p></p>
  </div>
</div>

<div style="text-align:center;padding:10px;margin-top:7px;"> <p>The Demos on this page use the following merchant ID: tt8srtpp8yfgfghp</p><p>{•̃̾_•̃̾}</p> </div>
</html>
