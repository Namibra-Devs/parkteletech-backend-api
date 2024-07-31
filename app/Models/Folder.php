<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Folder extends Model
// {
//     protected $fillable = [
//         'folder_name',
//         'vendor_name',
//         'offer_date',
//         'offer_link',
//         'status',
//         'file_path',
//     ];

//     protected $casts = [
//         'offer_date' => 'timestamp',
//     ];

//     public $timestamps = true;
// }


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
