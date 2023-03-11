<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_image',
        'cover_image',
        'city',
        'country',
        'about_me'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => Role::class
    ];


    public function profileImageUrl(): string
    {
        return Storage::url($this->profile_image ? $this->profile_image : "users/user-default.png");
    }

    public function coverImageUrl(): string
    {
        return Storage::url($this->cover_image);
    }

    public function hasCoverImage(): bool
    {
        return !is_null($this->cover_image);
    }

    public function url(): string
    {
        return route('author.show', $this->username);
    }

    public function inlineProfile()
    {
        return collect([
            $this->name,
            trim(join("/", [$this->city, $this->country]), "/"),
            "Member since " . $this->created_at->toFormattedDateString(),
            $this->getImagesCount()
        ])->filter()->implode(" . ");
    }

    public function updateSettings($data)
    {
        $this->update($data['user']);
        $this->updateSocialProfile($data['social']);
        $this->updateOptions($data['options']);
    }


    public function updateSocialProfile($social)
    {
        Social::updateOrCreate(
            [
                'user_id' => $this->id,
            ],
            $social
        );
    }

    public function updateOptions($options)
    {
        $this->setting()->update($options);
    }


    public static function makeDirectory(): string
    {
        $directory = 'users';

        Storage::makeDirectory($directory);

        return $directory;
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function social(): HasOne
    {
        return $this->hasOne(Social::class)->withDefault();
    }

    public function setting(): HasOne
    {
        return $this->hasOne(Setting::class)->withDefault();
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $user->setting()->create([
                'email_notification' => [
                    'new_comment' => 1,
                    'new_image' => 1
                ]
            ]);
        });
    }

    public function getImagesCount(): string
    {
        $imagesCount = $this->images()->published()->count();
        return $imagesCount . ' ' . str()->plural('image', $imagesCount);
    }


}
