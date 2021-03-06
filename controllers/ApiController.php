<?php
/**
 * @link http://www.herzogkommunikation.de/
 * @copyright Copyright (c) 2017 herzog kommunikation GmbH, Stuttgart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace hrzg\filefly\controllers;

use hrzg\filefly\components\FileManagerApi;
use hrzg\filefly\components\Rest;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 * Class ApiController
 * @package hrzg\filefly\controllers
 * @author Christopher Stebe <c.stebe@herzogkommunikation.de>
 */
class ApiController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'corsFilter' => [
                    'class' => \yii\filters\Cors::className(),
                    'cors'  => [
                        'Origin'                           => ['*'],
                        'Access-Control-Request-Method'    => [
                            'GET',
                            'POST',
                            'PUT',
                            'PATCH',
                            'DELETE',
                            'HEAD',
                            'OPTIONS'
                        ],
                        'Access-Control-Request-Headers'   => ['*'],
                        'Access-Control-Allow-Credentials' => true,
                        'Access-Control-Max-Age'           => 86400,
                    ],
                ],
                'access'     => [
                    'class' => AccessControl::className(),
                    'rules' => [
                        [
                            'allow'         => true,
                            'matchCallback' => function ($rule, $action) {
                                return \Yii::$app->user->can(
                                    $this->module->id . '_' . $this->id . '_' . $action->id,
                                    ['route' => true]
                                );
                            },
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        // Manager API
        $fileManagerApi = new FileManagerApi($this->module->filesystemComponent, $this->module->filesystem, false, $this->module);

        $rest = new Rest();
        $rest->post([$fileManagerApi, 'postHandler'])
            ->get([$fileManagerApi, 'getHandler'])
            ->handle();
    }
}
