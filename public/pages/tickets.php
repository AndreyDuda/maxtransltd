<!DOCTYPE html>
<html lang="<?= $Router->lang ?>">
<head>
    <link rel="stylesheet" href="/public/libs/jquery_ui_slider/jquery-ui.min.css">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/head.php' ?>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/header.php' ?>
    </div>
    <div class="content">
        <div class="main_filter_wrapper">
            <div class="container">
                <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/filter.php' ?>
            </div>
        </div>
        <div class="purchase_steps_wrapper">
            <div class="tabs_links_container">
                <div class="purchase_steps">
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title active">1. <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_VIBIR_AVTOBUSA'] ?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">2. <?= $Router->writetitle(85) ?></div>
                    </div>
                    <div class="purchase_step_wrapper">
                        <div class="purchase_step h4_title">3. <?= $Router->writetitle(86) ?></div >
                    </div>
                </div>
            </div>
        </div>
        <div class="page_content_wrapper">
            <?$filterParams = '';
            if ($filterDeparture > 0) {
                $filterParams .= " AND (t.departure = ".$filterDeparture." OR t.id 
                IN(SELECT tour_id FROM ".DB_PREFIX."_tours_stops_prices WHERE from_stop 
                IN(SELECT id FROM ".DB_PREFIX."_cities WHERE section_id = '".$filterDeparture."' ) ))";
                $departureCityTitle = $Db->getOne("SELECT title_" . $Router->lang . " AS title FROM `" . DB_PREFIX . "_cities` WHERE id = '" . $filterDeparture . "' ");

            }
            if ($filterArrival > 0) {
                $filterParams .= " AND (t.arrival = ".$filterArrival." OR t.id 
                IN(SELECT tour_id FROM ".DB_PREFIX."_tours_stops_prices WHERE to_stop 
                IN(SELECT id FROM ".DB_PREFIX."_cities WHERE section_id = '".$filterArrival."' ) ))";
                $arrivalCityTitle = $Db->getOne("SELECT title_" . $Router->lang . " AS title FROM `" . DB_PREFIX . "_cities` WHERE id = '" . $filterArrival . "' ");
            }
            $weekDay = date('N',time());
            if ($filterDate !== "today") {
                $weekDay = date('N',strtotime($filterDate));
                $filterParams .= " AND t.days LIKE '%".$weekDay."%' ";
                //$dateForCheckDateParam = implode('-',array_map('intval',$dateArray));
                $filterMonth = $Db->getOne("SELECT title_".$Router->lang." AS title FROM ".DB_PREFIX."_months WHERE id = '".(int)explode('-',$filterDate)[1]."' ");
            }
            $dateParam = "";
            /*if (strtotime($dateForCheckDateParam) == strtotime(date('Y-m-d',time()))){
                $dateParam = " AND ts.departure_time > CURRENT_TIME()";
            }*/

            $minTicketsPrice = 0;
            $maxTicketsPrice = 1;
            $priceFilterParam = "";
            if ($filterDeparture > 0){
                $priceFilterParam .= " AND tsp.from_stop IN(SELECT id FROM ".DB_PREFIX."_cities WHERE section_id = '".$filterDeparture."' )";
            }if ($filterArrival > 0){
                $priceFilterParam .= " AND tsp.to_stop IN(SELECT id FROM ".DB_PREFIX."_cities WHERE section_id = '".$filterArrival."' )";
            }
            $getTicketsPrices = $Db->getAll("SELECT MAX(tsp.price) AS price FROM ".DB_PREFIX."_tours_stops_prices tsp
            LEFT JOIN ".DB_PREFIX."_tours t ON t.id = tsp.tour_id
            LEFT JOIN ".DB_PREFIX."_tours_stops ts ON ts.tour_id = tsp.tour_id
            WHERE t.active = 1 $dateParam $priceFilterParam 
            GROUP BY tsp.tour_id 
            ORDER BY tsp.id ASC");
            $pricesArray = [];
            if ($getTicketsPrices){
                foreach ($getTicketsPrices AS $k=>$ticketsPrice){
                    $pricesArray[] = $ticketsPrice['price'];
                }

                $minTicketsPrice = min($pricesArray);
                $maxTicketsPrice = max($pricesArray);
            }

            $pagination = pagination("SELECT id FROM `" . DB_PREFIX . "_tours` t WHERE active = '1' $dateParam $filterParams ",6);

            $getTickets = $Db->getAll("SELECT DISTINCT(t.id),t.departure,t.arrival,t.days,
            dc.title_".$Router->lang." AS departure_city,
            dc.section_id AS departure_city_section_id,
            ac.title_".$Router->lang." AS arrival_city,
            ac.section_id AS arrival_city_section_id,
            b.title_" . $Router->lang . " AS bus_title 
            FROM `" . DB_PREFIX . "_tours` t 
            LEFT JOIN ".DB_PREFIX."_cities dc ON dc.id = t.departure
            LEFT JOIN ".DB_PREFIX."_cities ac ON ac.id = t.arrival
            LEFT JOIN `" . DB_PREFIX . "_buses` b ON t.bus = b.id 
            LEFT JOIN ".DB_PREFIX."_tours_stops_prices tsp ON tsp.tour_id = t.id 
            LEFT JOIN ".DB_PREFIX."_tours_stops ts ON ts.tour_id = t.id
            WHERE t.active = '1' $dateParam $priceFilterParam $filterParams 
            ORDER BY tsp.price DESC LIMIT ".$pagination['from'].",".$pagination['per_page']); ?>
            <div class="container">
                <?php if (empty($getTickets)): ?>
                <div class="ticket_page_title h2_title reccomend_title"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_RECOMMEND_DATES']?></div>
                <div class="recommend_dates">
                    <?php

                        $daysResult = $Db->getAll("SELECT DISTINCT t.days 
                        FROM `" . DB_PREFIX . "_tours` t 
                        LEFT JOIN " . DB_PREFIX . "_cities dc ON dc.id = t.departure
                        LEFT JOIN " . DB_PREFIX . "_cities ac ON ac.id = t.arrival
                        LEFT JOIN " . DB_PREFIX . "_cities dcountry ON dcountry.id = dc.section_id
                        LEFT JOIN `" . DB_PREFIX . "_buses` b ON t.bus = b.id  
                        LEFT JOIN " . DB_PREFIX . "_tours_stops_prices tsp ON tsp.tour_id = t.id
                        LEFT JOIN " . DB_PREFIX . "_tours_stops ts ON ts.tour_id = t.id
                        WHERE t.active = '1' AND (t.departure = " . $filterDeparture . " OR t.id 
                        IN(SELECT tour_id FROM " . DB_PREFIX . "_tours_stops_prices WHERE from_stop 
                        IN(SELECT id FROM " . DB_PREFIX . "_cities WHERE section_id = '" . $filterDeparture . "' ) ))
                         AND (t.arrival = " . $filterArrival . " OR t.id 
                        IN(SELECT tour_id FROM " . DB_PREFIX . "_tours_stops_prices WHERE to_stop 
                        IN(SELECT id FROM " . DB_PREFIX . "_cities WHERE section_id = '" . $filterArrival . "' ) )) 
                        ORDER BY dc.section_id ASC,tsp.price DESC");
                        $getMonths = $Db->getAll("SELECT id, title_" . $Router->lang . " AS title FROM " . DB_PREFIX . "_months");

                        // Преобразуем результат запроса в ассоциативный массив
                        $months = [];
                        foreach ($getMonths as $month) {
                            $months[$month['id']] = $month['title'];
                        }
                        $availableDays = [];
                        foreach ($daysResult as $row) {
                            $daysOfWeek = explode(',', $row['days']);
                            $availableDays = array_merge($availableDays, $daysOfWeek);
                        }
                        $availableDays = array_unique($availableDays);
                        foreach ($availableDays as $dayOfWeek) {
                            $currentWeekDay = date('N');
                            $nearestDay = ($currentWeekDay <= $dayOfWeek) ? ($dayOfWeek - $currentWeekDay) : (7 - $currentWeekDay + $dayOfWeek);
                            $nearestDate = date('Y-m-d', strtotime("+$nearestDay days"));
                            $weekday = $daysOfWeek[date('N', strtotime($nearestDate))];
                            $date = date('d', strtotime("+$nearestDay days"));
                            $monthId = date('n', strtotime($nearestDate));
                            $month = $months[$monthId];
                            echo '<div class="reccomend_date blue_btn"><a class="tour_date_link" href="#" data-date="' . $nearestDate . '">' . $date . ' ' . $month . '</a></div>';
                        }

                    ?>
                </div>
                <?php endif; ?>
                <div class="ticket_page_subtitle par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_VIZD_TA_PRIBUTTYA_ZA_MISCEVIM_CHASOM'] ?></div>
                <div class="ticket_page_title h2_title">
                    <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_ROZKLAD_AVTOBUSIV'] ?>
                    <?if ($filterParams){
                      echo $departureCityTitle['title'].' - '.$arrivalCityTitle['title'].' '.$GLOBALS['dictionary']['MSG_MSG_TICKETS_NA'].' '.date('d', strtotime($filterDate)).' '.$filterMonth['title'];
                    }?>
                </div>
                <div class="sort_block hidden-xl hidden-lg hidden-md hidden-sm hidden-xs">
                    <div class="sort_block_tile h3_title"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_SORTUVATI'] ?></div>
                    <div class="sort_options flex_ac">
                        <button class="sort_option active h5_title flex_ac desc" data-sort="1" data-sort-direction="1"
                                onclick="changeSort(this)">
                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CINA'] ?>
                        </button>
                        <button class="sort_option h5_title flex_ac desc" data-sort="2" data-sort-direction="1"
                                onclick="changeSort(this)">
                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_VIDPRAVLENNYA'] ?>
                        </button>
                        <button class="sort_option h5_title flex_ac desc" data-sort="3" data-sort-direction="1"
                                onclick="changeSort(this)">
                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_PRIBUTTYA'] ?>
                        </button>
                        <button class="sort_option h5_title flex_ac desc" data-sort="4" data-sort-direction="1"
                                onclick="changeSort(this)">
                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_POPULYATNISTI'] ?>
                        </button>
                    </div>
                </div>
                <div class="mobile_sort_filter hidden-xxl flex_ac">
                    <select class="sort_select flex_ac">
                        <option value="" hidden selected
                                disabled><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_SORTUVATII_ZA'] ?></option>
                        <option value="1"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CINA'] ?></option>
                        <option value="2"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_VIDPRAVLENNYA'] ?></option>
                        <option value="3"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_PRIBUTTYA'] ?></option>
                        <option value="4"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_POPULYATNISTI'] ?></option>
                    </select>
                    <button class="mobile_filter_btn" onclick="toggleMobileFilter()">
                        <img src="/public/images/common/filter.svg" alt="filter">
                    </button>
                </div>
            </div>
            <div class="catalog_filter_overlay overlay hidden-xxl" onclick="toggleMobileFilter()"></div>
            <div class="tickets_catalog_wrapper">
                <div class="container">
                    <div class="tickets_catalog">
                        <div class="catalog_filter">
                            <button class="close_filter hidden-xxl" onclick="toggleMobileFilter()">
                                <img src="/public/images/common/mobile_filter_arrow.svg" alt="arrow left">
                            </button>
                            <div class="catalog_filter_title h3_title">
                                <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_FILITRI'] ?>
                            </div>
                            <div class="catalog_filters">
                                <? if (isset($_SESSION['filter']['filter_params']) && $_SESSION['filter']['filter_params'] != '') { ?>
                                    <button class="catalog_filter_reset_btn h5_title flex_ac">
                                        <img src="/public/images/common/refresh.svg" alt="refresh">
                                        <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_SKINUTI'] ?>
                                    </button>
                                    <div class="selected_catalog_filters">
                                        <div class="selected_catalog_filter">
                                        <span class="par">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_TILIKI_PRYAMI_REJSI'] ?>
                                        </span>
                                            <button class="remove_selected_filter">
                                                <img src="/public/images/common/remove_filter.svg" alt="remove filter">
                                            </button>
                                        </div>
                                        <div class="selected_catalog_filter">
                                        <span class="par">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_PRIBUTTYA_RANOK'] ?>
                                        </span>
                                            <button class="remove_selected_filter">
                                                <img src="/public/images/common/remove_filter.svg" alt="remove filter">
                                            </button>
                                        </div>
                                        <div class="selected_catalog_filter">
                                        <span class="par">
                                            Wi-Fi
                                        </span>
                                            <button class="remove_selected_filter">
                                                <img src="/public/images/common/remove_filter.svg" alt="remove filter">
                                            </button>
                                        </div>
                                        <div class="selected_catalog_filter">
                                        <span class="par">
                                            Кава
                                        </span>
                                            <button class="remove_selected_filter">
                                                <img src="/public/images/common/remove_filter.svg" alt="remove filter">
                                            </button>
                                        </div>
                                    </div>
                                <? } ?>
                                <div class="ride_options">
                                    <label class="c_radio_wrapper flex_ac">
                                        <input type="radio" hidden class="c_radio_checker filter_option stops_option" value="0" name="ride_option" checked>
                                        <span class="c_radio"></span>
                                        <span class="c_radio_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_VSI_REJSI'] ?></span>
                                    </label>
                                    <label class="c_radio_wrapper flex_ac">
                                        <input type="radio" hidden class="c_radio_checker filter_option stops_option" value="1" name="ride_option">
                                        <span class="c_radio"></span>
                                        <span class="c_radio_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_TILIKI_PRYAMI_REJSI'] ?></span>
                                    </label>
                                    <label class="c_radio_wrapper flex_ac">
                                        <input type="radio" hidden class="c_radio_checker filter_option stops_option" value="2" name="ride_option">
                                        <span class="c_radio"></span>
                                        <span class="c_radio_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_TILIKI_Z_PERESADKAMI'] ?></span>
                                    </label>
                                </div>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title active"
                                             onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CINA'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_char_param">
                                                <div class="ranger_wrapper">
                                                    <div id="price_range" class="value_ranger"></div>
                                                </div>
                                                <div class="price_range_minmax_values flex_ac">
                                                    <div class="price_range_minmax_value btn_txt">
                                                        <span class="filter_price_min"><?= $minTicketsPrice ?></span> ₴
                                                    </div>
                                                    <div class="price_range_minmax_value btn_txt">
                                                        <span class="filter_price_max"><?= $maxTicketsPrice ?></span> ₴
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <? if (isset($_SESSION['filter']['departure']) && $_SESSION['filter']['departure'] != '') { ?>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title" onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_VIDPRAVLENNYA'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_checkbox_params">
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option departure_time_option" value="1" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_BUDI-YAKIJ'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option departure_time_option" value="2" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_RANOK_0600_-1200'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option departure_time_option" value="3" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_DENI_1200_-_1800'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option departure_time_option" value="4" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_VECHIR_1800_-_0000'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option departure_time_option" value="5" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_NICH_0000_-_0600'] ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> <? } ?>
                                <? if (isset($_SESSION['filter']['departure']) && $_SESSION['filter']['departure'] != '') { ?>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title" onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_PRIBUTTYA'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_checkbox_params">
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option arrival_time_option" value="1" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_BUDI-YAKIJ'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option arrival_time_option" value="2" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_RANOK_0600_-1200'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option arrival_time_option" value="3" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_DENI_1200_-_1800'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option arrival_time_option" value="4" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_VECHIR_1800_-_0000'] ?></span>
                                                </label>
                                                <label class="c_checkbox_wrapper flex_ac">
                                                    <input type="checkbox" class="c_checkbox_checker filter_option arrival_time_option" value="5" hidden>
                                                    <span class="c_checkbox"></span>
                                                    <span class="c_checkbox_title par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_NICH_0000_-_0600'] ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div> <? } ?>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title" onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_STANCIYA_VIDPRAVLENNYA'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_checkbox_params">
                                                <?$additionalParamDeparture = '';
                                                if ($filterDeparture > 0){
                                                    $additionalParamDeparture = "AND c.section_id = '" . $filterDeparture . "' ";
                                                }
                                                $getDepartureStations = $Db->getAll("SELECT DISTINCT(c.id),c.section_id,c.title_" . $Router->lang . " AS title 
                                                FROM `" . DB_PREFIX . "_cities` c
                                                LEFT JOIN `" . DB_PREFIX . "_tours_stops_prices` tsp ON tsp.from_stop = c.id
                                                WHERE c.active = '1' ".$additionalParamDeparture." 
                                                AND c.station = '1' 
                                                AND tsp.price > 0
                                                ORDER BY c.sort DESC");
                                                foreach ($getDepartureStations as $k => $departureStation) {
                                                    if ($filterDeparture == 0){
                                                        $departureCityTitle = $Db->getOne("SELECT id,title_".$Router->lang." AS title FROM ".DB_PREFIX."_cities WHERE id = '".$departureStation['section_id']."' ");
                                                    }?>
                                                    <label class="c_checkbox_wrapper flex_ac">
                                                        <input type="checkbox" class="c_checkbox_checker departure_station_checker filter_option" hidden value="<?= $departureStation['id'] ?>">
                                                        <span class="c_checkbox"></span>
                                                        <span class="c_checkbox_title par">
                                                        <?=  $departureCityTitle['title'] . ', ' . $departureStation['title'] ?>
                                                    </span>
                                                    </label>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title" onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_STANCIYA_PRIBUTTYA'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_checkbox_params">
                                                <? $additionalParamArrival = '';
                                                if ($filterArrival > 0){
                                                    $additionalParamArrival = " AND c.section_id = '" . $filterArrival . "' ";
                                                }
                                                $getArrivalStations = $Db->getAll("SELECT DISTINCT(c.id),c.section_id,c.title_" . $Router->lang . " AS title 
                                                FROM `" . DB_PREFIX . "_cities` c
                                                LEFT JOIN `" . DB_PREFIX . "_tours_stops_prices` tsp ON tsp.from_stop = c.id
                                                WHERE c.active = '1' ".$additionalParamArrival." 
                                                AND c.station = '1' 
                                                AND tsp.price > 0
                                                ORDER BY c.sort DESC");
                                                foreach ($getArrivalStations as $k => $arrivalStation) {
                                                    if ($filterArrival == 0){
                                                        $arrivalCityTitle = $Db->getOne("SELECT id,title_".$Router->lang." AS title FROM ".DB_PREFIX."_cities WHERE id = '".$arrivalStation['section_id']."' ");
                                                    }?>
                                                    <label class="c_checkbox_wrapper flex_ac">
                                                        <input type="checkbox" class="c_checkbox_checker arrival_station_checker filter_option" hidden value="<?= $arrivalStation['id'] ?>">
                                                        <span class="c_checkbox"></span>
                                                        <span class="c_checkbox_title par">
                                                        <?= $arrivalCityTitle['title'] . ', ' . $arrivalStation['title'] ?>
                                                    </span>
                                                    </label>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter_chars_block_wrapper">
                                    <div class="filter_chars_block">
                                        <div class="filter_chars_title h4_title" onclick="toggleFilterParams(this)">
                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_POSLUGI_V_AVTOBUSI'] ?>
                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                        </div>
                                        <div class="filter_char_params">
                                            <div class="filter_checkbox_params">
                                                <? $getBusesOptions = $Db->getall("SELECT id,title_" . $Router->lang . " AS title FROM `" . DB_PREFIX . "_buses_options` WHERE active = '1' ORDER BY sort DESC");
                                                foreach ($getBusesOptions as $k => $busOption) { ?>
                                                    <label class="c_checkbox_wrapper flex_ac">
                                                        <input type="checkbox" class="c_checkbox_checker filter_option bus_options_checker" hidden value="<?= $busOption['id'] ?>">
                                                        <span class="c_checkbox"></span>
                                                        <span class="c_checkbox_title par">
                                                            <?= $busOption['title'] ?>
                                                        </span>
                                                    </label>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <? if (isset($_POST['filters'])) { ?>
                                    <button class="catalog_filter_reset_btn h5_title flex_ac">
                                        <img src="/public/images/common/refresh.svg" alt="refresh">
                                        <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_SKINUTI'] ?>
                                    </button>
                                <? } ?>
                                
                            </div>
                        </div>
                        <div class="catalog_elements">
                            <div class="catalog_elements_title h3_title">
                                <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_ZNAJDENO'] . ' ' . count($getTickets) . ' ' . $GLOBALS['dictionary']['MSG_MSG_TICKETS_AVTOBUSIV'] ?></div>
                            <div class="catalog_elements_subtitle par"><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_CHAS_VIDPRAVLENNYA_TA_PRIBUTTYA_MISCEVIJ'] ?></div>

                            <div class="ticket_cards_wrapper">
                                <?
                                foreach ($getTickets as $k => $ticket) {
                                    $getTicketStops = $Db->getAll("SELECT stop_id,arrival_time,departure_time,arrival_day FROM ".DB_PREFIX."_tours_stops WHERE tour_id = '".$ticket['id']."' ORDER BY id ASC ");


                                    $tourDeparture = $ticket['departure'];
                                    if ($filterDeparture > 0){
                                        $tourDeparture = $filterDeparture;
                                    }
                                    $tourArrival = $ticket['arrival'];
                                    if ($filterArrival > 0){
                                        $tourArrival = $filterArrival;
                                    }
                                    $ticketDepartureDate = $filterDate;
                                    if ($filterDate == 'today'){
                                        $ticketDepartureDate = findNearestDayOfWeek(date('Y-m-d',time()), explode(',',$ticket['days']));
                                    }
                                    $dateArray = explode('-',$ticketDepartureDate);
                                    $month = $Db->getOne("SELECT title_".$Router->lang." AS title FROM ".DB_PREFIX."_months WHERE id = '".(int)$dateArray[1]."' ");
                                    $departureDate = $dateArray[2] . ' ' . $month['title'] . ' ' . $dateArray[0];

                                    $departureDetails = $Db->getOne("SELECT station.id,station.title_".$Router->lang." AS station,city.title_".$Router->lang." AS city,stop.departure_time FROM ".DB_PREFIX."_cities station 
                                    LEFT JOIN ".DB_PREFIX."_cities city ON city.id = station.section_id
                                    LEFT JOIN ".DB_PREFIX."_tours_stops stop ON stop.stop_id = station.id AND stop.tour_id = '".$ticket['id']."'
                                    WHERE station.station = 1 AND station.section_id = '".$tourDeparture."' AND station.id IN(SELECT stop_id FROM ".DB_PREFIX."_tours_stops WHERE tour_id = '".$ticket['id']."' )");
                                    $arrivalDetails = $Db->getOne("SELECT station.id,station.title_".$Router->lang." AS station,city.title_".$Router->lang." AS city,stop.arrival_time, stop.arrival_day FROM ".DB_PREFIX."_cities station 
                                    LEFT JOIN ".DB_PREFIX."_cities city ON city.id = station.section_id
                                    LEFT JOIN ".DB_PREFIX."_tours_stops stop ON stop.stop_id = station.id AND stop.tour_id = '".$ticket['id']."'
                                    WHERE station.station = 1 AND station.section_id = '".$tourArrival."' AND station.id IN(SELECT stop_id FROM ".DB_PREFIX."_tours_stops WHERE tour_id = '".$ticket['id']."' )");
                                    $rideTime = calculateTotalTravelTime($getTicketStops,$departureDetails['id'],$arrivalDetails['id'],$arrivalDetails['arrival_day']);
                                    $international = ($ticket['departure_city_section_id'] != $ticket['arrival_city_section_id']);
                                    $ticketPrice = $Db->getOne("SELECT price FROM ".DB_PREFIX."_tours_stops_prices WHERE from_stop = '".$departureDetails['id']."' AND to_stop = '".$arrivalDetails['id']."' AND tour_id = '".$ticket['id']."' "); ?>
                                    <div class="ticket_card shadow_block">
                                        <div class="d_none">
                                            <?=Out($tourArrival)?>
                                            <?=Out($ticket['arrival'])?>
                                            <?=Out($arrivalDetails)?>

                                        </div>
                                        <div class="flex-row">
                                            <div class="col-lg-9 col-xs-12">
                                                <div class="ticket_info">
                                                    <div class="ticket_info_header flex_ac">
                                                        <div class="ticket_info_date_block flex_ac">
                                                            <img src="/public/images/common/ticket_calendar.svg" alt="calendar">
                                                            <span class="ticket_info_date par">
                                                                <?= $departureDate ?>
                                                            </span>
                                                        </div>
                                                        <div class="ride_description_wrapper flex_ac">
                                                            <div class="ride_description par">
                                                                <span><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_REJS'] ?></span>
                                                                <span><?= $ticket['departure_city'] ?> — <?= $ticket['arrival_city'] ?></span>
                                                            </div>
                                                            <div class="ride_description par">
                                                                <span><?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_AVTOBUS'] ?></span>
                                                                <span><?= $ticket['bus_title'] ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ticket_ride_info_block flex-row gap-30">
                                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                                            <div class="ticket_ride_departure ticket_ride_info">
                                                                <div class="ticket_ride_time flex_ac">
                                                                    <img src="/public/images/common/clock.svg" alt="clock">
                                                                    <span class="btn_txt"><?= date("H:i", strtotime($departureDetails['departure_time'])) ?></span>
                                                                </div>
                                                                <div class="ticket_ride_city btn_txt">
                                                                    <?= $departureDetails['city'] ?>
                                                                </div>
                                                                <div class="ticket_ride_checkpoint manrope">
                                                                    <?= $departureDetails['station'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 hidden-md hidden-sm col-xs-12">
                                                            <div class="ticket_ride_info ride_total_time">
                                                                <div class="ticket_logo_wrapper">
                                                                    <img src="/public/images/common/ticket_logo_2.svg" alt="ticket logo" class="fit_img">
                                                                </div>
                                                                <div class="ticket_ride_total_time_wrapper">
                                                                    <div class="ticket_ride_total_time_info">
                                                                        <img src="/public/images/common/info.svg" alt="info">
                                                                        <div class="ticket_info_tooltip par">
                                                                            <?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_VKAZANIJ_CHAS_NE_VRAHOVU_ZATRIMOK_NA_KORDONI']?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="ticket_ride_total_time_data">
                                                                        <div class="ticket_ride_total_time par">
                                                                            <?= (int)explode(':',$rideTime)[0].' '.$GLOBALS['dictionary']['MSG_MSG_TICKETS_GOD'].' '.(int)explode(':',$rideTime)[1].' '.$GLOBALS['dictionary']['MSG_MSG_TICKETS_HV_V_DOROZI'] ?>
                                                                        </div>
                                                                        <? if ($international) { ?>
                                                                            <div class="ticket_ride_status par">
                                                                                <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_MIZHNARODNIJ'] ?>
                                                                            </div>
                                                                        <? } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-sm-6 col-xs-12">
                                                            <div class="ticket_ride_arrival ticket_ride_info">
                                                                <div class="ticket_ride_time flex_ac">
                                                                    <img src="/public/images/common/clock.svg" alt="clock">
                                                                    <span class="btn_txt"><?= date('H:i', strtotime($arrivalDetails['arrival_time'])) ?></span>
                                                                </div>
                                                                <div class="ticket_ride_city btn_txt">
                                                                    <?= $arrivalDetails['city'] ?>
                                                                </div>
                                                                <div class="ticket_ride_checkpoint">
                                                                    <?= $arrivalDetails['station'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button class="ticket_details_btn shedule_link flex_ac hidden-md hidden-sm hidden-xs" onclick="toggleRouteDetails('<?= $ticket['id'] ?>','<?=$departureDetails['id']?>','<?=$arrivalDetails['id']?>')">
                                                    <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_DETALINISHE'] ?>
                                                    <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                                </button>
                                            </div>
                                            <div class="col-lg-3 hidden-md hidden-sm hidden-xs">
                                                <div class="ticket_totals">
                                                    <div class="ticket_price"><?= $ticketPrice['price'] ?> ₴</div>
                                                    <button class="ticket_buy_btn flex_ac h5_title blue_btn" onclick="buyTicket(this,'<?= $ticket['id'] ?>','<?=$departureDetails['id']?>','<?=$arrivalDetails['id']?>', '<?=$filterDeparture?>', '<?=$filterArrival?>' )">
                                                        <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_KUPITI_KVITOK'] ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="hidden-xxl hidden-xl hidden-lg col-sm-12 hidden-xs">
                                                <div class="ride_total_time">
                                                    <div class="ticket_logo_wrapper">
                                                        <img src="/public/images/common/ticket_logo_2.svg" alt="ticket logo" class="fit_img">
                                                    </div>
                                                    <div class="mobile_ticket_ride_total_time_wrapper flex_ac">
                                                        <div class="ticket_ride_total_time_info flex_ac">
                                                            <img src="/public/images/common/info.svg" alt="info">
                                                            <div class="ticket_ride_total_time par">
                                                                <?= (int)explode(':',$rideTime)[0].' '.$GLOBALS['dictionary']['MSG_MSG_TICKETS_GOD'].' '.(int)explode(':',$rideTime)[1].' '.$GLOBALS['dictionary']['MSG_MSG_TICKETS_HV_V_DOROZI'] ?>
                                                            </div>
                                                        </div>
                                                        <? if ($international) { ?>
                                                            <div class="ticket_ride_status par">
                                                                <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_MIZHNARODNIJ'] ?>
                                                            </div>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="hidden-xxl hidden-xl hidden-lg col-xs-12">
                                                <div class="mobile_ticket_totals flex_ac">
                                                    <div class="mobile_ticket_details flex_ac">
                                                        <div class="ticket_price"><?= $ticketPrice['price'] ?> ₴</div>
                                                        <button class="ticket_details_btn shedule_link flex_ac" onclick="toggleRouteDetails('<?= $ticket['id'] ?>','<?=$departureDetails['id']?>','<?=$arrivalDetails['id']?>')">
                                                            <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_DETALINISHE'] ?>
                                                            <img src="/public/images/common/arrow_down_2.svg" alt="arrow down">
                                                        </button>
                                                    </div>
                                                    <button class="ticket_buy_btn flex_ac h5_title blue_btn" onclick="buyTicket(this,'<?= $ticket['id'] ?>','<?=$departureDetails['id']?>','<?=$arrivalDetails['id']?>', '<?=$filterDeparture?>', '<?=$filterArrival?>' )">
                                                        <?= $GLOBALS['dictionary']['MSG_MSG_TICKETS_KUPITI_KVITOK'] ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? } ?>
                            </div>
                            <div class="pagination_wrapper">
                                <?=paginatePublic($pagination)?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/footer.php' ?>
    </div>
</div>
<div class="route_details_popup blue_popup">

</div>
<div class="route_details_overlay overlay" onclick="toggleRouteDetails('0')"></div>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/public/blocks/footer_scripts.php' ?>
<script src="/public/libs/jquery_ui_slider/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"
        integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function buyTicket(item, id,departure,arrival,fromCity, toCity) {
        initLoader();
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'remember_ticket',
                'id': id,
                <?if ($filterDate){?>
                'date': '<?=$filterDate?>',
                <?}else{?>
                'date': '<?=date('Y-m-d',time())?>',
                <?}?>
                'passengers': '<?=$adults + $kids?>',
                'departure':departure,
                'arrival':arrival,
                'fromCity' : fromCity,
                'toCity': toCity
            },
            success: function (response) {
                removeLoader();
                if ($.trim(response) === 'ok') {
                    location.href = '<?=$Router->writelink(85)?>';
                }else if ($.trim(response) === 'late'){
                    out('<?=$GLOBALS['dictionary']['MSG_MSG_TICKETS_ETOT_BILET_BOLISHE_KUPITI_NELIZYA_TK_ETOT_REJS_UZHE_UEHAL']?>');
                }
            }
        })
    }

    $('.purchase_steps').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        dots: false,
        arrows: false,
        infinite: false,
        variableWidth: true,
        responsive: [
            {
                breakpoint: 576,
                settings: {
                    infinite: false,
                    slidesToShow: 1
                }
            },
        ]
    });

    $('.sort_select').niceSelect();
    $(function () {
        $("#price_range").slider({
            range: true,
            min: <?=$minTicketsPrice?>,
            max: <?=$maxTicketsPrice?>,
            values: [<?=$minTicketsPrice?>, <?=$maxTicketsPrice?>],
            slide: function (event, ui) {
                $(".filter_price_min").text(ui.values[0]);
                $(".filter_price_max").text(ui.values[1]);
            },
            stop: function(event,ui){
                filterTickets();
            }
        })
    });

    function toggleFilterParams(item) {
        $(item).next().slideToggle();
        setTimeout(function () {
            $(item).toggleClass('active');
        }, 400)
    }

    function toggleRouteDetails(id,departure,arrival) {
        if (parseInt(id) > 0) {
            initLoader();
            $.ajax({
                type: 'post',
                url: '<?=$Router->writelink(3)?>',
                data: {
                    'request': 'route_details',
                    'id': id,
                    'departure':departure,
                    'arrival':arrival
                },
                success: function (response) {
                    removeLoader();
                    if ($.trim(response) != 'err') {
                        $('.route_details_popup').html(response).toggleClass('active');
                        $('.route_details_overlay').fadeToggle();
                        $('body').toggleClass('overflow');
                    } else {
                        out('Ошибка');
                    }
                }
            })
        } else {
            $('.route_details_popup').html('').toggleClass('active');
            $('.route_details_overlay').fadeToggle();
            $('body').toggleClass('overflow');
        }
    }

    function toggleInfoBlock(item) {
        $(item).next().slideToggle();
        $(item).find('img').toggleClass('rotate');
    }

    function toggleMobileFilter() {
        $('.catalog_filter').toggleClass('active');
        $('.catalog_filter_overlay').fadeToggle();
        $('body').toggleClass('overflow');
    }

    function changeSort(item) {
        $('.sort_option').not(item).removeClass('active');
        if ($(item).hasClass('active')) {
            $(item).toggleClass('desc').toggleClass('asc');
            if ($(item).hasClass('desc')) {
                $(item).attr('data-sort-direction', '1');
            } else if ($(item).hasClass('asc')) {
                $(item).attr('data-sort-direction', '2');
            }
            $('.ticket_cards_wrapper').toggleClass('reverse');
        }
        $(item).addClass('active');
        $('.sort_select').val($(item).attr('data-sort'));
        $('.sort_select').niceSelect('update');
    }

    function filterTickets() {
        let min_price = parseInt($('.filter_price_min').text());
        let max_price = parseInt($('.filter_price_max').text());
        let stops = $('.stops_option:checked').val();
        let departure_time = [];
        if ($('.departure_time_option:checked').length > 0) {
            $('.departure_time_option:checked').each(function () {
                departure_time.push($(this).val());
            })
        }

        if (departure_time.includes('1') && departure_time.length === 1) {
        departure_time = [];
        }

        let arrival_time = [];
        if ($('.arrival_time_option:checked').length > 0) {
            $('.arrival_time_option:checked').each(function () {
                arrival_time.push($(this).val());
            })
        }

        if (arrival_time.includes('1') && arrival_time.length === 1) {
        arrival_time = [];
        }

        let departure_station = [];
        if ($('.departure_station_checker:checked').length > 0) {
            $('.departure_station_checker:checked').each(function () {
                departure_station.push($(this).val());
            })
        }

        let arrival_station = [];
        if ($('.arrival_station_checker:checked').length > 0) {
            $('.arrival_station_checker:checked').each(function () {
                arrival_station.push($(this).val())
            })
        }

        let comfort = [];
        if ($('.bus_options_checker:checked').length > 0) {
            $('.bus_options_checker:checked').each(function () {
                comfort.push($(this).val());
            })
        }

        let sort_option = $('.sort_option.active').attr('data-sort');
        let sort_direction = $('.sort_option.active').attr('data-sort-direction');
        initLoader();
        $.ajax({
            type: 'post',
            url: '<?=$Router->writelink(3)?>',
            data: {
                'request': 'filter',
                'stops': stops,
                'departure_time': departure_time,
                'arrival_time': arrival_time,
                'departure_station': departure_station,
                'arrival_station': arrival_station,
                'comfort': comfort,
                'sort_option': sort_option,
                'sort_direction': sort_direction,
                'arrival_city': '<?=$filterArrival?>',
                'departure_city': '<?=$filterDeparture?>',
                'adults': '<?=$adults?>',
                'kids': '<?=$kids?>',
                <?if ($filterDate){?>
                'date': '<?=$filterDate?>',
                <?}else{?>
                'date': '<?=date('Y-m-d',time())?>',
                <?}?>
                'min_price':min_price,
                'max_price':max_price
            },
            success: function (response) {
                removeLoader();
                if ($.trim(response) != 'err') {
                    $('.catalog_elements').html(response);
                } else {
                    out('Ошибка');
                }
            }
        })
    }

    $('.filter_option').on('change', function () {
        filterTickets();

    })
</script>
</body>
</html>