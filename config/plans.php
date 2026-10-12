<?php

return [

    /*
    | When this is false, every action is allowed. The limits below stay
    | in config so a paid plan can be turned on without reshaping the app.
    |
    | Limits belong to the list owner. A list you were invited to does not
    | count toward your own list cap.
    */
    'enforce' => false,

    'limits' => [
        'free' => [
            'owned_lists' => 2,
            'members_per_list' => 5,
            'upload_background' => false,
            'item_photo' => false,
        ],
        'paid' => [
            'owned_lists' => null,
            'members_per_list' => null,
            'upload_background' => true,
            'item_photo' => true,
        ],
    ],

];
