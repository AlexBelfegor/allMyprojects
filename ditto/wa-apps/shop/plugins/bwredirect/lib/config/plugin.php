<?php

return array(
    'name' => 'Redirect 301',
    'version' => '1.0',
    'description' => 'Redirect 301',
    'vendor' => 'blazewebart.com',
    'importexport'   => true,
    'export_profile' => true,
    'handlers' => array(
        'frontend_head' => 'redirect',
    ),
);
//EOF
