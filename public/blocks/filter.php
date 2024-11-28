<?
$filterDeparture = $filterArrival = 0;
$filterDate = "today";
$adults = 1;
$kids = 0;
if (isset($_POST['departure']) && (int)$_POST['departure'] > 0){
    $filterDeparture = $_SESSION['filter']['departure'] = (int)$_POST['departure'];
}elseif (empty($_POST['departure']) && isset($_SESSION['filter']['departure'])){
    $filterDeparture = $_SESSION['filter']['departure'];
}

if (isset($_POST['arrival']) && (int)$_POST['arrival'] > 0){
    $filterArrival = $_SESSION['filter']['arrival'] = (int)$_POST['arrival'];
}elseif (empty($_POST['arrival']) && isset($_SESSION['filter']['arrival'])){
    $filterArrival = $_SESSION['filter']['arrival'];
}



if (isset($_POST['date']) && trim($_POST['date']) != ''){
    $filterDate = $_SESSION['filter']['date'] = implode('-',array_map('intval', explode('-', $_POST['date'])));
}elseif (empty($_POST['date']) && isset($_SESSION['filter']['date'])){
    $filterDate = $_SESSION['filter']['date'];
}


if (isset($_POST['adults'])) {
    $adults = $_SESSION['filter']['adults'] = (int)$_POST['adults'];
}elseif (empty($_POST['adults']) && isset($_SESSION['filter']['adults'])){
    $adults = $_SESSION['filter']['adults'];
}

if (isset($_POST['kids'])) {
    $kids = $_SESSION['filter']['kids'] = (int)$_POST['kids'];
}elseif (empty($_POST['kids']) && isset($_SESSION['filter']['kids'])){
    $kids = $_SESSION['filter']['kids'];
}
?>

<form class="main_filter" method="post" action="<?=$Router->writelink(76)?>">
    <div class="flex-row gap-8">
        <div class="col-lg-20 col-sm-6 col-xs-12">
            <div class="filter_block_wrapper">
                <div class="filter_city_select_wrapper flex-row">
                    <div class="filter_block_title city_select_title par"><?= $GLOBALS['dictionary']['MSG_ALL_ZVIDKI'] ?></div>
                    <select class="filter_city_select" id="filter_departure" name="departure">
                        <? $getCities = $Db->getall("SELECT id,title_".$Router->lang." AS title FROM ".DB_PREFIX."_cities WHERE active = 1 AND section_id > 0 AND station = 0 ORDER BY sort DESC,title_".$Router->lang." ASC");
                        foreach ($getCities as $k => $city) { ?>
                            <option value="<?= $city['id'] ?>" <? if ($filterDeparture == $city['id'] || ($filterDeparture == "" && mb_strtoupper(mb_substr($city['title'], 0, 1)) === 'А')) { 
                                echo 'selected';
                            } ?>>
                                <?if ($city['station'] == 0){
                                    echo $city['title'];
                                }else{
                                    echo $city['city_title'].' '.$city['title'];
                                }?>
                            </option>

                        <? } ?>
                    </select>
                    <button class="reverse_filter_btn" onclick="switchDirections()" type="button">
                        <img src="/public/images/common/pair_arrows.svg" alt="pair_arrows">
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-20 col-sm-6 col-xs-12">
            <div class="filter_block_wrapper">
                <div class="filter_city_select_wrapper flex-row">
                    <div class="filter_block_title city_select_title par"><?= $GLOBALS['dictionary']['MSG_ALL_KUDA'] ?></div>
                    <select class="filter_city_select" id="filter_arrival" name="arrival">
                        <? foreach ($getCities as $k => $city) { ?>
                            <option value="<?= $city['id'] ?>" <? if ($filterArrival == $city['id'] || ($filterDeparture == "" && mb_strtoupper(mb_substr($city['title'], 0, 1)) === 'А')) {
                                echo 'selected';
                            } ?>>
                                <?if ($city['station'] == 0){
                                    echo $city['title'];
                                }else{
                                    echo $city['city_title'].' '.$city['title'];
                                }?>
                            </option>
                        <? } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-20 col-sm-6 col-xs-12">
            <div class="filter_block_wrapper">
                <div class="filter_date_wrapper">
                    <div class="filter_date_title par"><?= $GLOBALS['dictionary']['MSG_ALL_KOLI'] ?></div>
                    <input type="text" class="filter_date" name="date">
                    <button class="filter_calendar_btn" onclick="toggleFilterCalendar()" type="button">
                        <img src="/public/images/common/filter_calendar.svg" alt="calendar" class="fit_img">
                    </button>
                </div>
            </div>
        </div>
        <div class="col-lg-20 col-sm-6 col-xs-12">
            <div class="filter_block_wrapper passagers_filter_wrapper">
                <div class="filter_block passagers" onclick="toggleSubmenu(this)">
                    <div class="filter_block_title par"><?= $GLOBALS['dictionary']['MSG_ALL_PASAZHIRI'] ?></div>
                    <div class="filter_block_value flex_ac filter_passagers_total">
                        <div>
                            <span class="adults_total"><?= $adults ?></span> <?= $GLOBALS['dictionary']['MSG_ALL_DOROSLIH'] ?>
                        </div>
                        <div>
                            <span class="kids_total"><?= $kids ?></span> <?= $GLOBALS['dictionary']['MSG_ALL_DITEJ'] ?>
                        </div>
                    </div>
                </div>
                <div class="passagers_counter_wrapper filter_submenu">
                    <div class="passengers_counter_block flex_ac adult_passagers">
                        <div class="passengers_counter_title h5_title"><?= $GLOBALS['dictionary']['MSG_ALL_DOROSLIH'] ?></div>
                        <div class="passengers_counter adults flex_ac">
                            <button class="counter_btn minus" onclick="countPassagers(this,'minus','adults')" type="button">
                                <img src="/public/images/common/minus.svg" alt="minus">
                            </button>
                            <div class="p_counter_value par adults_passagers"><?= $adults ?></div>
                            <input type="hidden" name="adults" class="adults_passengers" value="<?= $adults ?>">
                            <button class="counter_btn plus" onclick="countPassagers(this,'plus','adults', 15)" type="button">
                                <img src="/public/images/common/plus.svg" alt="plus">
                            </button>
                        </div>
                    </div>
                    <div class="passengers_counter_block flex_ac">
                        <div class="passengers_counter_title h5_title">
                            <?= $GLOBALS['dictionary']['MSG_ALL_DITEJ'] ?>
                            <span><?= $GLOBALS['dictionary']['MSG_ALL_DO_3_ROKIV_-_BEZKOSHTOVNO'] ?></span>
                        </div>
                        <div class="passengers_counter kids flex_ac">
                            <button class="counter_btn minus" onclick="countPassagers(this,'minus','kids')" type="button">
                                <img src="/public/images/common/minus.svg" alt="minus">
                            </button>
                            <div class="p_counter_value par kids_passagers"><?= $kids ?></div>
                            <input type="hidden" name="kids" class="kids_passengers" value="<?= $kids ?>">
                            <button class="counter_btn plus" onclick="countPassagers(this,'plus','kids', 15)" type="button">
                                <img src="/public/images/common/plus.svg" alt="plus">
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-20 col-xs-12">
            <input type="submit" class="filter_btn btn_txt blue_btn flex_ac" value="<?= $GLOBALS['dictionary']['MSG_ALL_ZNAJTI_KVITOK'] ?>">
        </div>
    </div>
    <?//out($_SESSION['filter']);?>
</form>