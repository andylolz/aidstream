<?php namespace App\Services\CsvImporter\Entities\Activity\Components;

use App\Services\CsvImporter\Entities\Row;

class ActivityRow extends Row
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
