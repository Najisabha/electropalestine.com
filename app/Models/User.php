<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'whatsapp_prefix',
        'birth_year',
        'birth_month',
        'birth_day',
        'role',
        'id_image',
        'id_verified_status',
        'city',
        'district',
        'governorate',
        'address',
        'zip_code',
        'country_code',
        'secondary_address',
        'points',
        'balance',
        'balance_ils',
        'balance_usd',
        'balance_jod',
        'default_payment_wallet',
        'preferred_currency',
        'password',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'last_login_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(UserActivity::class)->orderByDesc('created_at');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(UserFavorite::class);
    }

    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'user_favorites')->withTimestamps();
    }

    public function userRewards(): HasMany
    {
        return $this->hasMany(UserReward::class);
    }

    public function coupons(): HasMany
    {
        return $this->userRewards()->whereHas('reward', function ($q) {
            $q->where('type', 'coupon');
        });
    }

    /**
     * إحصائيات العملاء (عدد الطلبات، إجمالي ما دفع، آخر طلب).
     */
    public static function customerStatsQuery()
    {
        return static::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                DB::raw('COUNT(orders.id) as orders_count'),
                DB::raw('COALESCE(SUM(orders.total), 0) as total_spent'),
                DB::raw('MAX(orders.created_at) as last_order_at'),
            ])
            ->leftJoin('orders', 'orders.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.name', 'users.email', 'users.phone');
    }

    /**
     * العملاء الأكثر إنفاقاً.
     */
    public static function topCustomers(int $limit = 20)
    {
        return static::customerStatsQuery()
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get();
    }

    /**
     * العملاء غير النشطين (لا طلبات خلال عدد أيام معين).
     */
    public static function inactiveCustomers(int $days = 90)
    {
        $cutoff = Carbon::now()->subDays($days);

        return static::customerStatsQuery()
            ->havingRaw('MAX(orders.created_at) IS NULL OR MAX(orders.created_at) < ?', [$cutoff])
            ->get();
    }

    /**
     * التحقق من اكتمال التسجيل
     * يعتبر التسجيل مكتملاً إذا كان لديه: id_image, address, city, district, governorate
     */
    public function isRegistrationComplete(): bool
    {
        return !empty($this->id_image) 
            && !empty($this->address) 
            && !empty($this->city) 
            && !empty($this->district) 
            && !empty($this->governorate);
    }
}
