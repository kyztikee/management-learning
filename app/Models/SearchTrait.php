<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait SearchTrait
{

    public $data;

    public function scopeSearch(Builder $builder)
    {
        $data = $builder;
        $requests = request()->only($this->getSearchable());
        foreach ($requests as $key => $value) {
            $param = explode('-', $key);

            if (count($param) > 1) {
                $data = $this->filterRelation($data, $param, $value);
            } else {
                $data = $data->where($key, $value);
            }
        }

        if (request()->has('q')) {
            $search = request()->query('q');
            $searchable = $this->getSearchable();
            $data = $data->where(function ($query) use ($searchable, $search) {
                foreach ($searchable as $field) {
                    $param = explode('-', $field);
                    if (count($param) > 1) {
                        $query = $this->filterRelation($query, $param, $search, 'like');
                    } else {
                        $query = $query->orWhere($field, 'like', '%' . $search . '%');
                    }
                }
            });
        }

        $this->data = $data;
        return $data;
    }

    public function scopeFilterDate()
    {
        $data = $this->data;
        if (request()->has(['filter_date_start', 'filter_date_end', 'filter_date_field'])) {
            $start = Carbon::parse(request()->query('filter_date_start'))->startOfDay();
            $end =  Carbon::parse(request()->query('filter_date_end'))->endOfDay();
            $field =  request()->query('filter_date_field');

            if (!in_array($field, $this->getSearchInRange())) {
                $field = 'created_at';
            }

            $data = $data->whereBetween($field, [$start, $end]);
        }

        $this->data = $data;
        return $data;
    }

    private function getSearchable()
    {
        $searchable = $this->searchable;
        array_push($searchable, 'created_at', 'updated_at');
        return $searchable;
    }

    private function getSearchInRange()
    {
        $searchInRange = $this->searchInRange;
        return $searchInRange;
    }

    private function filterRelation($data, $param, $value, $condition = "=")
    {
        // Collect $param
        $param = collect($param);

        // Get field
        $relationField = $param->pop();

        /*
            Formatting param name to eloquent relation name
                ex : invoice-customer to invoice.customer
        */
        $relation = '';
        foreach ($param as $formatting) {
            $relation .= $formatting . '.';
        }

        /*
            Cleaning string
            ex : invoice.customer. to invoice.customer
        */
        $relation = rtrim($relation, '.');

        // Setup WhereHas Callback
        $callback = function ($query) use ($relationField, $condition, $value) {
            if ($condition == 'like') {
                $value = '%' . $value . '%';
            }
            return $query->where($relationField, $condition, $value);
        };

        // Filtering
        if ($condition == '=') {
            $data = $data->whereHas($relation, $callback);
        } else {
            $data = $data->orWhereHas($relation, $callback);
        }

        return $data;
    }

    public function scopeGetResult()
    {
        $data = $this->data->get();

        if (request()->has('order_by')) {
            $orderBy = request()->query('order_by');
            $direction = request()->query('direction', 'asc');

            if (in_array($orderBy, $this->getSearchable())) {
                $orderBy = str_replace('-', '.', $orderBy);
                if ($direction == 'desc') {
                    $data = $data->sortByDesc($orderBy);
                } else {
                    $data = $data->sortBy($orderBy);
                }
            }
        }

        $item_per_page = request()->query('limit') ?? 999999;
        $current_page = request()->query('page') ?? 1;
        $data = $data->skip($item_per_page * ($current_page - 1))->take($item_per_page);

        $data = $data->values();

        return $data;
    }
}
