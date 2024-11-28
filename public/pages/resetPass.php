<?php
$token = isset($_GET['token']) ? $_GET['token'] : null;
$email = isset($_GET['email']) ? $_GET['email'] : null;
?>

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
        <div class="login_container">
            <div class="flex-row gap-30">
                <div class="col-lg-6">
                    <a href="<?=$Router->writelink(1)?>" class="login_backlink h3_title flex_ac">
                        <img src="/public/images/common/blue_arrow_left_2.svg" alt="">
                        <?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_POVERNUTISYA_NA_GOLOVNU']?>
                    </a>
                    <div class="login_form_wrapper">
        <?php
        if (!$email || !$token) { // Если email или token не переданы
            ?>

                                <div class="login_page_title h2_title">
                                    <?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET']?>
                                </div>
                                <div class="login_tabs">
                                <div class="row login_input_row">
                                    <input class="c_input" type="text" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_EMAIL']?>" id="email" pattern="[^\u0400-\u04FF]*" maxlength="255" oninput="this.value = this.value.replace(/[^\x00-\x7F]/g, '');">
                                </div>
                                <div class="row login_input_row">
                                    <button class="send_login_code_btn h4_title blue_btn flex_ac" onclick="resetPassMail()">
                                        <?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET']?>
                                    </button>
                                </div>

            <?php
        } else { ?>

                                <div class="login_page_title h2_title">
                                    <?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET_ENTER_PASS']?>
                                </div>
                                <div class="login_tabs">
                                <div class="row login_input_row">
                                    <input class="c_input" type="password" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_PAROLI']?>" id="password">
                                </div>
                                <div class="row login_input_row">
                                    <button class="send_login_code_btn h4_title blue_btn flex_ac" onclick="newPassword('<?= $email ?>', '<?= $token ?>')">
                                        <?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET_SAVE']?>
                                    </button>
                                </div>


        <?php } ?>
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
    <div class="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer.php'?>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer_scripts.php'?>

<script>
    function resetPassMail() {
        var email = $.trim($('#email').val());

        if (!isEmail(email)) {
            out('<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_NEVERNYJ_EMAIL']?>','<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_EMAIL_UKAZAN_NEVERNO']?>');
            return false;
        }
        initLoader();
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'resetPassMail',
                'email': email
            },
            success: function (response) {
                removeLoader();
                if ($.trim(response) == 'ok') {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET_INSTR']?>');
                } else if ($.trim(response) == 'email_not_found') {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_EMAIL_UKAZAN_NEVERNO']?>');
                } else {
                    out($.trim(response));
                }
            }
        });
    }

    function newPassword(email, token) {
        var password = $.trim($('#password').val());

        if (password.length < 6) {
            out('<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_NEVERNYE_DANNYE']?>');
            return false;
        }

        initLoader();
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'newPass',
                'email': email,
                'token': token,
                'password': password
            },
            success: function (response) {
                removeLoader();
                if ($.trim(response) == 'ok') {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET_SAVED']?>', function() {
                        window.location.href = '<?=$Router->writelink(77)?>';
                    });
                } else if ($.trim(response) == 'token_expired') {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG__PASS_RESET_EXPIRED']?>');
                } else  {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_LOGIN_NEVERNYE_DANNYE']?>');
                }
            }
        });
    }
</script>
</body>
</html>
