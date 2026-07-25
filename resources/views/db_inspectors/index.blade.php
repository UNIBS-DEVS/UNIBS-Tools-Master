@extends('layouts.app')

@section('title', 'Database Inspector')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <!-- Sidebar: Tables List -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3">
                    <span class="fw-bold text-dark"><i class="fa-solid fa-database text-primary me-2"></i>Database Tables</span>
                    <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1.5" id="table-count">{{ count($tables) }}</span>
                </div>
                <div class="card-body p-2 bg-white">
                    <div class="mb-2 p-1">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" id="table-search" class="form-control bg-light border-start-0" placeholder="Filter tables...">
                        </div>
                    </div>
                    <div class="list-group list-group-flush overflow-y-auto" style="max-height: 68vh; padding-right: 2px;" id="tables-list-container">
                        @foreach($tables as $table)
                            @php
                                $isActive = $selectedTable === $table['name'];
                            @endphp
                            <a href="?table={{ $table['name'] }}" 
                               data-table-name="{{ $table['name'] }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 rounded-2 mb-1 px-3 py-2 {{ $isActive ? 'bg-primary-subtle text-primary border-start border-3 border-primary fw-bold' : 'text-dark hover-bg-light' }}">
                                <span class="text-truncate">
                                    <i class="fa-solid fa-table me-2 {{ $isActive ? 'text-primary' : 'text-muted' }}"></i>
                                    {{ $table['name'] }}
                                </span>
                                <span class="badge {{ $isActive ? 'bg-primary text-white' : 'bg-light text-secondary border' }} rounded-pill" style="font-size: 11px;">
                                    {{ number_format($table['rows']) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Panel: Table Inspector -->
        <div class="col-md-9 mb-4">
            @if($selectedTable)
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div>
                            <h3 class="card-title h4 mb-1 fw-bold text-dark d-flex align-items-center" id="active-table-name">
                                {{ $selectedTable }}
                                <button id="copy-table-name" title="Copy table name" class="btn btn-sm btn-link text-muted p-0 ms-2">
                                    <i class="fa-regular fa-copy"></i>
                                </button>
                            </h3>
                            <p class="text-muted small mb-0">
                                Columns: <strong class="text-dark" id="active-col-count">{{ count($schema) }}</strong> &bull; 
                                Approx Records: <strong class="text-dark" id="active-row-count">{{ $data ? number_format($data->total()) : 0 }}</strong>
                            </p>
                        </div>

                        <!-- Row Search & Actions -->
                        <div class="d-flex align-items-center gap-2">
                            <form method="GET" action="" id="search-form" class="d-flex gap-2">
                                <input type="hidden" name="table" value="{{ $selectedTable }}">
                                <div class="input-group">
                                    <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search rows...">
                                    @if($search)
                                        <a href="?table={{ $selectedTable }}" class="btn btn-outline-secondary btn-sm"><i class="fa fa-times"></i></a>
                                    @endif
                                    <button type="submit" class="btn btn-primary btn-sm">Search</button>
                                </div>
                            </form>
                            
                            <form action="{{ route('db-disconnect') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Disconnect</button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabs Selector (Segmented Control) -->
                    <div class="card-header bg-white px-3 py-2 border-bottom">
                        <ul class="nav nav-pills gap-1 p-1 bg-light rounded-3" id="inspectorTabs" role="tablist" style="width: fit-content;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active py-1.5 px-3 rounded-2 fw-semibold text-xs uppercase tracking-wider" id="tab-data" data-bs-toggle="tab" data-bs-target="#tab-content-data" type="button" role="tab" aria-controls="tab-content-data" aria-selected="true">Browse Data</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1.5 px-3 rounded-2 fw-semibold text-xs uppercase tracking-wider text-secondary" id="tab-structure" data-bs-toggle="tab" data-bs-target="#tab-content-structure" type="button" role="tab" aria-controls="tab-content-structure" aria-selected="false">Table Structure</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1.5 px-3 rounded-2 fw-semibold text-xs uppercase tracking-wider text-secondary" id="tab-relations" data-bs-toggle="tab" data-bs-target="#tab-content-relations" type="button" role="tab" aria-controls="tab-content-relations" aria-selected="false">Relations</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0 bg-white">
                        @if(isset($error) && $error)
                            <div class="alert alert-danger m-3 font-monospace text-xs">
                                <strong><i class="fa fa-triangle-exclamation me-2"></i>Database Query Exception:</strong>
                                <p class="mb-0 mt-1">{{ $error }}</p>
                            </div>
                        @endif

                        <div class="tab-content" id="inspectorTabContent">
                            <!-- TAB CONTENT: BROWSE DATA -->
                            <div class="tab-pane fade show active" id="tab-content-data" role="tabpanel" aria-labelledby="tab-data">
                                <div class="table-responsive" style="max-height: 55vh;">
                                    @if(isset($schema) && count($schema) > 0)
                                        <table class="table table-hover table-bordered table-striped align-middle mb-0 text-nowrap">
                                            <thead class="table-light text-secondary text-xs uppercase border-bottom" style="position: sticky; top: 0; z-index: 10;">
                                                <tr>
                                                    @foreach($schema as $column)
                                                        <th class="py-3 px-3 fw-bold border-bottom-2">
                                                            <div class="d-flex align-items-center justify-content-between gap-2">
                                                                <span>{{ $column['name'] }}</span>
                                                                <span class="text-muted fw-normal lowercase font-monospace" style="font-size: 9px;">{{ $column['type_name'] }}</span>
                                                            </div>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($data && $data->count() > 0)
                                                    @php
                                                        $outgoingFks = [];
                                                        if (isset($relations['outgoing'])) {
                                                            foreach ($relations['outgoing'] as $fk) {
                                                                foreach ($fk['columns'] as $index => $col) {
                                                                    $outgoingFks[$col] = [
                                                                        'table' => $fk['foreign_table'],
                                                                        'column' => $fk['foreign_columns'][$index] ?? 'id',
                                                                    ];
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    @foreach($data as $row)
                                                        <tr>
                                                            @foreach($schema as $column)
                                                                @php
                                                                    $val = $row->{$column['name']};
                                                                    $isForeignKey = isset($outgoingFks[$column['name']]) && !is_null($val);
                                                                    if (is_null($val)) {
                                                                        $displayVal = 'NULL';
                                                                        $class = 'text-muted font-monospace italic small';
                                                                    } else {
                                                                        $rawString = is_scalar($val) ? (string)$val : json_encode($val);
                                                                        if (strlen($rawString) > 80) {
                                                                            $displayVal = mb_substr($rawString, 0, 80) . '...';
                                                                        } else {
                                                                            $displayVal = $rawString;
                                                                        }
                                                                        $class = is_numeric($val) ? 'text-primary font-monospace fw-medium' : '';
                                                                    }
                                                                @endphp
                                                                <td class="px-3 {{ $class }}" title="{{ is_null($val) ? 'NULL' : $rawString }}">
                                                                    @if($isForeignKey)
                                                                        <a href="?table={{ $outgoingFks[$column['name']]['table'] }}&search={{ $val }}" class="text-decoration-none fw-bold">
                                                                            {{ $displayVal }} <i class="fa fa-link small text-muted"></i>
                                                                        </a>
                                                                    @else
                                                                        {{ $displayVal }}
                                                                    @endif
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="{{ count($schema) }}" class="text-center py-5 text-muted">
                                                            <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary"></i>
                                                            <h5 class="fw-semibold">No records found</h5>
                                                            <p class="small mb-0">This table is empty or matches no search results.</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                                
                                <!-- PAGINATION FOOTER -->
                                @if($data && $data->hasPages())
                                    <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
                                        <div>
                                            {{ $data->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- TAB CONTENT: STRUCTURE -->
                            <div class="tab-pane fade" id="tab-content-structure" role="tabpanel" aria-labelledby="tab-structure">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered table-striped align-middle mb-0">
                                        <thead class="table-light text-secondary text-xs uppercase border-bottom">
                                            <tr>
                                                <th class="px-3 py-3">Column Name</th>
                                                <th>Type</th>
                                                <th>Full Definition</th>
                                                <th class="text-center">Nullable</th>
                                                <th>Default Value</th>
                                                <th>Attributes</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($schema as $column)
                                                <tr>
                                                    <td class="px-3 font-monospace fw-bold text-dark">{{ $column['name'] }}</td>
                                                    <td class="font-monospace text-primary small">{{ $column['type_name'] }}</td>
                                                    <td class="font-monospace text-muted small">{{ $column['type'] }}</td>
                                                    <td class="text-center">
                                                        @if(isset($column['nullable']) && $column['nullable'])
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Yes</span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">No</span>
                                                        @endif
                                                    </td>
                                                    <td class="font-monospace text-muted small">{{ is_null($column['default']) ? 'NULL' : $column['default'] }}</td>
                                                    <td>
                                                        @if(isset($column['auto_increment']) && $column['auto_increment'])
                                                            <span class="badge bg-info-subtle text-info border border-info-subtle">Auto-Increment</span>
                                                        @endif
                                                        @if(isset($column['generation']) && $column['generation'])
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Generated</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB CONTENT: RELATIONS -->
                            <div class="tab-pane fade p-4" id="tab-content-relations" role="tabpanel" aria-labelledby="tab-relations">
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <h4 class="h5 fw-bold text-dark mb-3"><i class="fa fa-arrow-right text-primary me-2"></i>Outgoing Relations (Belongs To)</h4>
                                        @if(isset($relations['outgoing']) && count($relations['outgoing']) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle text-xs mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Local Column</th>
                                                            <th>References Table</th>
                                                            <th>References Column</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($relations['outgoing'] as $fk)
                                                            <tr>
                                                                <td class="font-monospace fw-bold">{{ implode(', ', $fk['columns']) }}</td>
                                                                <td>
                                                                    <a href="?table={{ $fk['foreign_table'] }}" class="fw-semibold text-decoration-none">
                                                                        {{ $fk['foreign_table'] }} <i class="fa fa-link small"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="font-monospace">{{ implode(', ', $fk['foreign_columns']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-light border small text-muted mb-0">
                                                No outgoing relations defined for this table.
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <h4 class="h5 fw-bold text-dark mb-3"><i class="fa fa-arrow-left text-success me-2"></i>Incoming Relations (Has Many)</h4>
                                        @if(isset($relations['incoming']) && count($relations['incoming']) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle text-xs mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Referencing Table</th>
                                                            <th>Referencing Column</th>
                                                            <th>Local Referenced Column</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($relations['incoming'] as $fk)
                                                            <tr>
                                                                <td>
                                                                    <a href="?table={{ $fk['table'] }}" class="fw-semibold text-decoration-none">
                                                                        {{ $fk['table'] }} <i class="fa fa-link small"></i>
                                                                    </a>
                                                                </td>
                                                                <td class="font-monospace fw-bold">{{ implode(', ', $fk['columns']) }}</td>
                                                                <td class="font-monospace">{{ implode(', ', $fk['foreign_columns']) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-light border small text-muted mb-0">
                                                No incoming relations found for this table.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card shadow-sm py-5 text-center text-muted border-0 bg-white">
                    <div class="card-body">
                        <i class="fa fa-database fa-4x mb-3 text-secondary opacity-50"></i>
                        <h4 class="fw-bold text-dark">Select a table to inspect</h4>
                        <p class="text-muted small mx-auto" style="max-width: 400px;">Choose any table from the left sidebar list to inspect its columns schema, table structure, and explore the underlying records.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Table filtering
        const tableSearch = document.getElementById('table-search');
        if (tableSearch) {
            tableSearch.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                const tableItems = document.querySelectorAll('#tables-list-container a');
                let count = 0;
                tableItems.forEach(item => {
                    const tableName = item.getAttribute('data-table-name').toLowerCase();
                    if (tableName.includes(query)) {
                        item.classList.remove('d-none');
                        item.classList.add('d-flex');
                        count++;
                    } else {
                        item.classList.add('d-none');
                        item.classList.remove('d-flex');
                    }
                });
                document.getElementById('table-count').innerText = count;
            });
        }

        // Copy button
        const copyBtn = document.getElementById('copy-table-name');
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                const tableName = document.getElementById('active-table-name').textContent.trim();
                navigator.clipboard.writeText(tableName).then(() => {
                    const icon = copyBtn.querySelector('i');
                    icon.className = 'fa-solid fa-check text-success';
                    setTimeout(() => {
                        icon.className = 'fa-regular fa-copy';
                    }, 1500);
                });
            });
        }
    });
</script>
@endsection
