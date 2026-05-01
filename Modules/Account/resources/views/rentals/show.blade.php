@extends('theme::layouts.app', ['title' => $rental->property_type.' — Rental', 'heading' => 'Rental details'])

@section('content')
<div class="rental-show">
    <style>
        .rental-show{max-width:none;width:100%;margin:0;box-sizing:border-box;--rs-radius:10px;--rs-radius-sm:8px;--rs-font:13px;--rs-font-sm:11px;--rs-font-xs:9px;--rs-glow:color-mix(in srgb,var(--primary) 22%,transparent);}
        .rental-show *{box-sizing:border-box;}

        .rental-show__hero{
            position:relative;border-radius:var(--rs-radius);overflow:hidden;margin-bottom:12px;
            border:1px solid color-mix(in srgb,var(--primary) 22%,var(--border));
            background:linear-gradient(145deg,color-mix(in srgb,var(--primary) 14%,var(--card)) 0%,var(--card) 48%,color-mix(in srgb,var(--card) 92%,#0f172a) 100%);
            box-shadow:0 16px 42px -26px color-mix(in srgb,var(--primary) 22%,#000),0 0 0 1px color-mix(in srgb,#fff 4%,transparent) inset;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__hero{
            background:linear-gradient(165deg,#ffffff 0%,color-mix(in srgb,var(--primary) 8%,#fffefd) 40%,#f5f5f4 100%);
            box-shadow:0 16px 40px -24px rgba(0,0,0,.14);
            border-color:color-mix(in srgb,var(--primary) 22%,var(--border));
        }
        .rental-show__hero-glow{
            pointer-events:none;position:absolute;right:-20%;top:-40%;width:55%;height:120%;
            background:radial-gradient(ellipse at center,color-mix(in srgb,var(--primary) 18%,transparent) 0%,transparent 68%);
            opacity:.9;
        }
        .rental-show__hero-inner{position:relative;z-index:1;padding:11px 13px 13px;}

        .rental-show__hero-top{display:flex;flex-wrap:wrap;align-items:center;gap:7px 9px;justify-content:space-between;margin-bottom:8px;}
        .rental-show__back{
            display:inline-flex;align-items:center;gap:5px;padding:5px 9px;border-radius:8px;font-size:11px;font-weight:600;
            border:1px solid color-mix(in srgb,var(--border) 88%,transparent);background:color-mix(in srgb,var(--card) 55%,transparent);
            color:var(--text);text-decoration:none;backdrop-filter:blur(8px);transition:transform .18s ease,border-color .18s ease,background .18s ease;
        }
        .rental-show__back:hover{border-color:color-mix(in srgb,var(--primary) 40%,var(--border));background:color-mix(in srgb,var(--primary) 10%,var(--card));transform:translateX(-2px);}
        .rental-show__biz-chip{
            display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:999px;font-size:9px;font-weight:600;
            letter-spacing:.035em;text-transform:uppercase;color:color-mix(in srgb,var(--primary) 78%,var(--text));
            border:1px solid color-mix(in srgb,var(--primary) 24%,var(--border));background:color-mix(in srgb,var(--primary) 6%,transparent);
        }
        .rental-show__biz-chip i{opacity:.8;font-size:10px;}

        .rental-show__headline{margin:0 0 8px;font-size:clamp(1.02rem,1.55vw,1.2rem);font-weight:600;letter-spacing:-.024em;line-height:1.25;color:var(--text);}
        .rental-show__pills{display:flex;flex-wrap:wrap;gap:5px;}
        .rental-show__pill{
            display:inline-flex;align-items:center;gap:3px;font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;
            padding:2px 7px;border-radius:999px;border:1px solid color-mix(in srgb,var(--border) 78%,transparent);
            background:color-mix(in srgb,var(--card) 40%,transparent);color:var(--muted);
        }
        .rental-show__pill i{color:var(--primary);font-size:9px;opacity:.85;}

        .rental-show__highlight{
            margin-top:10px;display:grid;gap:9px;
            grid-template-columns:1fr;align-items:stretch;
        }
        @media(min-width:640px){
            .rental-show__highlight{grid-template-columns:minmax(0,1.15fr) minmax(0,1fr);gap:10px;align-items:stretch;}
        }
        .rental-show__cost-card{
            border-radius:var(--rs-radius-sm);padding:10px 12px;
            border:1px solid color-mix(in srgb,var(--primary) 35%,var(--border));
            background:linear-gradient(155deg,color-mix(in srgb,var(--primary) 16%,transparent),color-mix(in srgb,var(--card) 94%,transparent));
            position:relative;overflow:hidden;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__cost-card{background:linear-gradient(155deg,color-mix(in srgb,var(--primary) 10%,#fff),#fff);}
        .rental-show__cost-card::before{
            content:"";position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:4px 0 0 4px;
            background:linear-gradient(180deg,var(--primary),color-mix(in srgb,var(--primary) 45%,#1e293b));
        }
        .rental-show__cost-lab{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:3px;}
        .rental-show__cost-val{
            font-size:clamp(1.05rem,2.1vw,1.26rem);font-weight:700;letter-spacing:-.028em;line-height:1.12;
            color:color-mix(in srgb,var(--primary) 34%,var(--text));font-variant-numeric:tabular-nums;
        }
        .rental-show__curr{font-size:9px;opacity:.82;font-weight:600;margin-right:.2em;text-transform:uppercase;vertical-align:super;}

        /* Key money — same tier as recurring cost, visually distinct accent */
        .rental-show__key-card{
            display:flex;align-items:flex-start;gap:10px;padding:10px 12px;border-radius:var(--rs-radius-sm);
            border:1px solid color-mix(in srgb,#f59e0b 38%,var(--border));
            background:linear-gradient(145deg,color-mix(in srgb,#f59e0b 11%,transparent),color-mix(in srgb,var(--card) 94%,transparent));
            position:relative;overflow:hidden;
            box-shadow:0 14px 36px -28px color-mix(in srgb,#f59e0b 42%,#000);
        }
        .rental-show__key-card::before{
            content:"";position:absolute;left:0;top:0;bottom:0;width:4px;border-radius:4px 0 0 4px;
            background:linear-gradient(180deg,#fbbf24,color-mix(in srgb,#d97706 55%,#1e293b));
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__key-card{
            background:linear-gradient(158deg,color-mix(in srgb,#fffbeb 94%,#fff),#ffffff);
            border-color:color-mix(in srgb,#fbbf24 45%,var(--border));
            box-shadow:0 12px 32px -24px rgba(245,158,11,.35);
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__key-card::before{
            background:linear-gradient(180deg,#fbbf24,#d97706);
        }
        .rental-show__key-ico-wrap{
            width:32px;height:32px;border-radius:8px;flex-shrink:0;display:grid;place-items:center;
            font-size:12px;color:color-mix(in srgb,#fbbf24 85%,var(--text));
            background:color-mix(in srgb,#f59e0b 14%,transparent);
            border:1px solid color-mix(in srgb,#f59e0b 35%,var(--border));
            position:relative;z-index:1;
        }
        .rental-show__key-inner{min-width:0;flex:1;position:relative;z-index:1;}
        .rental-show__key-lab{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:3px;}
        .rental-show__key-val{
            font-size:clamp(1rem,1.9vw,1.14rem);font-weight:700;letter-spacing:-.03em;line-height:1.14;
            color:color-mix(in srgb,#eab308 14%,var(--text));font-variant-numeric:tabular-nums;
        }
        .rental-show__key-curr{font-size:9px;opacity:.88;font-weight:600;margin-right:.2em;text-transform:uppercase;vertical-align:super;color:color-mix(in srgb,#fbbf24 30%,var(--muted));}
        .rental-show__key-note{margin:4px 0 0;font-size:9px;font-weight:500;line-height:1.4;color:var(--muted);}

        .rental-show__mini-stats{display:flex;flex-direction:column;gap:6px;}
        .rental-show__mini{
            flex:1;display:flex;align-items:flex-start;gap:8px;padding:7px 10px;border-radius:var(--rs-radius-sm);
            border:1px solid color-mix(in srgb,var(--border) 85%,transparent);background:color-mix(in srgb,var(--card) 88%,transparent);
        }
        .rental-show__mini-ico{width:26px;height:26px;border-radius:7px;display:grid;place-items:center;flex-shrink:0;
            background:color-mix(in srgb,var(--primary) 10%,transparent);color:var(--primary);font-size:11px;}
        .rental-show__mini-txt{font-size:11px;font-weight:600;line-height:1.32;color:var(--text);}
        .rental-show__mini-sub{display:block;margin-top:1px;font-size:9px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.04em;}

        .rental-show__panels{display:grid;gap:9px;}@media(min-width:900px){.rental-show__panels{grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;align-items:start;}}

        .rental-show__panel{
            border-radius:var(--rs-radius);border:1px solid color-mix(in srgb,var(--border) 90%,transparent);
            background:linear-gradient(180deg,color-mix(in srgb,var(--card) 99%,transparent),color-mix(in srgb,var(--card) 94%,#0f172a05));
            box-shadow:0 8px 26px -20px rgba(0,0,0,.28);overflow:hidden;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__panel{background:linear-gradient(180deg,#fff,#fafaf9);box-shadow:0 8px 28px -20px rgba(0,0,0,.08);}
        .rental-show__panel-h{
            margin:0;padding:8px 11px;display:flex;align-items:center;gap:7px;font-size:var(--rs-font-sm);font-weight:600;letter-spacing:.01em;
            border-bottom:1px solid color-mix(in srgb,var(--border) 88%,transparent);
            background:color-mix(in srgb,var(--card) 97%,transparent);
        }
        .rental-show__panel-h i{width:24px;height:24px;display:grid;place-items:center;border-radius:7px;font-size:11px;color:var(--primary);
            background:color-mix(in srgb,var(--primary) 9%,transparent);border:1px solid color-mix(in srgb,var(--primary) 15%,var(--border));}
        .rental-show__panel-body{padding:10px 11px;}

        .rental-show__dl{display:grid;gap:8px;}@media(min-width:440px){.rental-show__dl--2{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px 12px;}}
        .rental-show__kv{padding:6px 0;border-bottom:1px dashed color-mix(in srgb,var(--border) 70%,transparent);}
        .rental-show__kv:last-child{border-bottom:0;padding-bottom:0;}
        .rental-show__dt{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);margin-bottom:2px;}
        .rental-show__dd{margin:0;font-size:var(--rs-font);font-weight:600;line-height:1.42;color:var(--text);word-break:break-word;}
        .rental-show__dd--soft{font-weight:500;color:var(--muted);font-size:var(--rs-font-sm);line-height:1.45;}
        .rental-show__dd--money{font-size:13px;font-weight:700;color:color-mix(in srgb,var(--primary) 40%,var(--text));font-variant-numeric:tabular-nums;}

        .rental-show__prose{margin:0;padding:8px 10px;border-radius:var(--rs-radius-sm);font-size:var(--rs-font-sm);line-height:1.45;color:var(--text);
            background:color-mix(in srgb,var(--primary) 4%,transparent);border-left:3px solid color-mix(in srgb,var(--primary) 45%,transparent);
            white-space:pre-wrap;margin-top:8px;}

        .rental-show__landlord-profile{display:flex;align-items:center;gap:9px;margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid color-mix(in srgb,var(--border) 88%,transparent);}
        .rental-show__avatar{
            width:38px;height:38px;border-radius:10px;display:grid;place-items:center;font-size:13px;font-weight:700;color:#fff;
            background:linear-gradient(135deg,var(--primary),color-mix(in srgb,var(--primary) 52%,#0f172a));
            box-shadow:0 12px 28px -14px color-mix(in srgb,var(--primary) 50%,transparent);flex-shrink:0;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__avatar{
            background:linear-gradient(135deg,#171717,#404040);
            color:#fde047;
            box-shadow:0 12px 28px -14px rgba(0,0,0,.22);
        }
        .rental-show__avatar-name{margin:0;font-size:var(--rs-font);font-weight:600;letter-spacing:-.015em;color:var(--text);line-height:1.22;}
        .rental-show__avatar-role{margin:2px 0 0;font-size:9px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;}

        .rental-show__contact-rows{display:flex;flex-direction:column;gap:2px;}
        .rental-show__row{
            display:flex;gap:10px;padding:8px 9px;border-radius:8px;align-items:flex-start;
            transition:background .15s ease;
        }
        .rental-show__row:hover{background:color-mix(in srgb,var(--primary) 5%,transparent);}
        .rental-show__row-ico{width:26px;height:26px;border-radius:6px;display:grid;place-items:center;flex-shrink:0;font-size:10px;color:var(--primary);
            background:color-mix(in srgb,var(--primary) 10%,transparent);border:1px solid color-mix(in srgb,var(--primary) 15%,var(--border));}
        .rental-show__row-body{min-width:0;padding-top:1px;}
        .rental-show__row-lab{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:1px;}
        .rental-show__row-val{margin:0;font-size:var(--rs-font-sm);font-weight:500;line-height:1.4;color:var(--text);word-break:break-word;}
        .rental-show__row-val a{color:color-mix(in srgb,var(--primary) 70%,var(--text));text-decoration:none;font-weight:600;border-bottom:1px solid color-mix(in srgb,var(--primary) 32%,transparent);transition:border-color .15s ease,color .15s ease;}
        .rental-show__row-val a:hover{border-bottom-color:var(--primary);color:var(--primary);}

        .rental-show__danger{
            margin-top:12px;padding:10px 12px;border-radius:var(--rs-radius);border:1px solid color-mix(in srgb,#f87171 28%,var(--border));
            background:color-mix(in srgb,#f87171 6%,transparent);display:flex;flex-wrap:wrap;align-items:center;gap:10px;justify-content:space-between;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__danger{background:color-mix(in srgb,#fef2f2 94%,transparent);}
        .rental-show__danger-copy{margin:0;max-width:42ch;font-size:var(--rs-font-sm);line-height:1.4;color:var(--muted);}
        .rental-show__danger-copy strong{display:block;color:var(--text);font-weight:600;margin-bottom:2px;font-size:var(--rs-font-sm);}
        .rental-show__del{
            display:inline-flex;align-items:center;gap:5px;padding:6px 10px;font-size:var(--rs-font-sm);font-weight:600;border-radius:8px;cursor:pointer;flex-shrink:0;
            border:1px solid color-mix(in srgb,#ef4444 50%,var(--border));background:color-mix(in srgb,#ef4444 12%,transparent);color:#f97373;
            transition:background .18s ease,transform .18s ease,border-color .18s ease;
        }
        .rental-show__del:hover{background:color-mix(in srgb,#ef4444 20%,transparent);border-color:color-mix(in srgb,#ef4444 65%,var(--border));transform:translateY(-1px);}
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__del{color:#b91c1c;}

        /* Tabs (CSS-only radios) */
        .rental-show__tab-input{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0;}
        .rental-show__tabs{margin-top:11px;}
        .rental-show__tablist{
            display:flex;flex-wrap:wrap;gap:5px;padding:3px;border-radius:var(--rs-radius);
            border:1px solid color-mix(in srgb,var(--border) 88%,transparent);
            background:color-mix(in srgb,var(--card) 94%,transparent);
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__tablist{background:#f5f5f4;}
        .rental-show__tab-btn{
            flex:1 1 auto;min-width:0;text-align:center;display:inline-flex;align-items:center;justify-content:center;gap:5px;
            padding:7px 10px;border-radius:var(--rs-radius-sm);font-size:10px;font-weight:600;letter-spacing:.02em;
            color:var(--muted);cursor:pointer;user-select:none;border:1px solid transparent;transition:background .15s ease,color .15s ease,border-color .15s ease,box-shadow .15s ease;
        }
        .rental-show__tab-btn i{font-size:10px;opacity:.85;}
        .rental-show__tab-btn:hover{color:var(--text);background:color-mix(in srgb,var(--primary) 8%,transparent);}
        #rental-show-tab-overview:checked ~ .rental-show__tablist label[for="rental-show-tab-overview"],
        #rental-show-tab-transaction:checked ~ .rental-show__tablist label[for="rental-show-tab-transaction"],
        #rental-show-tab-land:checked ~ .rental-show__tablist label[for="rental-show-tab-land"]{
            color:color-mix(in srgb,var(--primary) 72%,var(--text));
            background:color-mix(in srgb,var(--primary) 14%,var(--card));
            border-color:color-mix(in srgb,var(--primary) 28%,var(--border));
            box-shadow:0 1px 0 color-mix(in srgb,#fff 8%,transparent) inset;
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) #rental-show-tab-overview:checked ~ .rental-show__tablist label[for="rental-show-tab-overview"],
        :is(html[data-theme="light"],html[data-theme="light_blue"]) #rental-show-tab-transaction:checked ~ .rental-show__tablist label[for="rental-show-tab-transaction"],
        :is(html[data-theme="light"],html[data-theme="light_blue"]) #rental-show-tab-land:checked ~ .rental-show__tablist label[for="rental-show-tab-land"]{
            background:#fff;color:var(--text);box-shadow:0 1px 3px rgba(0,0,0,.06);
        }
        .rental-show__tabpanel{display:none;margin-top:10px;}
        #rental-show-tab-overview:checked ~ .rental-show__tabpanel--overview,
        #rental-show-tab-transaction:checked ~ .rental-show__tabpanel--transaction,
        #rental-show-tab-land:checked ~ .rental-show__tabpanel--land{display:block;}
        .rental-show__highlight{margin-top:0;}
        .rental-show__empty-muted{margin:0;font-size:var(--rs-font-sm);line-height:1.42;color:var(--muted);padding:10px;border-radius:var(--rs-radius-sm);
            border:1px dashed color-mix(in srgb,var(--border) 75%,transparent);background:color-mix(in srgb,var(--card) 92%,transparent);}
        .rental-show__overview-stack{display:flex;flex-direction:column;gap:10px;width:100%;}
        .rental-show__countdown{
            border-radius:var(--rs-radius);border:1px solid color-mix(in srgb,var(--primary) 28%,var(--border));
            background:linear-gradient(165deg,color-mix(in srgb,var(--primary) 8%,var(--card)),color-mix(in srgb,var(--card) 96%,transparent));
            overflow:hidden;box-shadow:0 10px 32px -24px color-mix(in srgb,var(--primary) 32%,#000);
        }
        :is(html[data-theme="light"],html[data-theme="light_blue"]) .rental-show__countdown{
            background:linear-gradient(165deg,color-mix(in srgb,var(--primary) 6%,#fff),#fafaf9);
            box-shadow:0 12px 32px -24px rgba(0,0,0,.08);
        }
        .rental-show__countdown-top{display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:10px;padding:10px 12px;border-bottom:1px solid color-mix(in srgb,var(--border) 82%,transparent);}
        .rental-show__countdown-kicker{font-size:8px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
        .rental-show__count-num{margin:3px 0 0;line-height:1.05;font-size:clamp(1.2rem,2.6vw,1.42rem);font-weight:700;letter-spacing:-.03em;color:color-mix(in srgb,var(--primary) 45%,var(--text));font-variant-numeric:tabular-nums;}
        .rental-show__count-num small{display:block;margin-top:3px;font-size:var(--rs-font-xs);font-weight:600;letter-spacing:0;color:var(--muted);text-transform:none;}
        .rental-show__count-next{margin:0;max-width:24ch;text-align:right;font-size:var(--rs-font-xs);line-height:1.38;color:var(--muted);font-weight:500;}
        .rental-show__count-next strong{display:block;color:var(--text);font-size:var(--rs-font-sm);margin-bottom:2px;font-weight:600;}
        .rental-show__meter-wrap{padding:0 12px 10px;}
        .rental-show__meter-lab{display:flex;justify-content:space-between;align-items:center;font-size:8px;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.035em;text-transform:uppercase;}
        .rental-show__meter{
            height:7px;border-radius:999px;overflow:hidden;
            background:color-mix(in srgb,var(--border) 70%,transparent);
            border:1px solid color-mix(in srgb,var(--border) 88%,transparent);
        }
        .rental-show__meter-fill{height:100%;border-radius:inherit;width:0;background:linear-gradient(90deg,color-mix(in srgb,var(--primary) 75%,var(--text)),var(--primary));transition:width .35s ease;}
        .rental-show__meter--hot .rental-show__meter-fill{background:linear-gradient(90deg,#f59e0b,#ef4444);}
    </style>

    <header class="rental-show__hero">
        <div class="rental-show__hero-glow" aria-hidden="true"></div>
        <div class="rental-show__hero-inner">
            <div class="rental-show__hero-top">
                <a class="rental-show__back" href="{{ route('account.rentals.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i>All rentals</a>
                @if($rental->business)
                    <span class="rental-show__biz-chip"><i class="fa fa-briefcase" aria-hidden="true"></i>{{ $rental->business->name }}</span>
                @endif
            </div>
            <h1 class="rental-show__headline">{{ $rental->property_type }}</h1>
            <div class="rental-show__pills">
                <span class="rental-show__pill"><i class="fa fa-clock" aria-hidden="true"></i>{{ $recurringTypes[$rental->recurring_type] ?? $rental->recurring_type }}</span>
                <span class="rental-show__pill"><i class="fa fa-calendar-days" aria-hidden="true"></i>Agreement until {{ $rental->agreement_valid_until_year }}</span>
                @if($rental->warehouse)
                    <span class="rental-show__pill"><i class="fa fa-code-branch" aria-hidden="true"></i>{{ $rental->warehouse->name }}</span>
                @endif
                @if($rental->due_date)
                    <span class="rental-show__pill"><i class="fa fa-calendar-day" aria-hidden="true"></i>Due {{ $rental->due_date->format('M j, Y') }}</span>
                @endif
                @if($rental->first_installment_due_date)
                    <span class="rental-show__pill"><i class="fa fa-receipt" aria-hidden="true"></i>First installment {{ $rental->first_installment_due_date->format('M j, Y') }}</span>
                @endif
            </div>
        </div>
    </header>

    <div class="rental-show__tabs">
        <input type="radio" name="rental-show-tab" id="rental-show-tab-overview" class="rental-show__tab-input" checked>
        <input type="radio" name="rental-show-tab" id="rental-show-tab-transaction" class="rental-show__tab-input">
        <input type="radio" name="rental-show-tab" id="rental-show-tab-land" class="rental-show__tab-input">

        <div class="rental-show__tablist" role="tablist" aria-label="Rental detail sections">
            <label id="rental-show-tab-label-overview" for="rental-show-tab-overview" class="rental-show__tab-btn" role="tab"><i class="fa fa-layer-group" aria-hidden="true"></i>Overview</label>
            <label id="rental-show-tab-label-transaction" for="rental-show-tab-transaction" class="rental-show__tab-btn" role="tab"><i class="fa fa-money-bill-wave" aria-hidden="true"></i>Transaction details</label>
            <label id="rental-show-tab-label-land" for="rental-show-tab-land" class="rental-show__tab-btn" role="tab"><i class="fa fa-map-location-dot" aria-hidden="true"></i>Land details</label>
        </div>

        <div class="rental-show__tabpanel rental-show__tabpanel--overview" role="tabpanel" id="rental-show-panel-overview" aria-labelledby="rental-show-tab-label-overview">
            <div class="rental-show__overview-stack">
            @if($nextPaymentInsight)
                <section class="rental-show__countdown" aria-labelledby="rental-countdown-heading">
                    <div class="rental-show__countdown-top">
                        <div>
                            <span class="rental-show__countdown-kicker" id="rental-countdown-heading">Next recurring payment</span>
                            <div class="rental-show__count-num" aria-live="polite">
                                @if($nextPaymentInsight['days_until'] === 0)
                                    Today <small>payment due</small>
                                @elseif($nextPaymentInsight['days_until'] === 1)
                                    1 <small>day left</small>
                                @else
                                    {{ $nextPaymentInsight['days_until'] }} <small>days left</small>
                                @endif
                            </div>
                        </div>
                        <div>
                            <p class="rental-show__count-next">
                                <strong>{{ $nextPaymentInsight['next_date']->format('M j, Y') }}</strong>
                                <span style="display:block;margin-top:4px;">{{ $recurringTypes[$rental->recurring_type] ?? '' }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="rental-show__meter-wrap">
                        <div class="rental-show__meter-lab"><span>Approaching due date</span><span>{{ $nextPaymentInsight['progress_percent'] }}%</span></div>
                        <div class="{{ $nextPaymentInsight['progress_percent'] >= 85 ? 'rental-show__meter rental-show__meter--hot' : 'rental-show__meter' }}"
                            role="progressbar"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="{{ (int) round($nextPaymentInsight['progress_percent']) }}"
                            aria-label="Progress within the countdown window until the next recurring payment">
                            <div class="rental-show__meter-fill" style="width: {{ $nextPaymentInsight['progress_percent'] }}%;"></div>
                        </div>
                    </div>
                </section>
            @else
                <div class="rental-show__panel" style="box-shadow:none;">
                    <div class="rental-show__panel-body">
                        <p class="rental-show__empty-muted" style="border:none;background:transparent;padding:4px 0;"><strong style="display:block;color:var(--text);margin-bottom:3px;font-weight:600;font-size:var(--rs-font-sm);">No countdown yet</strong>Add a due date or first installment date under <strong>Transaction details</strong> to estimate the next rent payment.</p>
                    </div>
                </div>
            @endif
            <div class="rental-show__highlight">
                <div class="rental-show__cost-card">
                    <div class="rental-show__cost-lab">Recurring cost</div>
                    <div class="rental-show__cost-val">
                        @if($detailCurrency)<span class="rental-show__curr">{{ $detailCurrency }}</span>@endif{{ number_format((float) $rental->recurring_cost, 2, '.', ',') }}
                    </div>
                    <div class="rental-show__mini-sub" style="margin-top:4px;opacity:.88;">Per billing period · {{ $recurringTypes[$rental->recurring_type] ?? $rental->recurring_type }}</div>
                </div>
                <div class="rental-show__mini-stats">
                    @if($rental->key_money !== null && (float) $rental->key_money > 0)
                        <div class="rental-show__key-card" role="group" aria-labelledby="rental-keymoney-label">
                            <span class="rental-show__key-ico-wrap" aria-hidden="true"><i class="fa fa-key"></i></span>
                            <div class="rental-show__key-inner">
                                <div class="rental-show__key-lab" id="rental-keymoney-label">Key money</div>
                                <div class="rental-show__key-val">
                                    @if($detailCurrency)<span class="rental-show__key-curr">{{ $detailCurrency }}</span>@endif{{ number_format((float) $rental->key_money, 2, '.', ',') }}
                                </div>
                                <p class="rental-show__key-note">One-off amount on the lease · not included in recurring cost above.</p>
                            </div>
                        </div>
                    @endif
                    @if($rental->remind_before_days !== null && (int) $rental->remind_before_days > 0)
                        <div class="rental-show__mini">
                            <span class="rental-show__mini-ico" aria-hidden="true"><i class="fa fa-bell"></i></span>
                            <div>
                                <span class="rental-show__mini-txt">{{ (int) $rental->remind_before_days }} day{{ (int) $rental->remind_before_days === 1 ? '' : 's' }} ahead</span>
                                <span class="rental-show__mini-sub">Renewal reminder</span>
                            </div>
                        </div>
                    @endif
                    @if(($rental->key_money === null || (float) $rental->key_money <= 0) && ($rental->remind_before_days === null || (int) $rental->remind_before_days <= 0))
                        <div class="rental-show__mini">
                            <span class="rental-show__mini-ico" aria-hidden="true"><i class="fa fa-circle-info"></i></span>
                            <div>
                                <span class="rental-show__mini-txt">Open <strong>Transaction details</strong> for billing and <strong>Land details</strong> for premises &amp; landlord.</span>
                                <span class="rental-show__mini-sub">Quick summary</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </div>

        <div class="rental-show__tabpanel rental-show__tabpanel--transaction" role="tabpanel" id="rental-show-panel-transaction" aria-labelledby="rental-show-tab-label-transaction">
            <div class="rental-show__panels" style="grid-template-columns:1fr;">
                <section class="rental-show__panel" aria-labelledby="rental-amounts-heading">
                    <h2 class="rental-show__panel-h" id="rental-amounts-heading"><i class="fa fa-coins" aria-hidden="true"></i>Amounts</h2>
                    <div class="rental-show__panel-body">
                        <dl class="rental-show__dl rental-show__dl--2">
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Recurring cost</dt>
                                <dd class="rental-show__dd rental-show__dd--money">
                                    @if($detailCurrency)<span class="rental-show__curr" style="font-size:9px;">{{ $detailCurrency }}</span>@endif{{ number_format((float) $rental->recurring_cost, 2, '.', ',') }}
                                    <span class="rental-show__mini-sub" style="display:block;margin-top:4px;font-weight:600;">{{ $recurringTypes[$rental->recurring_type] ?? $rental->recurring_type }}</span>
                                </dd>
                            </div>
                            @if($rental->key_money !== null && (float) $rental->key_money > 0)
                                <div class="rental-show__kv">
                                    <dt class="rental-show__dt">Key money</dt>
                                    <dd class="rental-show__dd rental-show__dd--money">
                                        @if($detailCurrency)<span class="rental-show__curr" style="font-size:9px;">{{ $detailCurrency }}</span>@endif{{ number_format((float) $rental->key_money, 2, '.', ',') }}
                                        <span class="rental-show__mini-sub" style="display:block;margin-top:4px;font-weight:600;">One-time</span>
                                    </dd>
                                </div>
                            @else
                                <div class="rental-show__kv">
                                    <dt class="rental-show__dt">Key money</dt>
                                    <dd class="rental-show__dd rental-show__dd--soft">—</dd>
                                </div>
                            @endif
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Billing cadence</dt>
                                <dd class="rental-show__dd">{{ $recurringTypes[$rental->recurring_type] ?? $rental->recurring_type }}</dd>
                            </div>
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Agreement ends (year)</dt>
                                <dd class="rental-show__dd">{{ $rental->agreement_valid_until_year }}</dd>
                            </div>
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Renewal reminder</dt>
                                <dd class="rental-show__dd">
                                    @if($rental->remind_before_days !== null && (int) $rental->remind_before_days > 0)
                                        {{ (int) $rental->remind_before_days }} day{{ (int) $rental->remind_before_days === 1 ? '' : 's' }} before end
                                    @else
                                        <span class="rental-show__dd--soft">Not set</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Due date</dt>
                                <dd class="{{ $rental->due_date ? 'rental-show__dd' : 'rental-show__dd rental-show__dd--soft' }}">{{ $rental->due_date ? $rental->due_date->format('M j, Y') : 'Not set' }}</dd>
                            </div>
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">First installment due</dt>
                                <dd class="{{ $rental->first_installment_due_date ? 'rental-show__dd' : 'rental-show__dd rental-show__dd--soft' }}">{{ $rental->first_installment_due_date ? $rental->first_installment_due_date->format('M j, Y') : 'Not set' }}</dd>
                            </div>
                            @if($rental->deductAccount)
                                <div class="rental-show__kv" style="grid-column:1/-1;">
                                    <dt class="rental-show__dt">Debit account</dt>
                                    <dd class="rental-show__dd rental-show__dd--soft">{{ $rental->deductAccount->deductOptionLabel() }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </section>
                @if($rental->notes)
                    <section class="rental-show__panel" aria-labelledby="rental-tx-notes-heading">
                        <h2 class="rental-show__panel-h" id="rental-tx-notes-heading"><i class="fa fa-note-sticky" aria-hidden="true"></i>Internal notes</h2>
                        <div class="rental-show__panel-body">
                            <p class="rental-show__prose" style="margin-top:0;">{{ $rental->notes }}</p>
                        </div>
                    </section>
                @endif
            </div>
        </div>

        <div class="rental-show__tabpanel rental-show__tabpanel--land" role="tabpanel" id="rental-show-panel-land" aria-labelledby="rental-show-tab-label-land">
            <div class="rental-show__panels">
                <section class="rental-show__panel" aria-labelledby="rental-premises-heading">
                    <h2 class="rental-show__panel-h" id="rental-premises-heading"><i class="fa fa-building" aria-hidden="true"></i>Premises &amp; use</h2>
                    <div class="rental-show__panel-body">
                        <dl class="rental-show__dl rental-show__dl--2">
                            <div class="rental-show__kv">
                                <dt class="rental-show__dt">Property type</dt>
                                <dd class="rental-show__dd">{{ $rental->property_type }}</dd>
                            </div>
                            @if($rental->warehouse)
                                <div class="rental-show__kv">
                                    <dt class="rental-show__dt">Branch / site</dt>
                                    <dd class="rental-show__dd">{{ $rental->warehouse->name }}</dd>
                                </div>
                            @endif
                            @if($rental->business)
                                <div class="rental-show__kv" style="grid-column:1/-1;">
                                    <dt class="rental-show__dt">Business</dt>
                                    <dd class="rental-show__dd">{{ $rental->business->name }}</dd>
                                </div>
                            @endif
                        </dl>
                        @if($rental->purpose)
                            <div>
                                <div class="rental-show__dt" style="margin-top:2px;">Purpose / use</div>
                                <p class="rental-show__prose">{{ $rental->purpose }}</p>
                            </div>
                        @else
                            <p class="rental-show__empty-muted" style="margin-top:10px;">No purpose or use description recorded.</p>
                        @endif
                    </div>
                </section>

                @if($rental->landlord)
                    <section class="rental-show__panel" aria-labelledby="rental-landlord-heading">
                        <h2 class="rental-show__panel-h" id="rental-landlord-heading"><i class="fa fa-address-book" aria-hidden="true"></i>Landlord</h2>
                        <div class="rental-show__panel-body">
                            <div class="rental-show__landlord-profile">
                                <div class="rental-show__avatar" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(trim((string) $rental->landlord->name), 0, 1) ?: '?') }}</div>
                                <div>
                                    <p class="rental-show__avatar-name">{{ $rental->landlord->name }}</p>
                                    <p class="rental-show__avatar-role">Address book contact</p>
                                </div>
                            </div>
                            <div class="rental-show__contact-rows">
                                @if($rental->landlord->email)
                                    <div class="rental-show__row">
                                        <span class="rental-show__row-ico" aria-hidden="true"><i class="fa fa-envelope"></i></span>
                                        <div class="rental-show__row-body">
                                            <div class="rental-show__row-lab">Email</div>
                                            <p class="rental-show__row-val"><a href="mailto:{{ $rental->landlord->email }}">{{ $rental->landlord->email }}</a></p>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->landlord->phone)
                                    <div class="rental-show__row">
                                        <span class="rental-show__row-ico" aria-hidden="true"><i class="fa fa-phone"></i></span>
                                        <div class="rental-show__row-body">
                                            <div class="rental-show__row-lab">Phone</div>
                                            <p class="rental-show__row-val"><a href="tel:{{ preg_replace('/\s+/', '', $rental->landlord->phone) }}">{{ $rental->landlord->phone }}</a></p>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->landlord->street_address)
                                    <div class="rental-show__row">
                                        <span class="rental-show__row-ico" aria-hidden="true"><i class="fa fa-location-dot"></i></span>
                                        <div class="rental-show__row-body">
                                            <div class="rental-show__row-lab">Address</div>
                                            <p class="rental-show__row-val">{{ $rental->landlord->street_address }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->landlord->bank_account_details)
                                    <div class="rental-show__row">
                                        <span class="rental-show__row-ico" aria-hidden="true"><i class="fa fa-building-columns"></i></span>
                                        <div class="rental-show__row-body">
                                            <div class="rental-show__row-lab">Bank / payment</div>
                                            <p class="rental-show__row-val" style="white-space:pre-wrap;">{{ $rental->landlord->bank_account_details }}</p>
                                        </div>
                                    </div>
                                @endif
                                @if($rental->landlord->notes)
                                    <div class="rental-show__row">
                                        <span class="rental-show__row-ico" aria-hidden="true"><i class="fa fa-note-sticky"></i></span>
                                        <div class="rental-show__row-body">
                                            <div class="rental-show__row-lab">Contact notes</div>
                                            <p class="rental-show__row-val" style="white-space:pre-wrap;font-weight:500;">{{ $rental->landlord->notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>
                @else
                    <section class="rental-show__panel" aria-labelledby="rental-landlord-heading">
                        <h2 class="rental-show__panel-h" id="rental-landlord-heading"><i class="fa fa-address-book" aria-hidden="true"></i>Landlord</h2>
                        <div class="rental-show__panel-body">
                            <p class="rental-show__empty-muted">No landlord linked to this rental. Add one when editing the rental if needed.</p>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>

    <footer class="rental-show__danger">
        <p class="rental-show__danger-copy"><strong>Remove rental</strong> — Deletes this lease from your portfolio; the landlord contact remains in your address book.</p>
        <form method="post" action="{{ route('account.rentals.destroy', $rental) }}" style="margin:0;" onsubmit="return confirm('Remove this rental record?');">
            @csrf
            @method('delete')
            <button type="submit" class="rental-show__del"><i class="fa fa-trash-can" aria-hidden="true"></i>Remove rental</button>
        </form>
    </footer>
</div>
@endsection
