<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'olix-backoffice' => [
        'version' => '1.0.7',
    ],
    '@fortawesome/fontawesome-free/css/all.min.css' => [
        'version' => '5.15.4',
        'type' => 'css',
    ],
    'toastr/build/toastr.min.css' => [
        'version' => '2.1.4',
        'type' => 'css',
    ],
    'icheck-bootstrap/icheck-bootstrap.min.css' => [
        'version' => '3.0.1',
        'type' => 'css',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.0.13',
        'type' => 'css',
    ],
    '@eonasdan/tempus-dominus/dist/css/tempus-dominus.min.css' => [
        'version' => '6.9.9',
        'type' => 'css',
    ],
    'bootstrap4-duallistbox/dist/bootstrap-duallistbox.min.css' => [
        'version' => '4.0.2',
        'type' => 'css',
    ],
    'datatables.net-bs4/css/dataTables.bootstrap4.min.css' => [
        'version' => '1.13.11',
        'type' => 'css',
    ],
    'datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css' => [
        'version' => '2.5.1',
        'type' => 'css',
    ],
    'admin-lte/dist/css/adminlte.min.css' => [
        'version' => '3.2.0',
        'type' => 'css',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'bootstrap/dist/js/bootstrap.bundle.min.js' => [
        'version' => '4.6.2',
    ],
    'admin-lte' => [
        'version' => '3.2.0',
    ],
    'datatables.net' => [
        'version' => '2.1.8',
    ],
    'datatables.net-bs4' => [
        'version' => '1.13.11',
    ],
    'datatables.net-responsive' => [
        'version' => '3.0.3',
    ],
    'datatables.net-responsive-bs4' => [
        'version' => '2.5.1',
    ],
    'bootstrap/js/src/modal.js' => [
        'version' => '4.6.2',
    ],
    'bootstrap4-duallistbox' => [
        'version' => '4.0.2',
    ],
    'bootstrap-switch' => [
        'version' => '3.4.0',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    '@eonasdan/tempus-dominus' => [
        'version' => '6.9.9',
    ],
    'toastr' => [
        'version' => '2.1.4',
    ],
    'select2/dist/js/select2.full.js' => [
        'version' => '4.0.13',
    ],
    'olix-backoffice/olixbo.min.css' => [
        'version' => '1.0.7',
        'type' => 'css',
    ],
];
