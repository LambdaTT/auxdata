<?php

namespace Auxdata\Migrations;

use SplitPHP\DbManager\Migration;
use SplitPHP\Database\DbVocab;

class CreateTableEducationLevel extends Migration
{
  public function apply()
  {
    if (!$this->isEntityAllowed('educationlevel')) return;

    $this->Table('AUX_EDUCATIONLEVEL')
      ->id('id_aux_educationlevel') // int primary key auto increment
      ->string('ds_key', 17)
      ->datetime('dt_created')->setDefaultValue(DbVocab::SQL_CURTIMESTAMP())
      ->datetime('dt_updated')->nullable()->setDefaultValue(null)
      ->int('id_iam_user_created')->nullable()->setDefaultValue(null)
      ->int('id_iam_user_updated')->nullable()->setDefaultValue(null)
      ->string('ds_title', 100)
      ->text('tx_description')
      ->Index('KEY', DbVocab::IDX_UNIQUE)->onColumn('ds_key');
  }

  private function isEntityAllowed($entityName)
  {
    $allowedEntities = json_decode(file_get_contents(dirname(__DIR__) . '/config.json'));
    if (empty($allowedEntities) || isset($allowedEntities['entities'])) return false;
    $allowedEntities = $allowedEntities['entities'];
    if (!is_array($allowedEntities)) return false;
    if (!in_array($entityName, $allowedEntities)) return false;

    return true;
  }
}
