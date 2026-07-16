@extends('layouts.app')

@section('content')
    <div class="container-fluid px-2 px-md-3 py-3">

        <div class="sale-card">

            {{-- HEADER --}}
            <div class="sale-header">

                <div class="header-left">

                    <div class="avatar">

                        {{ strtoupper(substr($sale->client_contact ?? 'N', 0, 1)) }}

                    </div>

                    <div>

                        <h4 class="client-name">
                            {{ $sale->client_contact ?: 'No Client Name' }}
                        </h4>

                        <div class="company-location">

                            <span>
                                {{ $sale->company ?: 'No Company' }}
                            </span>

                            <span class="dot"></span>

                            <span>
                                {{ $sale->location ?: 'No Location' }}
                            </span>

                        </div>

                    </div>

                </div>

                <div class="header-right">
                    <span class="status-pill">
                        {{ $sale->status ?: '-' }}
                    </span>&ensp;

                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary rounded-pill px-2 py-1">
                        <i class="fa fa-arrow-left"></i>
                    </a>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="action-bar">

                <a href="https://wa.me/91{{ $sale->mobile }}" target="_blank" class="action-btn whatsapp">

                    <i class="fa-brands fa-whatsapp" style="font-size: 16px"></i>
                    WhatsApp

                </a>

                <a href="mailto:{{ $sale->email }}" class="action-btn">

                    <i class="fa fa-envelope"></i> Email

                </a>

            </div>

            {{-- QUICK DETAILS --}}
            <div class="details-grid">

                <div class="detail-card">
                    <div class="detail-label">
                        📱 Mobile
                    </div>

                    <div class="detail-value">
                        {{ $sale->mobile ?: '-' }}
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">
                        ✉ Email
                    </div>

                    <div class="detail-value">
                        {{ $sale->email ?: '-' }}
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">
                        🌐 Source
                    </div>

                    <div class="detail-value">
                        {{ $sale->source ?: '-' }}
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">
                        🧩 Type
                    </div>

                    <div class="detail-value">
                        {{ $sale->type ?: '-' }}
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">
                        📅 Follow Up
                    </div>

                    <div class="detail-value">
                        {{ $sale->follow_up_date ? \Carbon\Carbon::parse($sale->follow_up_date)->format('d M Y') : '-' }}
                    </div>
                </div>

                <div class="detail-card">
                    <div class="detail-label">
                        👤 Created By
                    </div>

                    <div class="detail-value">
                        {{ $sale->creator?->name ?? '-' }}
                    </div>
                </div>

            </div>

            {{-- REQUIREMENT --}}
            <div class="modern-content-card requirement-card">

                <div class="modern-card-header">

                    <div class="icon-box requirement-icon">
                        📋
                    </div>

                    <div>
                        <div class="modern-title">
                            Requirement
                        </div>

                        <div class="modern-subtitle">
                            Client Requirement Details
                        </div>
                    </div>

                </div>

                <div class="modern-content-text">
                    {{ $sale->requirement ?: 'No Requirement Added' }}
                </div>

            </div>

            {{-- REMARKS --}}
            {{-- <div class="modern-content-card remarks-card">

                <div class="modern-card-header">

                    <div class="icon-box remarks-icon">
                        📝
                    </div>

                    <div>
                        <div class="modern-title">
                            Remarks
                        </div>

                        <div class="modern-subtitle">
                            Internal Notes & Followups
                        </div>
                    </div>

                </div>

                <div class="modern-content-text">
                    {{ $sale->remarks ?: 'No Remarks Added' }}
                </div>

            </div> --}}

            {{-- REMARKS HISTORY --}}
            <div class="modern-content-card remarks-card">

                <div class="modern-card-header">

                    <div class="icon-box remarks-icon">
                        📝
                    </div>

                    <div>
                        <div class="modern-subtitle">
                            Remarks History
                        </div>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-sm align-middle mb-0">

                        <thead>
                            <tr>
                                <th>Remark</th>
                                <th width="180">Sales Person</th>
                                <th width="180">Created At</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($sale->remarkHistories as $remark)
                                <tr>

                                    <td>
                                        {!! nl2br(e($remark->remarks)) !!}
                                    </td>

                                    <td>
                                        {{ $remark->creator?->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $remark->created_at?->format('d M Y h:i A') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="3" class="text-center text-muted py-4">
                                        No remark history available
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="footer-bar">

                <div>
                    Updated By :
                    <strong>{{ $sale->updater?->name ?? '-' }}
                </div>

                <div>
                    {{ $sale->updated_at?->format('d M Y h:i A') }}
                </div>

            </div>

        </div>

    </div>

    <style>
        body {
            background: #f4f7fb;
        }

        .sale-card {
            background: #fff;
            border-radius: 22px;
            padding: 18px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.06);
            border: 1px solid #edf0f5;
            max-width: 950px;
            margin: auto;
        }

        /* HEADER */

        .sale-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 5px;
            margin-bottom: 10px;
        }

        .header-left {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #4f8cff);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .client-name {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px;
            color: #1d2939;
        }

        .company-location {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #667085;
            font-size: 12px;
            flex-wrap: wrap;
        }

        .dot {
            width: 4px;
            height: 4px;
            background: #98a2b3;
            border-radius: 50%;
        }

        .status-pill {
            background: #e8fff1;
            color: #16a34a;
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ACTIONS */

        .action-bar {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .action-btn {
            text-decoration: none;
            background: #f1f5f9;
            color: #0f172a;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
        }

        .action-btn:hover {
            background: #dbeafe;
            color: #0f172a;
        }

        .whatsapp {
            background: #dcfce7;
            color: #15803d;
        }

        /* GRID */

        /* DETAILS GRID */

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 14px;
        }

        .detail-card {
            position: relative;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 14px;
            transition: 0.25s ease;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
        }

        .detail-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        .detail-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg,
                    #3b82f6,
                    #6366f1);
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 7px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
            line-height: 1.5;
        }

        /* MOBILE */

        @media(max-width:768px) {

            .details-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .detail-card {
                padding: 12px;
                border-radius: 14px;
            }

            .detail-value {
                font-size: 13px;
            }

        }

        @media(max-width:480px) {

            .details-grid {
                grid-template-columns: 1fr;
            }

        }

        /* MODERN CONTENT CARDS */

        .modern-content-card {
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border-radius: 22px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf2f7;
            box-shadow:
                0 4px 14px rgba(15, 23, 42, 0.05);
            transition: 0.25s ease;
        }

        .modern-content-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .modern-content-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
        }

        .requirement-card::before {
            background: linear-gradient(180deg,
                    #3b82f6,
                    #6366f1);
        }

        .remarks-card::before {
            background: linear-gradient(180deg,
                    #10b981,
                    #059669);
        }

        /* HEADER */

        .modern-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .icon-box {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .requirement-icon {
            background: #e0ecff;
        }

        .remarks-icon {
            background: #dcfce7;
        }

        /* TEXT */

        .modern-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .modern-subtitle {
            font-size: 12px;
            color: #64748b;
            /* margin-top: ; */
        }

        .modern-content-text {
            font-size: 14px;
            color: #334155;
            line-height: 1.8;
            /* white-space: pre-line; */
            background: #f8fafc;
            padding: 14px;
            border-radius: 16px;
            border: 1px dashed #dbe4ee;
            word-break: break-word;
        }

        /* MOBILE */

        @media(max-width:768px) {

            .modern-content-card {
                padding: 14px;
                border-radius: 18px;
            }

            .modern-content-text {
                font-size: 13px;
                line-height: 1.7;
                padding: 12px;
            }

            .icon-box {
                width: 38px;
                height: 38px;
                font-size: 16px;
                border-radius: 12px;
            }

            .modern-title {
                font-size: 14px;
            }

        }

        /* FOOTER */

        .footer-bar {
            border-top: 1px solid #eef2f6;
            padding-top: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
            color: #667085;
            font-size: 13px;
            flex-wrap: wrap;
        }

        /* MOBILE */

        @media(max-width:768px) {

            .sale-card {
                padding: 14px;
                border-radius: 18px;
            }

            .sale-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .client-name {
                font-size: 18px;
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .avatar {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }

            .action-btn {
                flex: 1;
                text-align: center;
                font-size: 12px;
                padding: 8px;
            }

            .footer-bar {
                flex-direction: column;
                font-size: 12px;
            }

        }
    </style>
@endsection
