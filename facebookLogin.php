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

//putenv("APP_ID_ENV_NAME=$APP_ID");

$fb = new Facebook\Facebook([
    'app_id' => $APP_ID,
    'app_secret' => $APP_SECRET,
    'default_graph_version' => $DEFAULT_VERSION,
]);

if(isset($_SESSION['fb_access_token'])) {

    echo "is set";
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
/*
        $response = $fb->get('/me/accounts', $accessToken);

      //  print_r($response->getAccessToken());
       // var_dump($response->getDecodedBody()['data'][0]["access_token"]);
        $pageToken = $response->getDecodedBody()['data'][0]["access_token"];
        $pageId = $response->getDecodedBody()['data'][0]["id"];

        echo '<pre>';
        print_r($response->getDecodedBody());
        echo '</pre>';

        print_r("/{$pageId}/feed");

        $responseFeed = $fb->get("/{$pageId}/feed", $pageToken);


        echo '<pre>';
        print_r($responseFeed->getDecodedBody());
        echo '</pre>';


        $responseFeed = $fb->post("/{$pageId}/feed", array(
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