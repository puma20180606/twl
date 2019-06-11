<?php
return array(
//-----------
	'router'=>array(
		'routes'=>array(
			'dailylog'=>array(
				'type'=>'Segment',
				'options'=>array(
						'route'=>'/dailylog[/:controller][/][:action][?date=:date]',
						'defaults'=>array(
						'__NAMESPACE__' => 'Dailylog\Controller',
                        'controller' => 'Dailylog\Controller\Index',
                        'action'     => 'mylog',
                    ),
				),
			),
			'label'=>array(
				'type'=>'Segment',
				'options'=>array(
					'route'=>'/dailylog[/][:controller][/][:action]',
					'defaults'=>array(
						'__NAMESPACE__'=>'Dailylog\Controller',
						'controller'=>'Dailylog\Controller\Label',
						'action'	=>'index',
					)
				)
			),
		),
	),

//----------------------------------
	'controllers'=>array(
		'invokables'=>array(
			'Dailylog\Controller\Index'=>'Dailylog\Controller\IndexController',
			'Dailylog\Controller\Label'=>'Dailylog\Controller\LabelController',
			'Dailylog\Controller\Statistic'=>'Dailylog\Controller\StatisticController',
	
		),
	),
//-------------------------
    'view_manager' => array(
       	'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
      	'not_found_template'       => 'error/404',
    	'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/dailylog_layout'           => __DIR__ . '/../view/layout/dailylog_layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
       'template_path_stack' => array(
           'dailylog'=> __DIR__ . '/../view',
        ),
        'strategies'=>array(
        	'ViewJsonStrategy'
        ),
    ),
    //-----------------------
);