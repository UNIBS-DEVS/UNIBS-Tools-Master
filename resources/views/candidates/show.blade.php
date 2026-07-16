@extends('layouts.app')

@section('title', 'Candidate Details')

@section('content')

    @include('partials.message')

    <div class="container-fluid px-2 px-md-1 py-1">

        <div class="sale-card">

            {{-- HEADER --}}
            <div class="sale-header">

                <div class="header-left">

                    <div class="avatar">
                        {{ strtoupper(substr($candidate->candidate_name ?? 'C', 0, 1)) }}
                    </div>

                    <div>

                        <h4 class="client-name">
                            {{ $candidate->candidate_name ?? 'No Candidate' }}
                        </h4>

                        <div class="company-location">

                            <span>
                                {{ $candidate->customer?->customer ?? 'No Customer' }}
                            </span>

                            <span class="dot"></span>

                            <span>
                                {{ $candidate->position?->position ?? 'No Position' }}
                            </span>

                        </div>

                    </div>

                </div>

                <div class="header-right d-flex align-items-center gap-2">

                    <span
                        class="status-pill
                        @if ($candidate->status == 'Mapped') status-open
                        @elseif($candidate->status == 'Joined')
                            status-closed
                        @else
                            status-hold @endif">

                        {{ $candidate->status ?? '-' }}

                    </span>

                    <a href="{{ route('candidates.edit', $candidate->id) }}" class="btn btn-warning rounded-pill px-2 py-1">

                        <i class="fa fa-pen"></i>

                    </a>

                    <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary rounded-pill px-2 py-1">

                        <i class="fa fa-arrow-left"></i>

                    </a>

                </div>

            </div>

            {{-- ACTION BAR --}}
            <div class="action-bar">

                @if ($candidate->resume_path)
                    <a href="{{ $candidate->resume_path }}" target="_blank" class="action-btn">

                        <i class="fa fa-file"></i>

                        Resume

                    </a>
                @endif

                @if ($candidate->email)
                    <a href="mailto:{{ $candidate->email }}" class="action-btn">

                        <i class="fa fa-envelope"></i>

                        Email

                    </a>
                @endif

                @if ($candidate->mobile)
                    <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $candidate->mobile) }}" target="_blank"
                        class="action-btn">

                        <i class="fab fa-whatsapp"></i>

                        WhatsApp

                    </a>
                @endif

                <span class="action-btn openings-btn">

                    <i class="fa fa-briefcase"></i>

                    {{ $candidate->experience_years ?? 0 }} Yrs |
                    @if (($candidate->experience_months ?? 0) > 0)
                        {{ $candidate->experience_months }} Mon
                    @endif

                </span>

                <span class="action-btn experience-btn">

                    <i class="fa fa-clock"></i>

                    {{ $candidate->notice_period ?? '-' }}

                </span>

            </div>

            {{-- PERSONAL DETAILS --}}
            <div class="details-grid">

                <div class="detail-card">
                    <span class="detail-label">📱 </span> {{ $candidate->mobile ?? '-' }}
                    {{-- <div class="detail-value">

                    </div> --}}
                </div>

                <div class="detail-card">
                    <span class="detail-label">📧 </span> {{ $candidate->email ?? '-' }}
                </div>

                <div class="detail-card">
                    <span class="detail-label">👤 </span> {{ $candidate->gender ?? '-' }}
                </div>

                <div class="detail-card">
                    <span class="detail-label">🏢 </span> {{ $candidate->current_company ?? '-' }}
                </div>

                <div class="detail-card">
                    <span class="detail-label">🎓 </span> {{ $candidate->education ?? '-' }}
                </div>

                <div class="detail-card">
                    <span class="detail-label">📍 </span> {{ $candidate->current_location ?? '-' }}
                </div>

            </div>

            {{-- RECRUITMENT INFORMATION --}}
            <div class="modern-content-card">

                <div class="modern-card-header">

                    <div class="icon-box recruitment-icon">
                        🎯
                    </div>

                    <div>

                        {{-- <div class="modern-title">
                            Recruitment Information
                        </div> --}}

                        <div class="modern-subtitle">
                            {{-- Candidate Hiring Information --}}
                            Recruitment Information
                        </div>

                    </div>

                </div>

                <div class="details-grid">

                    <div class="detail-card">
                        <span class="detail-label">🏢 </span> {{ $candidate->customer?->customer ?? '-' }}
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">💼 </span> {{ $candidate->position?->position ?? '-' }}
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">📍 </span> {{ $candidate->preferred_location ?? '-' }}
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">🧑‍💻 Exp: </span> {{ $candidate->experience_years ?? 0 }} Yrs |
                        @if (($candidate->experience_months ?? 0) > 0)
                            {{ $candidate->experience_months }} Mon
                        @endif
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">⭐ Rel Exp: </span> {{ $candidate->relevant_experience_years ?? 0 }} Yrs
                        |
                        @if (($candidate->relevant_experience_months ?? 0) > 0)
                            {{ $candidate->relevant_experience_months }} Mon
                        @endif
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">⏳ </span> {{ $candidate->notice_period ?? '-' }}
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">📅 Last Work Day : </span>
                        {{ $candidate->last_working_day ? \Carbon\Carbon::parse($candidate->last_working_day)->format('d M Y') : 'None' }}
                    </div>

                </div>

            </div>

            {{-- INTERVIEW INFORMATION --}}
            <div class="modern-content-card">

                <div class="modern-card-header">

                    <div class="icon-box interview-icon">
                        🎤
                    </div>

                    <div>

                        {{-- <div class="modern-title">

                        </div> --}}

                        <div class="modern-subtitle">
                            {{-- Interview Schedule & Progress --}}

                            Interview Information
                        </div>

                    </div>

                </div>

                <div class="details-grid">

                    <div class="detail-card">
                        <span class="detail-label">📌 </span> {{ $candidate->status ?? '-' }}
                        <div class="detail-value">

                        </div>
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">🎯 </span> {{ $candidate->interview_level ?? 'None' }}
                    </div>

                    <div class="detail-card">
                        <span class="detail-label">📅 </span>
                        {{ $candidate->interview_date ? \Carbon\Carbon::parse($candidate->interview_date)->format('d M Y h:i A') : 'None' }}
                    </div>

                </div>

            </div>

            {{-- COMPENSATION --}}
            <div class="modern-content-card">

                <div class="modern-card-header">

                    <div class="icon-box salary-icon">
                        💰
                    </div>

                    <div>

                        <div class="modern-subtitle">
                            {{-- Current & Expected Salary Details --}}

                            Compensation Information
                        </div>

                    </div>

                </div>

                <div class="details-grid">
                    @php
                        $ctcs = [
                            'Fixed CTC' => $candidate->current_fixed_ctc,
                            'Variable CTC' => $candidate->current_variable_ctc,
                            'Expected CTC' => $candidate->expected_ctc,
                        ];
                    @endphp

                    @foreach ($ctcs as $label => $value)
                        <div class="detail-card">
                            <span class="detail-label">{{ $label }} : </span>
                            {{ $value ? "₹ {$value} LPA" : '₹ 0 LPA' }}
                        </div>
                    @endforeach

                </div>

            </div>

            {{-- SKILLS --}}
            <div class="modern-content-card">

                <div class="modern-card-header">

                    <div class="icon-box skills-icon">
                        💻
                    </div>

                    <div>

                        <div class="modern-subtitle">
                            {{-- Technical Expertise --}}
                            Skills : @foreach (explode(',', $candidate->skill ?? '') as $skill)
                                @if (trim($skill))
                                    <span class="action-btn">

                                        {{ trim($skill) }}

                                    </span>
                                @endif
                            @endforeach
                        </div>

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
                        <div class="modern-subtitle">
                            {{-- Complete Candidate Activity & Remarks Timeline --}}
                            Remarks History
                        </div>

                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-sm align-middle mb-0">

                        <thead>

                            <tr>
                                <th>
                                    Remark
                                </th>

                                <th width="180">
                                    Recruiter
                                </th>

                                <th width="180">
                                    Created At
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($candidate->remarkHistories as $key => $remark)
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

                                    <td colspan="4" class="text-center text-muted py-4">

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

                    Recruiter :
                    <strong>

                        {{ $candidate->creator?->name ?? '-' }}

                    </strong>

                </div>

                <div>

                    Created :
                    {{ $candidate->created_at?->format('d M Y h:i A') }}

                </div>

                <div>

                    Updated :
                    {{ $candidate->updated_at?->format('d M Y h:i A') }}

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
            padding: 10px 14px;
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
            height: 2.5px;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
        }

        .detail-label {
            font-size: 14px;
            font-weight: 600;
            /* color: #64748b; */
            /* margin-bottom: 7px; */
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
            padding: 0px 18px;
            margin-bottom: 10px;
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
            /* margin-bottom: 14px; */
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
