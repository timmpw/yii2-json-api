<?php

namespace tuyakhov\jsonapi\commands;


use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use tuyakhov\jsonapi\models\QueueReport;
use Yii;
use yii\BaseYii;
use yii\console\Controller;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\BaseFileHelper;
use yii\helpers\Json;

/**
 * Class GenerateController
 * @package backend\modules\report\commands
 * @property $model backend\modules\report\models\QueueReport
 */
class GenerateController extends Controller
{

    /**
     * Main worker
     * @return int
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function actionIndex()
    {
        $notEnded = QueueReport::find()->where(['status' => QueueReport::STATUS_STARTED])->count();

        if ($notEnded > 0) {

            return 1;

        }


        $model = QueueReport::find()->where(['status' => QueueReport::STATUS_CREATED])->orderBy(['id' => SORT_ASC])->one();

        if ($model) {

            $model->status = QueueReport::STATUS_STARTED;
            $model->save();

            $file_name = $this->getFilename();
            $file_path = BaseYii::getAlias(Yii::$app->controller->module->params['file_path']). date("Y-m-d");
            $full_path = $file_path.'/'.$file_name;

            BaseFileHelper::createDirectory($file_path);

            if ($this->setFile($model, $full_path)) {

                $model->status = QueueReport::STATUS_ENDED;
                $model->report_base_url = BaseYii::getAlias(Yii::$app->controller->module->params['public_file_path']);
                $model->report_path = $file_name;
                $model->completed_at = time();
                $model->save();

                return 1;

            }

            $model->status = QueueReport::STATUS_FAILED;
            $model->completed_at = new Expression('NOW()');
            $model->save();

            return 0;

        }

        return 1;

    }

    /**
     * @param $reportItem
     * @param $filename
     * @return bool
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    private function setFile($reportItem, $filename)
    {

        $modelClass = new $reportItem->model;
        $query = $modelClass::find();

        if (!empty($reportItem->filter)) {
            $query->andWhere(json_decode($reportItem->filter, true));
        }

        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => false,
        ]);

        $title = 'report';
        $tableName = $modelClass->tableName();

        $fields = $this->getFieldsKeys($modelClass->fields());

        if (method_exists($modelClass, 'exportFields')) {
            $fields = $this->getFieldsKeys($modelClass->exportFields());
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title ? $title : $tableName);

        $letter = 65;

        foreach ($fields as $one) {

            $sheet->getColumnDimension(chr($letter))->setAutoSize(true);
            $letter++;

        }

        $letter = 65;

        foreach ($fields as $one) {

            $sheet->setCellValue(chr($letter) . '1', $modelClass->getAttributeLabel($one));

            $sheet->getStyle(chr($letter) . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $letter++;

        }

        $row = 2;
        $letter = 65;

        $data = $dataProvider->getModels();

        foreach ($dataProvider->getModels() as $model) {

            $fields = $modelClass->fields();

            if (method_exists($modelClass, 'exportFields')) {
                $fields = $modelClass->exportFields();
            }

            foreach ($fields as $one) {

                if (is_string($one)) {
                    $sheet->setCellValueExplicit(chr($letter) . $row, preg_replace('/[\xF0-\xF7].../s', ' ', $model[$one]),DataType::TYPE_STRING);
                    $sheet->getStyle(chr($letter) . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                } else {
                    if (is_array($one($model))) {
                        $data = implode(', ', $one($model));
                    } else {
                        $data = $one($model);
                    }
                    $sheet->setCellValueExplicit(chr($letter) . $row, preg_replace('/[\xF0-\xF7].../s', ' ', $data),DataType::TYPE_STRING);
                    $sheet->getStyle(chr($letter) . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                }

                $letter++;
            }

            $letter = 65;
            $row++;
        }

        try {

            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            return true;

        } catch (\Exception $e) {

            return false;

        }

    }

    /**
     * @param $fieldsSended
     * @return array
     */
    private function getFieldsKeys($fieldsSended)
    {

        $fields = [];
        $i = 0;

        foreach ($fieldsSended as $key => $value) {

            if (is_int($key)) {

                $fields[$i] = $value;

            } else {

                $fields[$i] = $key;

            }

            $i++;
        }

        return $fields;

    }


    /**
     * get secure filename
     * @return string
     */
    private function getFilename()
    {

        return md5(time()) . '.xlsx';

    }

}