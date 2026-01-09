<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de servicio</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .muted { color: #666; }
        .row { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; }
        .h1 { font-size: 18px; font-weight: 700; margin: 0 0 6px; }
        .h2 { font-size: 14px; font-weight: 700; margin: 18px 0 6px; }
        .box { border: 1px solid #ddd; padding: 10px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f6f6f6; }
        .thumb { width: 140px; height: auto; border: 1px solid #ddd; margin: 4px; }
        .grid { display: flex; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="row">
        <div class="col">
            <div class="h1">Reporte de servicio</div>
            <div class="muted">Tenant: {{ $tenant['name'] }} ({{ $tenant['slug'] }})</div>
        </div>
        <div class="col" style="text-align:right">
            <div class="muted">ServiceReport #{{ $report['id'] }}</div>
            <div class="muted">Finalizado: {{ $report['finalized_at'] ?? '—' }}</div>
        </div>
    </div>

    <div class="h2">Cliente y sitio</div>
    <div class="box">
        <div><strong>Cliente:</strong> {{ $customer['name'] }}</div>
        <div><strong>Sitio:</strong> {{ $site['name'] }}</div>
        <div class="muted">
            {{ $site['address_line1'] ?? '' }}
            {{ $site['city'] ? ', '.$site['city'] : '' }}
            {{ $site['state'] ? ', '.$site['state'] : '' }}
            {{ $site['country'] ? ' ('.$site['country'].')' : '' }}
        </div>
        <div class="muted">
            Ubicación sitio: {{ $site['lat'] ?? '—' }}, {{ $site['lng'] ?? '—' }}
        </div>
    </div>

    <div class="h2">Orden y ubicación</div>
    <div class="box">
        <div><strong>WorkOrder:</strong> #{{ $work_order['id'] }} — Estado: {{ $work_order['status'] }}</div>
        <div class="muted">Check-in: {{ $work_order['check_in_at'] ?? '—' }} ({{ $work_order['check_in_lat'] ?? '—' }}, {{ $work_order['check_in_lng'] ?? '—' }})</div>
        <div class="muted">Check-out: {{ $work_order['check_out_at'] ?? '—' }} ({{ $work_order['check_out_lat'] ?? '—' }}, {{ $work_order['check_out_lng'] ?? '—' }})</div>
    </div>

    <div class="h2">Checklist</div>
    <div class="box">
        @if(is_array($report['checklist']) && count($report['checklist']) > 0)
            <table>
                <thead>
                <tr>
                    <th>Ítem</th>
                    <th>Valor</th>
                </tr>
                </thead>
                <tbody>
                @foreach($report['checklist'] as $item)
                    <tr>
                        <td>{{ $item['key'] ?? '—' }}</td>
                        <td>{{ is_bool($item['value'] ?? null) ? (($item['value'] ?? false) ? 'Sí' : 'No') : ($item['value'] ?? '—') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">Sin checklist.</div>
        @endif
        @if(!empty($report['notes']))
            <div style="margin-top:10px"><strong>Notas:</strong> {{ $report['notes'] }}</div>
        @endif
    </div>

    <div class="h2">Químicos usados</div>
    <div class="box">
        @if(count($chemicals) > 0)
            <table>
                <thead>
                <tr>
                    <th>Químico</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                </tr>
                </thead>
                <tbody>
                @foreach($chemicals as $c)
                    <tr>
                        <td>{{ $c['chemical_name'] }}</td>
                        <td>{{ $c['quantity'] }}</td>
                        <td>{{ $c['unit'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <div class="muted">Sin químicos registrados.</div>
        @endif
    </div>

    <div class="h2">Evidencias</div>
    <div class="box">
        @if(count($evidences) > 0)
            <div class="grid">
                @foreach($evidences as $e)
                    <div style="margin-right:8px">
                        @if(!empty($e['thumbnail_data_uri']))
                            <img class="thumb" src="{{ $e['thumbnail_data_uri'] }}" alt="evidence"/>
                        @else
                            <div class="muted">Archivo: {{ $e['path'] }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="muted">Sin evidencias.</div>
        @endif
    </div>

    <div class="h2">Firma</div>
    <div class="box">
        @if(count($signatures) > 0)
            @foreach($signatures as $s)
                <div>
                    <strong>{{ $s['signed_by_name'] }}</strong>
                    <span class="muted">{{ $s['signed_by_role'] ?? '' }}</span>
                    <span class="muted">— {{ $s['signed_at'] }}</span>
                </div>
            @endforeach
        @else
            <div class="muted">Sin firma capturada.</div>
        @endif
    </div>
</body>
</html>

