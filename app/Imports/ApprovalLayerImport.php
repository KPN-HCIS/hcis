<?php

namespace App\Imports;

use App\Models\ApprovalLayer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ApprovalLayerImport implements ToModel, WithHeadingRow
{
    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        return new ApprovalLayer([
            'employee_id' => $row['employee_id'],
            'approver_id' => $row['approver_id'],
            'layer' => $row['layer'],
            'updated_by' => $this->userId,
        ]);
    }
}
