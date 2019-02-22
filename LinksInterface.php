<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\jsonapi;

use \yii\web\Linkable;

interface LinksInterface extends Linkable
{
    public function getRelationshipLinks($name);
}