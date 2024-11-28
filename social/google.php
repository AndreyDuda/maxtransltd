<?php

if (!empty($_GET['code'])) {

    // Отправляем код для получения токена (POST-запрос).
    $params = array(
        'client_id'     => '1047739033954-v7dqa3vbh69hu7j0drp36vvj2mbs6un3.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect_uri'  => 'https://www.maxtransltd.com/social/google.php',
        'grant_type'    => 'authorization_code',
        'code'          => $_GET['code']
    );

    $ch = curl_init('https://accounts.google.com/o/oauth2/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($data, true);

    if (!empty($data['access_token'])) {
        // Токен получили, получаем данные пользователя.
        $params = array(
            'access_token' => $data['access_token'],
            'id_token'     => $data['id_token'],
            'token_type'   => 'Bearer',
            'expires_in'   => 3599
        );

        $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
        $info = json_decode($info, true);
        include($_SERVER['DOCUMENT_ROOT'].'/config.php');
        include($_SERVER['DOCUMENT_ROOT']."/". ADMIN_PANEL ."/includes.php");
        $getClientInfo = mysqli_query($db,"SELECT id FROM `".DB_PREFIX."_clients` WHERE email = '".$info['email']."' AND uid = '".$info['id']."' ");
        $clientInfo = mysqli_fetch_assoc($getClientInfo);
        $crypt = hash('sha512', uniqid() . time());
        $now = date("Y-m-d H:i:s", time());
        $redirectLink = $Db->getOne("SELECT route FROM `".DB_PREFIX."_routes` WHERE `page_id` = '80' AND `lang` = '".$_SESSION['last_lang']."' ");
        if ($clientInfo){
            $auth = mysqli_query($db,"UPDATE `".DB_PREFIX."_clients` SET `crypt` = '".$crypt."',`last_auth_date` = '".$now."' WHERE id = '".$clientInfo['id']."' ");
            if ($auth){
                $_SESSION['user']['crypt'] = $crypt;
                if (isset($_SESSION['order'])){
                    $redirectLinkPayment = $Db->getOne("SELECT route FROM `".DB_PREFIX."_routes` WHERE `page_id` = '86' AND `lang` = '".$_SESSION['last_lang']."' ");
                    header("Location:".$redirectLinkPayment['route']);
                }else{
                    header("Location:".$redirectLink['route']);
                }
            }else{
                $_SESSION['invalid_social_auth'] = 'Google';
                header("Location:/");
            }
        }else{
            $addUser = mysqli_query($db,"INSERT INTO `".DB_PREFIX."_clients` 
            (`name`,`second_name`,`email`,`crypt`,`registration_date`,`last_auth_date`,`uid`) VALUES 
            ('".$info['given_name']."','".$info['family_name']."','".$info['email']."','".$crypt."','".$now."','".$now."','".$info['id']."') ");
            if ($addUser){
                $_SESSION['user']['crypt'] = $crypt;
                if (isset($_SESSION['order'])){
                    $redirectLinkPayment = $Db->getOne("SELECT route FROM `".DB_PREFIX."_routes` WHERE `page_id` = '86' AND `lang` = '".$_SESSION['last_lang']."' ");
                    header("Location:".$redirectLinkPayment['route']);
                }else{
                    header("Location:".$redirectLink['route']);
                }
            }else{
                $_SESSION['invalid_social_auth'] = 'Google';
                header("Location:/");
            }
        }
    }else{
        $_SESSION['invalid_social_auth'] = 'Google';
        header("Location:/");
    }
}