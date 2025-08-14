<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
   protected $allowedFields = [
    'order_number',
    'user_id',
    'event_id',
    'total_amount',
    'status',
    'paid',
    'paid_at',
    'proof_image'
];


    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    public function getOrderWithDetails($id)
{
    return $this->select('
                orders.*, 
                users.id as user_id,
                users.name as user_name, 
                users.email as user_email, 
                events.title as event_title, 
                events.slug as event_slug, 
                events.description as event_description,
                events.time as event_time,
                events.location as event_location,
                events.price as event_price,
                events.date as event_date,
                events.start_date as event_start_date,
                events.end_date as event_end_date
            ')
            ->join('users', 'users.id = orders.user_id')
            ->join('events', 'events.id = orders.event_id')
            ->where('orders.id', $id)
            ->first();
}


}
