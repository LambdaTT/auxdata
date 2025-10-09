SELECT 
    *,
    DATE_FORMAT(dt_created, '%d/%m/%Y %T') as dtCreated, 
    DATE_FORMAT(dt_updated, '%d/%m/%Y %T') as dtUpdated
  FROM `AUX_EMPLOYMENTSTATUS`