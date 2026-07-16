@extends('layouts.app')

@section('title', 'Position Details')

@section('content')

    <div class="container-fluid px-2 px-md-3 py-3">

        <div class="sale-card">

            {{-- HEADER --}}
            <div class="sale-header">

                <div class="header-left">

                    <div class="avatar">

                        {{ strtoupper(substr($customerJob->position ?? 'P', 0, 1)) }}

                    </div>

                    <div>

                        <h4 class="client-name">
                            {{ $customerJob->position ?: 'No Position' }}
                        </h4>

                        <div class="company-location">

                            <span>
                                {{ $customerJob->customer?->customer ?? 'No Customer' }}
                            </span>

                            <span class="dot"></span>

                            <span>
                                {{ $customerJob->location ?: 'No Location' }}
                            </span>

                        </div>

                    </div>

                </div>

                <div class="header-right d-flex align-items-center gap-2">

                    <span
                        class="status-pill
                        @if ($customerJob->status == 'Open') status-open
                        @elseif($customerJob->status == 'Closed')
                            status-closed
                        @else
                            status-hold @endif">

                        {{ $customerJob->status ?: '-' }}

                    </span>

                    <a href="{{ route('customer-jobs.edit', $customerJob->id) }}"
                        class="btn btn-warning rounded-pill px-2 py-1">

                        <i class="fa fa-pen"></i>

                    </a>

                    <a href="{{ route('customer-jobs.index') }}" class="btn btn-outline-secondary rounded-pill px-2 py-1">

                        <i class="fa fa-arrow-left"></i>

                    </a>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="action-bar">

                @if ($customerJob->jd_path)
                    <a href="{{ $customerJob->jd_path }}" target="_blank" class="action-btn">

                        <i class="fa fa-file"></i>
                        Open JD

                    </a>
                @endif

                <span class="action-btn openings-btn">

                    <i class="fa fa-users"></i>

                    Openings :
                    {{ $customerJob->count ?? 0 }}

                </span>

                <span class="action-btn experience-btn">

                    <i class="fas fa-graduation-cap"></i>

                    Experience :
                    {{ $customerJob->experience ?? 0 }}

                </span>

            </div>

            {{-- QUICK DETAILS --}}
            <div class="details-grid">

                <div class="detail-card">

                    <div class="detail-label">
                        💻 Skill
                    </div>

                    <div class="detail-value">

                        {{ collect(explode(',', $customerJob->skill))->map(fn($skill) => trim($skill))->implode(', ') ?:
                            '-' }}

                    </div>

                </div>

                <div class="detail-card">

                    <div class="detail-label">
                        📍 Location
                    </div>

                    <div class="detail-value">
                        {{ $customerJob->location ?: '-' }}
                    </div>

                </div>

                <div class="detail-card">

                    <div class="detail-label">
                        💰 Budget
                    </div>

                    <div class="detail-value">
                        ₹ {{ $customerJob->budget ?: '-' }}
                    </div>

                </div>

                <div class="detail-card">

                    <div class="detail-label">
                        👤 Customer
                    </div>

                    <div class="detail-value">
                        {{ $customerJob->customer?->customer ?? '-' }}
                    </div>

                </div>

                <div class="detail-card">

                    <div class="detail-label">
                        📅 Created
                    </div>

                    <div class="detail-value">
                        {{ $customerJob->created_at?->format('d M Y h:i A') ?? '-' }}
                    </div>

                </div>

                <div class="detail-card">

                    <div class="detail-label">
                        🔄 Updated
                    </div>

                    <div class="detail-value">
                        {{ $customerJob->updated_at?->format('d M Y h:i A') ?? '-' }}
                    </div>

                </div>

            </div>

            {{-- REMARKS --}}
            <div class="modern-content-card remarks-card">

                <div class="modern-card-header">

                    <div class="icon-box remarks-icon">
                        📝
                    </div>

                    <div>

                        <div class="modern-title">
                            Remarks
                        </div>

                        <div class="modern-subtitle">
                            Internal Notes & Position Details
                        </div>

                    </div>

                </div>

                <div class="modern-content-text">

                    {{ $customerJob->remarks ?: 'No Remarks Added' }}

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="footer-bar">

                <div>

                    Customer :
                    <strong>
                        {{ $customerJob->customer?->customer ?? '-' }}
                    </strong>

                </div>

                <div>

                    Last Updated :
                    {{ $customerJob->updated_at?->format('d M Y h:i A') }}

                </div>

            </div>

        </div>

    </div>



@endsection

@push('styles')
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
            gap: 10px;
            margin-bottom: 12px;
        }

        .header-left {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0d6efd, #4f8cff);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
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

        /* STATUS */

        .status-pill {
            padding: 5px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-open {
            background: #e8fff1;
            color: #16a34a;
        }

        .status-closed {
            background: #ffe7e7;
            color: #dc2626;
        }

        .status-hold {
            background: #fff7d6;
            color: #ca8a04;
        }

        /* ACTIONS */

        .action-bar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .action-btn {
            text-decoration: none;
            background: #f1f5f9;
            color: #0f172a;
            padding: 7px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .action-btn:hover {
            background: #dbeafe;
            color: #0f172a;
        }

        .openings-btn {
            background: #ecfeff;
            color: #0f766e;
        }

        .experience-btn {
            background: #fbdddd;
            color: #d61616;
        }

        /* DETAILS GRID */

        .details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 18px;
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
            background: linear-gradient(90deg, #3b82f6, #6366f1);
        }

        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 7px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
            line-height: 1.5;
        }

        /* CONTENT CARD */

        .modern-content-card {
            position: relative;
            overflow: hidden;
            background: #ffffff;
            border-radius: 22px;
            padding: 18px;
            margin-bottom: 18px;
            border: 1px solid #edf2f7;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.05);
            transition: 0.25s ease;
        }

        .modern-content-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .modern-content-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #10b981, #059669);
        }

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

        .remarks-icon {
            background: #dcfce7;
        }

        .modern-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .modern-subtitle {
            font-size: 12px;
            color: #64748b;
        }

        .modern-content-text {
            font-size: 14px;
            color: #334155;
            line-height: 1.8;
            background: #f8fafc;
            padding: 14px;
            border-radius: 16px;
            border: 1px dashed #dbe4ee;
            word-break: break-word;
            /* white-space: pre-line; */
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

            .details-grid {
                grid-template-columns: 1fr;
            }

            .action-btn {
                flex: 1;
                justify-content: center;
            }

            .footer-bar {
                flex-direction: column;
            }

        }
    </style>
@endpush
