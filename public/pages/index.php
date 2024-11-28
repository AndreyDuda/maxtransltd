<!DOCTYPE html>
<html lang="<?=$Router->lang?>">
<head>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/head.php' ?>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KPZPXJNJ"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="wrapper">
    <div class="header index_header">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/header.php' ?>
    </div>
    <div class="content">
        <div class="main_index_block">
            <? $mainBanner = $Db->getOne("SELECT image,title_" . $Router->lang . " AS title FROM `" . DB_PREFIX . "_main_banner` ") ?>
            <img src="/public/upload/main/<?= $mainBanner['image'] ?>" alt="main_img" class="fit_img mib_back_img">
            <div class="mib_content">
                <div class="container">
                    <h1 class="h1_title mib_content_header">
                        <?= $mainBanner['title'] ?>
                    </h1>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/filter.php' ?>
                </div>
            </div>
        </div>
        <div class="advantages_slider_block">
            <div class="container">
                <div class="flex-row gap-30">
                    <div class="col-xxl-8 col-lg-7 col-xs-12">
                        <div class="advantages_slider_wrapper">
                            <div class="advantages_slider">
                                <? $getAdvantages = $Db->getAll("SELECT image,title_" . $Router->lang . " AS title,preview_".$Router->lang." AS preview FROM `" . DB_PREFIX . "_advantages` WHERE active = '1' ORDER BY sort DESC");
                                foreach ($getAdvantages as $k => $advantage) {
                                    ?>
                                    <div class="advantage_slide">
                                        <div class="advantage_slide_content">
                                            <div class="advantage_img">
                                                <img src="/public/upload/advantage/<?=$advantage['image']?>" alt="advantage" class="fit_img">
                                            </div>
                                            <div class="advantage_description">
                                                <div class="advantage_title h2_title"><?=$advantage['title']?></div>
                                                <div class="advantage_txt par"><?=$advantage['preview']?></div>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="advantages_slider_nav slick_slider_nav"></div>
                        </div>
                    </div>
                    <div class="col-xxl-4 col-lg-5 col-xs-12">
                        <div class="advantages_card">
                            <div class="advantages_card_top">
                                <div class="advantage_card_title h2_title">
                                    <?=$GLOBALS['dictionary']['MSG__MAX_TRANS_TEPER_BLABLACAR']?>
                                </div>
                                <div class="advantage_card_subtitle par">
                                    <?=$GLOBALS['dictionary']['MSG__TI_ZH_AVTOBUSNI_REJSI_ZA_BILISH_VIGIDNOYU_CINOYU']?>
                                </div>
                            </div>
                            <div class="advantages_card_middle">
<!--                                <div class="advantage_card_images flex_ac">-->
<!--                                    <div class="row">-->
<!--                                        <img src="/public/images/common/maxtrans.svg" alt="maxtrans" class="fit_img">-->
<!--                                    </div>-->
<!--                                    <div class="row">-->
<!--                                        <img src="/public/images/common/arrow_right.svg" alt="arrow right" class="fit_img">-->
<!--                                    </div>-->
<!--                                    <div class="row">-->
<!--                                        <img src="/public/images/common/blabla_logo.svg" alt="bla bla" class="fit_img">-->
<!--                                    </div>-->
<!--                                </div>-->
                            </div>
                            <div class="advantages_card_bottom">
                                <a href="<?=$GLOBALS['site_settings']['BLABLACAR']?>" class="advantage_card_btn btn_txt" target="_blank">
                                    <?=$GLOBALS['dictionary']['MSG__KUPUJ_BEZPECHNO_NA_BLABLACAR']?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="welcome_block">
            <div class="container">
                <div class="flex-row gap-30 welcome_block_wrapper">
                    <?$wellcomeInfo = $Db->getOne("SELECT image,title_".$Router->lang." AS title,text_".$Router->lang." AS text FROM `".DB_PREFIX."_wellcome` ");?>
                    <div class="col-lg-6 col-xs-12">
                        <div class="welcome_txt_block">
                            <div class="welcome_title h2_title">
                                <?=$wellcomeInfo['title']?>
                            </div>
                            <div class="welcome_txt par">
                                <?=$wellcomeInfo['text']?>
                            </div>
                            <a href="<?=$Router->writelink(71)?>" class="about_details h4_title blue_btn">
                                <?=$GLOBALS['dictionary']['MSG__DETALINISHE_PRO_NAS']?>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xs-12">
                        <div class="welcome_img">
                            <img src="/public/upload/wellcome/<?=$wellcomeInfo['image']?>" alt="welcome" class="fit_img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="routes_block">
            <div class="container">
                <div class="routes_title h2_title">
                    <?=$GLOBALS['dictionary']['MSG__NASHI_NAPRAVLENNYA']?>
                </div>
                <div class="routes_subtitle par">
                    <?=$GLOBALS['dictionary']['MSG__BEZLICH_VARIANTIV_AVTOBUSNIH_POZDOK_DLYA_VASHIH_PODOROZHEJ_U_BUDI-YAKOMU_NAPRYAMKU']?>
                </div>
                <div class="routes_lists_wrapper">
                    <div class="route_list_block">
                        <div class="route_list_title h3_title"><?=$GLOBALS['dictionary']['MSG_ALL_KRANI']?></div>
                        <div class="route_list">
                            <?$getCountries = $Db->getall("SELECT id,title_".$Router->lang." AS title FROM `".DB_PREFIX."_cities` WHERE active = '1' AND section_id = '0' AND show_home = '1' ORDER BY sort DESC");
                            foreach ($getCountries AS $k=>$country){?>
                                <div>
                                    <a href="<?=$Router->writelink(73)?>?country=<?=$country['id']?>" class="shedule_link"><?=$country['title']?></a>
                                </div>
                            <?}?>
                        </div>
                    </div>
                    <div class="route_list_block">
                        <a href="<?=$Router->writelink(73)?>" class="route_list_title h3_title"><?=$GLOBALS['dictionary']['MSG_ALL_ROZKLAD']?></a>
                        <div class="route_list">
                            <?$getCities = $Db->getAll("SELECT id,title_".$Router->lang." AS title FROM `".DB_PREFIX."_cities` WHERE active = '1' AND section_id != 0 AND section_id != '175' AND station = '0' ORDER BY sort DESC LIMIT 10");
                            foreach ($getCities AS $k=>$city){?>
                                <div>
                                    <a href="<?=$Router->writelink(73)?>?city=<?=$city['id']?>" class="shedule_link"><?=$city['title']?></a>
                                </div>
                            <?}?>
                        </div>
                    </div>
                    <div class="route_list_block">
                        <div class="route_list_title h3_title"><?=$GLOBALS['dictionary']['MSG_ALL_MIZHNARODNI']?></div>
                        <div class="route_list">
                            <?$getInternationalTours = $Db->getAll("SELECT t.id,t.departure,t.arrival,departure_city.title_".$Router->lang." AS departure_city, arrival_city.title_".$Router->lang." AS arrival_city,
                            departure_city.id AS departure_city_id,arrival_city.id AS arrival_city_id
                                FROM `".DB_PREFIX."_tours` t 
                                 JOIN `".DB_PREFIX."_cities` departure_city ON t.departure = departure_city.id
                                 JOIN `".DB_PREFIX."_cities` arrival_city ON t.arrival = arrival_city.id
                                 WHERE departure_city.section_id != arrival_city.section_id");
                                 $printedRoutes = array();
                            foreach ($getInternationalTours AS $k=>$internationalTour){
                                $routeString = $internationalTour['departure_city_id']."_".$internationalTour['arrival_city_id'];

                                        if (!in_array($routeString, $printedRoutes)) {
                                ?>
                                <div>
                                    <a href="<?=$Router->writelink(73)?>?departure=<?=$internationalTour['departure_city_id']?>&arrival=<?=$internationalTour['arrival_city_id']?>" class="shedule_link"><?=$internationalTour['departure_city']?> → <?=$internationalTour['arrival_city']?></a>
                                </div>
                                <?php $printedRoutes[] = $routeString;
                                }}?>
                        </div>
                    </div>
                    <div class="route_list_block">
                        <div class="route_list_title h3_title"><?=$GLOBALS['dictionary']['MSG_ALL_VNUTRISHNI']?></div>
                        <div class="route_list">
                        <?php $getHomeTours = $Db->getAll("SELECT t.id,t.departure,t.arrival,departure_city.title_".$Router->lang." AS departure_city, arrival_city.title_".$Router->lang." AS arrival_city,
                            departure_city.id AS departure_city_id,arrival_city.id AS arrival_city_id
                                FROM `".DB_PREFIX."_tours` t 
                                 JOIN `".DB_PREFIX."_cities` departure_city ON t.departure = departure_city.id
                                 JOIN `".DB_PREFIX."_cities` arrival_city ON t.arrival = arrival_city.id
                                 WHERE departure_city.section_id = '13' AND arrival_city.section_id = '13' ");
                                 $printedRoutes = array();

                                foreach ($getHomeTours AS $k=>$homeTour){                                    
                                    $routeString = $homeTour['departure_city_id']."_".$homeTour['arrival_city_id'];                                    
                                    if (!in_array($routeString, $printedRoutes)) {
                                    ?>
                                <div>
                                    <a href="<?=$Router->writelink(73)?>?departure=<?=$homeTour['departure_city_id']?>&arrival=<?=$homeTour['arrival_city_id']?>" class="shedule_link"><?=$homeTour['departure_city']?> → <?=$homeTour['arrival_city']?></a>
                                </div>
                                <?php $printedRoutes[] = $routeString;
                                        }
                                    }
                                ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="index_options_block">
            <div class="container">
                <div class="flex-row gap-30">
                    <div class="col-xl-4 col-md-12">
                        <div class="index_option flex_ac shadow_block">
                            <div class="index_option_img">
                                <img src="/public/images/calendar_option.svg" alt="calendar">
                            </div>
                            <div class="index_option_description">
                                <a href="<?=$Router->writelink(73)?>" class="index_option_title h3_title">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_ROZKLAD_AVTOBUSIV']?>
                                </a>
                                <div class="index_option_subtitle par">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_ROZKLAD_MARSHRUTI_STANCI']?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-12">
                        <div class="index_option flex_ac shadow_block">
                            <div class="index_option_img">
                                <img src="/public/images/return_option.svg" alt="return">
                            </div>
                            <div class="index_option_description">
                                <div class="index_option_title h3_title">
                                    <a href="<?=$Router->writelink(87)?>">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_POVERNENNYA_KVITKIV']?>
                                    </a>
                                </div>
                                <div class="index_option_subtitle par">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_ZMINILISI_PLANI_POVERNITI_KOSHTI_ZA_KVITOK_CHEREZ_NASH_SAJT']?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-12">
                        <div class="index_option flex_ac shadow_block">
                            <div class="index_option_img">
                                <img src="/public/images/phone_option.svg" alt="phone">
                            </div>
                            <div class="index_option_description">
                                <a href="<?=$Router->writelink(76)?>" class="index_option_title h3_title">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_BEZ_KAS_TA_CHERG']?>
                                </a>
                                <div class="index_option_subtitle par">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_KVITKI_ONLAJN_U_BUDI-YAKIJ_CHAS_NA_NASHOMU_SAJTI_DLYA_ZRUCHNOGO_PRIDBANNYA_ABO_BRONYUVANNYA']?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="index_numbers_block">
            <div class="container">
                <div class="flex-row gap-30 numbers_wrapper">
                    <?$nubmbersInfo = $Db->getOne("SELECT image,title_".$Router->lang." AS title,text1_".$Router->lang." AS text1,text2_".$Router->lang." AS text2,text3_".$Router->lang." AS text3,text4_".$Router->lang." AS text4,number1,number2,number3,number4 FROM `".DB_PREFIX."_about_numbers` ");?>
                    <div class="col-xxl-6 col-xs-12">
                        <div class="index_numbers">
                            <div class="index_numbers_block_title h2_title"><?=$nubmbersInfo['title']?></div>
                            <div class="number_blocks_wrapper">
                                <?php if (!empty($nubmbersInfo['number1'])): ?>
                                <div class="number_block">
                                    <div class="number_block_title h3_title"><?=$nubmbersInfo['text1']?></div>

                                    <!--?$busesQty = $Db->getOne("SELECT COUNT(id) FROM `".DB_PREFIX."_buses` WHERE active = '1'");-->
                                    <?$busesNum = str_pad($nubmbersInfo['number1'], 3, '0', STR_PAD_LEFT);?>
                                    <div class="number_block_value">
                                        <div class="index_number_wrapper flex_ac">
                                            <div class="index_number h2_title"><?=$nubmbersInfo['number1']?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($nubmbersInfo['number2'])): ?>
                                <div class="number_block">
                                    <div class="number_block_title h3_title"><?=$nubmbersInfo['text2']?></div>
                                    <div class="number_block_value">
                                        <?$ordersQty = $Db->getOne("SELECT COUNT(id) FROM `".DB_PREFIX."_orders` WHERE active = '1'");
                                        $ordersNum = str_pad($nubmbersInfo['number2'], 3, '0', STR_PAD_LEFT);?>
                                        <div class="index_number_wrapper flex_ac">
                                            <div class="index_number h2_title"><?=$nubmbersInfo['number2']?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($nubmbersInfo['number3'])): ?>
                                <div class="number_block">
                                    <div class="number_block_title h3_title"><?=$nubmbersInfo['text3']?></div>
                                    <div class="number_block_value">
                                        <?$ukCitiesQty = $Db->getOne("SELECT COUNT(id) FROM `".DB_PREFIX."_cities` WHERE active = '1' AND section_id = '13' ");
                                        $ukCitiesNum = str_pad($nubmbersInfo['number3'], 3, '0', STR_PAD_LEFT);?>
                                        <div class="index_number_wrapper flex_ac">
                                            <div class="index_number h2_title"><?=$nubmbersInfo['number3']?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($nubmbersInfo['number4'])): ?>
                                <div class="number_block">
                                    <div class="number_block_title h3_title"><?=$nubmbersInfo['text4']?></div>
                                    <div class="number_block_value">
                                        <?$countriesQty = $Db->getOne("SELECT COUNT(id) FROM `".DB_PREFIX."_cities` WHERE active = '1' AND section_id = '0' ");
                                        $countriesNum = str_pad($nubmbersInfo['number4'], 3, '0', STR_PAD_LEFT);?>
                                        <div class="index_number_wrapper flex_ac">
                                            <div class="index_number h2_title"><?=$nubmbersInfo['number4']?></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <a href="<?=$Router->writelink(76)?>" class="h4_title buy_ticket_link blue_btn">
                                <?=$GLOBALS['dictionary']['MSG__ZAMOVITI_KVITOK']?>
                            </a>
                        </div>
                    </div>
                    <div class="col-xxl-5 col-xs-12">
                        <div class="index_map">
                            <img src="/public/upload/wellcome/<?=$nubmbersInfo['image']?>" alt="map" class="fit_img">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="why_we_block">
            <div class="container">
                <div class="flex-row gap-30">
                    <div class="col-xl-4 col-lg-5 col-md-12">
                        <div class="why_we_card">
                            <div class="why_we_card_top">
                                <div class="why_we_card_title h2_title"><?=$GLOBALS['dictionary']['MSG_ALL_NASHI_AVTOBUSI']?></div>
                                <div class="why_we_card_description par">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_OUR_BUSES_SUBTITLE']?>
                                </div>
                            </div>
                            <div class="why_we_card_middle">
                                <div class="why_we_card_logo">
                                    <img src="/public/upload/logos/<?=$logo['white_logo']?>" alt="logo" class="fit_img">
                                </div>
                            </div>
                            <div class="why_we_card_bottom">
                                <a href="<?=$Router->writelink(72)?>" class="autopark_link h4_title">
                                    <?=$GLOBALS['dictionary']['MSG_ALL_PEREGLYANUTI_AVTOPARK']?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 col-md-12">
                        <div class="why_we_slider_wrapper">
                            <div class="why_we_slider">
                                <?$getWhyWe = $Db->getAll("SELECT image,title_".$Router->lang." AS title,subtitle_".$Router->lang." AS subtitle, preview_".$Router->lang." AS preview FROM `".DB_PREFIX."_why_we` WHERE active = '1' ORDER BY sort ASC");
                                foreach ($getWhyWe AS $k=>$why){?>
                                    <div class="why_we_slide">
                                        <div class="why_we_slide_content">
                                            <div class="why_we_slide_image">
                                                <img src="/public/upload/why_we/<?=$why['image']?>" alt="slide" class="fit_img">
                                            </div>
                                            <div class="why_we_slide_description">
                                                <div class="why_we_slide_title h2_title"><?=$why['title']?></div>
                                                <div class="why_we_slide_subtitle manrope"><?=$why['subtitle']?></div>
                                                <div class="why_we_slide_txt par"><?=$why['preview']?></div>
                                            </div>
                                        </div>
                                    </div>
                                <?}?>
                            </div>
                            <div class="why_we_slider_nav slick_slider_nav"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="reviews_block">
            <div class="container">
                <div class="reviews_block_title h2_title">
                    <?=$GLOBALS['dictionary']['MSG_ALL_VIDGUKI']?>
                </div>
            </div>
            <div class="slider_container">
                <div class="reviews_slider_wrapper">
                    <div class="reviews_slider">
                        <?$getReviews = $Db->getAll("SELECT image,name,review_".$Router->lang." AS review FROM `".DB_PREFIX."_reviews` WHERE active = '1' ORDER BY sort DESC");
                        foreach ($getReviews AS $k=>$review){?>
                            <div class="review_slide">
                                <div class="review_slide_content shadow_block">
                                    <div class="review_slide_icon">
                                        <img src="/public/images/common/review_icon.svg" alt="review icon">
                                    </div>
                                    <div class="review_slide_txt par">
                                        <?=$review['review']?>
                                    </div>
                                    <div class="review_slide_reviewer_info flex_ac">
                                        <div class="review_slider_reviewer_image">
                                            <img src="/public/upload/reviews/<?=$review['image']?>" alt="<?=$review['name']?>">
                                        </div>
                                        <div class="review_slider_reviewer_name">
                                            <?=$review['name']?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?}?>
                    </div>
                    <div class="reviews_slider_nav slick_slider_nav"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/footer.php' ?>
    </div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/footer_scripts.php' ?>
<script>
    <?if ($_SESSION['invalid_social_auth']){?>
      out('<?=$GLOBALS['dictionary']['MSG_ALL_NE_UDALOSI_AVTORIZOVATISYA_CHEREZ']?> <?=$_SESSION['invalid_social_auth']?>','<?=$GLOBALS['dictionary']['MSG_ALL_POPROBUJTE_POZZHE']?>');
    <?unset($_SESSION['invalid_social_auth']);}?>
    $('.advantages_slider').slick({
        dots: true,
        dotsClass: 'advantages_slider_nav slick_slider_nav',
        arrows: false,
    });
    $('.why_we_slider').slick({
        dots: true,
        dotsClass: 'why_we_slider_nav slick_slider_nav',
        arrows: false,
    });
    $('.reviews_slider').slick({
        slidesToShow: 2,
        slidesToScroll: 2,
        dots: true,
        dotsClass: 'reviews_slider_nav slick_slider_nav',
        arrows: false,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1.04,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                }
            }
        ]
    })
</script>
</body>
</html>