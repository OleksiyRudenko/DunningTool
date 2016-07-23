<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 20/07/2016
 * Time: 20:40
 */

MODULE::initialize(

    [   // MODULE::path2mod
        'dateadjustments' =>  'DateBiz',
        'devmanual'  =>  'DevManual',
    ],

    [   // MODULE::$setting
        'DateBiz'   =>  [
            'navmenu'   =>  'Календарь',
            'heading'   =>  'Переносы рабочих дней',
            'onSubmit'  =>  true,
        ],
        'DevManual'   =>  [
            'navmenu'   =>  'Разработчику',
            'heading'   =>  'Руководство разработчика',
        ],

    ]
);