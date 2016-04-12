<?php
session_start();

ini_set('display_errors', 'On');
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

		// Função para limpar os dados de entrada
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}

        if(!isset($_POST['appID'])){
            $APP_ID = test_input($_SESSION['appID']);
            $APP_SECRET = test_input($_SESSION['app_secret']);
            $DEFAULT_VERSION = test_input($_SESSION['default_graph_version']);
        }else{
            $APP_ID = test_input($_POST["appID"]);
            $APP_SECRET = test_input($_POST['appSecret']);
            $DEFAULT_VERSION = test_input($_POST['defVersion']);
        }

$fb = new Facebook\Facebook([
    'app_id' => $APP_ID,
    'app_secret' => $APP_SECRET,
    'default_graph_version' => $DEFAULT_VERSION,
]);

if(isset($_SESSION['fb_access_token'])) {

    $accessToken = $_SESSION['fb_access_token'];

} else {

    $helper = $fb->getRedirectLoginHelper();
    try {
    $accessToken = $helper->getAccessToken();

        if(isset($accessToken)) {
            $oAuth2Client = $fb->getOAuth2Client();

            // longlived access token
            if (!$accessToken->isLongLived()) {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            }
        }

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
}

if(isset($accessToken)) {
    // Logged in!

    echo 'Logged in! Making get(/me) request';
    $_SESSION['fb_access_token'] = (string) $accessToken;

    try {

        $response = $fb->get('/me/accounts', $accessToken);                     // requests info on user's managed pages

        //$pageToken = $response->getDecodedBody()['data'][0]["access_token"];    // page token
        //$pageId = $response->getDecodedBody()['data'][0]["id"];                 // id of the first page

        foreach($response->getDecodedBody()['data'] as $pageRetrieved){           // for each page retrieved prints
            echo '<pre>';                                                         //id and token
                print_r("Page id: ".$pageRetrieved["id"]);
                print_r("Page token: ".$pageRetrieved["access_token"]);
            echo "<br>";
            echo '</pre>';
        }

        /*$responseFeed = $fb->get("/{$pageId}/feed", $pageToken);                //requests page's last posts
                                                                                  // (limit=default=100)
/*
        $responseFeed = $fb->post("/{$pageId}/feed", array(                     //issues facebook post to that page
            'message' => 'postado atraves da app!!'
        ), $pageToken);
*/

    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
} else {
    $helper = $fb->getRedirectLoginHelper();

    $redirect_url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

    $loginUrl = $helper->getLoginUrl($redirect_url, [ 'manage_pages', 'publish_actions','publish_pages' ]);

    $_SESSION['appID'] = $APP_ID;
    $_SESSION['app_secret'] = $APP_SECRET;
    $_SESSION['default_graph_version'] = $DEFAULT_VERSION;

    echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
}
?>