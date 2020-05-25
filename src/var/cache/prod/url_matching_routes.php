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
                .'|/orders(?'
                    .'|(?:/([^/]++))?(*:31)'
                    .'|/list(?:/([^/]++)(?:/([^/]++))?)?(*:71)'
                .')'
            .')/?$}sDu',
    ],
    [ // $dynamicRoutes
        31 => [[['_route' => 'take-orders', 'id' => null, '_controller' => 'App\\Controller\\OrdersController::takeAction'], ['id'], ['PATCH' => 0], null, false, true, null]],
        71 => [
            [['_route' => 'list-orders', 'page' => null, 'limit' => null, '_controller' => 'App\\Controller\\OrdersController::listAction'], ['page', 'limit'], ['GET' => 0], null, false, true, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
