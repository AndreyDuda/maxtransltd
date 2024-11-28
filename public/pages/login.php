<!DOCTYPE html>
<html lang="<?=$Router->lang?>">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/head.php'?>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/header.php' ?>
    </div>
    <div class="content">
        <div class="page_content_wrapper">
            <div class="login_container">
                <div class="flex-row gap-30">
                    <div class="col-lg-6">
                        <a href="<?=$Router->writelink(1)?>" class="login_backlink h3_title flex_ac">
                            <img src="/public/images/common/blue_arrow_left_2.svg" alt="">
                            <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_POVERNUTISYA_NA_GOLOVNU']?>
                        </a>
                        <div class="login_form_wrapper">
                            <div class="login_page_title h2_title">
                                <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_UVIJTI']?>
                            </div>
                            <div class="login_tabs">
                                <div class="login_inputs_wrapper">
                                    <div class="row login_input_row">
                                        <input class="c_input" type="text" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_EMAIL']?>" id="email" pattern="[^\u0400-\u04FF]*" maxlength="255" oninput="this.value = this.value.replace(/[^\x00-\x7F]/g, '');">
                                    </div>
                                    <div class="row login_input_row">
                                        <input class="c_input" type="password" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_PAROLI']?>" id="password">
                                    </div>
                                    <div class="row login_input_row">
                                        <button class="send_login_code_btn h4_title blue_btn flex_ac" onclick="auth()">
                                            <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_VOJTI']?>
                                        </button>
                                    </div>

                                    <div class="row login_input_row">
                                        <a href="<?=$Router->writelink(88)?>" class="send_login_code_btn h4_title orange_btn flex_ac" >
                                            <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_ZAREGISTRIROVATISYA']?>
                                        </a>
                                    </div>
                                    <div class="login_social_auth">
                                        <div class="row login_input_row">
                                            <?
                                            $params = array(
                                                'client_id'     => '1047739033954-v7dqa3vbh69hu7j0drp36vvj2mbs6un3.apps.googleusercontent.com',
                                                'redirect_uri'  => 'https://www.maxtransltd.com/social/google.php',
                                                'response_type' => 'code',
                                                'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                                                'state'         => '123'
                                            );
                                            $googleAuthLink = 'https://accounts.google.com/o/oauth2/auth?'.urldecode(http_build_query($params)); ?>
                                            <a href="<?=$googleAuthLink?>" class="social_auth_link google flex_ac">
                                                <img src="/public/images/google.svg" alt="" class="fit_img">
                                                Google
                                            </a>
                                        </div>
                                        <div class="row login_input_row">
                                            <?
                                            $params = array(
                                                'client_id'     => '740501071244051',
                                                'redirect_uri'  => 'https://www.maxtransltd.com/social/facebook.php',
                                                'scope'         => 'email',
                                                'response_type' => 'code',
                                                'state'         => '123'
                                            );

                                            $facebookAuthLink = 'https://www.facebook.com/dialog/oauth?' . urldecode(http_build_query($params));
                                            ?>
                                            <a href="<?=$facebookAuthLink?>" class="social_auth_link google flex_ac">
                                                <img src="/public/images/facebook.svg" alt="" class="fit_img">
                                                Facebook
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="login_clarification par">
                                    <div class="row">
                                        <a href="<?=$Router->writelink(93) ?>" class="forg_pass"><?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_FORGOT_PASS']?></a>
                                    </div>
                                    <div class="row">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_ESCHE_NET_LICHNOGO_KABINETA']?> <a href="<?=$Router->writelink(88)?>"><?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_ZAREGISTRIROVATISYA']?></a>
                                    </div>
                                    <?$loginPageTxt = $Db->getOne("SELECT text_".$Router->lang." AS text FROM `".DB_PREFIX."_txt_blocks` WHERE id = '6' ")?>
                                    <?=$loginPageTxt['text']?>
                                    <p>
                                        <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_UMOVI']?> <a href="<?=$Router->writelink(84)?>"> <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_PUBLICHNO_OFERTI']?> </a> <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_TA']?> <a href="<?=$Router->writelink(83)?>"> <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_POLITIKI_KONFIDENCIJNOSTI']?></a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="login_logo">
                            <img src="/public/images/common/login_page_logo.png" alt="login logo" class="fit_img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer.php'?>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer_scripts.php'?>
<script>
    function auth(){
        var email = $.trim($('#email').val());
        var password = $.trim($('#password').val());
        if (!isEmail(email)){
            out('<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_NEVERNYJ_EMAIL']?>','<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_EMAIL_UKAZAN_NEVERNO']?>');
            return false;
        }
        initLoader();
        $.ajax({
            type:'post',
            url:'<?=$Router->writelink(3)?>',
            data:{
                'request':'auth',
                'login':email,
                'password':password
            },
            success:function(response){
                removeLoader();
                if ($.trim(response) == 'ok'){
                    <?if (isset($_SESSION['order'])){?>
                    location.href = '<?=$Router->writelink(86)?>';
                    <?}else{?>
                    location.href = '<?=$Router->writelink(80)?>';
                    <?}?>
                }else if ($.trim(response) == 'email_not_found') { // Добавляем этот блок для обработки случая, когда email не найден
                out('<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_EMAIL_UKAZAN_NEVERNO']?>');
                }else{
                    out($.trim(response));
                }
            }
        })
    }
</script>
</body>
</html>