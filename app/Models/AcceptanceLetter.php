<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceptanceLetter extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposal_id',
        'letter_number',
        'content',
        'file_path',
        'created_by',
        'sent_at',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Proposal
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Creator (Operator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate letter number
     */
    public static function generateLetterNumber(Proposal $proposal): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $count = self::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('SM/%s/%03d/TLL/%s', $month, $count, $year);
    }

    /**
     * Generate letter content
     */
    public static function generateContent(Proposal $proposal): string
    {
        $membersText = $proposal->members->map(function ($member, $index) {
            return ($index + 1) . '. ' . $member->name . ' (' . $member->nim . ')';
        })->implode("\n");

        return <<<LETTER
SURAT PENERIMAAN MAGANG

Nomor: {letter_number}

Kepada Yth.
{$proposal->user->name}
{$proposal->user->university}

Dengan hormat,

Sehubungan dengan pengajuan proposal magang yang Saudara/i ajukan, dengan ini kami sampaikan bahwa proposal dengan judul "{$proposal->title}" telah DITERIMA untuk melaksanakan program magang di perusahaan kami.

Detail Magang:
- Departemen: {$proposal->department}
- Periode: {$proposal->start_date->format('d M Y')} s/d {$proposal->end_date->format('d M Y')}
- Jumlah Peserta: {$proposal->members->count()} orang

Peserta yang diterima:
{$membersText}

Saudara/i diharapkan untuk:
1. Hadir pada hari pertama magang sesuai tanggal yang ditentukan
2. Membawa dokumen asli yang telah diupload
3. Mengikuti orientasi yang akan dilaksanakan di hari pertama

Demikian surat penerimaan ini kami sampaikan. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.

Hormat kami,
PT Tellinter Indonesia

[TTD Digital]
Manager HRD
LETTER;
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    /**
     * Send letter
     */
    public function send(): void
    {
        $this->update(['sent_at' => now()]);
    }
}
