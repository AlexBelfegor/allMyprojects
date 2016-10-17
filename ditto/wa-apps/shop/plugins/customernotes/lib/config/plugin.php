<?php
return array (
    'name' => _wp("Customer`s notes"),
    'description' => _wp("Allows you to create and view notes about the customer in the order."),
    'img' => 'img/customernotes16.png',
    'icon' => 'img/customernotes16.png',
    'version' => '1.0.7',
    'vendor' => '964801',
    'handlers' =>
        array (
            'backend_order' => 'backendOrder',
        ),
);
