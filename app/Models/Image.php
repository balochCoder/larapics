<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'dimension', 'file', 'user_id', 'slug'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function uploadedDate()
    {
        return $this->created_at->diffForHumans();
    }

    public static function makeDirectory(): string
    {
        $subFolder = 'images/' . date('Y/m/d');

        Storage::makeDirectory($subFolder);

        return $subFolder;
    }

    public static function getDimension($image): string
    {
        [$width, $height] = getimagesize(Storage::path($image));

        return $width . "x" . $height;
    }


    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeVisibleFor($query,User $user)
    {
        if ($user->role ===  Role::Admin || $user->role === Role::Editor){
            return;
        }
        $query->where("user_id",$user->id);
    }

    public function fileUrl(): string
    {
        return Storage::url($this->file);
    }

    public function permalink()
    {
        return $this->slug ? route("images.show", $this->slug) : "#";
    }

    public function route($method, $key = 'id')
    {
        return route("images.{$method}", $this->$key);
    }

    public function getSlug()
    {
        $slug = str($this->title)->slug();

        $numSlugFound = static::where('slug', 'regexp', "^" . $slug . "(-[0-9])?")->count();

        if ($numSlugFound > 0) {
            return $slug . "-" . $numSlugFound + 1;
        }

        return $slug;
    }

    protected static function booted()
    {
        static::creating(function ($image) {
            if ($image->title) {
                $image->slug = $image->getSlug();
                $image->is_published = true;
            }
        });

        static::updating(function ($image) {
            if ($image->title && !$image->slug) {
                $image->slug = $image->getSlug();
                $image->is_published = true;
            }
        });

        static::deleted(function ($image) {
            Storage::delete($image->file);
        });
    }
}

