<?php
$params = require_once(__DIR__ . '/params.php');
$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'sourceLanguage'=>'es-XX',
    'bootstrap' => ['log'],
    'extensions' => require(__DIR__ . '/../vendor/yiisoft/extensions.php'),
    'modules' => [
        'defaultController' => 'df',
        'cyc' => [
            'class' => 'Module',
        ],
    ],

    'aliases' => [
        '@views' => '@app/views',
    ],
    
    'components' => [
        'request' => [
            'cookieValidationKey' => 'c&c',
             'enableCsrfValidation' => false,
        ],

        
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\xUsuario',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest'],
        ],
        'errorHandler' => [
            'errorAction' => 'df/error',
        ],
        
         
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer', 
           
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'correo.dominio.com',
                'username' => 'apps@dominio.com',
                'password' => '*****',
                'port' => '465',
                'encryption' => 'ssl',
            ],
            ],        
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'crm.php',
                        'app/error' => 'error.php',
                        'app/crm' => 'crm/df.php',
                        'app/crm/help' => 'crm/help.php',
                    ],
                ],
            ],
        ],
       
    ],
    'params' => $params,
];

if( YII_ENV_DEV ){
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;




