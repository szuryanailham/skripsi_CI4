<?php

// app/Models/BlacklistTokenModel.php
namespace App\Models;

use CodeIgniter\Model;

class BlacklistTokenModel extends Model
{
    protected $table = 'blacklist_tokens';
    protected $allowedFields = ['token', 'expired_at'];
}
