<?php
include($_SERVER['DOCUMENT_ROOT'] . "/config.php");
include($_SERVER['DOCUMENT_ROOT'] . "/" . ADMIN_PANEL . "/includes.php");

require_once('private/LiqPay.php');
require_once('private/payment_keys.php');

function logToFile($message) {
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/log.txt';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

// Получение данных от LiqPay
$data = $_POST['data'] ?? null;
$signature = $_POST['signature'] ?? null;


if ($data && $signature) {
    // Проверка подписи
    $liqpay = new LiqPay($public_key, $private_key);
    $valid_signature = $liqpay->str_to_sign($private_key . $data . $private_key);


    if ($signature === $valid_signature) {
        // Декодирование данных
        $decoded_data = base64_decode($data);
        $json_data = json_decode($decoded_data, true);


        if ($json_data && $json_data['status'] === 'success') {
            // Получение информации о заказе
            $order_id = $json_data['order_id'];
            $order_id_safe = addslashes($order_id); // Защита от SQL-инъекций
            $tour_info = $Db->getOne("SELECT tour_id AS tour_id, tour_date AS tour_date, passagers AS passagers FROM " . DB_PREFIX . "_orders WHERE uniqId = '$order_id_safe'");
            $orderInfo = $Db->getOne("SELECT id, date AS order_date, from_stop, to_stop, tour_date, passagers, client_name, client_surname, client_email, client_phone FROM " . DB_PREFIX . "_orders WHERE uniqid = '$order_id_safe'");
            logToFile("Tour info: " . print_r($orderInfo, true));
            logToFile("Tour info: " . print_r($tour_info, true));

            $ticketInfo = $Db->getOne("SELECT
                    from_stop.departure_time,
                    from_city.title_uk AS departure_station,
                    departure_city.title_uk AS departure_city,
                    to_stop.arrival_time,
                    to_city.title_uk AS arrival_station,
                    arrival_city.title_uk AS arrival_city,
                    bus.title_uk AS bus,
                    prices.price
                FROM " . DB_PREFIX . "_tours_stops AS from_stop
                JOIN " . DB_PREFIX . "_cities AS from_city ON from_stop.stop_id = from_city.id
                JOIN " . DB_PREFIX . "_tours AS tours ON from_stop.tour_id = tours.id
                JOIN " . DB_PREFIX . "_cities AS departure_city ON departure_city.id = tours.departure
                JOIN " . DB_PREFIX . "_tours_stops AS to_stop ON from_stop.tour_id = to_stop.tour_id
                JOIN " . DB_PREFIX . "_cities AS to_city ON to_stop.stop_id = to_city.id
                JOIN " . DB_PREFIX . "_cities AS arrival_city ON arrival_city.id = tours.arrival
                JOIN " . DB_PREFIX . "_buses AS bus ON tours.bus = bus.id
                JOIN " . DB_PREFIX . "_tours_stops_prices AS prices ON
                        prices.tour_id = from_stop.tour_id AND
                        prices.from_stop = from_stop.stop_id AND
                        prices.to_stop = to_stop.stop_id
                WHERE from_stop.tour_id = '" . addslashes($tour_info['tour_id']) . "' 
                AND from_stop.stop_id = '" . addslashes($orderInfo['from_stop']) . "' 
                AND to_stop.stop_id = '" . addslashes($orderInfo['to_stop']) . "'");
            logToFile("Tour info: " . print_r($ticketInfo, true));
            $from_stopId = $orderInfo['from_stop'];
            $to_stopId = $orderInfo['to_stop'];

            $client_stops= $Db->getAll("SELECT title_uk AS station_title, section_id AS city FROM ".DB_PREFIX."_cities WHERE id IN ('$from_stopId', '$to_stopId') ORDER BY FIELD(id, '".$from_stopId."', '".$to_stopId."') ");

            $client_cities = $Db->getAll("
                SELECT title_uk AS city_title 
                FROM ".DB_PREFIX."_cities 
                WHERE id IN ('".$client_stops['0']['city']."', '".$client_stops['1']['city']."') 
                ORDER BY FIELD(id, '".$client_stops['0']['city']."', '".$client_stops['1']['city']."')
            ");

            $from_stop= $client_stops['0']['station_title'];
            $to_stop=$client_stops['1']['station_title'];
            $from_city= $client_cities['0']['city_title'];
            $to_city= $client_cities['1']['city_title'];
            logToFile("Tour info: " . print_r($from_stop, true));
            logToFile("Tour info: " . print_r($to_stop, true));
            logToFile("Tour info: " . print_r($from_city, true));
            logToFile("Tour info: " . print_r($to_city, true));

            if ($tour_info) {
                // Обновление статуса оплаты и продаж
                $Db->query("UPDATE " . DB_PREFIX . "_orders SET payment_status = 2 WHERE uniqId = '$order_id_safe'");
                $Db->query("UPDATE " . DB_PREFIX . "_tours_sales SET tickets_order = tickets_order - " . (int)$tour_info['passagers'] . ", tickets_buy = tickets_buy + " . (int)$tour_info['passagers'] . " WHERE tour_id ='" . $tour_info['tour_id'] . "' AND tour_date = '" . $tour_info['tour_date'] . "'");

                // Отправка email с PDF
                generateAndSendPdf($paymentData, $orderInfo, $ticketInfo, $tourInfo, $from_city, $to_city, $from_stop, $to_stop);

                echo 'ok';
                http_response_code(200); // OK
            } else {
                echo 'Order not found';
                http_response_code(404); // Not Found
            }
        } else {
            echo 'Invalid payment status';
            http_response_code(400); // Bad Request
        }
    } else {
        echo 'Invalid signature';
        http_response_code(400); // Bad Request
    }
} else {
    echo 'No data or signature';
    http_response_code(400); // Bad Request
}

// Генерация и отправка PDF
function generateAndSendPdf($paymentData, $orderInfo, $ticketInfo, $tourInfo, $from_city, $to_city, $from_stop, $to_stop) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/' . ADMIN_PANEL . '/libs/mpdf/autoload.php';
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'orientation' => 'p'
    ]);

    $order_id = $paymentData['order_id'];
    $total_price = $paymentData['amount'];

    // Генерация PDF
    $html = '
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            padding: 15px;
            border-collapse: collapse; /* Для объединения границ ячеек */
        }
        img{
        max-width: 100%;
        width: 200px;
        }
        td {
            vertical-align: top;
            padding: 10px;
        }
        .container {
            padding: 0 30px;
            width: 1140px;
        }
        .tiket_section {
            padding: 20px;
            border-bottom: 2px dashed #000;
        }
        table.tiket_bordered {
            padding: 20px;
            border: 2px dashed #000;
            border-radius: 10px;
        }
        .tiket_column.small_info {
            width: 25%; /* Ширина для small_info */
            text-align: center;
            border-right: 1px solid #000;
            padding-right: 20px;
            margin-right: 20px;
        }
        .tiket_logo img {
            max-width: 100%;
            height: auto;
        }
        .title {
            font-weight: bold;
        }
        .big_title {
            font-size: 18px;
            text-align: center;
        }
        .add_info {
            padding-top: 50px;
            border-top: 1px solid #000;
            text-align: right;
            padding-right: 20px;
        }
        .pass_info-section {
            padding: 20px;
        }
        .pass_info-columns_wrapper {
            display: flex;
            justify-content: space-between;
        }
        .pass_info-column {
            flex: 1;
            padding: 10px;
        }
        .tr_border_top{
            padding-top: 30px; 
            border-top: 1px solid #000;
        }
        .qr-code{
        position: relative;
        margin-top: 30px;
        }
        .add_info_title{
        white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="container" >
    <section class="tiket_section">
        <table class="tiket_bordered" style="border-collapse: collapse; width: 100%;">
        <tr>
            <td class="tiket_column small_info" style="width:25%;">
                <div class="tiket_logo"><img src="https://www.maxtransltd.com/public/upload/logos/maxTransLogo.png" alt=""></div>
                <div class="date_title title">Продано/Sales</div>
                <div class="date_info">' . $orderInfo['order_date'] . '</div>
                <div class="tiket_id" style="margin-bottom: 30px;">№' . $orderInfo['id'] . '</div>
                <div class="qr-code" style="margin-top: 30px;"><img src="https://www.maxtransltd.com/public/upload/logos/qr-code.png" alt=""></div>
            </td>
            <td class="tiket_column passanger_data" style="width: 100%;">
                <div class="big_title title" style="text-align: center; width: 100%;">ЕЛЕКТРОННИЙ КВИТОК</div>
                <table>
                    <tr>
                        <td><b>Рейс/Flight</b>
                            <div>' . $ticketInfo['departure_city'] . ' - ' . $ticketInfo['arrival_city'] . '</div>
                        </td>
                        <td><b>Відправлення/Departure</b>
                            <div>' . $tourInfo['tour_date'] . ' ' . $ticketInfo['departure_time'] . '<br>' . $from_city . ' ' . $from_stop . '</div>
                        </td>
                        <td><b>Прибуття/Arrival</b>
                            <div>' . $to_city . ' ' . $to_stop . '</div>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Пасажир/Passenger</b>
                            <div>' . $orderInfo['client_name'] . ' ' . $orderInfo['client_surname'] . '</div>
                        </td>
                        <td><b>Місце/Seat</b>
                            <div>На вільне місце</div>
                        </td>
                        <td><b>Перевізник/Carrier</b>
                            <div>Maks Trans LTD</div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td>
                        </td>
                        <td><div>Тариф<br>Tariff</div>
                            <div>' . $ticketInfo['price'] . '</div>
                        </td>
                        <td><div>Страховий збір<br>Insurance fee</div>
                            <div>0.00</div>
                        </td>
                        <td><div>В т.ч. ПДВ<br>Including VAT</div>
                            <div>0.00</div>
                        </td>
                        <td>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Збір/Послуга<br>Fee/Service</b>
                            <div>' . $ticketInfo['price'] . '</div>
                        </td>
                        <td><b>Проїзд<br>Passage</b>
                            <div>' . $ticketInfo['price'] . '</div>
                        </td>
                        <td><b>Багаж<br>Luggage</b>
                            <div></div>
                        </td>
                        <td><b>Тип<br>Type</b>
                            <div>ПОВНИЙ</div>
                        </td>
                        <td><b>Знижка<br>Discount</b>
                            <div></div>
                        </td>
                        <td><b>Всього, грн<br>Total, UAH</b>
                            <div>' . $ticketInfo['price'] . '</div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr class="tr_border_top" style="padding-top: 30px; border-top: 1px solid;">
                        <td> 
                        </td>
                        <td> 
                        </td>                                                                     
                        <td>
                            <div class="add_info_title title">
                            Служба підтримки / Support service
                            </div>
                            <div class="add_info_phone">
                                +38 093 272 11 54
                            </div>
                        </td>                    
                    </tr>
                </table>            
            </td>
        </tr>
    </table>
</section>
<section class="pass_info-section">
    <div class="tiket_wrapper container">
        <div class="pass_info_container">
            <div class="pass_info_title title">
                До відома пасажирів:
            </div>
            <div class="pass_info-columns_wrapper">
            <div class="pass_info-column">
                <div class="pass_info">1.Після оплати проїзду пасажиру рекомендовано перевірити усі реєстраційні дані, вказані у ваучері бронювання.</div>
                <div class="pass_info">2.Для забезпечення організованої посадки, пасажиру бажано прибути до місця відправлення автобусу </div>
                <div class="pass_info">3.Відправлення автобусу у рейс здійснюється за місцевим часом</div>
                <div class="pass_info">4.Пасажир несе відповідальність за домтримання візового режиму та умов перетину кордону</div>
                <div class="pass_info">5.Для отримання інформації щодо переоформлення або відміни поїздки пасажир може звернутися до офіційних представництв компаніі, або за телефонами Служби підтримки</div>
                <div class="pass_info">6.Оплата поїздки свідчить про згоду пасажира з умовами договору оферти, розміщенного на сайті та в офіційних прдставництвах компанії.</div>
                <div class="pass_info">7.Квиток є дійсним, тільки за умови, якщо прізвище та Імя пасажира відповідають його паспортним даним.</div>
            </div>
            </div>
        </div>
        <div class="pass_info_container">
            <div class="pass_info_title title">
    Умови повернення квитків:
            </div>
            <div class="pass_info">- від 72 год і більше до відправлення – 75% від вартості поїздки</div>
            <div class="pass_info">- від 24 год до 72 год до відправлення - 50% від вартості поїздки</div>
            <div class="pass_info">- від 12 год до 24 год до відправлення – 25% від вартості поїздки</div>
            <div class="pass_info">- менше 12 год до відправлення - гроші за поїздку не повертаються</div>
        </div>
    </div>
</section>
</div>
</body>
</html>
    ';


    $mpdf->WriteHTML($html);

    // Создание PDF и сохранение в файл
    $pdfFilePath = $_SERVER['DOCUMENT_ROOT'] . '/tickets_pdf/ticket_' . $orderInfo['id'] . '.pdf';
    $mpdf->Output($pdfFilePath, 'F');



    sendEmailWithAttachment($pdfFilePath, $orderInfo, $ticketInfo);


    return $pdfFilePath; // Возвращаем путь к сохраненному файлу
}

function sendEmailWithAttachment($pdfFilePath, $orderInfo, $ticketInfo) {
    $departureCity = $ticketInfo['departure_city'];
    $departureTime = substr($ticketInfo['departure_time'], 0, 5);
    $arrivalCity = $ticketInfo['arrival_city'];
    $date = $orderInfo['tour_date'];
    $email = $orderInfo['client_email'];
    $name = $orderInfo['client_name'];
    $familyName = $orderInfo['client_surname'];
    $phone = $orderInfo['client_phone'];
    $price = $ticketInfo['price'];
    $ticketId = $orderInfo['id'];

    // Сообщение для клиента
    $message1 = "
        <html>
        <head>
            <title>Ваш квиток</title>
            <style>
                .email-content {
                    border-left: 4px solid #40A6FF;
                    padding-left: 10px;
                }
                .email-content table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .email-content td {
                    padding: 5px 10px;
                }
                .email-titles {
                    font-weight: bold;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 150px;
                }
                .img_logo img{
                    pointer-events: none;
                }
            </style>
        </head>
        <body>
            <div class='header' oncontextmenu='return false;'>
                <a href='https://www.maxtransltd.com'>
                <img src='https://www.maxtransltd.com/public/upload/logos/mailLogo.jpeg' alt='MaxTrans LTD' class='logo'>
                </a>
            </div>
            <p>Ваш квиток:</p>
            <div class='email-content'>
                <table>
                    <tr>
                        <td class='email-titles'>Квиток</td>
                        <td>$ticketId</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Рейс</td>
                        <td>$departureCity - $arrivalCity</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Виїзд</td>
                        <td>$date $departureTime</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Пасажир</td>
                        <td>$name $familyName</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Телефон</td>
                        <td>$phone</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>E-mail</td>
                        <td>$email</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Ціна</td>
                        <td>$price</td>
                    </tr>
                </table>
                <p>Перевізник: Maks Trans LTD</p>
            </div>
        </body>
        </html>
    ";

    // Сообщение для администратора
    $message2 = "
        <html>
        <head>
            <title>Покупка білета:</title>
            <style>
                .email-content {
                    border-left: 4px solid #FF5733;
                    padding-left: 10px;
                }
                .email-content table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .email-content td {
                    padding: 5px 10px;
                }
                .email-titles {
                    font-weight: bold;
                }
                .header {
                    text-align: center;
                    margin-bottom: 20px;
                }
                .logo {
                    max-width: 150px;
                }
                .img_logo img{
                    pointer-events: none;
                }
            </style>
        </head>
        <body>
            <div class='header' oncontextmenu='return false;'>
                <a href='https://www.maxtransltd.com'>
                <img src='https://www.maxtransltd.com/public/upload/logos/mailLogo.jpeg' alt='MaxTrans LTD' class='logo'>
                </a>
            </div>
            <p>Покупка білета:</p>
            <div class='email-content'>
                <table>
                    <tr>
                        <td class='email-titles'>Квиток</td>
                        <td>$ticketId</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Рейс</td>
                        <td>$departureCity - $arrivalCity</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Виїзд</td>
                        <td>$date $departureTime</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Пасажир</td>
                        <td>$name $familyName</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Телефон</td>
                        <td>$phone</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>E-mail</td>
                        <td>$email</td>
                    </tr>
                    <tr>
                        <td class='email-titles'>Ціна</td>
                        <td>$price</td>
                    </tr>
                </table>
                <p>Перевізник: Maks Trans LTD</p>
            </div>
        </body>
        </html>
    ";

    $separator = md5(time());
    $eol = "\r\n";

    $fromName = "Max Trans LTD";
    $fromEmail = "info@maxtransltd.com";

    $headers = "From: $fromName <$fromEmail>" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"$separator\"" . $eol;

// Настройка параметров SMTP
    ini_set('SMTP', 'mail.adm.tools');
    ini_set('smtp_port', '465'); // Порт для SSL
    ini_set('sendmail_from', 'info@maxtransltd.com');
    ini_set('sendmail_path', '"/usr/sbin/sendmail -t -i"');

// Функция отправки письма
    function sendMail($to, $subject, $message, $headers, $pdfFilePath, $separator, $eol) {
        $body  = "--" . $separator . $eol;
        $body .= "Content-Type: text/html; charset=\"utf-8\"" . $eol;
        $body .= "Content-Transfer-Encoding: 7bit" . $eol . $eol;
        $body .= $message . $eol;

        // Вложение
        $body .= "--" . $separator . $eol;
        $body .= "Content-Type: application/pdf; name=\"ticket.pdf\"" . $eol;
        $body .= "Content-Transfer-Encoding: base64" . $eol;
        $body .= "Content-Disposition: attachment; filename=\"ticket.pdf\"" . $eol . $eol;
        $body .= chunk_split(base64_encode(file_get_contents($pdfFilePath))) . $eol;
        $body .= "--" . $separator . "--";

        // Отправка письма
        $mail_sent = mail($to, $subject, $body, $headers);

        if ($mail_sent) {
            return true;
        } else {
            return false;
        }
    }

    // Отправка письма клиенту
    $clientEmail = $orderInfo['client_email'];
    $clientSubject = "Ваш квиток";
    sendMail($clientEmail, $clientSubject, $message1, $headers, $pdfFilePath, $separator, $eol);

    // Отправка письма администратору
    $adminEmail = "max210183@ukr.net";
    $adminSubject = "Покупка білета";
    sendMail($adminEmail, $adminSubject, $message2, $headers, $pdfFilePath, $separator, $eol);
}
?>
