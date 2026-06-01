<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BoletaPago extends Model
{
    protected $table = 'tb_boletapago';

    protected $primaryKey = 'Id_Boletapago';

    protected $fillable = [
        'BoletaImagen',
        'Id_Pedido',
    ];

    public function getRouteKeyName(): string
    {
        return 'Id_Boletapago';
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'Id_Pedido', 'Id_Pedido');
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->whereHas('pedido', function ($pedido) use ($like, $termino) {
                $pedido->where('Ped_Numero', 'like', $like)
                    ->orWhereHas('usuario', function ($usuario) use ($like) {
                        $usuario->where('Usu_Nombre', 'like', $like)
                            ->orWhere('Usu_Correo', 'like', $like);
                    });

                if (ctype_digit($termino)) {
                    $pedido->orWhere('Id_Pedido', (int) $termino);
                }
            });

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Boletapago', (int) $termino)
                    ->orWhere('Id_Pedido', (int) $termino);
            }
        });
    }

    public function esPdf(): bool
    {
        return str_ends_with(strtolower((string) $this->BoletaImagen), '.pdf');
    }

    public function esImagen(): bool
    {
        if ($this->BoletaImagen === null || $this->BoletaImagen === '') {
            return false;
        }

        return ! $this->esPdf();
    }

    public function urlArchivo(): ?string
    {
        if ($this->BoletaImagen === null || $this->BoletaImagen === '') {
            return null;
        }

        if (! Storage::disk('public')->exists($this->BoletaImagen)) {
            return null;
        }

        return Storage::disk('public')->url($this->BoletaImagen);
    }

    public function archivoDisponible(): bool
    {
        return $this->BoletaImagen !== null
            && $this->BoletaImagen !== ''
            && Storage::disk('public')->exists($this->BoletaImagen);
    }

    public function extensionArchivo(): string
    {
        return strtolower(pathinfo((string) $this->BoletaImagen, PATHINFO_EXTENSION));
    }

    public function etiquetaFormato(): string
    {
        return match ($this->extensionArchivo()) {
            'pdf' => 'PDF',
            'png' => 'PNG',
            'jpg', 'jpeg' => 'JPG',
            default => strtoupper($this->extensionArchivo() ?: 'Archivo'),
        };
    }

    public function nombreArchivoDescarga(): string
    {
        $this->loadMissing('pedido');

        $numeroPedido = $this->pedido?->Ped_Numero ?? $this->Id_Pedido;
        $extension = $this->extensionArchivo() ?: 'bin';

        return 'comprobante-pedido-'.$numeroPedido.'.'.$extension;
    }
}
