<?php

namespace tuyakhov\jsonapi\models;

use Yii;

/**
 * This is the model class for table "queue_report".
 *
 * @property int $id
 * @property int $user_id
 * @property string $report_name
 * @property string $status
 * @property string $model
 * @property string $filter
 * @property string $report_base_url
 * @property string $report_path
 * @property int $created_at
 * @property int $completed_at
 * @property bool $viewed
 */
class QueueReport extends \yii\db\ActiveRecord
{

    const STATUS_CREATED = 'CREATED';
    const STATUS_STARTED = 'STARTED';
    const STATUS_ENDED = 'ENDED';
    const STATUS_FAILED = 'FAILED';

    public static $default_report_name = 'Выгрузка без названия';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'queue_report';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at', 'completed_at'], 'default', 'value' => null],
            [['user_id', 'created_at', 'completed_at'], 'integer'],
            [['report_name', 'status', 'model', 'created_at'], 'required'],
            [['report_name', 'model', 'filter', 'report_base_url', 'report_path'], 'string'],
            [['viewed'], 'boolean'],
            [['status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'report_name' => 'Report Name',
            'status' => 'Status',
            'model' => 'Model',
            'filter' => 'Filter',
            'report_base_url' => 'Report Base Url',
            'report_path' => 'Report Path',
            'created_at' => 'Created At',
            'completed_at' => 'Completed At',
            'viewed' => 'Viewed',
        ];
    }

    /**
     * Set new report with incoming params
     * @param $modelClass
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public static function newReport($modelClass, $filter = null)
    {
        $model = new $modelClass;

        $report = new QueueReport();
        $report->user_id = (!Yii::$app->user->isGuest) ? null : Yii::$app->user->id;
        $report->report_name = (property_exists($model, 'report_name')) ? $model->report_name : self::$default_report_name;
        $report->status = QueueReport::STATUS_CREATED;
        $report->model = $modelClass;
        $report->filter = ($filter) ? json_encode($filter) : null;
        $report->created_at = time();

        if ($report->save()) {
            return [
                'status' => $report->status,
                'report_id' => $report->id,
                'created_at' => $report->created_at,
            ];
        } else {
            return ['errors' => $report->errors];
        }
    }
}
