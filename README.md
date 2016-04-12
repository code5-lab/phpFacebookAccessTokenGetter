# phpFacebookAccessTokenGetter
Facebook access_token and page_id generator.
  
  Firstly you will be prompted to type app_id, app_secret and default graph version. After, you will be redirected to Facebook login page. Succeeding it will query Facebook's graph API for logged in user's managed page list.  

## Dependencies and installation
  Depends on facebook-php-sdk

  To install via composer add the following lines to "composer.json" file at the root of your project and run "composer install":

  {
    "require" : {
      "facebook/php-sdk-v4" : "~5.0"
    }
  }

## Usage

e.g getting logged user's info:

<?php
  $response = $fb->get('/me', $accessToken); 
>

e.g. getting user's managed pages(specifically page token and page id for the first page on the retrieved list):

<?php
  $response = $fb->get('/me/accounts', $accessToken);
  
  $pageToken = $response->getDecodedBody()['data'][0]["access_token"];  
  $pageId = $response->getDecodedBody()['data'][0]["id"];
>

e.g. post to user's first managed page:

<?php
  $responseFeed = $fb->post("/{$pageId}/feed", array(
            'message' => 'message posted through app!!'
        ), $pageToken);
>

## License

  This project is licensed under the GNU General Public License v2.0 - see the LICENSE.md file for details



