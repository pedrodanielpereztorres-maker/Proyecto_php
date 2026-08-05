<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\JornadaParametro;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BloqueHorarioService
{
    public function generarBloques(JornadaParametro $parametro, bool $flotarRecesos = false): Collection
    {
        $inicio = Carbon::parse($parametro->hora_inicio);
        $fin = Carbon::parse($parametro->hora_fin);
        $duracionBloque = max(1, $parametro->duracion_bloque_minutos);
        $duracionReceso = max(0, $parametro->duracion_receso_minutos);
        $bloques = collect();

        if ($inicio->gte($fin) || $duracionBloque >= $inicio->diffInMinutes($fin)) {
            return $bloques;
        }

        if (! $flotarRecesos) {
            $actual = $inicio->clone();
            while ($actual->clone()->addMinutes($duracionBloque)->lte($fin)) {
                $finBloque = $actual->clone()->addMinutes($duracionBloque);
                $bloques->push([
                    'inicio' => $actual->format('H:i'),
                    'fin' => $finBloque->format('H:i'),
                    'duracion_minutos' => $duracionBloque,
                    'receso_minutos' => $duracionReceso,
                ]);
                $actual = $finBloque->addMinutes($duracionReceso);
            }

            return $bloques;
        }

        $totalMinutos = $inicio->diffInMinutes($fin);
        $maxBloques = intdiv($totalMinutos + $duracionReceso, $duracionBloque + $duracionReceso);

        if ($maxBloques < 1) {
            return $bloques;
        }

        if ($maxBloques === 1) {
            $bloques->push([
                'inicio' => $inicio->format('H:i'),
                'fin' => $inicio->clone()->addMinutes($duracionBloque)->format('H:i'),
                'duracion_minutos' => $duracionBloque,
                'receso_minutos' => 0,
            ]);

            return $bloques;
        }

        $totalBloques = $maxBloques * $duracionBloque;
        $restoReceso = $totalMinutos - $totalBloques;
        $recesos = intdiv($restoReceso, $maxBloques - 1);
        $resto = $restoReceso % ($maxBloques - 1);
        $actual = $inicio->clone();

        for ($i = 0; $i < $maxBloques; ++$i) {
            $finBloque = $actual->clone()->addMinutes($duracionBloque);
            $bloques->push([
                'inicio' => $actual->format('H:i'),
                'fin' => $finBloque->format('H:i'),
                'duracion_minutos' => $duracionBloque,
                'receso_minutos' => $i < $maxBloques - 1 ? $recesos + ($resto > 0 ? 1 : 0) : 0,
            ]);

            $actual = $finBloque->addMinutes($recesos + ($resto > 0 ? 1 : 0));
            if ($resto > 0) {
                --$resto;
            }
        }

        return $bloques;
    }

    public function validarCapacidad(int $capacidadEspacio, int $cantidadAlumnos, bool $omitirCapacidad = false): void
    {
        if ($omitirCapacidad) {
            return;
        }

        if ($cantidadAlumnos > $capacidadEspacio) {
            throw new \InvalidArgumentException('La capacidad del espacio es insuficiente para la sección.');
        }
    }
}
