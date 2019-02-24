<?php

namespace tuyakhov\jsonapi\commands;


use tuyakhov\jsonapi\models\QueueReport;
use Yii;
use yii\BaseYii;
use yii\console\Controller;
use yii\db\Expression;
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

            $file = $this->getFilename();

            $upload_filename = BaseYii::getAlias(Yii::$app->controller->module->params['file_path'] . $file);

            if (!file_exists(BaseYii::getAlias(Yii::$app->controller->module->params['file_path']))) {

                mkdir(BaseYii::getAlias(Yii::$app->controller->module->params['file_path']), 0777, true);

            }

            if ($this->setFile($model, $upload_filename)) {

                $model->status = QueueReport::STATUS_ENDED;
                $model->report_base_url = BaseYii::getAlias(Yii::$app->controller->module->params['public_file_path']);
                $model->report_path = $file;
                $model->completed_at = new Expression('NOW()');
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
        $searchModel = new $reportItem->search;
        $dataProvider = $searchModel->search(unserialize($reportItem->params));
        $dataProvider->pagination = false;

        $title = 'report';
        $tableName = $searchModel->tableName();
        $fields = $this->getFieldsKeys($searchModel->exportFields());

        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle($title ? $title : $tableName);
        $letter = 65;

        foreach ($fields as $one) {

            $objPHPExcel->getActiveSheet()->getColumnDimension(chr($letter))->setAutoSize(true);
            $letter++;

        }

        $letter = 65;

        foreach ($fields as $one) {

            $objPHPExcel->getActiveSheet()->setCellValue(chr($letter) . '1', $searchModel->getAttributeLabel($one));
            $objPHPExcel->getActiveSheet()->getStyle(chr($letter) . '1')->getAlignment()->setHorizontal(
                \PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $letter++;

        }

        $row = 2;
        $letter = 65;

        foreach ($dataProvider->getModels() as $model) {

            foreach ($searchModel->exportFields() as $one) {

                if (is_string($one)) {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($letter) . $row, preg_replace('/[\xF0-\xF7].../s', ' ', $model[$one]));
                    $objPHPExcel->getActiveSheet()->getStyle(chr($letter) . $row)->getAlignment()->setHorizontal(
                        \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                } else {
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit(chr($letter) . $row, preg_replace('/[\xF0-\xF7].../s', ' ', $one($model)));
                    $objPHPExcel->getActiveSheet()->getStyle(chr($letter) . $row)->getAlignment()->setHorizontal(
                        \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

                }

                $letter++;
            }

            $letter = 65;
            $row++;
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        try {

            $objWriter->save($filename);
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

        return md5(time()). '.xls';

    }

}