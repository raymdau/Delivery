<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/orders' => [[['_route' => 'place-orders', '_controller' => 'App\\Controller\\OrdersController::placeAction'], null, ['POST' => 0], null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/_error/(\\d+)(?:\\.([^/]++))?(*:35)'
                .'|/orders(?'
                    .'|(?:/([^/]++))?(*:66)'
                    .'|/list(?:/([^/]++)(?:/([^/]++))?)?(*:106)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        35 => [[['_route' => '_preview_error', '_controller' => 'error_controller::preview', '_format' => 'html'], ['code', '_format'], null, null, false, true, null]],
        66 => [[['_route' => 'take-orders', 'id' => null, '_controller' => 'App\\Controller\\OrdersController::takeAction'], ['id'], ['PATCH' => 0], null, false, true, null]],
        106 => [
            [['_route' => 'list-orders', 'page' => null, 'limit' => null, '_controller' => 'App\\Controller\\OrdersController::listAction'], ['page', 'limit'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
