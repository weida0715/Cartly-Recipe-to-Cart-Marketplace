<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Ingredient extends Model
{
    protected string $table = 'ingredients';
    protected string $primaryKey = 'ingredient_id';
}
