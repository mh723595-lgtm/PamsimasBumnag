<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PakasirTransaction extends Model
{
    use HasFactory;

    protected $table = 'pakasir_transactions';

    protected $fillable = [
        'tagihan_id',
        'pelanggan_id',
        'pembayaran_id',
        'pakasir_transaction_id',
        'merchant_ref',
        'amount',
        'status',
        'payment_url',
        'qris_url',
        'paid_at',
        'expired_at',
        'raw_response',
        'raw_webhook',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'paid_at'      => 'datetime',
        'expired_at'   => 'datetime',
        'raw_response' => 'array',
        'raw_webhook'  => 'array',
    ];

    // ──────────────────────────────────────────────
    // Relations
    // ──────────────────────────────────────────────

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(TagihanAir::class, 'tagihan_id');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }

    public function pembayaran(): BelongsTo
    {
        return $this->belongsTo(Pembayaran::class, 'pembayaran_id');
    }

    // ──────────────────────────────────────────────
    // Helper methods
    // ──────────────────────────────────────────────

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->expired_at !== null && $this->expired_at->isPast() && $this->isPending());
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isActive(): bool
    {
        return $this->isPending() && !$this->isExpired();
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'paid');
    }

    public function scopeForTagihan(Builder $query, int $tagihanId): Builder
    {
        return $query->where('tagihan_id', $tagihanId);
    }
}