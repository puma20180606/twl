<?php
return array(
	'router'=>array(
		'routes'=>array(
			'user'=>array(
				'type'=>'Segment',
				'options'=>array(
					'route'=>'/user[/][:controller][/][:action]',
					'defaults'=>array(
						'__NAMESPACE__' => 'User\Controller',
						'controller'=>'Index',
    					'action'	=>'index'
					),
				)
			),
			'userlogin'=>array(
    			'type'=>'Literal',
    			'options'=>array(
    				'route'=>'/user/index/index',
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'User\Controller',
    					'controller'=>'Index',
    					'action'	=>'login'
    				),
    			)
    		
    		),
    		'userinfo'=>array(
    			'type'=>'Segment',
    			'options'=>array(
    				'route'=>'/dashboard',
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'User\Controller',
    					'controller'=>'Index',
    					'action'	=>'index'
    				),
    			)
    		
    		),
    		'logout'=>array(
    			'type'=>'Literal',
    			'options'=>array(
    				'route'=>'/user/index/logout',
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'User\Controller',
    					'controller'=>'Index',
    					'action'	=>'logout'
    				),
    			),
    		),
    		'register'=>array(
    			'type'=>'Literal',
    			'options'=>array(
    				'route'=>'/user/index/register',
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'User\Controller',
    					'controller'=>'Index',
    					'action'	=>'register'
    				),
    			),
    		),
		),
	),
	'controllers'=>array(
		'invokables'=>array(
			'User\Controller\Index'=>'User\Controller\IndexController'
	
		)
	),
	'view_manager' => array(
	    'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
			'layout/layout_fresh'	  =>__DIR__ . '/../view/layout/layout_fresh.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
		'template_path_stack' => array(
            'user'=>__DIR__ . '/../view',
        ),
        'strategies'=>array(
        	'ViewJsonStrategy'
        ),
	),
);