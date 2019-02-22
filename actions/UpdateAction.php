<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\jsonapi\actions;

use yii\base\Model;
use yii\db\ActiveRecord;
use Yii;
use yii\web\ServerErrorHttpException;

class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * Updates an existing resource.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $request = Yii::$app->getRequest();
        $model->scenario = $this->scenario;
        $model->load($request->getBodyParams());
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        $this->linkRelationships($model, $request->getBodyParam('relationships', []));

        return $model;
    }
}