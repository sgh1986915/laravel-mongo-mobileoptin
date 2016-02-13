<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */


Route::get('/', array('as' => 'front', 'uses' => 'Auth\AuthController@getLogin'));
Route::get('home', array('as' => 'home', 'uses' => 'HomeController@index'));
Route::post('handle_display_announce', array('as' => 'handle_display_announce', 'uses' => 'HomeController@handle_display_announce'));
Route::any('support', array('as' => 'support', 'uses' => 'StaticController@support'));
Route::any('support/faq', array('as' => 'support.with.faq_categories.first', 'uses' => 'StaticController@faq'));
Route::any('support/faq/{faq_category_id}', array('as' => 'support.with.faq_categories', 'uses' => 'StaticController@faq'))->where([ 'faq_category_id' => '[0-9]+']);
Route::get('expert_traffic_academy', array('as' => 'support.with.expert_categories.first', 'uses' => 'StaticController@expert'));
Route::get('expert_traffic_academy/{faq_category_id}', array('as' => 'support.with.expert_categories', 'uses' => 'StaticController@expert'))->where([ 'faq_category_id' => '[0-9]+']);
Route::get('/c{campaign_id}/{campaing_name?}', [ 'as' => 'campaign_link', 'uses' => 'CampaignsController@show_item'])->where([ 'campaign_id' => '[0-9]+', 'campaing_name' => '(.*)?+']);
//halabuda@gmail.com
//added route for message read ajax endpoint
//\app\Http\Controllers\MessagesController.php::read()
Route::any('messages/read/{message_id}', array('uses' => 'MessagesController@read'))->where([ 'message_id' => '[0-9]+']);


Route::get('pixel/{user_template_id}', [
    'as' => 'get_pixel',
    'uses' => 'CampaignsController@get_pixel'
])->where([
    'user_template_id' => '[0-9]+',
]);

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);


$router->group(
        [
    'prefix' => 'api/users',
        ], function () use ( $router ) {

    /**
     * split testing results
     */
    $router->get('/list', [
        'as' => 'api.users.list',
        'uses' => 'API\UserManagement@all'
    ]);
    $router->get('/show', [
        'as' => 'api.users.getone',
        'uses' => 'API\UserManagement@show'
    ]);
    $router->post('/update', [
        'as' => 'api.users.updateone',
        'uses' => 'API\UserManagement@update'
    ]);
    $router->post('/create', [
        'as' => 'api.users.updateone',
        'uses' => 'API\UserManagement@create'
    ]);
    $router->post('/create_package', [
        'as' => 'api.users.updateonepack',
        'uses' => 'API\UserManagement@createPackage'
    ]);
    $router->post('/delete/{user_id?}', [
        'as' => 'api.users.delete',
        'uses' => 'API\UserManagement@destroy'
    ]);
    $router->post('/disable', [
        'as' => 'api.users.disable',
        'uses' => 'API\UserManagement@disable'
    ]);
    $router->get('/roles', [
        'as' => 'api.users.roles',
        'uses' => 'API\UserManagement@roles'
    ]);

    $router->post('/add_template_group/{user_id}', [
        'as' => 'api.users.addtemplategroup',
        'uses' => 'API\UserManagement@addTemplateGroup'
    ]);

    $router->get('/assign_module/{user_id}/{module_id}', [
        'as' => 'api.users.assignModule',
        'uses' => 'API\UserManagement@assignModule'
    ]);
    
    $router->get('/assign_package/{user_id}/{package_id}', [
        'as' => 'api.users.assignPackageToUser',
        'uses' => 'API\UserManagement@assignPackageToUser'
    ]);
    $router->any('/parse_sendgrid_responce/{otherLinks?}', [
        'as' => 'api.users.parseSendGridResponce',
        'uses' => 'API\UserManagement@parseSendGridResponce'
    ]);
	
	$router->any('/parse_oneshop_responce/{otherLinks?}', [
    	'as' => 'api.users.parseOneShopResponce',
    	'uses' => 'API\UserManagement@parseOneShopResponce'
    ]);
	
    $router->any('/parse_responce/{otherLinks?}', [
        'as' => 'api.users.parseResponce',
        'uses' => 'API\UserManagement@parseResponce'
    ]);
    $router->post('/post_assign_package', [
        'as' => 'api.users.postAssignPackageToUser',
        'uses' => 'API\UserManagement@postAssignPackageToUser'
    ]);
    
    $router->get('/revoke_module/{user_id}/{module_id}', [
        'as' => 'api.users.revokeModule',
        'uses' => 'API\UserManagement@revokeModule'
    ]);
    $router->post('/assign_module/{user_id}/{module_id}', [
        'as' => 'api.users.assignModule',
        'uses' => 'API\UserManagement@assignModule'
    ]);

    $router->post('/revoke_module/{user_id}/{module_id}', [
        'as' => 'api.users.revokeModule',
        'uses' => 'API\UserManagement@revokeModule'
    ]);


    $router->post('/remove_template_group/{user_id}', [
        'as' => 'api.users.removeTemplateGroup',
        'uses' => 'API\UserManagement@removeTemplateGroup'
    ]);
    $router->get('/available_template_groups', [
        'as' => 'api.users.availableTemplateGroups',
        'uses' => 'API\UserManagement@availableTemplateGroups'
    ]);
}
);

Route::get('api/zapier/test', 'API\ZapierController@test');
Route::post('api/zapier/hooks', 'API\ZapierController@postHook');
Route::get('api/zapier/hooks', 'API\ZapierController@postHook'); // delete this, just copied for testing
Route::get('api/zapier/hooks-sample', 'API\ZapierController@getHookSample');

Route::post('api/track-event', 'API\PublicApiController@postTrackEvent');
Route::get('api/getstats/{signle?}', 'API\PublicApiController@getStats');

//campiagns
$router->group(
        [
    'prefix' => 'campaigns',
    'permission' => 'access_campaigns',
    'middleware' => [
        'auth',
        'acl'
    ]
        ], function () use ( $router ) {

    /**
     * split testing results
     */
    $router->get('/str/{campaign_id}', [
        'as' => 'extended_testing_results',
        'uses' => 'StatsController@show'
    ])->where([
        'campaign_id' => '[0-9]+'
    ]);
    $router->get('/rstcmp/{campaign_id}', [
        'as' => 'reset_stats',
        'uses' => 'StatsController@reset'
    ])->where([
        'campaign_id' => '[0-9]+'
    ]);
    $router->post('/str/get_data/{campaign_id}', [
        'as' => 'extended_testing_results_ajax',
        'uses' => 'StatsController@get_data'
    ])->where([
        'campaign_id' => '[0-9]+'
    ]);

    // preview html of the campaing used in the template editor
    $router->get('/preview/{template_id}/{org_tmp_id}', [
        'as' => 'campaign_preview',
        'uses' => 'CampaignsController@preview_template'
    ])->where(
            [
                'template_id' => '[0-9]+',
                'org_tmp_id' => '[0-9]+'
    ]); // preview html of the campaing used in the template editor
    $router->post('/get_fresh_stats', [
        'as' => 'Campaigns.get.fresh.stats',
        'uses' => 'CampaignsController@get_fresh_stats'
    ]);
    // preview html of the campaing used in the template editor
    $router->get('/add_template_toc/{campaign_id}', [
        'as' => 'campaign_add_template',
        'uses' => 'CampaignsController@add_template'
    ])->where(
            [
                'campaign_id' => '[0-9]+'
    ]);

    // preview html of the campaing used in the template editor
    $router->get('/add_change_template_toc/{campaign_id}', [
        'as' => 'campaign_add_change_template',
        'uses' => 'CampaignsController@add_change_template'
    ])->where(
            [
                'campaign_id' => '[0-9]+'
    ]);
    
    $router->get('/add_change_template_modal/{campaign_id}', [
    		'as' => 'campaign_add_change_template_modal',
    		'uses' => 'CampaignsController@add_change_template_modal'
    		])->where([ 'campaign_id' => '[0-9]+' ]);


    $router->post('save_user_template', [
        'as' => 'save_campaign_utemplate',
        'uses' => 'CampaignsController@save_user_template'
    ]);
    // delete user template
    $router->post('/rut', [
        'as' => 'campaign_remove_user_template',
        'uses' => 'CampaignsController@remove_user_template'
            ]
    );
    // list campaigns
    $router->get('/', [
        'as' => 'campaigns',
        'uses' => 'CampaignsController@index'
    ]);
    // open add form
    $router->get('add', [
        'as' => 'add_campaigns',
        'permission' => 'manage_campaign',
        'uses' => 'CampaignsController@add'
            ]
    );
    //open edit form
    $router->get(
            'edit/{id}', [
        'as' => 'edit_campaigns',
        'permission' => 'manage_campaign',
        'uses' => 'CampaignsController@edit'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // delete the campaign
    $router->get('delete/{id}', [
        'as' => 'delete_campaigns',
        'permission' => 'manage_campaign',
        'uses' => 'CampaignsController@delete'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // update and insert
    $router->post('upsert', [
        'as' => 'upsert_campaigns',
        'permission' => 'manage_campaign',
        'uses' => 'CampaignsController@upsert'
            ]
    );
    // change the status from active to disabled
    $router->get('chstatus/{id}/{status}', [
        'as' => 'change_status_campaigns',
        'permission' => 'manage_campaign',
        'uses' => 'CampaignsController@change_satus'
            ]
    )->where(
            [
                'id' => '[0-9]+',
                'status' => '[0-9]'
            ]
    );
    // list assigned users to a campaign
    $router->get('assinged/{id}', [
        'as' => 'campaigns_assinged',
        'permission' => 'assinging_campaign',
        'uses' => 'CampaignsController@campaigns_assinged'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // save asigned usert to campaign
    $router->post('save_asigned', [
        'as' => 'save_campaigns_assinged',
        'permission' => 'assinging_campaign',
        'uses' => 'CampaignsController@save_campaigns_assinged'
            ]
    );
    // remove user for campaign
    $router->get('remove_assingment/{campaign}/{user_id}', [
        'as' => 'unassigne_from_campaign',
        'permission' => 'assinging_campaign',
        'uses' => 'CampaignsController@remove_assingment'
            ]
    )->where(
            [
                'campaign' => '[0-9]+',
                'user_id' => '[0-9]+'
            ]
    );
    // the campaign list
    $router->get('camplist/{integration_id}', [
        'as' => 'camplist',
        'uses' => 'CampaignsController@camplist'
            ]
    )->where(
            [
                'integration_id' => '[0-9]+'
            ]
    );
}
);
//domains
$router->group(
        [
    'prefix' => 'domains',
    'permission' => 'access_campaigns',
    'middleware' => [
        'auth',
        'acl'
    ]
        ], function () use ( $router ) {

    // list campaigns
    $router->get('/', [
        'as' => 'domains',
        'uses' => 'DomainsController@index'
    ]);
    // open add form
    $router->get('add', [
        'as' => 'add_domain',
        'permission' => 'manage_campaign',
        'uses' => 'DomainsController@add'
            ]
    );
    
    //open edit form
    $router->get(
            'edit/{id}', [
        'as' => 'edit_domain',
        'permission' => 'manage_campaign',
        'uses' => 'DomainsController@edit'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // delete the campaign
    $router->get('delete/{id}', [
        'as' => 'delete_domain',
        'permission' => 'manage_campaign',
        'uses' => 'DomainsController@delete'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // update and insert
    $router->post('upsert', [
        'as' => 'upsert_domain',
        'permission' => 'manage_campaign',
        'uses' => 'DomainsController@upsert'
            ]
    );
    // change the status from active to disabled
    $router->get('chstatus/{id}/{status}', [
        'as' => 'change_status_domain',
        'permission' => 'manage_campaign',
        'uses' => 'DomainsController@change_satus'
            ]
    )->where(
            [
                'id' => '[0-9]+',
                'status' => '[0-9]'
            ]
    );
    // list assigned users to a campaign
    $router->get('assinged/{id}', [
        'as' => 'domains_assinged',
        'permission' => 'assinging_campaign',
        'uses' => 'CampaignsController@domains_assinged'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
}
);



//integrations
$router->group(
        [
    'prefix' => 'integrations',
    'permission' => 'access_campaigns',
    'middleware' => [
        'auth',
        'acl'
    ]
        ], function () use ( $router ) {

    // list campaigns
    $router->get('/', [
        'as' => 'integrations',
        'uses' => 'IntegrationsController@index'
    ]);
    // open add form
    $router->get('add', [
        'as' => 'add_integration',
        'permission' => 'manage_campaign',
        'uses' => 'IntegrationsController@add'
            ]
    );
    //open edit form
    $router->get(
            'edit/{id}', [
        'as' => 'edit_integration',
        'permission' => 'manage_campaign',
        'uses' => 'IntegrationsController@edit'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // delete the campaign
    $router->get('delete/{id}', [
        'as' => 'delete_integration',
        'permission' => 'manage_campaign',
        'uses' => 'IntegrationsController@delete'
            ]
    )->where(
            [
                'id' => '[0-9]+'
            ]
    );
    // update and insert
    $router->post('upsert', [
        'as' => 'upsert_integration',
        'permission' => 'manage_campaign',
        'uses' => 'IntegrationsController@upsert'
            ]
    );
    // AWeber OAuth response
    $router->get('aweber-oauth/{id}', [
        'as' => 'aweber_oauth_integration',
        'permission' => 'manage_campaign',
        'uses' => 'IntegrationsController@aweber_oauth'
    ]);
    
    // Goto Webinar OAuth response
    $router->get('goto-webinar', [
    	'as' => 'gotowebinar_oauth_integration',
    	'permission' => 'manage_campaign',
    	'uses' => 'IntegrationsController@goto_webinar'
    ]);
    
    // Goto Webinar OAuth response
    $router->get('goto-webinar-online', [
    	'as'         => 'gotowebinar_oauth_integration',
    	'permission' => 'manage_campaign',
    	'uses'       => 'IntegrationsController@goto_webinar_online'
    ]);
}
);


$router->get('/admin/modules', [
    'uses' => 'Admin\DashboardController@modules',
    'as' => 'admin.modules'
]);
$router->get('/admin/activatemodule/{id}/{status}', [
    'uses' => 'Admin\DashboardController@activateModule',
    'as' => 'admin.activatemodule'
]);

// reconnect as user
Route::get('reconnect_as_admin/{id}', [
		'uses' => 'Admin\UserController@reconnect_as_admin',
		'as' => 'reconnect_as_admin'
	]
);

/**
 * User Admin route start
 */
$router->group(
        [
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' =>
    [
        'auth',
        'acl'
    ]
        ], function () use ( $router ) {
    // Admin dash board
    $router->get('/', [
        'uses' => 'DashboardController@index',
        'as' => 'dashboard',
        'permission' => 'manage_own_dashboard',
    ]);
    
    // open user content form
    $router->get('user_content', [
    		'as'   => 'user_content',
    		'uses' => 'DashboardController@user_content',
    		'permission' => 'manage_index_user_content' 
    	]
    );
    
    $router->post('user_content', [
    		'as'   => 'admin.save.user_content',
    		'uses' => 'DashboardController@save_user_content',
    		'permission' => 'manage_index_user_content'
    	]
    );
	
	$router->get('reset_announcement_settings', [
    		'as'   => 'admin.reset_announcement_settings',
    		'uses' => 'DashboardController@reset_announcement_settings',
    		'permission' => 'manage_index_user_content'
    	]
    );
	
	
	$router->group(
		[
			'prefix'      => 'faq',
			'permission'  => 'manage_index_user_content'
		],
		function() use ($router){
			
			// List faq categories
			$router->get('/', [
				'as'   => 'admin.faq.categories',
				'uses' => 'FaqController@index'	
			]);
			
			// open add form
			$router->get('add_category', [
					'as'   => 'admin.faq.add.category',
					'uses' => 'FaqController@add_category'
				]
			);
			
			// open edit form
	        $router->get('edit_category/{id}', [
		            'as'    => 'admin.faq.category.edit',
		            'uses'  => 'FaqController@edit_category'
	       		 ]
			)->where( ['id' => '[0-9]+' ] );
	        
	        //Edit or Add new faq category values
	        $router->post('add_edit_category', [
	        		'as'   => 'admin.faq.upsert_category',
	        		'uses' => 'FaqController@add_edit_category'
	        		]
	        );
	        
	        // delete a FAQ's Category
	        $router->get('delete_category/{id}', [
	        		'as'    => 'admin.faq.category.delete',
	        		'uses'  => 'FaqController@delete_category'
	        		]
	        )->where( ['id' => '[0-9]+' ] );
	        
	        // FAQ's Category list question and answer
	        $router->get('category_list_answers/{id}', [
					'as'    => 'admin.faq.category.answers',
	        		'uses'  => 'FaqController@category_index'
	       		]
	        )->where( ['id' => '[0-9]+' ] );
	        
	        
	        // open add Question form
	        $router->get('add/{id}', [
	        		'as'   => 'admin.faq.add',
	        		'uses' => 'FaqController@add'
	        	]
	        )->where( ['id' => '[0-9]+' ] );
	        
	        // open edit Question form
	        $router->get('edit/{category_id}/{id}', [
	        		'as'    => 'admin.faq.edit',
	        		'uses'  => 'FaqController@edit'
	        	]
	        )->where( [
	        	'id'          => '[0-9]+',
	        	'category_id' => '[0-9]+'	 
			]);
	         
	        //Edit or Add new faq category values
	        $router->post('add_edit', [
	        		'as'   => 'admin.faq.add_edit',
	        		'uses' => 'FaqController@add_edit'
	        	]
	        );
	         
	        // delete a FAQ's Category
	        $router->get('delete/{id}', [
	        		'as'    => 'admin.faq.delete',
	        		'uses'  => 'FaqController@delete'
	        	]
	        )->where( ['id' => '[0-9]+' ] );
	        
	        $router->get('delete_pdf/{id}', [
	        		'as'    => 'admin.faq.delete.pdf',
	        		'uses'  => 'FaqController@delete_pdf'
	        		]
	        )->where( ['id' => '[0-9]+' ] );
	        
		}	
	);
	
	$router->group(
			[
			'prefix'      => 'expert_traffic_academy',
			'permission'  => 'manage_index_user_content'
			],
			function() use ($router){
					
				// List faq categories
				$router->get('/', [
						'as'   => 'admin.expert_traffic.categories',
						'uses' => 'ExpertController@index'
						]);
					
				// open add form
				$router->get('add_expert_category', [
						'as'   => 'admin.expert_traffic.add.category',
						'uses' => 'ExpertController@add_category'
						]
				);
					
				// open edit form
				$router->get('edit_expert_category/{id}', [
						'as'    => 'admin.expert_traffic.category.edit',
						'uses'  => 'ExpertController@edit_category'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
				//Edit or Add new faq category values
				$router->post('add_edit_expert_category', [
						'as'   => 'admin.expert_traffic.upsert_category',
						'uses' => 'ExpertController@add_edit_category'
						]
				);
				 
				// delete a FAQ's Category
				$router->get('delete_expert_category/{id}', [
						'as'    => 'admin.expert_traffic.category.delete',
						'uses'  => 'ExpertController@delete_category'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
				// FAQ's Category list question and answer
				$router->get('expert_category_list_answers/{id}', [
						'as'    => 'admin.expert_traffic.category.answers',
						'uses'  => 'ExpertController@category_index'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
				 
				// open add Question form
				$router->get('add/{id}', [
						'as'   => 'admin.expert_traffic.add',
						'uses' => 'ExpertController@add'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
				// open edit Question form
				$router->get('edit/{category_id}/{id}', [
						'as'    => 'admin.expert_traffic.edit',
						'uses'  => 'ExpertController@edit'
						]
				)->where( [
						'id'          => '[0-9]+',
						'category_id' => '[0-9]+'
						]);
	
				//Edit or Add new faq category values
				$router->post('add_edit', [
						'as'   => 'admin.expert_traffic.add_edit',
						'uses' => 'ExpertController@add_edit'
						]
				);
	
				// delete a FAQ's Category
				$router->get('delete/{id}', [
						'as'    => 'admin.expert_traffic.delete',
						'uses'  => 'ExpertController@delete'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
				$router->get('delete_pdf/{id}', [
						'as'    => 'admin.expert_traffic.delete.pdf',
						'uses'  => 'ExpertController@delete_pdf'
						]
				)->where( ['id' => '[0-9]+' ] );
				 
			}
	);
    
    $router->group(
            [
        'prefix' => 'package',
        'permission' => 'access_campaigns',
        'middleware' => [
            'auth',
            'acl'
        ]
            ], function () use ( $router ) {

        // list campaigns
        $router->get('/', [
            'as' => 'admin.package',
            'uses' => 'PackageController@index'
        ]);
        // open add form
        $router->get('add', [
            'as' => 'admin.add_package',
            'permission' => 'manage_campaign',
            'uses' => 'PackageController@add'
                ]
        );
        //open edit form
        $router->get(
                'edit/{id}', [
            'as' => 'admin.edit_package',
            'permission' => 'manage_campaign',
            'uses' => 'PackageController@edit'
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        // delete the campaign
        $router->get('delete/{id}', [
            'as' => 'admin.delete_package',
            'permission' => 'manage_campaign',
            'uses' => 'PackageController@delete'
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        // update and insert
        $router->post('upsert', [
            'as' => 'admin.upsert_package',
            'permission' => 'manage_campaign',
            'uses' => 'PackageController@upsert'
                ]
        );
        $router->get('activatepackage/{id}/{status}', [
            'as' => 'admin.activatepackage',
            'permission' => 'manage_campaign',
            'uses' => 'PackageController@activatepackage'
                ]
        );
    });


    $router->group(
            [
        'prefix' => 'domains',
        'permission' => 'access_campaigns',
        'middleware' => [
            'auth',
            'acl'
        ]
            ], function () use ( $router ) {

        // list campaigns
        $router->get('/', [
            'as' => 'admin.domains',
            'uses' => 'DomainsController@index'
        ]);
        // open add form
        $router->get('add', [
            'as' => 'admin.add_domain',
            'permission' => 'manage_campaign',
            'uses' => 'DomainsController@add'
                ]
        );
        //open edit form
        $router->get(
                'edit/{id}', [
            'as' => 'admin.edit_domain',
            'permission' => 'manage_campaign',
            'uses' => 'DomainsController@edit'
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        // delete the campaign
        $router->get('delete/{id}', [
            'as' => 'admin.delete_domain',
            'permission' => 'manage_campaign',
            'uses' => 'DomainsController@delete'
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        // update and insert
        $router->post('upsert', [
            'as' => 'admin.upsert_domain',
            'permission' => 'manage_campaign',
            'uses' => 'DomainsController@upsert'
                ]
        );
        // change the status from active to disabled
        $router->get('chstatus/{id}/{status}', [
            'as' => 'admin.change_status_domain',
            'permission' => 'manage_campaign',
            'uses' => 'DomainsController@change_satus'
                ]
        )->where(
                [
                    'id' => '[0-9]+',
                    'status' => '[0-9]'
                ]
        );
        // list assigned users to a campaign
        $router->get('assinged/{id}', [
            'as' => 'admin.domains_assinged',
            'permission' => 'assinging_campaign',
            'uses' => 'DomainsController@domains_assinged'
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
    }
    );


    /**
     * User route start
     */
    $router->group([ 'prefix' => 'users'], function () use ( $router ) {
        // add form for user
        $router->get('add', [
            'uses' => 'UserController@add',
            'as' => 'add_user',
            'permission' => 'add_user'
                ]
        );
        // save and update
        $router->post('upsert', [
            'uses' => 'UserController@upsert',
            'as' => 'upsert_user',
            'permission' => 'manage_users'
                ]
        );
        // edit form for user
        $router->get('edit/{id}', [
            'uses' => 'UserController@edit',
            'as' => 'edit_user',
            'permission' => 'manage_users'
                ]
        );
        
        // connect as user
        $router->get('connect_as_user/{id}', [
        		'uses' => 'UserController@connect_as_user',
        		'as' => 'connect_as_user',
        		'permission' => 'manage_users'
        		]
        );
        
        //delete user
        $router->get('delete/{id}', [
            'uses' => 'UserController@delete',
            'as' => 'delete_user',
            'permission' => 'manage_users'
                ]
        );
        // list users by "role"
        $router->get('/{role?}', [
            'uses' => 'UserController@index',
            'as' => 'admin.users',
            'permission' => 'view_user'
                ]
        )->where('role', '[a-zA-Z]+');
    }
    );
    /**
     * User route stop
     */
    /**
     * Template routes
     */
    $router->group([ 'prefix' => 'templates', 'permission' => 'edit_templates'], function () use ( $router ) {
        $router->get('/', [
            'uses' => 'TemplateController@index',
            'as' => 'admin.templateslist'
        ]);

        // add form for user
        $router->get('add', [
            'uses' => 'TemplateController@create',
            'as' => 'add_template',
                ]
        );
        // save and update
        $router->post('upsert', [
            'uses' => 'TemplateController@upsert',
            'as' => 'upsert_template',
                ]
        );
        // edit form for user
        $router->get('edit/{id}', [
            'uses' => 'TemplateController@edit',
            'as' => 'edit_template',
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        //delete user
        $router->get('delete/{id}', [
            'uses' => 'TemplateController@destroy',
            'as' => 'delete_template',
                ]
        )->where(
                [
                    'id' => '[0-9]+'
                ]
        );
        // activate or deactivate the tempalte
        $router->get('change_stat/{tid}/{sid}', [
            'uses' => 'TemplateController@chstats',
            'as' => 'act_deact_template',
                ]
        )->where(
                [
                    'tid' => '[0-9]+',
                    'sid' => '[0-9]+'
                ]
        );

        /**
         * templates groups
         */
        $router->group([ 'prefix' => 'groups', 'permission' => 'edit_templates_groups'], function () use ( $router ) {
            $router->get('/', [
                'uses' => 'TemplateGroupsController@index',
                'as' => 'admin.templates.groups.list'
            ]);

            // add form for user
            $router->get('add', [
                'uses' => 'TemplateGroupsController@create',
                'as' => 'admin.templates.groups.add',
                    ]
            );
            // save and update
            $router->post('upsert', [
                'uses' => 'TemplateGroupsController@upsert',
                'as' => 'admin.templates.groups.upsert',
                    ]
            );
            // edit form for user
            $router->get('edit/{id}', [
                'uses' => 'TemplateGroupsController@edit',
                'as' => 'admin.templates.groups.edit',
                    ]
            )->where(
                    [
                        'id' => '[0-9]+'
                    ]
            );
            //delete user
            $router->get('delete/{id}', [
                'uses' => 'TemplateGroupsController@destroy',
                'as' => 'admin.templates.groups.delete',
                    ]
            )->where(
                    [
                        'id' => '[0-9]+'
                    ]
            );
        });
    });
});
/**
 * User Admin routes end
 */
/**
 * profile
 *
 *
 */
Route::any('profile', array('as' => 'profile', 'uses' => function () {

        $user = MobileOptin\Models\User::with('owner', 'profile', 'allowed_groups')->where('id', '=', \Auth::user()->id)->first();
        if ($user) {
            if (Request::isMethod('post')) {

                $validator = \Validator::make(\Input::only('password', 'password_confirmation'), [

                            'password' => 'required|confirmed',
                            'password_confirmation' => 'required',
                ]);
                if ($validator->fails()) {
                    // The given data did not pass validation
                    return redirect()->back()->withInput()->withErrors($validator);
                } else {
                    $user->password = Hash::make(\Input::get('password'));
                    $user->save();
                    return redirect()->back()->withSuccess("saved");
                }
            } else {
            	
            	$user_owner = 0;
            	if (isset($user->owner) && method_exists($user->owner, 'first')) {
            		$user_owner_r = $user->owner->first();
            		if (isset($user_owner_r->id)) {
            			$user_owner = $user_owner_r->id;
            		}
            	}

                
                \SEOMeta::setTitle('Profile');

                if($pack_id = Auth::user()->hasPackageActive()){
                    
                }
                $data = array();

               $data['packages'] = MobileOptin\Models\Package::where('status', 1)->lists('name', 'id');
               $data['packages'] += [0 => 'No Package'];

              
               $data['hide_campaign_data'] = $user_owner != 0;
               $data['user'] = $user;

                return view('users.profile', $data);
            }
        }
        return redirect()->route('front')->withError('User not found');
    }));

        