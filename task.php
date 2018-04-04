<?php

require __DIR__ . '/vendor/autoload.php';


$service = new \DomainAvailability\DomainAvailability();

var_dump($service->isAvailable('test.com'));
var_dump($service->isAvailable('test.co.uk'));
//var_dump($service->isAvailable('test.dasdsad'));
var_dump($service->isAvailable('test.cat'));
var_dump($service->isAvailable('testdasdsadasdewqewqec3423dsr23rfwe.com'));
