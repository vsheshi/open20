<?php
namespace lispa\amos\utility\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;
use AD7six\Dsn\Dsn;

class DatabaseController extends Controller
{
    
    /**
     * Disable layout
     * @var bool
     */
    public $layout = false;
    
    /**
     * Disable CSRF
     */
    public function init() {
        parent::init();
        $this->enableCsrfValidation = false;
        $this->setUpLayout();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                        ],
                        'roles' => ['ADMIN']
                    ],
                ],
            ],
        ];
    }



    /*
     * 
     */
    public function actionIndex()
    {
        $src = Yii::getAlias('@vendor/lispa/amos-utility/src');

        //Configured DSN
        $dsn = Yii::$app->db->dsn;

        //Preg match from dsn data
        preg_match('/host=(?P<host>[^;]*)?/i', $dsn, $matchHost);
        preg_match('/dbname=(?P<dbname>[^;]*)?/i', $dsn, $matchDbName);
        preg_match('/port=(?P<port>[^;]*)?/i', $dsn, $matchPort);

        //Configured host withhout port
        $host = $matchHost['host'];

        //Configured database name
        $database = $matchDbName['dbname'];

        //Configured database name
        $port = isset($matchPort['port']) ? $matchPort : '';

        //$dsn is an instance of \AD7six\Dsn\Db\MysqlDsn;
        $dsnParsed = Dsn::parse($dsn)->toArray();

        /*$dsn$dsn->toArray();
        [
            'scheme' => 'mysql',
            'host' => 'host',
            'port' => 3306,
            'database' => 'dbname'
        ]*/

        /**
         *
         * auth[driver]    server
         * auth[server]    mysql
         * auth[username]    admin
         * auth[password]    Test12345!
         * auth[db]    fgu
         * username    demos
         * password    demospwd
         * db    host=localhost;dbname=demos_poi_test4
         * port    host=localhost;dbname=demos_poi_test4
         */

        //Override GET PARAMS
        if (empty($_GET)) {
            $_POST['auth'] = [
                'driver' => 'server',
                'server' => $host . ($port ? ':' . $port : ''),
                'username' => Yii::$app->db->username,
                'password' => Yii::$app->db->password,
                'db' => $database
            ];

        }

        //pr( Yii::$app->db->dsn);
        //pr($schema);

        return $this->render('index');

        //echo $this->renderPartial('style');

        // die;
    }

    /**
     * @param null $layout
     * @return bool
     */
    public function setUpLayout($layout = null){
        if ($layout === false){
            $this->layout = false;
            return true;
        }
        $module = \Yii::$app->getModule('layout');
        if(empty($module)){
            $this->layout =  '@vendor/lispa/amos-core/views/layouts/' . (!empty($layout) ? $layout : $this->layout);
            return true;
        }
        $this->layout = (!empty($layout)) ? $layout : $this->layout;
        return true;
    }
}