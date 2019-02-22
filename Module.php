<?php

namespace tuyakhov\jsonapi;


class Module extends \yii\base\Module
{

    public $file_path;
    public $public_file_path;
    public $controllerNamespace = 'tuyakhov\jsonapi\controllers';

    /**
     * initialize
     */
    public function init()
    {

        parent::init();

    }

}
