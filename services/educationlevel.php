<?php

namespace Auxdata\Services;

use SplitPHP\Service;

class EducationLevel extends Service
{
  const TABLE = "AUX_EDUCATIONLEVEL";

  public function list($params = [])
  {
    return $this->getDao(self::TABLE)
      ->bindParams($params)
      ->find('educationlevel/read');
  }

  public function get($params = [])
  {
    return $this->getDao(self::TABLE)
      ->bindParams($params)
      ->first('educationlevel/read');
  }

  public function create($data)
  {
    // Removes forbidden fields from $data
    $data = $this->getService('utils/misc')->dataBlackList($data, [
      'id_aux_educationlevel',
      'ds_key',
      'id_iam_user_created',
      'id_iam_user_updated',
      'dt_created',
      'dt_updated'
    ]);

    // Set refs
    $loggedUser = null;
    if ($this->getService('modcontrol/control')->moduleExists('iam'))
      $loggedUser = $this->getService('iam/session')->getLoggedUser();

    // Set default values
    $data['ds_key'] = 'edu-' . uniqid();
    $data['id_iam_user_created'] = empty($loggedUser) ? null : $loggedUser->id_iam_user;

    return $this->getDao(self::TABLE)->insert($data);
  }

  public function upd($params, $data)
  {
    // Removes forbidden fields from $data
    $data = $this->getService('utils/misc')->dataBlackList($data, [
      'id_snd_education',
      'ds_key',
      'id_iam_user_created',
      'id_iam_user_updated',
      'dt_created',
      'dt_updated'
    ]);

    // Set refs
    $loggedUser = null;
    if ($this->getService('modcontrol/control')->moduleExists('iam'))
      $loggedUser = $this->getService('iam/session')->getLoggedUser();

    // Set default values
    $data['id_iam_user_updated'] = empty($loggedUser) ? null : $loggedUser->id_iam_user;
    $data['dt_updated'] = date("Y-m-d H:i:s");

    return $this->getDao(self::TABLE)
      ->bindParams($params)
      ->update($data);
  }

  public function remove($params)
  {
    return $this->getDao(self::TABLE)
      ->bindParams($params)
      ->delete();
  }
}
