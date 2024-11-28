
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
        <div class="thx_content_wrapper">
            <div class="thx_block">
                <div class="container">
                    <div class="thx_block_title h2_title">
                        <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_DYAKUYU_ZA_BRONYUVANNYA_BILETU']?>
                    </div>
                    <div class="thx_block_subtitle par">
                        <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_DANI_VASHOGO_BILETU']?>
                    </div>
                    <?if (!$User->auth) { ?>
                        <a href="<?=$Router->writelink(77)?>" class="private_link h4_title blue_btn">
                        <span class="hidden-xs">
                            <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_PEREJTI_U_PERSONALINIJ_KABINET']?>
                        </span>
                            <span class="hidden-xxl hidden-xl hidden-lg hidden-md hidden-sm col-xs-12">
                            <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_PERSONALINIJ_KABINET']?>
                        </span>
                        </a>
                    <?} else { ?>
                    <a href="<?=$Router->writelink(80)?>" class="private_link h4_title blue_btn">
                        <span class="hidden-xs">
                            <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_PEREJTI_U_PERSONALINIJ_KABINET']?>
                        </span>
                        <span class="hidden-xxl hidden-xl hidden-lg hidden-md hidden-sm col-xs-12">
                            <?=$GLOBALS['dictionary']['MSG_MSG_THX_PAGE_PERSONALINIJ_KABINET']?>
                        </span>
                    </a>
                    <? } ?>
                </div>
            </div>
            <div class="txh_image">
                <img src="/public/images/common/thx_img.png" alt="thanks" class="fit_img">
            </div>
        </div>
    </div>
    <div class="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer.php'?>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/footer_scripts.php'?>
<script>
    $(document).ready(function() {
    // AJAX-запрос при загрузке страницы
    $.ajax({
        type: 'post',
        url: '<?=$Router->writelink(3)?>',
        data: { 'request': 'clear_session_data' },
        success: function(response) {
            // Обработка ответа
            if ($.trim(response) == 'ok') {
                // Данные из сессии успешно удалены
                console.log('Данные из сессии успешно удалены');
            } else {
                // Произошла ошибка при удалении данных из сессии
                console.log('Произошла ошибка при удалении данных из сессии');
            }
        }
    });
});
</script>
</body>
</html>