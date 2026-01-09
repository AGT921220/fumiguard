<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Certificado de fumigación</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .h1 { font-size: 20px; font-weight: 800; margin: 0 0 10px; text-align: center; }
        .muted { color: #666; }
        .box { border: 1px solid #ddd; padding: 12px; border-radius: 4px; }
        .row { display: table; width: 100%; }
        .col { display: table-cell; vertical-align: top; }
        .folio { font-size: 14px; font-weight: 700; }
    </style>
</head>
<body>
    <div class="h1">Certificado de fumigación</div>

    <div class="row" style="margin-bottom:12px">
        <div class="col">
            <div><strong>Tenant:</strong> {{ $tenant['name'] }}</div>
            <div class="muted">{{ $tenant['slug'] }}</div>
        </div>
        <div class="col" style="text-align:right">
            <div class="folio">Folio: {{ $certificate['folio'] }}</div>
            <div class="muted">Emitido: {{ $certificate['issued_at'] }}</div>
        </div>
    </div>

    <div class="box">
        <div><strong>Cliente:</strong> {{ $customer['name'] }}</div>
        <div><strong>Sitio:</strong> {{ $site['name'] }}</div>
        <div class="muted">
            {{ $site['address_line1'] ?? '' }}
            {{ $site['city'] ? ', '.$site['city'] : '' }}
            {{ $site['state'] ? ', '.$site['state'] : '' }}
            {{ $site['country'] ? ' ('.$site['country'].')' : '' }}
        </div>
        <div style="margin-top:10px">
            <strong>Servicio:</strong> {{ $service_plan['name'] ?? '—' }}
        </div>
        @if(!empty($service_plan['certificate_template']))
            <div class="muted" style="margin-top:6px">
                Plantilla: {{ is_array($service_plan['certificate_template']) ? json_encode($service_plan['certificate_template']) : (string)$service_plan['certificate_template'] }}
            </div>
        @endif
    </div>

    <div style="margin-top:12px" class="muted">
        Referencia ServiceReport #{{ $report['id'] }} — WorkOrder #{{ $work_order['id'] }}
    </div>
</body>
</html>

