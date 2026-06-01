<?php

namespace App\Models;

// Cambiamos Model por Authenticatable para que sirva para iniciar sesión
use App\Services\WhatsAppService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'tb_usuario';
    protected $primaryKey = 'Id_Usuario'; // Corregida la 'K' mayúscula

    // public $timestamps = false;

    protected $fillable = [
        'Usu_Nombre',
        'Usu_Correo',
        'Usu_Pass',
        'Usu_Telefono',
        'Id_Rol'
    ];

    // Oculta el campo de contraseña
    protected $hidden = [
        'Usu_Pass',
        'remember_token',
    ];

    // Le decimos a Laravel que la contraseña no se llama 'password' sino 'Usu_Pass'
    public function getAuthPassword()
    {
        return $this->Usu_Pass;
    }


    // 📧 IMPORTANTE para reset de contraseña
    public function getEmailForPasswordReset()
    {
        return $this->Usu_Correo;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));

        app(WhatsAppService::class)->sendPasswordReset($this, $token);
    }

    public function getAuthIdentifierName()
    {
        return 'Usu_Correo';
    }
    // 🔥 ESTE ES EL QUE TE FALTABA
    public function getAttribute($key)
    {
        if ($key === 'email') {
            return $this->Usu_Correo;
        }

        if ($key === 'password') {
            return $this->Usu_Pass;
        }

        return parent::getAttribute($key);
    }
    /* =======================================================
       TUS RELACIONES (Se quedan exactamente igual que antes)
       ======================================================= */

    public function roles(){
        return $this->belongsTo(Rol::class, 'Id_Rol', 'Id_Rol');
    }

    public function direcciones(){
        return $this->hasMany(Direccion::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function carrito(){
        return $this->hasOne(Carrito::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function pedidos(){
        return $this->hasMany(Pedido::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function comentarios(){
        return $this->hasMany(Comentario::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function listadeseos(){
        return $this->hasMany(ListaDeseo::class, 'Id_Usuario', 'Id_Usuario');
    }

    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'Id_Usuario', 'Id_Usuario');
    }

    public const ROL_ADMIN = 1;

    public const ROL_USUARIO = 2;

    public function esAdministrador(): bool
    {
        return (int) $this->Id_Rol === self::ROL_ADMIN;
    }

    public function getRouteKeyName(): string
    {
        return 'Id_Usuario';
    }

    public function scopeBuscarAdmin($query, string $termino)
    {
        $termino = trim($termino);

        if ($termino === '') {
            return $query;
        }

        $like = '%'.$termino.'%';

        return $query->where(function ($q) use ($termino, $like) {
            $q->where('Usu_Nombre', 'like', $like)
                ->orWhere('Usu_Correo', 'like', $like)
                ->orWhere('Usu_Telefono', 'like', $like)
                ->orWhereHas('roles', fn ($rol) => $rol->where('Rol_Nombre', 'like', $like))
                ->orWhereHas('direcciones', function ($dir) use ($like) {
                    $dir->where('Direccion', 'like', $like)
                        ->orWhereHas('municipio', fn ($mun) => $mun->where('Nom_Municipio', 'like', $like)
                            ->orWhereHas('departamento', fn ($dep) => $dep->where('Nom_Departamento', 'like', $like)));
                });

            if (ctype_digit($termino)) {
                $q->orWhere('Id_Usuario', (int) $termino);
            }
        });
    }

    public function textoDireccionPrincipal(): string
    {
        $direccion = $this->relationLoaded('direcciones')
            ? $this->direcciones->first()
            : $this->direcciones()->with('municipio.departamento')->first();

        if ($direccion === null) {
            return '—';
        }

        if (! $direccion->relationLoaded('municipio')) {
            $direccion->load('municipio.departamento');
        }

        return collect([
            $direccion->Direccion,
            $direccion->municipio?->Nom_Municipio,
            $direccion->municipio?->departamento?->Nom_Departamento,
        ])->filter(fn ($parte) => $parte !== null && $parte !== '')->implode(', ');
    }
}