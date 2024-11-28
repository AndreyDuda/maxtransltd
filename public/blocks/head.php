<meta charset="UTF-8">
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KPZPXJNJ');</script>
<!-- End Google Tag Manager -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?if(isset($page_data)){ echo $page_data['page_title']; }else{?> Page title <?}?></title>
<meta name="description" content="<?if(isset($page_data)){ echo $page_data['meta_d']; }else{?>Page description<?}?>">
<meta name="keywords" content="<?if(isset($page_data)){ echo $page_data['meta_k']; }else{?>Page keywords<?}?>">

<link rel="shortcut icon" type="image/png" href="/public/upload/logos/favicon.svg"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&family=Manrope:wght@400;700&family=Montserrat:wght@400;500&family=Play:wght@400;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/public/libs/nice_select/nice-select.css" />
<link rel="stylesheet" href="/public/libs/slick/slick.css"/>


<link rel="stylesheet" href="/public/css/nag.css">
<link rel="stylesheet" href="/public/css/common.css">
<link rel="preload" as="style" href="/public/css/style.css">
<link rel="stylesheet" href="/public/css/style.css">
<link rel="stylesheet" href="/public/css/mobile.css">

<script>
    var close_btn = 'OK';
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var forms = document.querySelectorAll('form');

        // Устанавливаем обработчик события submit для каждой формы
        forms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                // Удаляем обработчик события beforeunload при отправке формы
                window.onbeforeunload = null;
            });
        });

        // Сброс события beforeunload при попытке покинуть страницу
        window.addEventListener('beforeunload', function(event) {
            // Обнуляем обработчик, чтобы избежать предупреждения
            window.onbeforeunload = null;
        });
    });
</script>

<?php
$arrowDown = '<svg width="17" height="9" viewBox="0 0 17 9" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M1.15332 0.567871L8.65333 8.06786L16.1533 0.567871" stroke="white"/>
</svg>';

if ($pageData['page_id'] != '76'){
    unset($_SESSION['filter']);
}
?>