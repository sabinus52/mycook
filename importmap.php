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
    'tom-select/dist/css/tom-select.default.css' => [
        'version' => '2.4.1',
        'type' => 'css',
    ],
    'bootstrap' => [
        'version' => '4.6.2',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'popper.js' => [
        'version' => '1.16.1',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '4.6.2',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.min.css' => [
        'version' => '5.15.4',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'tom-select' => [
        'version' => '2.4.1',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
    'tom-select/dist/css/tom-select.default.min.css' => [
        'version' => '2.4.1',
        'type' => 'css',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
    'animate.css' => [
        'version' => '3.7.2',
    ],
    'animate.css/animate.min.css' => [
        'version' => '3.7.2',
        'type' => 'css',
    ],
    'wow.js' => [
        'version' => '1.2.2',
    ],
    'olix-backoffice/plugins/modal.js' => [
        'version' => '1.1.0',
    ],
    'bootstrap/js/src/modal.js' => [
        'version' => '4.6.2',
    ],
    'olix-backoffice/plugins/select2.js' => [
        'version' => '1.1.0',
    ],
    'select2/dist/css/select2.min.css' => [
        'version' => '4.0.13',
        'type' => 'css',
    ],
    'select2/dist/js/select2.full.js' => [
        'version' => '4.0.13',
    ],
    'olix-backoffice/plugins/collection.js' => [
        'version' => '1.1.0',
    ],
];
