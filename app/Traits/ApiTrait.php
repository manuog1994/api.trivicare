<?php

namespace App\Traits;

use Illuminate\Support\Facades\Request;
use Illuminate\Database\Eloquent\Builder;

trait ApiTrait{

    // Metodo para obtener relaciones por la query, ?included=posts
    public function scopeIncluded(Builder $query)
    {
        // condicional para saber si el parametro included esta presente en la peticion o si $allowIncluded esta definida

        if(empty($this->allowIncluded) || empty(request('included'))) {
            return;
        }


        $relations = explode(',', request()->query('included')); // devuelve un array ['posts','users']
        
        // con el siguiente metodo evitamos que nos devuelva un error si se escribe bien la query, devolviendo la peticion principal

        $allowIncluded = collect($this->allowIncluded); // convertir el array en una coleccion de Laravel

        // iteramos la coleccion
        foreach ($relations as $key => $relationship) {
            // si el parametro included no esta presente en la peticion
            if (!$allowIncluded->contains($relationship)) {
                // eliminamos de la coleccion el parametro, y devolvemos la query sin el parametro
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }
    
     // Metodo para filtrar por la query, ?filter[name]=nombre&filter[id]=id incluso no completando el campo
     public function scopeFilter(Builder $query)
     {
         // condicional para saber si el parametro filter esta presente en la peticion o si $allowFilter esta definida
         if(empty($this->allowFilter) || empty(request('filter'))) {
             return;
         }
         // obtenemos el parametro filter de la peticion
         $filters = request('filter');
         // metemos los filtros en una coleccion para poder iterarlo
         $allowFilter = collect($this->allowFilter);
         // iteramos la coleccion
         foreach ($filters as $filter => $value) {
             // si el parametro filter no esta presente en la peticion query
             if ($allowFilter->contains($filter)) {
                 // buscamos el parametro en la tabla y lo filtramos ya sea por id, nombre o slug como ya definimos en $allowFilter
                 $query->where($filter, 'LIKE', '%' . $value . '%');
             }
         }
     }
 
     // Metodo para ordenar por la query, ?sort=name o ?sort=-name para ordenar de forma descendente
 
     public function scopeSort(Builder $query)
     {
         // condicional para comprobar si esta la variable sort por la url
         if (empty($this->allowSort) || empty(request('sort'))) {
             return;
         }
 
         $sortFields = explode(',', request('sort')); // devuelve un array ['name', '-slug']
         $allowSort = collect($this->allowSort); // convertir el array en una coleccion de Laravel
 
         // iteramos la coleccion
         foreach ($sortFields as $sortField) {
 
             $direction = 'asc'; // definimos la variable para el campo ascendente
 
             // condicional para saber si el campo es descendente
             if (substr($sortField, 0, 1) === '-') {
                 $direction = 'desc'; // definimos la variable para el campo descendente
                 $sortField = substr($sortField, 1); // eliminamos el guion del campo
             }
 
             // añadimos una condicional para saber si la variable $allowSort esta dentro de la coleccion
 
             if($allowSort->contains($sortField)){
                 // si este, esta dentro de la coleccion, entonces ordenamos por el campo
                 $query->orderBy($sortField, $direction); // ordenar sengún el valor de la variable $direction
             }
         }
     }
 
     // Metodo para paginar por la query, si el cliente asi lo quiere ?perPage=10
     public function scopeGetOrPaginate(Builder $query)
     {
         // condicional para saber si el parametro perPage esta presente en la peticion
         if (request('perPage')) {
             // si esta presente, entonces recuperamos en una variable el valor de la peticion
             $perPage = intval(request('perPage'));// convertimos el valor a numero entero
             
             // condicional para que devuelva todos los registros en el caso de que haya un valor menor a 1
             if ($perPage){ // como el 0 es considerado false en caso de que el valor sea 0, entonces devolvera todos los registros
                 return $query->paginate($perPage); // si es true entonces paginamos
             }
         }
         // si no esta presente, entonces devolvemos la query
         return $query->get();
     }

     public function scopeTags(Builder $query)
     {
         if (empty(request('tags'))) {
             return;
         }

         $query->whereHas('tags', function ($q) {
            $q->where('tag_id', request('tags'));
        });
            
     }
 
}