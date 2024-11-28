<?php if ( !isset($_SESSION['order']['tour_id'])){header('Location:'.$Router->writelink(1));}
Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
Header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
Header("Pragma: no-cache"); // HTTP/1.1
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
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
        <div class="main_filter_wrapper">
            <div class="container">
                <!--?php include $_SERVER['DOCUMENT_ROOT'].'/public/blocks/filter.php'?-->
            </div>
        </div>
        <div class="purchase_steps_wrapper">
            <div class="tabs_links_container">
                <div class="purchase_steps">
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">1. <?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_VIBIR_AVTOBUSA']?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">2. <?=$Router->writetitle(85)?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title active">3. <?=$Router->writetitle(86)?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page_content_wrapper">
            <div class="container">
                <?php $ticketInfo = $Db->getOne(" SELECT
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
                    ");
                $month = $Db->getOne("SELECT title_".$Router->lang." AS title FROM ".DB_PREFIX."_months WHERE id = '".(int)explode('-',$_SESSION['order']['date'])[1]."' ");
                $paymentDateTime = (int)explode('-',$_SESSION['order']['date'])[2].' '.$month['title'].' '.date('H:i',strtotime($ticketInfo['departure_time']));
                $totalPrice = $_SESSION['order']['passengers'] * $ticketInfo['price'];
                ?>
                <div class="flex-row gap-30 booking_blocks">
                    <div class="col-xl-7 col-xs-12">
                        <div class="paymethods_block shadow_block">
                            <div class="block_title h2_title"><?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_OBERITI_SPOSIB_OPLATI']?></div>
                            <div class="paymethods_block_subtitle par">
                                <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_DLYA_OFORMLENNYA_ZAMOVLENNYA_OPLATITI_JOGO_DO'].' '.$paymentDateTime?>
                            </div>
                            <div class="paymethod_rows">
                                <div class="paymethod_row flex_ac flex-row">
                                    <div class="col-sm-6 col-xs-9">
                                        <label class="c_checkbox_wrapper flex_ac">
                                            <input type="radio" name="paymethod" class="c_checkbox_checker" hidden data-cardpay="true" value="cardpay">
                                            <span class="c_checkbox"></span>

                                            <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_BANKIVSIKA_KARTKA']?></span>
                                        </label>
                                    </div>
                                    <div class="col-md-3 hidden-sm hidden-xs">
                                        <div class="paymethod_logo">
                                            <img src="/public/images/common/bank_card.svg" alt="bank card">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-3">
                                        <div class="total_price pay_total h3_title">
                                            <?=$totalPrice.' '.$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_GRN']?>
                                        </div>
                                    </div>
                                </div>

                                <div class="paymethod_row flex_ac flex-row">
                                    <div class="col-sm-6 col-xs-9">
                                        <label class="c_checkbox_wrapper flex_ac">
                                            <input type="radio" name="paymethod" class="c_checkbox_checker" hidden data-cardpay="false" value="cash" checked>
                                            <span class="c_checkbox"></span>
                                            <span class="c_checkbox_title par"><?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_GOTIVKOYU']?></span>
                                        </label>
                                    </div>
                                    <div class="col-md-3 hidden-sm hidden-xs">
                                        <div class="paymethod_logo">
                                            <img src="/public/images/common/cash.svg" alt="bank card">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-3">
                                        <div class="total_price pay_total h3_title">
                                            <?=$totalPrice.' '.$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_GRN']?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="hidden">
                            <?php


                            require_once('private/LiqPay.php');
                            require_once('private/payment_keys.php');
                            $orderId = $_SESSION['order']['order_id'];
                            $total_sum = $totalPrice;
                            $order = $_SESSION['order'];

                            $server_url = 'https://maxtransltd.com/public/pages/liqPaycallback.php';
                            $result_url = 'https://maxtransltd.com/dyakuyu-za-bronyuvannya-biletu/';
                            // Подготовка объекта LiqPay
                            $liqpay = new LiqPay($public_key, $private_key);

                            // Формирование данных для платежа
                            $data = array(
                                'action'         => 'pay',
                                'amount'         => $total_sum, // сумма платежа
                                'currency'       => 'UAH',
                                'description'    => 'Билет на маршрут ' . $ticketInfo['departure_city'] . ' ' . $ticketInfo['departure_station'] . ' - ' . $ticketInfo['arrival_city'] . ' ' . $ticketInfo['arrival_station'] . ' на ' . $order['date'] . ', ' . $order['passengers'] . ' пассажиров. Покупатель: ' . $order['email'] .' ' . $order['name'] .' ' . $order['family_name'] .' ' . $order['phone'] .'',
                                'order_id'       => $orderId,
                                'version'        => '3',
                                'server_url'     => $server_url, // URL для уведомлений о статусе платежа
                                'result_url'     => $result_url,
                            );

                            // Формирование HTML-кода формы оплаты
                            $liqpayForm = $liqpay->cnb_form($data);
                            ?>

                            <!-- Вывод формы оплаты на страницу-->
                            <?=$liqpayForm?>

                        </div>


                        <button class="payment_btn h4_title blue_btn flex_ac" onclick="orderTicket()">
                            <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_OPLATITI']?>
                        </button>
                        <div class="payment_clarification par">
                            <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_VASHI_PLATIZHNI_TA_OSOBISTI_DANI_NADIJNO_ZAHISCHENI']?>
                        </div>
                    </div>
                    <div class="col-xxl-1 hidden-xl hidden-lg hidden-md hidden-sm hidden-xs"></div>
                    <div class="col-xxl-4 col-xl-5 col-xs-12">
                        <div class="route_block">
                            <div class="route_block_title h3_title hidden-md hidden-sm hidden-xs">
                                <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_MARSHRUT']?>
                            </div>
                            <div class="mobile_route_block_title flex_ac h3_title hidden-xxl hidden-xl hidden-lg" onclick="toggleRouteInfo(this)">
                                <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_MARSHRUT']?>
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
                                    <div class="route_options flex-row gap-y-20">
                                        <div class="payment_date_wrapper">
                                            <div class="payment_date_title par"><?= $GLOBALS['dictionary']['MSG_ALL_KOLI'] ?></div>
                                            <div class="payment_date"><?=$_SESSION['order']['date']?></div>
                                        </div>
                                    </div>
                                    <div class="route_options flex-row gap-y-20">
                                        <?php $getBusOptions = $Db->getall("SELECT title_".$Router->lang." AS title FROM `".DB_PREFIX."_buses_options` 
                                         WHERE id IN(SELECT option_id FROM `".DB_PREFIX."_buses_options_connector` WHERE bus_id = '".$ticketInfo['bus']."' )");
                                        foreach ($getBusOptions AS $k=>$busOption){?>
                                            <div class="col-md-<?php if ($k % 2 == 0){echo '5';}else{echo '7';}?>">
                                                <div class="bus_option flex_ac par">
                                                    <div class="check_imitation"></div>
                                                    <?=$busOption['title']?>
                                                </div>
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div class="route_passagers h5_title">
                                        <span><?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_PASAZHIRIV']?></span>
                                        <span><?=$_SESSION['order']['passengers']?></span>
                                    </div>
                                </div>
                                <div class="route_details_delimiter"></div>
                                <div class="route_details_info">
                                    <div class="route_price h4_title flex_ac">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_CINA']?>
                                        <span class="total_price h3_title">

                                    <?=$ticketInfo['price'].' '.$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_GRN']?>
                                        </span>
                                    </div>
                                    <div class="route_price h4_title flex_ac route_payment_price">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_DO_SPLATI']?>
                                        <span class="total_price h3_title">
                                        <?=$totalPrice.' '.$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_GRN']?>
                                        </span>
                                    </div>
                                    <?php/*
                                    <div class="route_price_promo_clarification">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_PROMOKOD']?>
                                        <span class="route_price_promo">
                                            ХЕТ43ЕЕЕ
                                        </span>
                                    </div>
                                    */?>
                                    <button class="payment_btn h4_title flex_ac blue_btn" onclick="orderTicket()">
                                        <?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_OPLATITI']?>
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
<div class="d_none">
    <?php out($ticketInfo)?>
</div>
<script src="/public/libs/jquery_maskedinput/jquery.maskedinput.min.js"></script>
<script>
    $('#card_number').mask("9999 9999 9999 9999");
    $('#card_valid_date').mask("99/99");
    $('#card_cvv').mask("999");

    function deleteOrderTourId() {
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'delete_order_tour_id'
            }
        });
    }

    var ticketInfo = <?php echo json_encode($ticketInfo); ?>;
    var order = <?php echo json_encode($order); ?>;

    function orderTicket(){
        let card_number = $.trim($('#card_number').val());
        let card_valid_date = $.trim($('#card_valid_date').val());
        let card_cvv = $.trim($('#card_cvv').val());
        let cardholder_name = $.trim($('#cardholder_name').val());
        let saveCard = 0;
        let paymethod = $('input[name="paymethod"]:checked').val();
        if ($('#save_card').is(':checked')){
            saveCard = 1;
        };
        initLoader();
        $.ajax({
            type:'post',
            url:'<?=$Router->writelink(3)?>',
            data:{
                'request':'order_route',
                'paymethod':paymethod,
                'card_number':card_number,
                'card_valid_date':card_valid_date,
                'card_cvv':card_cvv,
                'cardholder_name':cardholder_name,
                'save_card':saveCard,
                'ticket_info': ticketInfo,
                'order': order
            },
            success:function(response){
                removeLoader();
                if ($.trim(response) == 'ok') {
                    console.log("TI:", ticketInfo, "order:", order);

                    // Отправляем запрос только если метод оплаты - "cash"
                    if (paymethod === 'cash') {
                        $.ajax({
                            type: 'post',
                            url: '<?=$Router->writelink(3)?>',
                            data: {
                                'request':'order_mail',
                                'ticket_info': ticketInfo,
                                'order': order
                            },
                            success: function(emailResponse) {
                                if (emailResponse == 'ok') {
                                    console.log('Email sent successfully');
                                } else {
                                    console.error(emailResponse);
                                }
                            }
                        });
                    }

                    // Если запись заказа в базу данных успешна, и выбран метод оплаты картой,
                    // триггерим клик на кнопку LiqPay
                    if ($('input[name=paymethod]:checked').data('cardpay')) {
                        $('#liqpay_form').submit();
                    } else {
                        // Если метод оплаты не карта, перенаправляем пользователя на страницу благодарности
                        location.href = '<?=$Router->writelink(90)?>';
                    }
                    deleteOrderTourId();
                } else {
                    // Если запись не успешна, выводим сообщение об ошибке
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_NE_UDALOSI_OFORMITI_ZAKAZ_POPROBUJTE_POZZHE']?>');
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error('AJAX error: ', textStatus, errorThrown);
                out('<?=$GLOBALS['dictionary']['MSG_MSG_PAYMENT_PAGE_NE_UDALOSI_OFORMITI_ZAKAZ_POPROBUJTE_POZZHE']?>');
            }
        })
    };


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
        ]
    });

    $(document).ready(function(){
        if ($(window).width() < 576){
            $('.purchase_steps').slick('slickGoTo',2 , true)
        }
    });

    $('.doc_select').niceSelect();

    function toggleRouteInfo(item){
        $('.route').slideToggle();
        $(item).find('img').toggleClass('rotate');
    };

    $('input[name=paymethod]').on('change',function(){
        if ($(this).data('cardpay')){
            $('.payment_data').show();
        }else{
            $('.payment_data').hide();
        }
    });

    function toggleCvv(item){
        $(item).toggleClass('active');
        if ($(item).hasClass('active')){
            $('.cvv_input').attr('type','text');
        }else {
            $('.cvv_input').attr('type','password');
        }
    };

    function testMail(){
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request':'order_mail',
                'ticket_info': ticketInfo,
                'order': order
            },
            success: function(emailResponse) {
                if (emailResponse == 'ok') {
                    console.log('Email sent successfully');
                } else {
                    console.error(emailResponse);
                }
            }
        });
    };
</script>
</body>
</html>