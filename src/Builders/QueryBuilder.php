<?php


namespace Wovosoft\LaravelPermissions\Builders;

use Illuminate\Support\Facades\Request;

class QueryBuilder
{
    public $model = null;
    public $items = null;

    public function __construct($class)
    {
        $this->model = $class::query();
    }

    public function select($selects = ["*"])
    {
        $this->items = $this->model->select($selects);
        return $this;
    }

    public function with($relations = [])
    {
        $this->items->with($relations);
        return $this;
    }

    public function leftJoin($table, $closure)
    {
        $this->items->leftJoin($table, $closure);
        return $this;
    }

    public function call($method, $params = [])
    {
        $this->items->$method(...$params);
        return $this;
    }

    public function orderBy($column = "id", $order = "desc")
    {
        $this->items->orderBy($column, $order);
        return $this;
    }

    public function groupBy($group)
    {
        $this->items->groupBy($group);
        return $this;
    }

    /**
     * @param array $args [column, like, query] or [column, query]
     * @return $this
     */
    public function where($args = [])
    {
        if (count($args))
            $this->items->where(...$args);
        return $this;
    }

    public function orWhere($args = [])
    {
        if (count($args))
            $this->items->orWhere(...$args);
        return $this;
    }

    public function filter($filter = null, $columns = [], $filter_type = "or")
    {
        if ($filter && is_array($columns) && count($columns)) {
            if (trim($filter_type) === "or") {
                for ($x = 0; $x < count($columns); $x++) {
                    if ($x === 0) {
                        $this->items->where($columns[$x], "LIKE", "%" . trim($filter) . "%");
                    } else {
                        $this->items->orWhere($columns[$x], "LIKE", "%" . trim($filter) . "%");
                    }
                }
            } elseif (trim($filter_type) === 'and') {
                foreach ($columns as $column) {
                    $this->items->where($column, "LIKE", "%" . trim($filter) . "%");
                }
            }
        }
        return $this;
    }

    public function limit($value)
    {
        $this->items->limit($value);
        return $this;
    }

    public function datatable($options = [])
    {
        if (isset($options["filter"]) && $options["filter"] && isset($options["columns"]) && count($options["columns"])) {
            $this->filter($options["filter"], $options["columns"], isset($options["filter_type"]) ? $options["filter_type"] : "or");
        }
        $orderBy = (isset($options["orderBy"]) && $options["orderBy"]) ? $options["orderBy"] : "id";
        $order = (isset($options["order"]) && $options["order"]) ? $options["order"] : "desc";
        return $this
            ->items
            ->orderBy($orderBy, $order)
            ->paginate(isset($options['per_page']) ? $options['per_page'] : env('PER_PAGE') ?? 15);
    }

    public function defaultDatatable(Request $request = null)
    {
        return $this
            ->select(['*'])
            ->datatable([
                "filter" => $request->post("filter") ?? null,
                "filter_type" => "or",
                "per_page" => $request->post("per_page") ?? 10,
                "orderBy" => $request->post("orderBy") ?? "id",
                "order" => $request->post("order") ?? "asc",
                "columns" => []
            ]);
    }

    public function getBuilder()
    {
        return $this->items;
    }

    public function toSql()
    {
        return $this->items->toSql();
    }

    public function get()
    {
        return $this->items->get();
    }
}
