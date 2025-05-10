<?php

namespace App\Models;

use CodeIgniter\Model;
use Error;

class DTRModel extends Model
{
    protected $programsModel;
    protected $table = 'time_record';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'emp_id', 'emp_name', 'day', 'month', 'year', 'time'];

    public function add($row)
    {
        return $this->insert($row);
    }

    public function check($condition)
    {
        $this->where($condition);
        $result = $this->countAllResults(false);
        $this->resetQuery(); // Clean up so the next call starts fresh
        return $result > 0;
    }

    public function provide_ids()
    {
        return $this->select('DISTINCT(emp_id), emp_name')->orderBy('emp_name', 'ASC')->findAll();
    }

    public function provide_year()
    {
        return $this->select('year')->distinct()->orderBy('year', 'ASC')->findColumn('year');
    }

    public function provide_month($year)
    {
        return $this->select('month')
            ->distinct()
            ->where('year', $year)
            ->orderBy('month', 'ASC')
            ->findColumn('month');
    }

    public function provide_emp_dtr($condition)
    {
        $result = $this->select('day, time')
            ->where($condition)
            ->orderBy('day', 'ASC')
            ->findAll();

        return $result ?? null;
    }

    public function getEmpName(string $emp): ?array
    {
        return $this->select('emp_name')
            ->where('emp_id', $emp)
            ->first();
    }
}
