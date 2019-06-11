<?php
return array(
   'service_manager' => array(
    	'invokables'=>array(
	    	'Authenticate'=>'Admin\Service\Authenticate',
			'FreshImageConfig'=>'Admin\Service\FreshImageConfig',
		)
    ),
    'router'=>array(
    	'routes'=>array(
    		'admin'=>array(
    			'type'=>'Segment',
    			'options'=>array(
    				'route'=>'/admin[/][:controller][/][:action]',
    				'constraints' => array(
                     	'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                     ),
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'Admin\Controller',
    					'controller'=>'Index',
    					'action'	=>'index'
    				),
    			),	
    		),
    		'adminlogin'=>array(
    			'type'=>'Literal',
    			'options'=>array(
    				'route'=>'/admin/index/index',
    				'defaults'=>array(
	                    '__NAMESPACE__' => 'Admin\Controller',
    					'controller'=>'Index',
    					'action'	=>'index'
    				),
    			)
    		
    		)
    	),
    ),
    'controllers'=>array(
    	'invokables'=>array(
    		'Admin\Controller\Index'=>'Admin\Controller\IndexController',
    	),
    
    ),
     'view_manager' => array(
    	'template_path_stack' => array(
            'Admin'=>__DIR__ . '/../view',
        ),
    ),
);