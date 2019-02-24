<?php

namespace tuyakhov\jsonapi;


use yii\base\BootstrapInterface;

class Module extends \yii\base\Module implements BootstrapInterface
{

    public $file_path;
    public $public_file_path;

    /**
     * initialize
     */
    public function init()
    {

        \Yii::setAlias('@tuyakhov/jsonapi/commands', __DIR__ . '/commands');

        parent::init();

    }

    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'tuyakhov\jsonapi\commands';
        }
    }


}
