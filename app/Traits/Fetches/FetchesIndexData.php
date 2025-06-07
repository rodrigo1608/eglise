<?php

declare(strict_types=1);

namespace App\Traits\Fetches;

use Illuminate\Http\Request;

trait FetchesIndexData
{
    public function fetchesIndexData(Request $request, $query)
    {
        $perPage = $request->query('perPage', 20);

        $query->orderBy('id', 'DESC');

        return $perPage === 'all' ?
            $query->get()
            :
            $query->paginate($perPage);
    }
}
