<?php
require_once 'inc/user.php';
require_once 'inc/facebook.php';

$fbHelper = $fb->getRedirectLoginHelper();

try {
    $accessToken = $fbHelper->getAccessToken();
}
catch (Exception $e) {
    echo 'Failed to login with facebook. Error: ' . $e->getMessage();
    exit();
}

if (!$accessToken) {
    exit('Failed to login with facebook. Please try again.'); //TODO: vypsat v hezci podobe
}

$oAuth2Client = $fb->getOAuth2Client();
$accessTokenMetadata = $oAuth2Client->debugToken($accessToken);
$fbUserId = $accessTokenMetadata->getUserId();

$response = $fb->get('me?fields=name,email', $accessToken);
$graphUser = $response->getGraphUser();

$fbUserEmail = $graphUser->getEmail();
$fbUserName = $graphUser->getName();

$query = $db->prepare('SELECT * FROM mt_user WHERE facebook_id=:fbId LIMIT 1;');
$query->execute([':fbId' => $fbUserId]);

if ($query->rowCount() > 0) {                                               //uzivatel je jiz registrovan facebookem
    $user = $query->fetch(PDO::FETCH_ASSOC);
}
else {
    $query = $db->prepare('SELECT * FROM mt_user WHERE email=:mail LIMIT 1;');
    $query->execute([':mail' => $fbUserEmail]);

    if ($query->rowCount() > 0) {                                           //uzivatel je jiz registrovan lokalne
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $updateQuery = $db->prepare('UPDATE mt_user SET facebook_id=:fbId WHERE id=:id LIMIT 1;');
        $updateQuery->execute([':fbId' => $fbUserId, ':id' => $user['id']]);
    }
    else {                                                                //uzivatel jeste neni registrovan
        $registrationQuery = $db->prepare(
            'INSERT INTO mt_user (email, name, nation, facebook_id) VALUES (:mail, :name, "FB", :fbId);');
        $registrationQuery->execute([':mail' => $fbUserEmail,
                                     ':name' => $fbUserName,
                                     ':fbId' => $fbUserId]);
        $query = $db->prepare('SELECT * FROM mt_user WHERE facebook_id=:fbId LIMIT 1;');
        $query->execute([':fbId' => $fbUserId]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
    }
}

if (!empty($user)){
    $_SESSION['user_id']=$user['id'];
    $_SESSION['user_name']=$user['name'];
    $_SESSION['user_role']=$user['role'];
    if($user['nation']!="FB"){
        $_SESSION['user_nation']=$user['nation'];
    }
}

header('Location: index.php');