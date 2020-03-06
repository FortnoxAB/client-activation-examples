<?php
/**
 * In this Integration example will we simulate a user activating an integration. The process is as follows:
 *      1) The user clicks to activate an integration
 *      2) The provided redirect_uri is fetched and called with an authorization code
 *      3) The integration uses the authorization code and a client secret to get an access token
 *      4) The access token is used to make additional call to the Fortnox api
 */

define( 'ENDPOINT',                   'https://api.fortnox.se/3/' );
define( 'COMPANY_INFO_ENDPOINT',      'https://api.fortnox.se/3/settings/company' );
define( 'CLIENT_SECRET',              'your-client-secret');

// Entry point
$accessToken = getAccessToken($_GET['authorization-code']);
if($accessToken != null) {
    $companyInfo = getCompanyInformation($accessToken);

    /**
     * You can now use the company information to verify that the company is an existing customer or not,
     * in this example we will just present the fetched company name.
     */
    if($companyInfo != null && array_key_exists('CompanySettings', $companyInfo)) {
        $companyName = $companyInfo['CompanySettings']['Name'];
        echo $companyName . ' has now been activated! Thank you for using our integration!';
    } else {
        echo 'Unable to get company information';
    }
}

function getAccessToken($authorization){
    $curl = curl_init();

    /**
     * The authorization code will be provided by Fortnox when the user is activating the integration.
     * The client secret is the code that was sent when the integration was registered with Fortnox.
    */
    $headers = array(
        'authorization-code: '.  $authorization,
        'Client-Secret: '. CLIENT_SECRET
    );

    curl_setopt_array( $curl, [
        CURLOPT_URL => ENDPOINT,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true
        ]);

    // Execute the request to fetch an access token, using the authorization code and client secret
    $curlResponse = curl_exec( $curl );
    curl_close( $curl );

    $json = json_decode($curlResponse, true);

    /*
     * Given the correct authorization code is provided, an access token is returned, with this access token
     * you can now start posting / getting your own information (provided that youre integration has access to the correct scopes).
     * Note that the authorization code can only be used once.
    */
    if(array_key_exists('Authorization', $json)) {
        $accessToken = $json['Authorization']['AccessToken'];
    } else {
        return handleError($json);
    }

    return $accessToken;
}

function getCompanyInformation($accessToken) {
    $curl = curl_init( );

    /**
     * To fetch the customers company information you will use our client secret and the newly received access token.
     */
    $headers = array(
        'Client-Secret: '. CLIENT_SECRET,
        'Access-Token: '.  $accessToken
    );

    curl_setopt_array( $curl, [
        CURLOPT_URL => COMPANY_INFO_ENDPOINT,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPGET => true
    ]);

    // Execute the request
    $curlResponse = curl_exec( $curl );
    curl_close( $curl );

    $json = json_decode($curlResponse, true);

    if(hasErrors($json)) {
        return handleError($json);
    }

    return $json;
}

// Check if the json has any error information
function hasErrors($json) {
    if(array_key_exists('ErrorInformation', $json)) {
        return $json['ErrorInformation']['Error'] != null;
    }

    return false;
}

// Display error message
function handleError($error) {
    if(array_key_exists('ErrorInformation', $error)) {
        $errorMsg = isset($error['ErrorInformation']['Message']) ? 'Error: ' . $error['ErrorInformation']['Message'] : '';
        echo 'Failed to activate your company! ' . $errorMsg;
    } else {
        echo 'Failed to activate your company!';
    }

    return null;
}

?>