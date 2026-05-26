<?php

namespace App;

enum OrderStatusEnum : string   
{
    
    case Pendiente = 'Pendiente';
    case Procesando = 'Procesando';
    case Completado = 'Completado';
    case Cancelado = 'Cancelado';
}
