<?
$Lang = $Main->GetDefaultLanguage();
$lang = $Lang['code'];
if(isset($_SESSION['last_lang']) && in_array($_SESSION['last_lang'], $Router->langList)){
    $lang = $_SESSION['last_lang'];
}

$Router->lang = $Main->lang = $lang;

$GLOBALS['ar_define_langterms'] = $Main->GetDefineLangTerms( $lang );
$defaultLinks['index'] = $Router->writelinkOne(1);
$page_404 = 0 ;
?>
<!DOCTYPE html>
<html lang="<?=$Router->lang?>">
<head>
    <?include($_SERVER['DOCUMENT_ROOT']."/public/blocks/head.php")?>
</head>
<body>
<div id="content" >
    <div id="page">

        <? include($_SERVER['DOCUMENT_ROOT']."/public/blocks/header.php") ?>

        <div class="container">

            <div style="text-align: center">
                <img src="/images/404.png" alt="">
            </div>
            <div style="text-align: center">
                <h3>
                    Page not found
                </h3>
            </div>

        </div>

    </div>
</div>


<?include($_SERVER['DOCUMENT_ROOT']."/public/blocks/footer.php")?>

</body>
</html>