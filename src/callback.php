<?php

$email              = array();
$email['from']      = '';
$email['method']    = 'SMTP';
$email['smtp_host'] = '';
$email['smtp_port'] = '25';

$phone         = ! empty($_POST['phone']) ? $_POST['phone'] : '';
$date          = date('F j, Y, H:i:s');
$email_to      = '';
$email_subject = 'Заказ звонка!';
$email_message = "
    Номер телефона: {$phone}<br>
    Дата заказа: {$date}
";


if ( ! empty($phone)) {
    try {
        if (sendMail($email_to, $email_subject, $email_message, $email)) {
            echo json_encode(array(
               'status' => 'success'
            ));
        } else {
            echo json_encode(array(
                'status'     => 'error',
                'error_code' => 1,
                'message'    => 'Ой! К сожалению, по техническим причинам, мы не можем сейчас вам перезвонить. Попробуйте пожалуйста позже.',
            ));
        }
    } catch(Exception $e) {
        echo json_encode(array(
            'status'     => 'error',
            'error_code' => 2,
            'message'    => 'Ой! К сожалению, по техническим причинам, мы не можем сейчас вам перезвонить. Попробуйте пожалуйста позже.',
        ));
    }

} else {
    echo json_encode(array(
        'status'     => 'error',
        'error_code' => 3,
        'message'    => 'Укажите номер телефона.',
    ));
}












/**
 * Отправка письма
 * @param string $to Email поучателя. Могут содержать несколько адресов разделенных зяпятой.
 * @param string $subject Тема письма
 * @param string $message Тело письма
 * @param array $options
 *      Опциональные значения для письма.
 *      Может содержать такие ключи как
 *      charset - Кодировка сообщения. По умолчанию содержет - utf-8
 *      content_type - Тип сожержимого. По умолчанию содержет - text/html
 *      from - Адрес отправителя. По умолчанию содержет - noreply@localhost
 *      cc - Адреса вторичных получателей письма, к которым направляется копия. По умолчанию содержет - false
 *      bcc - Адреса получателей письма, чьи адреса не следует показывать другим получателям. По умолчанию содержет - false
 *      method - Метод отправки. Может принимать значения smtp и mail. По умолчанию содержет - mail
 *      smtp_host - Хост для smtp отправки. По умолчанию содержет - localhost
 *      smtp_port - Порт для smtp отправки. По умолчанию содержет - 25
 *      smtp_auth - Признак аутентификации для smtp отправки. По умолчанию содержет - false
 *      smtp_secure - Название шифрования, TLS или SSL. По умолчанию без шифрования
 *      smtp_user - Пользователь при использовании аутентификации для smtp отправки. По умолчанию содержет пустую строку
 *      smtp_pass - Пароль при использовании аутентификации для smtp отправки. По умолчанию содержет пустую строку
 *      smtp_timeout - Таймаут для smtp отправки. По умолчанию содержет - 15
 * @return bool Успешна либо нет отправка сообщения
 * @throws Exception Исключение с текстом произошедшей ошибки
 */
function sendMail($to, $subject, $message, array $options = array()) {

    $options['charset']      = isset($options['charset']) && trim($options['charset']) != '' ? $options['charset'] : 'utf-8';
    $options['content_type'] = isset($options['content_type']) && trim($options['content_type']) != '' ? $options['content_type'] : 'text/html';
    $options['server_name']  = isset($options['server_name']) && trim($options['server_name']) != '' ? $options['server_name'] : 'localhost';
    $options['from']         = isset($options['from']) && trim($options['from']) != '' ? $options['from'] : 'noreply@' . $options['server_name'];
    $options['cc']           = isset($options['cc']) && trim($options['cc']) != '' ? $options['cc'] : false;
    $options['bcc']          = isset($options['bcc']) && trim($options['bcc']) != '' ? $options['bcc'] : false;
    $subject                 = $subject != null && trim($subject) != '' ? $subject : '(No Subject)';


    $headers = array(
        "MIME-Version: 1.0",
        "Content-type: {$options['content_type']}; charset={$options['charset']}",
        "From: {$options['from']}",
        "Content-Transfer-Encoding: base64",
        "X-Mailer: PHP/" . phpversion()
    );

    if ($options['cc']) $headers[] = $options['cc'];
    if ($options['bcc']) $headers[] = $options['bcc'];


    if (isset($options['method']) && strtoupper($options['method']) == 'SMTP') {

        $options['smtp_host']    = isset($options['smtp_host']) && trim($options['smtp_host']) != '' ? $options['smtp_host'] : $options['server_name'];
        $options['smtp_port']    = isset($options['smtp_port']) && (int)($options['smtp_port']) > 0  ? $options['smtp_port'] : 25;
        $options['smtp_secure']  = isset($options['smtp_secure']) ? $options['smtp_secure'] : '';
        $options['smtp_auth']    = isset($options['smtp_auth']) ? (bool)$options['smtp_auth'] : false;
        $options['smtp_user']    = isset($options['smtp_user']) ? $options['smtp_user'] : '';
        $options['smtp_pass']    = isset($options['smtp_pass']) ? $options['smtp_pass'] : '';
        $options['smtp_timeout'] = isset($options['smtp_timeout']) && (int)($options['smtp_timeout']) > 0 ? $options['smtp_timeout'] : 15;

        $headers[] = "Subject: {$subject}";
        $headers[] = "To: <" . implode('>, <', explode(',', $to)) . ">";
        $headers[] = "\r\n";
        $headers[] = wordwrap(base64_encode($message), 75, "\n", true);
        $headers[] = "\r\n";

        $recipients = explode(',', $to);
        $errno      = '';
        $errstr     = '';


        if (strtoupper($options['smtp_secure']) == 'SSL') {
            $options['smtp_host'] = 'ssl://' . preg_replace('~^([a-zA-Z0-9]+:|)//~', '', $options['smtp_host']);
        }


        if ( ! ($socket = fsockopen($options['smtp_host'], $options['smtp_port'], $errno, $errstr, $options['smtp_timeout']))) {
            throw new Exception("Error connecting to '{$options['smtp_host']}': {$errno} - {$errstr}");
        }

        if (substr(PHP_OS, 0, 3) != "WIN") socket_set_timeout($socket, $options['smtp_timeout'], 0);

        serverParse($socket, '220');

        fwrite($socket, 'EHLO ' . $options['smtp_host'] . "\r\n");
        serverParse($socket, '250');

        if (strtoupper($options['smtp_secure']) == 'TLS') {
            fwrite($socket, 'STARTTLS' . "\r\n");
            serverParse($socket, '250');
        }


        if ($options['smtp_auth']) {
            fwrite($socket, 'AUTH LOGIN' . "\r\n");
            serverParse($socket, '334');

            fwrite($socket, base64_encode($options['smtp_user']) . "\r\n");
            serverParse($socket, '334');

            fwrite($socket, base64_encode($options['smtp_pass']) . "\r\n");
            serverParse($socket, '235');
        }

        fwrite($socket, "MAIL FROM: <{$options['from']}>\r\n");
        serverParse($socket, '250');


        foreach ($recipients as $email) {
            fwrite($socket, 'RCPT TO: <' . $email . '>' . "\r\n");
            serverParse($socket, '250');
        }

        fwrite($socket, 'DATA' . "\r\n");
        serverParse($socket, '354');

        fwrite($socket, implode("\r\n", $headers));
        fwrite($socket, '.' . "\r\n");
        serverParse($socket, '250');

        fwrite($socket, 'QUIT' . "\r\n");
        fclose($socket);

        return true;

    } else {

        return mail($to, $subject, wordwrap(base64_encode($message), 75, "\n", true), implode("\r\n", $headers));
    }
}


/**
 * Получение ответа от сервера
 * @param resource $socket
 * @param string $expected_response
 * @throws Exception
 */
function serverParse($socket, $expected_response) {

    $server_response = '';
    while (substr($server_response, 3, 1) != ' ') {
        if ( ! ($server_response = fgets($socket, 256)))  {
            throw new Exception('Error while fetching server response codes.');
        }
    }
    if (substr($server_response, 0, 3) != $expected_response) {
        throw new Exception("Unable to send e-mail: {$server_response}");
    }
}
