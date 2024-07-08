<div role="form">
<form method="POST"  action="" name="EWForm">
<p><label>Name<br /><span class="wpcf7-form-control-wrap"><input type="text" name="FirstName" value="Birender" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="FirstName" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>surname<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="surname" value="Unknown" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="surname" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>suburbsupply<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="SuburbSupply" value="Richmond" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="SuburbSupply" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>MobileNumber<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="MobileNumber" value="0422420694" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="MobileNumber" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>EmailM<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="EmailM" value="birender@wibexa.com" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="EmailM" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>MoveinDate<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="MoveinDate" value="04/29/2019" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="MoveinDate" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>

<p class="textbox-top-margin"><label>ConnectionDate<br />
<span class="wpcf7-form-control-wrap"><input type="text" name="ConnectionDate" value="Unknown" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required" id="ConnectionDate" readonly aria-required="true" aria-invalid="false" required /></span> </label></p>
<br />
<input type="submit" value="Submit" name="btnpostlead" id="btnpostlead" class="button button-primary" style="width:100% !important;"   />
</form>
</div>



<?php

if (isset($_POST['btnpostlead']))
{
    $url = 'https://secure.velocify.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=94&XmlResponse=True';
    $response = curl_post_data();
    var_dump(htmlspecialchars($response));
}


function curl_post_data()
{

    $submission['submitted']['FirstName'] = $_POST['FirstName'];
    $submission['submitted']['surname'] =$_POST['surname'];
    $submission['submitted']['SuburbSupply'] = $_POST['SuburbSupply'];
    $submission['submitted']['MobileNumber'] = $_POST['MobileNumber'];
    $submission['submitted']['EmailM'] =$_POST['EmailM'];
    $submission['submitted']['MoveinDate'] = $_POST['MoveinDate'];
    $submission['submitted']['ConnectionDate'] = $_POST['ConnectionDate'];
    
    $request = http_build_query($submission, '', '&');
    print_r($request);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://secure.velocify.com/Import.aspx?Provider=RSMSolutions&Client=RSMSolutions&CampaignId=94&XmlResponse=True");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>