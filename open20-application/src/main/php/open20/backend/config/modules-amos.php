<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\basic\template
 * @category   CategoryName
 */

$modules = [
    'amministra-utenti' => [
        'class' => 'mdm\admin\Module',
        'layout' => "@vendor/lispa/amos-core/views/layouts/admin",
        //'left-menu', // it can be '@path/to/your/layout'.
        'controllerMap' => [
            'assignment' => [
                'class' => 'mdm\admin\controllers\AssignmentController',
                'userClassName' => 'common\models\User',
                'idField' => 'id'
            ],
            /*
              'other' => [
              'class' => 'path\to\OtherController', // add another controller
              ],
             */
        ],
        'menus' => [
            'assignment' => [
                'label' => 'Gestisci Assegnazioni' // TODO translate
            ],
        ]
    ],
    'chat' => [
        'class' => 'lispa\amos\chat\AmosChat',
    ],
    'comments' => [
       'class' => 'lispa\amos\comments\AmosComments',
       'htmlMailContent' => [
            'lispa\amos\news\models\News' => '@backend/mail/comment/content_news',
            'lispa\amos\discussioni\models\DiscussioniTopic' => '@backend/mail/comment/content_discussioni',
            'lispa\amos\documenti\models\Documenti' => '@backend/mail/comment/content_documenti'
        ],
       'modelsEnabled' => [
	   'lispa\amos\discussioni\models\DiscussioniTopic',
           'lispa\amos\documenti\models\Documenti',
           'lispa\amos\events\models\Event',
           'lispa\amos\news\models\News',
       ],
	],
    'comuni' => [
        'class' => 'lispa\amos\comuni\AmosComuni',
    ],
    'cwh' => [
        'class' => 'lispa\amos\cwh\AmosCwh',
        'cached' => false,
        'rbacEnabled' => false,
    ],
    'dashboard' => [
        'class' => 'lispa\amos\dashboard\AmosDashboard',
        'useWidgetGraphicDashboardVisible' => true
    ],
    'discussioni' => [
        'class' => 'lispa\amos\discussioni\AmosDiscussioni',
        'hideWidgetGraphicsActions' => true,
    ],
    'documenti' => [
        'class' => 'lispa\amos\documenti\AmosDocumenti',
        'hideWidgetGraphicsActions' => true,
		'showCountDocumentRecursive' => false,
        'enableFolders' => true,
        'enableCategories' => false,
        'enableDocumentVersioning' => true,
        'hidePubblicationDate' => true,
        'hideWizard' => true,
        'enableGroupNotification' => true,
        'layoutPublishedByWidget' => [
            'layout' => '{publisher}',
            'layoutAdmin' => '{publisher}'
        ],
        'params' => [
            'img-default' => '/img/defaultProfilo.png',
            'site_publish_enabled' => false,
            'site_featured_enabled' => false,
            //active the search
            'searchParams' => [
                'documenti' => [
                    'enable' => true,
                ]
            ],
            //do not activate the order
            'orderParams' => [
                'documenti' => [
                    'enable' => false,
                ]
            ],
        ]
    ],
    'events' => [
        'class' => 'lispa\amos\events\AmosEvents',
        'enableGoogleMap' => false,
        'enableInvitationManagement' => false,
        'eventLengthRequired' => true,
        'eventMURequired' => true,
        'hidePubblicationDate' => true,
    ],
    'favorites' => [
        'class' => 'lispa\amos\favorites\AmosFavorites',
        'modelsEnabled' => [
 	     'lispa\amos\discussioni\models\DiscussioniTopic',
	     'lispa\amos\documenti\models\Documenti',
            'lispa\amos\news\models\News',
	     'lispa\amos\events\models\Event'
        ]
    ],
    /*
     'faq' => [
        'class' => 'lispa\amos\faq\AmosFaq',
    ],
    'inforeq' => [
        'class' => 'lispa\amos\inforeq\AmosInforeq',
    ],*/
    'groups' => [
        'class' => 'lispa\amos\groups\Module',
        'hiddenFields' =>  [
            'codice_fiscale'
        ]
    ],
    'myactivities' => [
         'class' => 'lispa\amos\myactivities\AmosMyActivities',
    ],
    'news' => [
        'class' => 'lispa\amos\news\AmosNews',
        'hidePubblicationDate' => true,
        'hideWidgetGraphicsActions' => true,
    ],
    'privileges' => [
	    'class' => 'lispa\amos\privileges\AmosPrivileges',
    ],
    'statistics' => [
        'class' => 'amos\statistics\Module',
    ],
	'socialauth' => [
		'class' => 'lispa\amos\socialauth\Module',
		'enableLogin' => false,
		'enableLink' => false,
		'enableRegister' => false,
		'providers' => [
		    //
		]
	],
    'upload' => [
        'class' => 'lispa\amos\upload\AmosUpload',
    ],
    'uploader' => [
        'class' => 'lispa\amos\uploader\Module',
	    'uploaderServer' => 'http://pcd20.open2.0.appdemoweb.org:8080/upload',
    ],
	 'import' => [
        'class' => 'pcd20\import\Module',
    ],
    'utility' => [
        'class' => 'lispa\amos\utility\Module'
    ],
    'report' => [
        'class' => 'lispa\amos\report\AmosReport',
        'htmlMailContent' => '@backend/mail/report/report_notification',
        'modelsEnabled' => [
 	    'lispa\amos\discussioni\models\DiscussioniTopic',
	    'lispa\amos\documenti\models\Documenti',
	    'lispa\amos\events\models\Event',
            'lispa\amos\news\models\News',
        ]
    ],
    'workflow' => [
       'class' => 'lispa\amos\workflow\AmosWorkflow',
   ],
    'layout' => [
        'class' => 'lispa\amos\layout\Module'
    ],

];


return $modules;
