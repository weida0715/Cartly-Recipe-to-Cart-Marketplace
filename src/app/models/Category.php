<?php
declare(strict_types=1);
namespace App\Models;
use App\Helpers\Model;

class Category extends Model
{
    protected string $table = 'categories';
    protected string $primaryKey = 'category_id';

    public function active(): array
    {
        return $this->query("SELECT * FROM categories WHERE status='active' ORDER BY category_name");
    }
}
