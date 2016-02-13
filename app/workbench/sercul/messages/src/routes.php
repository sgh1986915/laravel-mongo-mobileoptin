<?php
Route::group(
    [
        'prefix'     => 'messages',
        'permission' => 'access_campaigns',
        'middleware' => [
            'auth',
            'acl',
            'moduleAccess'
        ]
    ],
    function (){

        // list campaigns
        Route::get( '/',
            [
                'as'   => 'messages',
                'uses' => 'sercul\messages\MessagesController@index'
            ] );
           // read
        //open edit form
        Route::get(
            'read/{id}',
            [
                'as'         => 'read_message',
                'uses'       => 'sercul\messages\MessagesController@read'
            ]
        )->where(
            [
                'id' => '[0-9]+'
            ]
        );
        // open add form 
        Route::get( 'add',
            [
                'as'         => 'add_message',
                'permission' => 'manage_campaign',
                'uses'       => 'sercul\messages\MessagesController@add'
            ]
        );
        //open edit form
        Route::get(
            'edit/{id}',
            [
                'as'         => 'edit_message',
                'permission' => 'manage_campaign',
                'uses'       => 'sercul\messages\MessagesController@edit'
            ]
        )->where(
            [
                'id' => '[0-9]+'
            ]
        );
        // delete the campaign
        Route::get( 'delete/{id}',
            [
                'as'         => 'delete_message',
                'permission' => 'manage_campaign',
                'uses'       => 'sercul\messages\MessagesController@delete'
            ]
        )->where(
            [
                'id' => '[0-9]+'
            ]
        );
        // update and insert
        Route::post( 'upsert',
            [
                'as'         => 'upsert_message',
                'permission' => 'manage_campaign',
                'uses'       => 'sercul\messages\MessagesController@upsert'
            ]
        );

 }
);
