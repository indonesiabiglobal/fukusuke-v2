<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MsProduct extends Model
{
    use HasFactory;
    protected $table = "msproduct";
    protected $guarded = ['id'];

    // public $timestamps = false;

    // custom created and updated
    const CREATED_AT = 'created_on';
    const UPDATED_AT = 'updated_on';

    // Livewire mengirim input angka yang dikosongkan sebagai "", bukan null.
    // PostgreSQL menolak "" pada kolom numerik (SQLSTATE 22P02), jadi dinormalisasi saat simpan.
    public const NUMERIC_COLUMNS = [
        'ketebalan', 'diameterlipat', 'productlength', 'unit_weight',
        'inflation_thickness', 'inflation_fold_diameter', 'one_winding_m_number',
        'gazette_dimension_a', 'gazette_dimension_b', 'gazette_dimension_c', 'gazette_dimension_d',
        'extracted_dimension_a', 'extracted_dimension_b', 'extracted_dimension_c',
        'number_of_color', 'back_color_number', 'katanuki_id',
        'from_seal_design', 'lower_sealing_length', 'palet_jumlah_baris', 'palet_isi_baris',
        // case_box_count sengaja null, bukan 0: dipakai sebagai pembagi di query laporan (x / 0 = error di PostgreSQL).
        'case_box_count',
    ];

    // Kolom isi kemasan: default DB = 0 dan laporan LPK menyembunyikan 0, jadi kosong/null disimpan 0.
    public const COUNT_COLUMNS = ['case_gaiso_count', 'case_inner_count'];

    public function normalizeEmptyNumerics(): void
    {
        foreach (self::NUMERIC_COLUMNS as $column) {
            if (($this->attributes[$column] ?? null) === '') {
                $this->attributes[$column] = null;
            }
        }

        foreach (self::COUNT_COLUMNS as $column) {
            // array_key_exists: jangan menyentuh kolom yang tidak ikut di-select.
            if (array_key_exists($column, $this->attributes) && ($this->attributes[$column] === '' || $this->attributes[$column] === null)) {
                $this->attributes[$column] = 0;
            }
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(fn ($model) => $model->normalizeEmptyNumerics());

        static::creating(function ($model) {
            $model->created_by = Auth::user()->username;
            $model->updated_by = Auth::user()->username;
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::user()->username;
        });
    }

    // protected $fillable = [
    //     'title',
    //     'content',
    // ];

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
