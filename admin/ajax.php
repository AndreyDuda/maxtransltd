<?php include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/guard.php';
include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/includes.php';
if(!isset($_POST) || empty($_POST) || !isset($_POST['request'])){exit;}
$cleanPost = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);


if ($cleanPost['request'] === 'refresh_captcha'){
    session_start();
    include $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/libs/captcha/simple-php-captcha.php';
    $_SESSION['captcha'] = simple_php_captcha();
    echo $_SESSION['captcha']['image_src'];
}

if ($cleanPost['request'] === 'change_theme'){
    $currentTheme = $Db->getone("SELECT theme FROM ".DB_PREFIX."_users WHERE id = '".$Admin->id."' ");
    if ((int)$currentTheme['theme'] == 1){
        $upd = $Db->query("UPDATE ".DB_PREFIX."_users SET `theme` = '2' WHERE id = '".$Admin->id."' ");
    }elseif ($currentTheme['theme'] == 2){
        $upd = $Db->query("UPDATE ".DB_PREFIX."_users SET `theme` = '1' WHERE id = '".$Admin->id."' ");
    }
    if ($upd){
        echo 'ok';
    }else{
        echo 'err';
    }
}

if($cleanPost['request']=="refresh"){
    $elem_id = (int)$cleanPost['id'];
    $table = $cleanPost['table'];

    $get_data = mysqli_query($db, "SELECT active FROM `".$table."` WHERE `id`= ".$elem_id);
    if ( $el = mysqli_fetch_array($get_data) ) {

        if ( $el['active'] == 0 )
            $active = 1;
        if ( $el['active'] == 1 )
            $active = 0;

        $ch_act = mysqli_query($db, "UPDATE `".$table."` SET active='$active' WHERE `id`= ".$elem_id);
    }
}

if ($cleanPost['request'] === 'send_messages'){
    $getClientsPhones = $Db->getAll("SELECT phone FROM ".DB_PREFIX."_clients WHERE id IN (".implode(',',array_unique($cleanPost['clients'])).") ");
    $recipients = [];
    foreach ($getClientsPhones AS $k=>$clientsPhone){
        $recipients[] = str_replace(array(')',' ','(','+'),'',$clientsPhone['phone']);
    }
    $apiKey = '40de5c81e6360bb0bfda2ada1a00304cbb4d4dfa';
    $apiUrl = 'https://api.turbosms.ua';
    $message = 'Тест отправки';
    $params = array(
        'recipients' => array_unique($recipients),
        'sms'=> array(
            "sender" => "Max Trans",
            'text' => $message,
        )
    );
    $headers = array(
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . '/message/send.json');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['response_code']) && $result['response_status'] === 'OK') {
            echo 'Сообщения успешно отправлены!';
        } else {
            echo 'Ошибка при отправке сообщения: ' . $result['response_status'];
        }
    } else {
        echo 'Ошибка при выполнении запроса к API Turbosms.';
    }
}

if ($cleanPost['request'] === 'edit_sales'){
    $free_tickets = $Db->getOne("SELECT free_tickets FROM ". DB_PREFIX ."_tours_sales WHERE tour_id = '".(int)$cleanPost['id']."' AND tour_date = '".$cleanPost['date']."'"); ?>

    <input type="number" class="form-control input-sm edit_tickets_num" value="<?=$free_tickets['free_tickets']?>">
    <button class="btn btn-success" title="Сохранить изменения" type="button" onclick="acceptFreeTickets(this,'<?=(int)$cleanPost['id']?>', '<?=$cleanPost['date']?>', )">
        <i class="fas fa-check"></i>
    </button>
    <?
}

if ($cleanPost['request'] === 'accept_sales_changes'){
    $edit = mysqli_query($db,"UPDATE ". DB_PREFIX ."_tours_sales SET free_tickets = '".$cleanPost['tickets']."' WHERE tour_id = '".(int)$cleanPost['id']."' AND tour_date = '".$cleanPost['date']."' ");
    if($edit){
        $free_tickets = $Db->getOne("SELECT free_tickets FROM ". DB_PREFIX ."_tours_sales WHERE tour_id = '".(int)$cleanPost['id']."' AND tour_date = '".$cleanPost['date']."'")
        ?>
        <?=$free_tickets['free_tickets']?>
        <button class="btn btn-default" title="Редактировать" type="button" onclick="editFreeTickets(this,'<?=$cleanPost['id']?> ','<?=$cleanPost['date']?>') ">
            <i class="fas fa-pencil-alt"></i>
        </button>

        <?
    }
}

if ($cleanPost['request'] === 'setActive_race'){
    $edit = mysqli_query($db,"UPDATE ". DB_PREFIX ."_tours_sales SET active='1' WHERE id = '".(int)$cleanPost['id']."'");
    if($edit) { ?>
        <button class="btn btn-danger" title="Дезактивировать" type="button" onclick="setInactive(this,'<?=$cleanPost['id']?> ') ">
            <i class="fas fa-times"></i>
        </button>
    <? }
}

if ($cleanPost['request'] === 'setInactive_race'){
    $edit = mysqli_query($db,"UPDATE ". DB_PREFIX ."_tours_sales SET active='0' WHERE id = '".(int)$cleanPost['id']."'");
    if($edit) { ?>
        <button class="btn btn-success" title="Активировать" type="button" onclick="setActive(this,'<?=$cleanPost['id']?> ') ">
            <i class="fas fa-check"></i>
        </button>
    <? }
}

if ($cleanPost['request'] === 'filterRaces') {
    $salesDate = $cleanPost['date'];
    ?>

    <table class="table m-0">
        <thead>
        <tr>
            <!--th>ID маршрута</th-->
            <th>Маршрут</th>
            <th>Автобус</th>
            <th>Куплено билетов</th>
            <th>Забронировано билетов</th>
            <th>Свободных мест</th>
            <th style="text-align:center;">Действия</th>
        </tr>
        </thead>
        <tbody>
        <? $getTableElems = $Db->getAll("SELECT * FROM ".DB_PREFIX."_tours_sales WHERE tour_date = '" .$salesDate. "'  GROUP BY tour_id,tour_date ORDER BY tour_date DESC");
        foreach ($getTableElems AS $k=>$Elem) {

            $mainInfo = $Db->getOne("SELECT 
                                    departure_city.title_" . $Admin->lang . " AS departure_city, 
                                    arrival_city.title_" . $Admin->lang . " AS arrival_city,
                                    b.title_".$Admin->lang." AS bus
                                    FROM `" . DB_PREFIX . "_tours` t 
                                    LEFT JOIN `" . DB_PREFIX . "_cities` departure_city ON departure_city.id = t.departure 
                                    LEFT JOIN `" . DB_PREFIX . "_cities` arrival_city ON arrival_city.id = t.arrival 
                                    LEFT JOIN `".DB_PREFIX."_buses` b ON b.id = t.bus 
                                    WHERE t.id = '".$Elem['tour_id']."'");
            $ticketsBuy = $Db->getOne("SELECT tickets_buy FROM ".DB_PREFIX."_tours_sales WHERE tour_id = '".$Elem['tour_id']."' AND tour_date = '".$Elem['tour_date']."' ");
            $ticketsOrder = $Db->getOne("SELECT tickets_order FROM ".DB_PREFIX."_tours_sales WHERE tour_id = '".$Elem['tour_id']."' AND tour_date = '".$Elem['tour_date']."' ");
            $free_tickets = $Db->getOne("SELECT free_tickets FROM ". DB_PREFIX ."_tours_sales WHERE tour_id = '".$Elem['tour_id']."' AND tour_date = '".$Elem['tour_date']."'");
            $departureTimeQuery = $Db->getOne("SELECT departure_time FROM ".DB_PREFIX."_tours_stops WHERE tour_id = '".$Elem['tour_id']."' ORDER BY id ASC");
            $departureTime = substr($departureTimeQuery['departure_time'], 0, 5);

            ?>
            <tr <?if ($Elem['active'] == '0'){?>
                class="disabled"
            <?}?>>
                <!--td><?=$Elem['tour_id']?></td-->
                <td>
                    <b><?=date('Y.m.d',strtotime($Elem['tour_date']))?></b>
                    <div>
                        <?=$mainInfo['departure_city'].' - '.$mainInfo['arrival_city']?>
                    </div>
                    <div><?=$departureTime ?></div>
                </td>
                <td><?=$mainInfo['bus']?></td>
                <td><?=$ticketsBuy['tickets_buy']?></td>
                <td><?=$ticketsOrder['tickets_order']?></td>
                <td class="free_tickets_td"><?=$free_tickets['free_tickets']?>
                    <button class="btn btn-default" title="Редактировать" type="button" onclick="editFreeTickets(this,'<?=$Elem['tour_id']?> ','<?=$Elem['tour_date']?>') ">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                </td>
                <td align="center" width="210">
                    <div class="btn-group wgroup">
                        <? if ($ticketsBuy['tickets_buy'] > 0 || $ticketsOrder['tickets_order'] > 0) { ?>
                            <a href="pdf.php?id=<?= $Elem['tour_id'] ?>&date=<?=$Elem['tour_date']?>" class="btn btn-default" title="скачать ведомость">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        <? } ?>
                        <a href="edit.php?id=<?= $Elem['tour_id'] ?>&date=<?=$Elem['tour_date']?>" class="btn btn-default" title="Посмотреть детали">
                            <i class="fas fa-folder-open"></i>
                        </a>
                        <? if ($Elem['active'] == '0') {?>
                            <button class="btn btn-success" title="Активировать" type="button" onclick="setActive(this,'<?=$Elem['id']?> ') ">
                                <i class="fas fa-check"></i>
                            </button>
                        <?} else { ?>
                            <button class="btn btn-danger" title="Дезактивировать" type="button" onclick="setInactive(this,'<?=$Elem['id']?> ') ">
                                <i class="fas fa-times"></i>
                            </button>
                        <? } ?>
                    </div>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table> <?
}
?>

