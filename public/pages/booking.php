<?if (!isset($_SESSION['order']['tour_id'])){header('Location:'.$Router->writelink(1));}?>
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
        <div class="main_filter_wrapper">
            <div class="container">
                <!--?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/filter.php'?-->
            </div>
        </div>
        <div class="purchase_steps_wrapper">
            <div class="tabs_links_container">
                <div class="purchase_steps flex_ac">
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">1. <?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_VIBIR_AVTOBUSA']?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title active">2. <?=$Router->writetitle(85)?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">3. <?=$Router->writetitle(86)?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page_content_wrapper">
            <div class="container">
                <? $ticketInfo = $Db->getOne(" SELECT
                    from_stop.departure_time AS departure_time,
                    from_city.title_".$Router->lang." AS departure_station,
                    departure_city.title_".$Router->lang." AS departure_city,
                    to_stop.arrival_time AS arrival_time,
                    to_city.title_".$Router->lang." AS arrival_station,
                    arrival_city.title_".$Router->lang." AS arrival_city,
                    bus.title_".$Router->lang." AS bus,
                    prices.price AS price
                    FROM ".DB_PREFIX."_tours_stops AS from_stop
                    JOIN ".DB_PREFIX."_cities AS from_city ON from_stop.stop_id = from_city.id
                    JOIN ".DB_PREFIX."_tours AS tours ON from_stop.tour_id = tours.id
                    JOIN ".DB_PREFIX."_cities AS departure_city ON departure_city.id = tours.departure
                    JOIN ".DB_PREFIX."_tours_stops AS to_stop ON from_stop.tour_id = to_stop.tour_id
                    JOIN ".DB_PREFIX."_cities AS to_city ON to_stop.stop_id = to_city.id
                    JOIN ".DB_PREFIX."_cities AS arrival_city ON arrival_city.id = tours.arrival
                    JOIN ".DB_PREFIX."_buses AS bus ON tours.bus = bus.id
                    JOIN ".DB_PREFIX."_tours_stops_prices AS prices ON
                            prices.tour_id = from_stop.tour_id AND
                            prices.from_stop = from_stop.stop_id AND
                            prices.to_stop = to_stop.stop_id
                    WHERE from_stop.tour_id = '".(int)$_SESSION['order']['tour_id']."' 
                    AND from_stop.stop_id = '".(int)$_SESSION['order']['from']."' 
                    AND to_stop.stop_id = '".(int)$_SESSION['order']['to']."'
                    "); ?>
                <div class="flex-row gap-30 booking_blocks">
                    <div class="col-xxl-7 col-xs-12">
                        <div class="ticket_order_block shadow_block">
                            <div class="block_title h2_title">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_OFORMLENNYA_KVITKA']?>
                            </div>
                            <div class="ticket_order_block_subtitle par">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_ZAZNACHENI_DANI_NEOBHIDNI_DLYA_ZDIJSNENNYA_BRONYUVANNYA_I_BUDUTI_PEREVIRENI_PID_CHAS_POSADKI_V_AVTOBUS']?>
                            </div>
                            <div class="customer_data">
                                <?$clientInfo = $Db->getOne("SELECT name,second_name,patronymic,email,phone,birth_date,phone_code FROM `".DB_PREFIX."_clients` WHERE id = '".$User->id."' "); ?>
                                <div class="flex-row gap-y-26 gap-x-30">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="row">
                                            <input type="text" class="c_input par req_input" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PRIZVISCHE']?>" id="family_name"
                                                <?if (trim($clientInfo['second_name']) != ''){?>
                                                    value="<?=$clientInfo['second_name']?>"
                                                <?}?>
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="row">
                                            <input type="text" class="c_input par req_input" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_IMYA_']?>" id="name"
                                                <?if (trim($clientInfo['name']) != ''){?>
                                                    value="<?=$clientInfo['name']?>"
                                                <?}?>>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="row">
                                            <input type="text" class="c_input par " placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PO-BATIKOVI_']?>" id="patronymic"
                                                <?if (trim($clientInfo['patronymic']) != ''){?>
                                                    value="<?=$clientInfo['patronymic']?>"
                                                <?}?>
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="row ">
                                            <div class="client_birth_date_wrapper">
                                                <input type="text" class="c_input par  calendar_input" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_DATA_NARODZHENNYA_']?>" id="birthdate"
                                                    <?if ($clientInfo['birth_date'] != '0000-00-00'){?>
                                                        value="<?=$clientInfo['birth_date']?>"
                                                    <?}?>
                                                >
                                                <button class="input_calendar_btn" onclick="toggleBirthDateCalendar()">
                                                    <img src="/public/images/common/input_calendar.svg" alt="calendar">
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?/*
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <select class="doc_select par" id="doc_select">
                                                <option value="" selected hidden disabled><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_TIP_DOKUMENTA']?></option>
                                                <?$getBookingDocs = $Db->getAll("SELECT id,title_".$Router->lang." AS title FROM `".DB_PREFIX."_client_booking_docs` WHERE active = '1' ORDER BY sort DESC");
                                                foreach ($getBookingDocs AS $k=>$bookingDoc){?>
                                                    <option value="<?=$bookingDoc['id']?>"><?=$bookingDoc['title']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>*/?>
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="ticket_seat par flex_ac">
                                            <span><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_MISCE_V_AVTOBUSI']?></span>
                                            <span><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_VILINA_ROZSADKA']?></span>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $passengers = $_SESSION['order']['passengers'];

                                for ($i = 1; $i < $passengers; $i++) {
                                    generatePassengerInputs($i, $clientInfo[$i] ?? []);
                                }

                                function generatePassengerInputs($index, $clientInfo) {
                                    $passenger_count = $index + 1;
                                    ?>
                                    <div class="ticket_order_block_subtitle passengers_inputs">
                                        Контактные данные пассажира <?= $passenger_count ?>
                                    </div>
                                    <div class="flex-row gap-y-26 gap-x-30">


                                        <div class="col-sm-6 col-xs-12">
                                            <div class="row">
                                                <input type="text" class="c_input par req_input" placeholder="<?= $GLOBALS['dictionary']['MSG_MSG_BOOKING_PRIZVISCHE'] ?>" name="passengers[<?= $index ?>][family_name]" value="<?= htmlspecialchars($clientInfo['second_name'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="row">
                                                <input type="text" class="c_input par req_input" placeholder="<?= $GLOBALS['dictionary']['MSG_MSG_BOOKING_IMYA_'] ?>" name="passengers[<?= $index ?>][name]" value="<?= htmlspecialchars($clientInfo['name'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="row">
                                                <input type="text" class="c_input par" placeholder="<?= $GLOBALS['dictionary']['MSG_MSG_BOOKING_PO-BATIKOVI_'] ?>" name="passengers[<?= $index ?>][patronymic]" value="<?= htmlspecialchars($clientInfo['patronymic'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="row ">
                                                <div class="client_birth_date_wrapper">
                                                    <input type="text" class="c_input par calendar_input" placeholder="<?= $GLOBALS['dictionary']['MSG_MSG_BOOKING_DATA_NARODZHENNYA_'] ?>" name="passengers[<?= $index ?>][birthdate]" value="<?= htmlspecialchars($clientInfo['birth_date'] ?? '') ?>">
                                                    <button class="input_calendar_btn" onclick="toggleBirthDateCalendar()">
                                                        <img src="/public/images/common/input_calendar.svg" alt="calendar">
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?
                                };
                                ?>

                            </div>
                        </div>
                        <div class="customer_contact_data shadow_block">
                            <div class="block_title h2_title">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_KONTAKTNA_INFORMACIYA']?>
                            </div>
                            <div class="par">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_VKAZUJTE_KOREKTNI_E-MAIL']?>
                            </div>
                            <div class="customer_data">
                                <div class="flex-row gap-y-26 gap-x-30">
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="row">
                                            <input type="text" class="c_input par" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_E-MAIL']?>" id="email" value="<?=$clientInfo['email']?>" pattern="[^\u0400-\u04FF]*" maxlength="255" oninput="this.value = this.value.replace(/[^\x00-\x7F]/g, ''); validateEmail(this)" >
                                            <span id="email-error" style="display: none; color: red;"><?=$GLOBALS['dictionary']['MSG_MSG_REGISTER_EMAIL_UKAZAN_NEVERNO']?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-xs-12">
                                        <div class="phone_input_wrapper flex_ac">
                                            <select class="phone_country_code flex_ac" onchange="changeInputMask(this)">
                                                <?if ($clientInfo['phone_code'] > 0){
                                                    $getFirstPhoneData = $Db->getOne("SELECT phone_example,phone_mask FROM `".DB_PREFIX."_phone_codes` WHERE id = '".(int)$clientInfo['phone_code']."' ");
                                                    $firstPhoneExample = $getFirstPhoneData['phone_example'];
                                                    $firstPhoneMask = $getFirstPhoneData['phone_mask'];
                                                }

                                                $getPhoneCodes = $Db->getAll("SELECT * FROM `".DB_PREFIX."_phone_codes` WHERE active = '1' ORDER BY sort DESC");
                                                foreach ($getPhoneCodes AS $k=>$phoneCode){
                                                    if ($k == 0 && $clientInfo['phone_code'] == 0){
                                                        $firstPhoneExample = $phoneCode['phone_example'];
                                                        $firstPhoneMask = $phoneCode['phone_mask'];
                                                    }?>
                                                    <option value="<?=$phoneCode['id']?>" data-mask="<?=$phoneCode['phone_mask']?>" data-placeholder="<?=$phoneCode['phone_example']?>"
                                                        <?if ($clientInfo['phone_code'] == $phoneCode['id']){echo 'selected';}?>>
                                                        <?=$phoneCode['phone_country']?>
                                                    </option>
                                                <?}?>
                                            </select>
                                            <input type="text" class="customer_phone_input inter req_input" placeholder="<?=$firstPhoneExample?>" id="phone"
                                                <?if (trim($clientInfo['phone']) != ''){?>
                                                    value="<?=$clientInfo['phone']?>"
                                                <?}?>
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="customer_contact_data_bottom flex_ac">
                                <label class="c_checkbox_wrapper flex_ac">
                                    <input type="checkbox" class="c_checkbox_checker" hidden>
                                    <span class="c_checkbox"></span>
                                    <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_NADSILAJTE_MENI_ZNIZHKI_TA_IDE_BYUDZHETNIH_PODOROZHEJ']?></span>
                                </label>
                                <button class="have_promocode_btn par flex_ac" onclick="togglePromocodeBlock()">
                                    <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_U_MENE__PROMOKOD']?>
                                    <img src="/public/images/common/blue_arrow_down.svg" alt="arrow down">
                                </button>
                            </div>
                        </div>
                        <div class="customer_promocode shadow_block">
                            <div class="customer_promocode_header">
                                <div class="block_title h2_title flex_ac customer_promocode_block_title">
                                    <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PROMOKOD']?>
                                    <span class="customer_promocode_clarification par">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_OPCIONALINO']?>
                                    </span>
                                </div>
                                <button class="close_customer_promocode" onclick="togglePromocodeBlock()">
                                    <img src="/public/images/common/close.svg" alt="close">
                                </button>
                            </div>
                            <div class="row">
                                <input type="text" class="c_input par" placeholder="<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PROMOKOD']?>">
                            </div>
                        </div>
                        <div class="for_payment shadow_block">
                            <div class="for_payment_title h2_title flex_ac">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_DO_SPLATI']?>
                                <span class="total_price h2_title">
                                <?php
                                $totalPrice = $_SESSION['order']['passengers'] * $ticketInfo['price'];
                                ?>
                                <?=$totalPrice.' '.$GLOBALS['dictionary']['MSG_MSG_BOOKING_GRN']?>
                                </span>
                            </div>
                            <div class="for_payment_subtitle par">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_VASHI_PLATIZHNI_']?>
                            </div>
                            <div class="for_payment_paymethod_logos flex_ac">
                                <div class="row">
                                    <img src="/public/images/common/maestro.svg" alt="maestro" class="fit_img">
                                </div>
                                <div class="row">
                                    <img src="/public/images/common/mastercard.svg" alt="mastercard" class="fit_img">
                                </div>
                                <div class="row">
                                    <img src="/public/images/common/visa.svg" alt="visa" class="fit_img">
                                </div>
                            </div>
                            <div class="for_payment_accept">
                                <label class="c_checkbox_wrapper flex_ac">
                                    <input type="checkbox" class="c_checkbox_checker" hidden id="terms_accept" checked>
                                    <span class="c_checkbox"></span>
                                    <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_YA_PRIJMAYU_UMOVI']?> <a href="<?=$Router->writelink(84)?>" class="small_link"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PUBLICHNO_OFERTI']?></a>, <a href="<?=$Router->writelink(83)?>" class="small_link"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_POLITIKI_KONFIDENCIJNOSTI']?></a> <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_I']?> <a href="<?=$Router->writelink(87)?>" class="small_link"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_POVERNENNYA']?></a></span>
                                </label>
                                <label class="c_checkbox_wrapper flex_ac">
                                    <input type="checkbox" class="c_checkbox_checker req_check" hidden id="personal_data_process" checked>
                                    <span class="c_checkbox"></span>
                                    <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_YA_DAYU_ZGODU_NA_OBROBKU_PERSONALINIH_DANIH']?></span>
                                </label>
                                <label class="c_checkbox_wrapper flex_ac">
                                    <input type="checkbox" class="c_checkbox_checker" hidden id="save_my_data">
                                    <span class="c_checkbox"></span>
                                    <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_ZBEREGTI_DANI']?></span>
                                </label>
                            </div>
                        </div>
                        <button class="payment_btn h4_title flex_ac blue_btn" onclick="goPayment()">
                            <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PEREJTI_DO_OPLATI']?>
                        </button>
                        <div class="payment_clarification par">
                            <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_VASHI_PLATIZHNI_TA_OSOBISTI_DANI_NADIJNO_ZAHISCHENI']?>
                        </div>
                    </div>
                    <div class="col-xxl-1 hidden-xxl hidden-lg hidden-md hidden-sm hidden-xs"></div>
                    <div class="col-xxl-4 col-xs-12">
                        <div class="route_block">
                            <div class="route_block_title h3_title hidden-md hidden-sm hidden-xs">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_MARSHRUT']?>
                            </div>
                            <div class="mobile_route_block_title flex_ac h3_title hidden-xxl hidden-xl hidden-lg" onclick="toggleRouteInfo(this)">
                                <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_MARSHRUT']?>
                                <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                            </div>
                            <div class="route">
                                <div class="route_details_info">
                                    <div class="route_points">
                                        <div class="route_point_block par">
                                            <div class="route_point active"></div>
                                            <div class="route_time">
                                                <?=date('H:i',strtotime($ticketInfo['departure_time']))?>
                                            </div>
                                            <div class="route_point_title">
                                                <?=$ticketInfo['departure_city'].' '.$ticketInfo['departure_station']?>
                                            </div>
                                        </div>

                                        <div class="route_point_block par">
                                            <div class="route_point"></div>
                                            <div class="route_time">
                                                <?=date('H:i',strtotime($ticketInfo['arrival_time']))?>
                                            </div>
                                            <div class="route_point_title">
                                                <?=$ticketInfo['arrival_city'].' '.$ticketInfo['arrival_station']?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="filter_block_wrapper ">
                                        <div class="filter_date_wrapper">
                                            <div class="filter_date_title par"><?= $GLOBALS['dictionary']['MSG_ALL_KOLI'] ?></div>
                                            <input type="text" class="filter_date_booking" name="date">
                                            <button class="filter_calendar_btn" onclick="toggleFilterCalendar()" type="button">
                                                <img src="/public/images/common/filter_calendar.svg" alt="calendar" class="fit_img">
                                            </button>
                                        </div>
                                    </div>
                                    <div class="route_options flex-row gap-y-20">

                                        <?$getBusOptions = $Db->getall("SELECT title_".$Router->lang." AS title FROM `".DB_PREFIX."_buses_options` 
                                         WHERE id IN(SELECT option_id FROM `".DB_PREFIX."_buses_options_connector` WHERE bus_id = '".$ticketInfo['bus']."' )");
                                        foreach ($getBusOptions AS $k=>$busOption){?>
                                            <div class="col-md-<?if ($k % 2 == 0){echo '5';}else{echo '7';}?>">
                                                <div class="bus_option flex_ac par">
                                                    <div class="check_imitation"></div>
                                                    <?=$busOption['title']?>
                                                </div>
                                            </div>
                                        <?}?>
                                    </div>
                                    <div class="route_passagers h5_title">
                                        <span><?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PASAZHIRIV']?></span>
                                        <span><?=$_SESSION['order']['passengers']?></span>
                                    </div>
                                </div>
                                <div class="route_details_delimiter"></div>
                                <div class="route_details_info">
                                    <div class="route_price h4_title flex_ac">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_CINA']?>
                                        <span class="total_price h3_title">
                                            <?=$ticketInfo['price'].' '.$GLOBALS['dictionary']['MSG_MSG_BOOKING_GRN']?>
                                        </span>
                                    </div>
                                    <?php
                                    $totalPrice = $_SESSION['order']['passengers'] * $ticketInfo['price'];
                                    ?>
                                    <div class="route_price h4_title flex_ac route_payment_price">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_DO_SPLATI']?>
                                        <span class="total_price h3_title">
                                        <?=$totalPrice.' '.$GLOBALS['dictionary']['MSG_MSG_BOOKING_GRN']?>
                                        </span>
                                    </div>
                                    <?/*
                                    <div class="route_price_promo_clarification">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PROMOKOD']?>
                                        <span class="route_price_promo">
                                            ХЕТ43ЕЕЕ
                                        </span>
                                    </div>

                                    */?>
                                    <button class="payment_btn h4_title flex_ac blue_btn" onclick="goPayment()">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_PEREJTI_DO_OPLATI']?>
                                    </button>
                                    <a href="<?=$Router->writelink(87)?>" class="small_link"><?=$Router->writetitle(87)?></a>
                                </div>
                            </div>
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
<script src="/public/libs/jquery_maskedinput/jquery.maskedinput.min.js"></script>
<script>
    function validateEmail(input) {
        let email = input.value;
        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        let isValid = emailRegex.test(email);
        let errorSpan = document.getElementById("email-error");

        if (!isValid && email.length > 0) {
            errorSpan.style.display = "inline";
            input.setCustomValidity("Invalid email");
        } else {
            errorSpan.style.display = "none";
            input.setCustomValidity("");
        }
    }


    const clientBirthDatePicker = flatpickr("#birthdate", {
        dateFormat: "Y-m-d",
        locale:'<?=$Router->lang?>',
        static:true,
        "maxDate": threeYearsAgo
    });

    function toggleBirthDateCalendar(){
        clientBirthDatePicker.open()
    }

    function goPayment(){
        let allFieldsFilled = true;
        let family_name = $.trim($('#family_name').val());
        let name = $.trim($('#name').val());
        let patronymic = $.trim($('#patronymic').val());
        let birth_date = $('#birthdate').val();
        <?/*let doc_type = $('#doc_select').val();*/?>
        let email = $.trim($('#email').val());
        let phone = $.trim($('#phone').val());
        let saveMyData = 0;
        let phone_code = $('.phone_country_code').val();

        let passengers = [];
        let totalPassengers = document.querySelectorAll('[name^="passengers"]').length / 4 +1; // Assuming there are 4 fields per passenger

        for (let i = 1; i < totalPassengers; i++) {
            let family_name = $.trim($('input[name="passengers[' + i + '][family_name]"]').val());
            let name = $.trim($('input[name="passengers[' + i + '][name]"]').val());
            let patronymic = $.trim($('input[name="passengers[' + i + '][patronymic]"]').val());
            let birth_date = $('input[name="passengers[' + i + '][birthdate]"]').val();

            passengers.push({
                family_name: family_name,
                name: name,
                patronymic: patronymic,
                birth_date: birth_date,
            });
        }

        if ($('#save_my_data').is(':checked')){
            saveMyData = 1;
        }
        $('.req_input').each(function () {
            if ($.trim($(this).val()) === '') {
                out('<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_ZAPOLNITE_VSE_OBYAZATELINYE_POLYA']?>', '<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_OBYAZATELINYE_POLYA_OTMECHENY_']?>');
                allFieldsFilled = false; // Устанавливаем флаг в false если хотя бы одно поле не заполнено
                return false; // Прерываем цикл
            }
        });

        if (!allFieldsFilled) { // Если хотя бы одно поле не заполнено
            return false; // Прерываем выполнение функции и не отправляем данные
        }
        if (!isEmail(email)){
            out('<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_EMAIL_UKAZAN_NEVERNO']?>');
            return false;
        }
        if (!$('#terms_accept').is(':checked')){
            out('<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_DLYA_OFORMLENIYA_ZAKAZA_VY_DOLZHNY_PRINYATI_USLOVIYA']?>');
            return false;
        }
        if (!$('#personal_data_process').is(':checked')){
            out('<?=$GLOBALS['dictionary']['MSG_MSG_BOOKING_DLYA_OFORMLENIYA_ZAKAZA_VY_DOLZHNY_DATI_SOGLASIE_NA_OBRABOTKU_LICHNYH_DANNYH']?>');
            return false;
        }
        initLoader();
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'check_OrderTicket'
            },
            success: function(response) {
                removeLoader();
                if ($.trim(response) === 'ok') {
                    initLoader();
                    $.ajax({
                        type: 'post',
                        url: '<?=$Router->writelink(3)?>',
                        data: {
                            'request': 'remember_private_data',
                            'family_name': family_name,
                            'name': name,
                            'patronymic': patronymic,
                            'birthDate': birth_date,
                            'email': email,
                            'phone': phone,
                            'save_data': saveMyData,
                            'phone_code': phone_code,
                            'passengers': passengers
                        },
                        success: function (response) {
                            removeLoader();
                            if ($.trim(response) === 'ok') {
                                location.href = '<?=$Router->writelink(86)?>';
                            } else {
                                out('Ошибка');
                            }
                        }
                    });
                } else if ($.trim(response) === 'soldout') {
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_NET_SVOBODNYH_MEST']?>');
                } else if ($.trim(response) === 'late') {
                    console.log(response);
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_ETOT_BILET_BOLISHE_KUPITI_NELIZYA_TK_ETOT_REJS_UZHE_UEHAL']?>');
                }
            }
        });
    }

    $('.purchase_steps').slick({
        slidesToShow:4,
        slidesToScroll:1,
        dots:false,
        arrows:false,
        infinite:false,
        variableWidth: true,
        responsive:[
            {
                breakpoint: 576,
                settings: {
                    infinite:false,
                    slidesToShow: 1
                }
            },
        ],
    });

    $(document).ready(function(){
        if ($(window).width() < 576){
            $('.purchase_steps').slick('slickGoTo',1 , true)
        }
    });

    <?/*$('.doc_select').niceSelect();*/?>
    $('.phone_country_code').niceSelect();
    $('.customer_phone_input').mask("<?=$firstPhoneMask?>");
    function changeInputMask(item){
        let selectedOption = $(item).find(':selected');
        $('.customer_phone_input').mask($(selectedOption).data('mask'));
        $('.customer_phone_input').attr('placeholder',$(selectedOption).data('placeholder'));
    };

    function toggleRouteInfo(item){
        $('.route').slideToggle();
        $(item).find('img').toggleClass('rotate');
    };

    function togglePromocodeBlock(){
        $('.customer_promocode').slideToggle();
    };

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector(".filter_date_booking").click();
    });

</script>
</body>
</html>