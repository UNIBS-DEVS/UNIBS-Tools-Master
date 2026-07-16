<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .incoming {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .outgoing {
            background-color: #17a2b8;
            color: #000;
            font-weight: bold;
            text-align: center;
        }

        .missed {
            background-color: #ffc107;
            color: #000;
            font-weight: bold;
            text-align: center;
        }

        .rejected {
            background-color: #dc3545;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .header {
            margin-bottom: 15px;
        }

        .logo {
            float: left;
        }

        .title {
            text-align: right;
        }

        .clearfix {
            clear: both;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('assets/images/company-logo.png') }}" height="60">
        </div>

        <div class="title">
            <h3>Reviews Report</h3>
            <p>{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>
    </div>

    {{-- OPTIONAL FILTER INFO --}}
    <div style="margin-bottom:10px;">
        <strong>Total Records:</strong> {{ count($reviews) }}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>From</th>
                <th>To</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Type</th>
                <th>Note</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($reviews as $r)
                @php
                    $h = floor($r->duration / 3600);
                    $m = floor(($r->duration % 3600) / 60);
                    $s = $r->duration % 60;

                    $typeClass = match (strtolower($r->type)) {
                        'incoming' => 'incoming',
                        'outgoing' => 'outgoing',
                        'missed' => 'missed',
                        'rejected' => 'rejected',
                        default => '',
                    };
                @endphp

                <tr>
                    <td> {{ $r->contactUser->name ?? 'no-contact' }} </td>
                    <td>{{ $r->from_number ?? '-' }}</td>
                    <td>{{ $r->to_number ?? '-' }}</td>

                    <td>
                        {{ !empty($r->call_date) ? \Carbon\Carbon::parse($r->call_date)->format('d M Y') : '-' }}
                    </td>

                    <td>
                        {{ !empty($r->call_time) ? \Carbon\Carbon::parse($r->call_time)->format('H:i:s') : '-' }}
                    </td>

                    <td>{{ $h }}h {{ $m }}m {{ $s }}s</td>

                    {{-- TYPE WITH COLOR --}}
                    <td class="{{ $typeClass }}">
                        {{ ucfirst($r->type) }}
                    </td>

                    <td>{{ $r->notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ date('Y') }} UNI Business Solutions
    </div>

</body>

</html>
