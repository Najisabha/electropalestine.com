<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'city',
        'district',
        'governorate',
        'address',
        'zip_code',
        'country_code',
        'secondary_address',
        'points',
        'balance',
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
}
