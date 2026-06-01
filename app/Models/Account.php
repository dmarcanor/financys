<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id', 'user_id', 'name', 'balance', 'currency'])]
class Account extends Model
{
    use HasFactory;
}