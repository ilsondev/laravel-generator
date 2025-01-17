@php
    echo "<?php".PHP_EOL;
@endphp

namespace {{ $config->namespaces->dataTables }};

use {{ $config->namespaces->model }}\{{ $config->modelNames->name }};
@if($config->options->localized)
use Yajra\DataTables\Html\Column;
@endif
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;

class {{ $config->modelNames->name }}DataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);

        return $dataTable->addColumn('action', '{{ $config->modelNames->snakePlural }}.datatables_actions');
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\{{ $config->modelNames->name }} $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query({{ $config->modelNames->name }} $model)
    {
        // return $model->newQuery();
        return $this->applyScopes($model::query()->where('user_id', auth()->id()));
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['width' => '120px', 'printable' => false])
            ->responsive(true)
            ->parameters([
                'dom'       => 'Bfrtip',
                'stateSave' => true,
                'order'     => [[0, 'desc']],
                'language'  => [
                    'paginate' => [
                    'next'     => __('app.datatable.next') . ' <i class="fa fa-arrow-right"></i>',
                    'previous' => '<i class="fa fa-arrow-left"></i> ' . __('app.datatable.previous'),
                    ],
                    'info'     => __('app.datatable.showing') . ' _START_ - _END_ ' . __('app.datatable.of') . ' _TOTAL_ ' . __('app.datatable.entries'),
                    'search'   => __('app.datatable.search') . ':',
                ],
                'buttons'   => [
                    ['extend' => 'create', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-plus"></i> ' . __('app.datatable.add-new')],
                    ['extend' => 'export', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-download"></i> ' . __('app.datatable.export')],
                    ['extend' => 'print', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-print"></i> ' . __('app.datatable.print')],
                    ['extend' => 'reset', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-undo"></i> ' . __('app.datatable.reset')],
                    ['extend' => 'reload', 'className' => 'btn btn-default btn-sm no-corner', 'text' => '<i class="fa fa-sync"></i> ' . __('app.datatable.reload')],
                ],
@if($config->options->localized)
                'language' => [
                    'url' => url('//cdn.datatables.net/plug-ins/1.10.12/i18n/English.json'),
                ],
@endif
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            {!! $columns !!}
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return '{{ $config->modelNames->snakePlural }}_datatable_' . time();
    }
}
