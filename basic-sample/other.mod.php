<?
$data= file_get_contents('http://api.fixer.io/latest');
$data= json_decode($data);

core::reg('rates',$data);