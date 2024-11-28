    <div class="container">
        <?$logo = $Db->getOne("SELECT white_logo_".$Router->lang." AS white_logo,black_logo_".$Router->lang." AS black_logo FROM ".DB_PREFIX."_logos WHERE id = 1 "); ?>
        <div class="header_content flex_ac">
            <div class="logo">
                <a href="<?=$Router->writelink(1)?>">
                    <?php $image_logo = $logo['black_logo'];
                    $burger = 'burger_dark.svg';
                    $langs_class = 'dark';
                    if($pageData['page_id'] == '1'){
                        $image_logo = $logo['white_logo'];
                        $burger = 'burger.svg';
                        $langs_class = '';
                    }?>
                    <img src="/public/upload/logos/<?php echo $image_logo;?>" alt="logo" class="fit_img">
                </a>
            </div>
            <div class="menu flex_ac">
                <div class="menu_links hidden-md hidden-sm hidden-xs">
                    <div class="support_wrapper">
                        <button class="link dropdown_link" onclick="toggleSupport(this)">
                            <?=$GLOBALS['dictionary']['MSG_ALL_SLUZHBA_PIDTRIMKI']?>
                            <?php echo $arrowDown?>
                        </button>
                        <div class="support_phones">
                            <a href="tel:<?=str_replace(" ","",$GLOBALS['site_settings']['SUPPORT_PHONE_1'])?>" class="support_phone">
                                <img src="/public/images/common/lifecell.svg" alt="lifecell">
                                <?=$GLOBALS['site_settings']['SUPPORT_PHONE_1']?>
                            </a>
                            <a href="tel:<?=str_replace(" ","",$GLOBALS['site_settings']['SUPPORT_PHONE_2'])?>" class="support_phone">
                                <img src="/public/images/common/kyivstar.svg" alt="kyivstar">
                                <?=$GLOBALS['site_settings']['SUPPORT_PHONE_2']?>

                            </a>
                        </div>
                    </div>
                    <?if ($User->auth){
                        $privateLink = $Router->writelink(79);
                    }else{
                        $privateLink = $Router->writelink(77);
                    }?>
                    <a href="<?=$privateLink?>" class="link">
                        <?=$GLOBALS['dictionary']['MSG_ALL_OSOBISTIJ_KABINET']?>
                    </a>
                    <?if ($User->auth){
                        ?>
                        <button class="link" onclick="exitAccount()">
                    Выход
                </button>
                   <? }?>
                </div>
                <?$siteLangs = get_list_lang_public();?>
                <div class="langs_block">
                    <select class="langs_select <?php echo $langs_class?>" onchange="location.href = $(this).val();">
                        <?foreach ($siteLangs['lang'] AS $langCode=>$langInfo){?>
                            <option value="<?=$langInfo['href']?>" <?if ($langInfo['code'] == $Router->lang){echo 'selected';}?>><?=strtoupper($langInfo['code'])?></option>
                        <?}?>
                    </select>
                </div>
                <button class="burger" onclick="toggleMobileMenu()">
                    <img src="/public/images/common/<?php echo $burger?>" alt="burger">
                </button>
            </div>
        </div>
    </div>
    <div class="mobile_menu blue_popup">
        <div class="mobile_menu_content">
            <button class="close_menu" onclick="toggleMobileMenu()">
                <img src="/public/images/common/arrow_left.svg" alt="arrow left">
            </button>
            <div class="mobile_menu_links">
                <ul>
                    <li><a href="<?=$Router->writelink(1)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '1'){echo 'active';}?>"><?=$Router->writetitle(1)?></a></li>
                    <li><a href="<?=$Router->writelink(71)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '71'){echo 'active';}?>"><?=$Router->writetitle(71)?></a></li>
                    <li><a href="<?=$Router->writelink(72)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '72'){echo 'active';}?>"><?=$Router->writetitle(72)?></a></li>
                    <li><a href="<?=$Router->writelink(73)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '73'){echo 'active';}?>"><?=$Router->writetitle(73)?></a></li>
                    <li><a href="<?=$Router->writelink(74)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '74'){echo 'active';}?>"><?=$Router->writetitle(74)?></a></li>
                    <li><a href="<?=$Router->writelink(75)?>" class="mobile_menu_link manrope <?if ($pageData['page_id'] == '75'){echo 'active';}?>"><?=$Router->writetitle(75)?></a></li>
                </ul>
            </div>
            <div class="mobile_menu_social">
                <div class="mobile_menu_social_header btn_txt">
                    <?=$GLOBALS['dictionary']['MSG_ALL_MI_U_SOCMEREZHAH']?>
                </div>
                <div class="mobile_menu_social_links flex_ac">
                    <a href="<?=$GLOBALS['site_settings']['VIBER']?>">
                        <img src="/public/images/common/viber.svg" alt="viber">
                    </a>
                    <a href="<?=$GLOBALS['site_settings']['TELEGRAM']?>">
                        <img src="/public/images/common/telegram.svg" alt="telegram">
                    </a>
                    <a href="<?=$GLOBALS['site_settings']['FB']?>">
                        <img src="/public/images/common/facebook.svg" alt="facebook">
                    </a>
                    <a href="<?=$GLOBALS['site_settings']['INST']?>">
                        <img src="/public/images/common/instagram.svg" alt="instagram">
                    </a>
                </div>
            </div>
            <div class="menu_links mobile hidden-xxl hidden-xl hidden-lg">
                <div class="support_wrapper">
                    <button class="link dropdown_link" onclick="toggleSupport(this)">
                        <?=$GLOBALS['dictionary']['MSG_ALL_SLUZHBA_PIDTRIMKI']?>
                        <?php echo $arrowDown?>
                    </button>
                    <div class="support_phones">
                        <a href="tel:<?=str_replace(" ","",$GLOBALS['site_settings']['SUPPORT_PHONE_1'])?>">
                            <img src="/public/images/common/lifecell.svg" alt="lifecell">
                            <?=$GLOBALS['site_settings']['SUPPORT_PHONE_1']?>
                        </a>
                        <a href="tel:<?=str_replace(" ","",$GLOBALS['site_settings']['SUPPORT_PHONE_2'])?>">
                            <img src="/public/images/common/kyivstar.svg" alt="kyivstar">
                            <?=$GLOBALS['site_settings']['SUPPORT_PHONE_2']?>
                        </a>
                    </div>
                </div>
                <a href="<?=$privateLink?>" class="link">
                    <?=$GLOBALS['dictionary']['MSG_ALL_OSOBISTIJ_KABINET']?>
                </a>
            </div>
        </div>
    </div>
    <div class="mobile_menu_overlay overlay" onclick="toggleMobileMenu()"></div>