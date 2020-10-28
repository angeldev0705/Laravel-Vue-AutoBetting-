<?php

namespace App\Helpers\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Query
{
    private $model;
    private $request;
    private $rowsCount;

    /**
     * Extra where clauses to be applied, e.g. ['account_id', '=', 1]
     *
     * @var array
     */
    protected $whereClauses = [];

    /**
     * Regular or associative array of sortable columns, e.g.:
     *
     * ['id', 'name']
     * ['id' => 'trades.id', 'name' => 'assets.name']
     *
     * Override this variable in a child class if you like to restrict the sortable columns
     *
     * @var array
     */
    protected $sortableColumns = [];

    /**
     * Default sort column
     * @var string
     */
    protected $defaultOrderBy = 'id';

    /**
     * Default order direction (asc / desc)
     * @var string
     */
    protected $defaultOrderDirection = 'desc';

    /**
     * How many items per page is allowed to select
     * @var array
     */
    protected $itemsPerPageOptions = [5, 10, 25, 50];

    /**
     * Default number of items per page
     *
     * @var int
     */
    protected $defaultItemsPerPage = 10;

    /**
     * Columns that can be filtered
     *
     * @var array
     */
    protected $searchableColumns = [];

    /**
     * Query constructor.
     *
     * @param Request $request
     * @param Model $model
     */
    public function __construct(Model $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;

        return $this;
    }


    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Get total rows count (required for pagination on the frontend side)
     *
     * @return int
     */
    public function getRowsCount(): int
    {
        if (is_null($this->rowsCount)) {
            $this->rowsCount = $this->calculateRowsCount();
        }

        return $this->rowsCount;
    }

    /**
     * Calculate rows count
     *
     * @return int
     */
    protected function calculateRowsCount(): int
    {
        return $this->getBaseBuilder()->count();
    }

    /**
     * Get number of rows to skip in the result recordset
     *
     * @return int
     */
    public function getRowsToSkip(): int
    {
        return ($this->getPage() - 1) * $this->getItemsPerPage();
    }

    /**
     * Get number of items displayed per page
     *
     * @return array|int|string|null
     */
    public function getItemsPerPage(): int
    {
        $itemsPerPage = $this->request->query('items_per_page');
        return in_array($itemsPerPage, $this->itemsPerPageOptions) ? $itemsPerPage : $this->defaultItemsPerPage;
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function getPage(): int
    {
        $page = (int) $this->request->query('page');
        return max(1, min($this->getPagesCount(), $page));
    }

    /**
     * Get sort column id
     *
     * @param Request $request
     * @return string
     */
    public function getOrderBy(): string
    {
        $orderBy = $this->request->query('sort_by') ?: $this->defaultOrderBy;

        return empty($this->sortableColumns) // if columns are not restricted
            ? $orderBy // return passed name
            : (array_key_exists($orderBy, $this->sortableColumns) // otherwise check that the column is present in sortableColumns array
                ? $this->sortableColumns[$orderBy]
                : (in_array($orderBy, $this->sortableColumns)
                    ? $orderBy
                    : $this->defaultOrderBy));
    }

    /**
     * Get order direction
     *
     * @return string
     */
    public function getOrderDirection(): string
    {
        $orderDirection = $this->request->query('sort_direction');

        return in_array($orderDirection, ['asc', 'desc'])
            ? $orderDirection
            : $this->defaultOrderDirection;
    }

    /**
     * Get search string
     *
     * @return string|null
     */
    public function getSearch(): ?string
    {
        $search = $this->request->query('search');
        return strlen($search) <= 20 ? $search : substr($search, 0, 20);
    }

    /**
     * Get model
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get query builder instance with pagination and order applied
     *
     * @return Builder
     */
    public function getBuilder(): Builder
    {
        return $this->getBaseBuilder()
            ->skip($this->getRowsToSkip())
            ->take($this->getItemsPerPage())
            ->orderByRaw($this->getOrderBy() . ' ' . $this->getOrderDirection());
    }

    /**
     * Get base builder with potential search filter applied
     *
     * @return Builder
     */
    protected function getBaseBuilder(): Builder
    {
        return $this->getModel()
            ::when(!empty($this->whereClauses), function($query) {
                $query->where($this->whereClauses);
            })
            ->when(!empty($this->searchableColumns) && $this->getSearch(), function($query) {
                foreach ($this->searchableColumns as $i => $column) {
                    $clause = $i==0 ? 'whereRaw' : 'orWhereRaw';
                    $query->{$clause}('LOWER(' . $column . ') LIKE ?', ['%'. strtolower($this->getSearch()) . '%']);
                }
            });
    }

    public function where(array $clauses): Query
    {
        $this->whereClauses[] = $clauses;

        return $this;
    }

    /**
     * Get total number of pages
     *
     * @return float
     */
    private function getPagesCount(): int
    {
        return (int) ceil($this->getRowsCount() / $this->getItemsPerPage());
    }
}
