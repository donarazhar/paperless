<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') – Al Azhar Paperless</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    /* ════════════════════════════════════════════
       DESIGN TOKENS
    ════════════════════════════════════════════ */
    :root {
        --blue:        #1a73e8;
        --blue-dark:   #1557b0;
        --blue-light:  #e8f0fe;
        --blue-active: #d3e3fd;

        --surface:  #ffffff;
        --bg:       #f6f8fc;
        --border:   #e2e8f0;
        --text:     #202124;
        --text-2:   #5f6368;
        --text-3:   #94a3b8;

        --red:      #d93025;
        --green:    #188038;
        --amber:    #e37400;
        --purple:   #7e22ce;

        --sidebar-w:  260px;
        --header-h:   64px;
        --radius-pill: 0 100px 100px 0;
        --shadow-card: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.04);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
        font-family: 'Inter', -apple-system, sans-serif;
        background: var(--bg);
        color: var(--text);
        height: 100%;
    }

    body { overflow: hidden; }
    a    { text-decoration: none; color: inherit; }

    /* ════════════════════════════════════════════
       HEADER
    ════════════════════════════════════════════ */
    .g-header {
        position: fixed; top: 0; left: 0; right: 0;
        height: var(--header-h);
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex; align-items: center;
        padding: 0 1rem; gap: .75rem;
        z-index: 1000;
    }

    /* Hamburger */
    .g-hamburger {
        width: 40px; height: 40px;
        border: none; background: none;
        border-radius: 50%; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        color: var(--text-2); font-size: 1.35rem;
        transition: background .15s; flex-shrink: 0;
    }
    .g-hamburger:hover { background: #f1f3f4; }

    /* Brand */
    .g-brand {
        display: flex; align-items: center; gap: .55rem;
        flex-shrink: 0; text-decoration: none;
    }
    .g-brand img {
        width: 34px; height: 34px; border-radius: 8px;
        object-fit: contain; padding: 3px;
        background: var(--blue-light);
    }
    .g-brand-text { line-height: 1.2; }
    .g-brand-name {
        font-size: .88rem; font-weight: 800;
        color: var(--text); letter-spacing: -.015em;
    }
    .g-brand-sub {
        font-size: .6rem; font-weight: 500;
        color: var(--text-2);
    }

    /* Search */
    .g-search {
        flex: 1; max-width: 580px; margin: 0 auto;
        position: relative;
    }
    .g-search-input {
        width: 100%;
        background: #f1f3f4;
        border: 1.5px solid transparent;
        border-radius: 24px;
        padding: .6rem 1.25rem .6rem 2.75rem;
        font-size: .9rem; font-family: inherit;
        color: var(--text); outline: none;
        transition: all .2s;
    }
    .g-search-input:focus {
        background: var(--surface);
        border-color: var(--blue);
        box-shadow: 0 1px 6px rgba(26,115,232,.2);
    }
    .g-search-icon {
        position: absolute; left: .9rem; top: 50%;
        transform: translateY(-50%);
        color: var(--text-2); font-size: .9rem;
        pointer-events: none;
    }

    /* Profile area */
    .g-profile {
        margin-left: auto; flex-shrink: 0;
        position: relative;
    }
    .g-profile-trigger {
        display: flex; align-items: center; gap: .6rem;
        padding: .3rem .55rem .3rem .3rem;
        border-radius: 100px;
        border: none; background: none;
        cursor: pointer;
        transition: background .15s;
    }
    .g-profile-trigger:hover { background: #f1f3f4; }
    .g-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--blue);
        color: #fff; font-size: .88rem; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; flex-shrink: 0;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1.5px rgba(26,115,232,.35);
    }
    .g-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .g-profile-info {
        line-height: 1.2; text-align: left;
        max-width: 160px;
    }
    .g-profile-name {
        font-size: .82rem; font-weight: 700;
        color: var(--text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 140px;
    }
    .g-profile-role {
        font-size: .68rem; font-weight: 500;
        color: var(--text-2);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 140px;
        text-transform: capitalize;
    }
    .g-profile-chevron {
        font-size: .65rem; color: var(--text-2); flex-shrink: 0;
    }
    /* Mobile: sembunyikan teks role, tampilkan hanya nama */
    @media (max-width: 480px) {
        .g-profile-info { max-width: 90px; }
        .g-profile-name { max-width: 90px; font-size: .78rem; }
        .g-profile-role { display: none; }
        .g-profile-chevron { display: none; }
    }

    /* Profile dropdown */
    .g-profile-dd {
        position: absolute; top: calc(100% + 8px); right: 0;
        min-width: 220px; max-width: 260px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,.12);
        padding: .5rem;
        opacity: 0; visibility: hidden;
        transform: translateY(6px);
        transition: all .2s cubic-bezier(.16,1,.3,1);
        z-index: 200;
    }
    .g-profile.open .g-profile-dd {
        opacity: 1; visibility: visible; transform: translateY(0);
    }
    .g-dd-info {
        padding: .65rem .75rem .5rem;
        border-bottom: 1px solid var(--border);
        margin-bottom: .35rem;
    }
    .g-dd-name  { 
        font-size: .85rem; font-weight: 700; color: var(--text); 
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .g-dd-email { 
        font-size: .72rem; color: var(--text-2); margin-top: 2px;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .g-dd-item {
        display: flex; align-items: center; gap: .6rem;
        padding: .5rem .75rem;
        border-radius: 8px;
        font-size: .84rem; font-weight: 500; color: var(--text);
        cursor: pointer; transition: background .12s;
        text-decoration: none; border: none; background: none;
        width: 100%; text-align: left;
    }
    .g-dd-item:hover { background: #f1f3f4; color: var(--text); }
    .g-dd-item.danger { color: var(--red); }
    .g-dd-item.danger:hover { background: #fce8e6; }
    .g-dd-sep { height: 1px; background: var(--border); margin: .35rem 0; }

    /* ════════════════════════════════════════════
       APP SHELL
    ════════════════════════════════════════════ */
    .g-app {
        display: flex;
        height: 100vh;
        padding-top: var(--header-h);
    }

    /* ════════════════════════════════════════════
       SIDEBAR OVERLAY (mobile)
    ════════════════════════════════════════════ */
    .g-overlay {
        display: none;
        position: fixed; inset: 0;
        top: var(--header-h);
        background: rgba(32,33,36,.5);
        z-index: 900;
        opacity: 0; pointer-events: none;
        transition: opacity .25s;
    }
    .g-overlay.show { opacity: 1; pointer-events: auto; }

    /* ════════════════════════════════════════════
       SIDEBAR – Gmail style
    ════════════════════════════════════════════ */
    .g-sidebar {
        width: var(--sidebar-w);
        height: 100%;
        background: var(--surface);
        display: flex; flex-direction: column;
        overflow-y: auto; overflow-x: hidden;
        flex-shrink: 0;
        padding: .5rem 0 1.5rem;
        scrollbar-width: thin;
        scrollbar-color: #dadce0 transparent;
        transition: transform .25s cubic-bezier(.4,0,.2,1);
    }
    .g-sidebar::-webkit-scrollbar { width: 4px; }
    .g-sidebar::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 4px; }

    /* Compose button */
    .g-compose-wrap { padding: .5rem 1rem 1.25rem; flex-shrink: 0; }
    .g-compose-btn {
        display: inline-flex; align-items: center; gap: .75rem;
        padding: 1rem 1.5rem 1rem 1.1rem;
        background: var(--surface);
        border: none; border-radius: 18px;
        font-size: .875rem; font-weight: 600; color: var(--text);
        cursor: pointer; text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.14), 0 4px 12px rgba(0,0,0,.08);
        transition: box-shadow .2s, transform .15s;
    }
    .g-compose-btn:hover {
        box-shadow: 0 2px 6px rgba(0,0,0,.16), 0 6px 20px rgba(0,0,0,.1);
        transform: translateY(-1px); color: var(--text);
        background: #f8f9fa;
    }
    .g-compose-icon {
        width: 32px; height: 32px; border-radius: 10px;
        background: linear-gradient(135deg, var(--blue), #4285f4);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .g-compose-icon i { color: #fff; font-size: .9rem; }

    /* Nav list */
    .g-nav { list-style: none; padding: 0; margin: 0; }
    .g-nav-item { margin-bottom: 1px; }

    /* Nav link — Gmail pill style (full-rounded right) */
    .g-nav-link {
        display: flex; align-items: center; gap: .85rem;
        padding: .5rem 1rem .5rem 1.25rem;
        border-radius: var(--radius-pill);
        margin-right: .75rem;
        font-size: .875rem; font-weight: 500;
        color: var(--text); text-decoration: none;
        min-height: 40px;
        transition: background .12s;
        cursor: pointer;
    }
    .g-nav-link:hover { background: #f1f3f4; color: var(--text); }
    .g-nav-link.active {
        background: var(--blue-active);
        color: #041e49; font-weight: 700;
    }
    .g-nav-link i {
        font-size: 1.05rem; width: 1.2rem; text-align: center;
        flex-shrink: 0; color: #444746;
    }
    .g-nav-link.active i { color: #041e49; }

    /* Badge count — bold number, Gmail-style */
    .g-nav-badge {
        margin-left: auto; font-size: .82rem;
        font-weight: 700; color: var(--text);
        padding: 0 .25rem; flex-shrink: 0;
        min-width: 1.5rem; text-align: right;
    }
    .g-nav-badge.red   { color: var(--red); }
    .g-nav-badge.amber { color: var(--amber); }

    /* Sidebar divider */
    .g-sb-divider {
        height: 1px; background: var(--border);
        margin: .5rem 1rem .5rem .75rem;
    }

    /* Collapse toggle (Master Data) */
    .g-nav-toggle {
        display: flex; align-items: center; gap: .85rem;
        padding: .5rem 1rem .5rem 1.25rem;
        border-radius: var(--radius-pill);
        margin-right: .75rem;
        font-size: .875rem; font-weight: 500; color: var(--text);
        min-height: 40px; cursor: pointer;
        background: none; border: none;
        width: calc(100% - .75rem); text-align: left;
        transition: background .12s;
    }
    .g-nav-toggle:hover { background: #f1f3f4; }
    .g-nav-toggle i.nav-icon { font-size: 1.05rem; width: 1.2rem; text-align: center; color: #444746; flex-shrink: 0; }
    .g-nav-toggle .g-chevron { margin-left: auto; font-size: .7rem; color: var(--text-2); transition: transform .3s; }
    .g-nav-toggle.open .g-chevron { transform: rotate(180deg); }

    /* Sub items */
    .g-nav-sub .g-nav-link { padding-left: 2.5rem; font-size: .83rem; }

    /* Sidebar footer */
    .g-sb-footer { margin-top: auto; padding-top: .25rem; }

    /* ════════════════════════════════════════════
       MAIN CONTENT AREA
    ════════════════════════════════════════════ */
    .g-main {
        flex: 1; min-width: 0;
        display: flex; flex-direction: column;
        overflow: hidden;
        background: var(--surface);
        margin: .5rem .5rem 0 .5rem;
        border-radius: 16px 16px 0 0;
        border: 1px solid var(--border);
        box-shadow: 0 0 0 0 transparent;
    }
    .g-content {
        flex: 1; overflow-y: auto; overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: #dadce0 transparent;
    }
    .g-content::-webkit-scrollbar { width: 6px; }
    .g-content::-webkit-scrollbar-thumb { background: #dadce0; border-radius: 6px; }

    /* ════════════════════════════════════════════
       SHARED CHILD-VIEW COMPONENTS
    ════════════════════════════════════════════ */

    /* ── Page Hero Header ── */
    .inbox-hero {
        padding: 1.25rem 1.5rem 1rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
        flex-shrink: 0;
    }
    .hero-title {
        font-size: 1.25rem; font-weight: 800;
        color: var(--text); letter-spacing: -.02em;
    }
    .hero-sub { font-size: .82rem; color: var(--text-2); margin-top: .15rem; }
    .stat-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        background: var(--blue-light); color: var(--blue);
        font-size: .75rem; font-weight: 700;
        padding: .3rem .85rem; border-radius: 100px;
    }

    /* ── Filter card ── */
    .filter-card {
        padding: .875rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: #fafbfd;
    }
    .f-label {
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em;
        color: var(--text-2); margin-bottom: .3rem; display: block;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        height: 40px; border: 1.5px solid var(--border);
        border-radius: .65rem; background: var(--surface);
        font-size: .875rem; padding: 0 .9rem;
        box-shadow: none !important; outline: none;
        transition: border-color .15s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(26,115,232,.1) !important;
    }
    .btn-filter {
        display: inline-flex; align-items: center; gap: .45rem;
        height: 40px; padding: 0 1.1rem;
        background: var(--blue); color: #fff;
        border: none; border-radius: .65rem;
        font-size: .84rem; font-weight: 600; font-family: inherit;
        cursor: pointer; transition: background .15s, box-shadow .15s;
        text-decoration: none;
    }
    .btn-filter:hover { background: var(--blue-dark); color: #fff; box-shadow: 0 2px 8px rgba(26,115,232,.3); }
    .btn-reset {
        display: inline-flex; align-items: center; gap: .45rem;
        height: 40px; padding: 0 1.1rem;
        background: var(--surface); color: var(--text-2);
        border: 1.5px solid var(--border); border-radius: .65rem;
        font-size: .84rem; font-weight: 600; font-family: inherit;
        cursor: pointer; transition: all .15s; text-decoration: none;
    }
    .btn-reset:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-light); }

    /* ── Table Container ── */
    .table-container {
        padding: .75rem 1.25rem 1.25rem;
        flex: 1; overflow-x: auto;
    }

    /* ── Inbox table (used in index, arsip, outbox, etc.) ── */
    .inbox-table {
        width: 100%; border-collapse: collapse;
    }
    .inbox-table thead th {
        font-size: .7rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: .07em;
        color: var(--text-2);
        border-bottom: 1.5px solid var(--border);
        padding: .5rem .75rem .75rem;
        white-space: nowrap;
        background: transparent;
    }
    .inbox-table tbody tr {
        border-bottom: 1px solid #f1f3f4;
        transition: background .1s;
    }
    .inbox-table tbody tr:hover { background: #f8f9fa; }
    .inbox-table tbody td {
        padding: .75rem .75rem;
        font-size: .875rem; vertical-align: middle;
        color: var(--text);
    }
    .subject-cell .s-title {
        font-size: .875rem; font-weight: 600; color: var(--text);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 320px;
    }
    .subject-cell .s-num { font-size: .72rem; color: var(--text-2); margin-top: 2px; }
    .date-cell .d-date { font-size: .82rem; color: var(--text-2); white-space: nowrap; }
    .person-cell .p-name {
        font-size: .84rem; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis; max-width: 160px;
    }

    /* ── Badges ── */
    .g-badge {
        display: inline-flex; align-items: center; gap: .2rem;
        font-size: .65rem; font-weight: 700;
        padding: .15rem .5rem; border-radius: 4px;
        letter-spacing: .04em; text-transform: uppercase; flex-shrink: 0;
    }
    .badge-ext     { background: #fce7f3; color: #9d174d; border: 1px solid #fbcfe8; }
    .badge-agenda  { background: var(--blue-light); color: var(--blue); border: 1px solid #bfdbfe; }
    .badge-pending { background: #fef2f2; color: var(--red); border: 1px solid #fecaca; }
    .badge-done    { background: #f0fdf4; color: var(--green); border: 1px solid #bbf7d0; }
    .badge-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .badge-draft   { background: #fef9c3; color: #854d0e; border: 1px solid #fde68a; }

    /* ── Empty state ── */
    .g-empty {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        text-align: center; padding: 4rem 2rem;
        gap: .75rem; color: var(--text-2);
    }
    .g-empty i { font-size: 3.5rem; color: #dadce0; }
    .g-empty h3 { font-size: 1.05rem; font-weight: 700; color: var(--text-2); margin: 0; }
    .g-empty p  { font-size: .84rem; color: var(--text-3); margin: 0; max-width: 280px; }

    /* ── DataTables overrides ── */
    .dataTables_wrapper .dataTables_length label {
        display: flex; flex-direction: column;
        align-items: flex-start; margin: 0; width: 100%;
    }
    .dataTables_wrapper .dataTables_length select {
        height: 40px; border-radius: .65rem;
        border: 1.5px solid var(--border);
        background: #fafbfd; font-size: .855rem;
        padding: 0 .9rem; outline: none; cursor: pointer; color: var(--text);
    }
    .dataTables_wrapper .dataTables_length select:focus {
        border-color: var(--blue);
        box-shadow: 0 0 0 3px rgba(26,115,232,.1);
    }
    .dataTables_wrapper .dataTables_info { font-size: .8rem; color: var(--text-3); }
    .dataTables_wrapper .dataTables_paginate .paginate_button { padding: 0 !important; margin: 0 !important; }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current a:hover {
        background: var(--blue) !important; color: #fff !important;
        border-color: var(--blue) !important; border-radius: .4rem !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button a { border-radius: .4rem !important; font-size: .82rem; }
    .dataTables_bottom {
        display: flex; align-items: center;
        justify-content: space-between; gap: 1rem;
        flex-wrap: wrap; margin-top: 1rem;
    }

    /* ── Action buttons in tables ── */
    .btn-act {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px;
        font-size: .85rem; border: none; cursor: pointer;
        transition: background .12s, color .12s; text-decoration: none;
    }
    .btn-act-view { background: var(--blue-light); color: var(--blue); }
    .btn-act-view:hover { background: #bfdbfe; color: var(--blue-dark); }
    .btn-act-edit { background: #fef9c3; color: #854d0e; }
    .btn-act-edit:hover { background: #fde68a; }
    .btn-act-del  { background: #fef2f2; color: var(--red); }
    .btn-act-del:hover  { background: #fecaca; }
    .btn-act-disp { background: #fdf4ff; color: var(--purple); }
    .btn-act-disp:hover { background: #ede9fe; }

    /* ── Dashboard: greeting banner ── */
    .greeting-banner {
        background: linear-gradient(135deg, #e8f0fe 0%, #f0f4ff 100%);
        border-bottom: 1px solid #c5d8ff;
        padding: 1.5rem 1.5rem 1.25rem;
        display: flex; align-items: flex-start;
        justify-content: space-between; gap: 1rem;
    }
    .gb-emoji { font-size: 2rem; line-height: 1; display: block; margin-bottom: .5rem; }
    .greeting-banner h3 {
        font-size: 1.2rem; font-weight: 800; color: var(--text);
        letter-spacing: -.02em; margin-bottom: .35rem;
    }
    .greeting-banner p { font-size: .84rem; color: var(--text-2); margin-bottom: .75rem; }
    .gb-chip {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(26,115,232,.12); color: var(--blue);
        border: 1px solid rgba(26,115,232,.2);
        font-size: .76rem; font-weight: 700;
        padding: .3rem .85rem; border-radius: 100px;
    }
    .gb-right { display: flex; align-items: center; color: var(--blue); }

    /* ── Dashboard: stat cards ── */
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        text-decoration: none; color: inherit;
        transition: box-shadow .2s, transform .15s;
        box-shadow: var(--shadow-card);
    }
    .stat-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,.1);
        transform: translateY(-2px); color: inherit;
    }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; flex-shrink: 0;
    }
    .stat-label { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-2); }
    .stat-value { font-size: 1.8rem; font-weight: 800; color: var(--text); line-height: 1.1; letter-spacing: -.03em; margin: .1rem 0; }
    .stat-sub   { font-size: .75rem; color: var(--text-3); }

    /* ── Dashboard: panel card ── */
    .panel-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        box-shadow: var(--shadow-card);
    }
    .section-header {
        display: flex; align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }
    .section-title {
        font-size: .88rem; font-weight: 700; color: var(--text);
        display: flex; align-items: center; gap: .5rem;
    }
    .section-title i { color: var(--blue); }

    /* ── Dashboard: notif items ── */
    .notif-item {
        display: flex; align-items: flex-start; gap: .875rem;
        padding: .75rem; border-radius: 10px;
        text-decoration: none; color: inherit;
        transition: background .12s; margin-bottom: .25rem;
    }
    .notif-item:hover { background: #f8f9fa; }
    .notif-icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: .95rem; flex-shrink: 0;
    }
    .notif-title { font-size: .84rem; font-weight: 700; color: var(--text); margin-bottom: 2px; }
    .notif-text  { font-size: .78rem; color: var(--text-2); margin-bottom: 2px; }
    .notif-time  { font-size: .72rem; color: var(--text-3); display: flex; align-items: center; gap: .3rem; }

    /* Dashboard padding wrapper */
    .dashboard-body { padding: 1.25rem 1.5rem; }

    /* ════════════════════════════════════════════
       RESPONSIVE
    ════════════════════════════════════════════ */

    /* Tablet (≤ 1024px): sidebar hidden by default */
    @media (max-width: 1024px) {
        .g-search { display: none; }
        .g-overlay { display: block; }
        .g-sidebar {
            position: fixed;
            top: var(--header-h); left: 0; bottom: 0;
            z-index: 950;
            transform: translateX(-100%);
            box-shadow: 4px 0 24px rgba(0,0,0,.12);
        }
        .g-sidebar.open { transform: translateX(0); }
        .g-main { margin: .25rem .25rem 0 .25rem; border-radius: 12px 12px 0 0; }
    }

    /* Mobile (≤ 640px) */
    @media (max-width: 640px) {
        .g-main { margin: 0; border-radius: 0; border-left: none; border-right: none; }
        .g-brand-text { display: none; }
        body { overflow: auto; }
        .g-app { height: auto; min-height: calc(100vh - var(--header-h)); }
        .g-content { overflow: visible; }
    }

    /* Desktop (> 1024px): always show search */
    @media (min-width: 1025px) {
        .g-search { display: block; }
        .g-hamburger { display: none; }
    }

    /* ── Utility ── */
    .text-truncate-2 {
        display: -webkit-box; -webkit-line-clamp: 2;
        -webkit-box-orient: vertical; overflow: hidden;
    }
    </style>
    @stack('styles')
</head>
<body>
@php $role = Auth::user()->role ?? ''; @endphp

<!-- ════ HEADER ════ -->
<header class="g-header">
    <button class="g-hamburger" id="gHamburger" aria-label="Menu">
        <i class="bi bi-list" id="gHamIcon"></i>
    </button>

    <a href="{{ route('letters.inbound') }}" class="g-brand">
        <img src="{{ asset('img/logo.png') }}" alt="Logo">
        <div class="g-brand-text">
            <div class="g-brand-name">Al Azhar Paperless</div>
            <div class="g-brand-sub">YPI Al Azhar</div>
        </div>
    </a>

    <div class="g-search">
        <i class="bi bi-search g-search-icon"></i>
        <input type="text" class="g-search-input" placeholder="Telusuri surat...">
    </div>

    <!-- Profile -->
    @php
        $authUser    = Auth::user();
        $profileName = $authUser->name ?? 'Pengguna';
        $profileRole = ucwords(str_replace('_', ' ', $authUser->role ?? ''));
        $profileUnit = $authUser->unit->name ?? null;
        $profileSub  = $profileUnit ?? $profileRole;
    @endphp
    <div class="g-profile" id="gProfile">
        <button class="g-profile-trigger" id="gAvatarBtn" type="button" aria-label="Profil">
            <!-- Avatar -->
            <div class="g-avatar">
                @if($authUser && $authUser->photo)
                    <img src="{{ asset('storage/' . $authUser->photo) }}" alt="Avatar">
                @else
                    {{ strtoupper(substr($profileName, 0, 1)) }}
                @endif
            </div>
            <!-- Info teks -->
            <div class="g-profile-info">
                <div class="g-profile-name">{{ $profileName }}</div>
                <div class="g-profile-role">{{ $profileSub }}</div>
            </div>
            <i class="bi bi-chevron-down g-profile-chevron"></i>
        </button>

        <div class="g-profile-dd">
            <div class="g-dd-info">
                <div class="g-dd-name">{{ $profileName }}</div>
                <div class="g-dd-email">{{ $authUser->email ?? '' }}</div>
            </div>
            <a href="{{ route('profile.edit') }}" class="g-dd-item">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
            <div class="g-dd-sep"></div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="g-dd-item danger">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Overlay -->
<div class="g-overlay" id="gOverlay"></div>

<!-- ════ APP SHELL ════ -->
<div class="g-app">

    <!-- ════ SIDEBAR ════ -->
    <aside class="g-sidebar" id="gSidebar">

        {{-- Compose / Tulis Surat --}}
        @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
        <div class="g-compose-wrap">
            <a href="{{ route('letters.create') }}" class="g-compose-btn">
                <span class="g-compose-icon"><i class="bi bi-pencil-fill"></i></span>
                Tulis Surat
            </a>
        </div>
        @else
        <div style="height:.75rem;"></div>
        @endif

        <ul class="g-nav">

        {{-- ── ADMIN GROUP ── --}}
            @if(in_array($role, ['admin_sekretariat', 'admin_unit', 'admin']))
            <li class="g-nav-item">
                <a href="{{ route('letters.inbound') }}"
                   class="g-nav-link {{ request()->routeIs('letters.inbound*') ? 'active' : '' }}">
                    <i class="bi bi-inbox-fill"></i>
                    <span>Kotak Masuk</span>
                    @if(isset($unreadInboxCount) && $unreadInboxCount > 0)
                        <span class="g-nav-badge red">{{ $unreadInboxCount }}</span>
                    @endif
                </a>
            </li>
            <li class="g-nav-item">
                <a href="{{ route('letters.drafts') }}"
                   class="g-nav-link {{ request()->routeIs('letters.drafts') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Draft</span>
                    @if(isset($draftCount) && $draftCount > 0)
                        <span class="g-nav-badge amber">{{ $draftCount }}</span>
                    @endif
                </a>
            </li>
            <li class="g-nav-item">
                <a href="{{ route('letters.outbound') }}"
                   class="g-nav-link {{ request()->routeIs('letters.outbound*') ? 'active' : '' }}">
                    <i class="bi bi-send-fill"></i>
                    <span>Terkirim</span>
                    @if(isset($pendingSendingCount) && $pendingSendingCount > 0)
                        <span class="g-nav-badge red">{{ $pendingSendingCount }}</span>
                    @endif
                </a>
            </li>
            <li class="g-nav-item">
                <a href="{{ route('letters.arsip') }}"
                   class="g-nav-link {{ request()->routeIs('letters.arsip') ? 'active' : '' }}">
                    <i class="bi bi-archive-fill"></i>
                    <span>Arsip</span>
                </a>
            </li>

            <div class="g-sb-divider"></div>

            <li class="g-nav-item">
                <a href="{{ route('letters.createExternal') }}"
                   class="g-nav-link {{ request()->routeIs('letters.createExternal') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-plus-fill"></i>
                    <span>Catat Manual</span>
                </a>
            </li>
            @endif

            {{-- ── SUBAG / KEPALA UNIT ── --}}
            @if(in_array($role, ['subag_persuratan', 'kepala_unit']))
            <li class="g-nav-item">
                <a href="{{ route('tugas.accSurat') }}"
                   class="g-nav-link {{ request()->routeIs('tugas.accSurat') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>Draft / ACC</span>
                    @if(isset($pendingAccCount) && $pendingAccCount > 0)
                        <span class="g-nav-badge red">{{ $pendingAccCount }}</span>
                    @endif
                </a>
            </li>
            <li class="g-nav-item">
                <a href="{{ route('tugas.disposisi') }}"
                   class="g-nav-link {{ request()->routeIs('tugas.disposisi') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check-fill"></i>
                    <span>Disposisi</span>
                    @if(isset($pendingDispCount) && $pendingDispCount > 0)
                        <span class="g-nav-badge">{{ $pendingDispCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- ── KEPALA SEKRETARIAT / SUB UNIT / BAGIAN TU ── --}}
            @if(in_array($role, ['kepala_sekretariat', 'sub_unit', 'bagian_tu']))
            <li class="g-nav-item">
                <a href="{{ route('tugas.myDisposisi') }}"
                   class="g-nav-link {{ request()->routeIs('tugas.myDisposisi') ? 'active' : '' }}">
                    <i class="bi bi-inboxes-fill"></i>
                    <span>{{ $role === 'bagian_tu' ? 'Disposisi' : 'Disposisi Saya' }}</span>
                    @if(isset($pendingMyDispCount) && $pendingMyDispCount > 0)
                        <span class="g-nav-badge red">{{ $pendingMyDispCount }}</span>
                    @endif
                </a>
            </li>
            @endif

            {{-- ── LOG TASK (semua role pekerja) ── --}}
            @if(in_array($role, ['subag_persuratan', 'kepala_unit', 'kepala_sekretariat', 'sub_unit', 'bagian_tu']))
            <li class="g-nav-item">
                <a href="{{ route('tugas.index') }}"
                   class="g-nav-link {{ request()->routeIs('tugas.index') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Log Task</span>
                </a>
            </li>
            @endif

            {{-- ── MASTER DATA (admin only) ── --}}
            @if(in_array($role, ['admin', 'admin_sekretariat']))
            @php $isMasterActive = request()->routeIs('users.*', 'units.*', 'branches.*', 'organs.*'); @endphp
            <div class="g-sb-divider"></div>
            <li class="g-nav-item">
                <button class="g-nav-toggle {{ $isMasterActive ? 'open' : '' }}"
                        id="toggleMaster" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapseMaster"
                        aria-expanded="{{ $isMasterActive ? 'true' : 'false' }}">
                    <i class="bi bi-database-fill nav-icon"></i>
                    <span>Master Data</span>
                    <i class="bi bi-chevron-down g-chevron"></i>
                </button>
            </li>
            <div class="collapse g-nav-sub {{ $isMasterActive ? 'show' : '' }}" id="collapseMaster">
                <li class="g-nav-item">
                    <a href="{{ route('branches.index') }}"
                       class="g-nav-link {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i> Cabang
                    </a>
                </li>
                <li class="g-nav-item">
                    <a href="{{ route('units.index') }}"
                       class="g-nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                        <i class="bi bi-building-fill"></i> Unit Kerja
                    </a>
                </li>
                <li class="g-nav-item">
                    <a href="{{ route('organs.index') }}"
                       class="g-nav-link {{ request()->routeIs('organs.*') ? 'active' : '' }}">
                        <i class="bi bi-diagram-3-fill"></i> Organ
                    </a>
                </li>
                <li class="g-nav-item">
                    <a href="{{ route('users.index') }}"
                       class="g-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Pengguna
                    </a>
                </li>
            </div>
            @endif

        </ul>

        <!-- Sidebar footer -->
        <div class="g-sb-footer">
            <div class="g-sb-divider"></div>
            <a href="{{ route('profile.edit') }}" class="g-nav-link">
                <i class="bi bi-person-circle"></i>
                <span>Profil Saya</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="g-nav-link border-0 bg-transparent w-100 text-start"
                        style="color: #c5221f; cursor:pointer;">
                    <i class="bi bi-box-arrow-right" style="color:#c5221f;"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ════ MAIN CONTENT ════ -->
    <main class="g-main">
        <div class="g-content" id="gContent">
            @yield('content')
        </div>
    </main>

</div><!-- /.g-app -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const hamburger = document.getElementById('gHamburger');
    const sidebar   = document.getElementById('gSidebar');
    const overlay   = document.getElementById('gOverlay');
    const hamIcon   = document.getElementById('gHamIcon');
    const profile   = document.getElementById('gProfile');
    const avatarBtn = document.getElementById('gAvatarBtn');

    /* ── Sidebar toggle ── */
    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('show');
        hamIcon.className = 'bi bi-x-lg';
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('show');
        hamIcon.className = 'bi bi-list';
        document.body.style.overflow = '';
    }

    if (hamburger) {
        hamburger.addEventListener('click', function (e) {
            e.stopPropagation();
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
    }
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close on link click (mobile)
    sidebar.querySelectorAll('a.g-nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 1024) closeSidebar();
        });
    });

    // Resize to desktop → close
    window.addEventListener('resize', function () {
        if (window.innerWidth > 1024) closeSidebar();
    });

    /* ── Master data toggle chevron ── */
    const toggleMaster = document.getElementById('toggleMaster');
    if (toggleMaster) {
        toggleMaster.addEventListener('click', function () {
            this.classList.toggle('open');
        });
    }

    /* ── Profile dropdown ── */
    if (avatarBtn && profile) {
        avatarBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            profile.classList.toggle('open');
        });
        document.addEventListener('click', function () {
            profile.classList.remove('open');
        });
    }
})();
</script>

@if(session('success'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({ toast:true, icon:'success', title:'{!! session("success") !!}', position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true });
});
</script>
@endif
@if(session('error'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({ toast:true, icon:'error', title:'{!! session("error") !!}', position:'top-end', showConfirmButton:false, timer:3500, timerProgressBar:true });
});
</script>
@endif

@stack('scripts')
</body>
</html>
