<?php
require_once('site_settings.php');

define('USER_ADMIN', 1);
define('USER_AGENT', 2);
define('USER_TYPES', serialize(array(
    USER_ADMIN => 'Admin',
    USER_AGENT => 'Agent',
)));
define('AU_STATES', serialize(array(
    'VIC' => 'Victoria',
    'SA' => 'South Australia',
    'QLD' => 'Queensland',
    'NSW' => 'New South Wales',
    'ACT' => 'Australian Capital Territory',
)));
define('AU_PAYMENTS', serialize(array(
    'bpay' => 'BPAY',
    'credit_card'=> 'Credit Card',
    'easipay'=> 'EasiPay',
    'online'=> 'Online',
    'centrepay'=> 'Centrepay',
    'cash'=> 'Cash',
    'cheque'=> 'Cheque',
    'post_billpay'=> 'POST billpay',
    'pay_by_phone'=> 'Pay By Phone',
    'amex'=> 'AMEX',
)));
define('AU_STATES_ABBREVS', serialize(array(
    'VIC',
    'SA',
    'QLD',
    'NSW',
    'ACT',
)));
define('STAFF_IPS', serialize(array(
    '119.98.77.97', // Sean
    '122.199.31.108', // Gaurav
    '13.55.57.184', //Freshping IP
    '115.240.10.112',
    '14.99.111.112',
    '36.255.67.240',
    '220.227.230.232',
    '14.99.198.250',
    '103.233.119.16',
    '203.129.28.0',
    '115.146.72.16',
    '54.79.205.5',
    '54.206.247.93',
    '116.255.20.254',
    '14.99.111.116',
    '220.227.230.234',
    '115.240.10.116',
    '36.255.67.246',
)));
define('BAN_PHONE_NUMBERS', serialize(array(
    '1300359779',
    '0400123456',
    '0411123456',
    '0412123456',
    '0408654321',
    '0418151657',
    '0438222333',
    '0412345678',
    '0403678392',
    '0421412723',
    '0247878188',
    '0428287122',
    '0428051004',
    '0404040404',
    '0432123456',
    '0400001002',
    '0425252525',
    '0450634623',
    '0401234567',
    '0405633633',
    '0399821629',
    '0249912580',
    '0401884377',
    '0415510244',
    '0406972885',
    '0408054065',
    '0400100100',
)));

define('BAN_STRING', serialize(array(
    'fuck',
    'screw',
    'whore',
    'porn',
    'wtupid',
    'idiot',
    'wastage',
)));

define('CONSUMPTION_LEVELS', serialize(array(
    '1' => 'Low',
    '2' => 'Medium',
    '3' => 'High',
    '4' => 'Low / Medium',
    '5' => 'Medium / High',
)));

define('LEADS360_USERNAME', 'api@voucherstore.com.au');
define('LEADS360_PASSWORD', 'Vs6220508!@');
define('LEADS360_URL_1', 'https://secure.velocify.com'); // http://208.39.75.141 or https://secure.velocify.com
define('LEADS360_URL_2', 'https://service.prod.velocify.com'); // http://208.39.75.40 or https://service.prod.velocify.com

define('APP_KEY', '48347de54501ba15d16d84dbcbe348fd');

?>