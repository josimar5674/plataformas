<style>

:root{

    --bg:#f3f4f6;

    --surface:#ffffff;

    --surface-2:#f9fafb;

    --surface-3:#f3f4f6;

    --text:#111827;

    --text-secondary:#6b7280;

    --border:#e5e7eb;

    --primary:#2563eb;

    --danger:#dc2626;
--card-info:#f9fafb;

    --card-info-border:#e5e7eb;
    --shadow:0 4px 12px rgba(0,0,0,.05);

}

html[data-theme="dark"]{

    --bg:#0f172a;

    --surface:#1e293b;

    --surface-2:#334155;

    --surface-3:#475569;

    --text:#f8fafc;

    --text-secondary:#cbd5e1;

    --border:#475569;

    --primary:#2563eb;

    --danger:#ef4444;
     --card-info:#334155;

    --card-info-border:#475569;

    --shadow:0 8px 20px rgba(0,0,0,.45);

}

.form-control {

    width: 100%;

    padding: 12px;

    border: 1px solid var(--border);

    border-radius: 10px;

    font-size: 14px;

    background: var(--surface);

    color: var(--text);

    transition: .2s;

}



body{

    margin:0;

    padding:0;

    background:var(--bg);

    color:var(--text);

    transition:background .3s,color .3s;

}


.topbar {

    background: var(--surface);

    padding: 15px 25px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    border-bottom: 1px solid var(--border);

    box-shadow: var(--shadow);

}
/* ===========================
   DASHBOARD
=========================== */

.investment-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

    padding:20px;

    background:var(--surface);

    border-radius:12px;

    box-shadow:var(--shadow);

}

.investment-header h1{

    margin:0;

    font-size:28px;

    color:var(--text);

}

.investment-header small{

    color:var(--text-secondary);

    font-size:14px;

}

.summary-grid{

    display:grid;

    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));

    gap:15px;

    margin-bottom:35px;

}

.summary-card{

    background:var(--surface);

    border-radius:12px;

    padding:20px;

    box-shadow:var(--shadow);

    display:flex;

    flex-direction:column;

    gap:8px;

    font-size:14px;

    color:var(--text-secondary);

}

.summary-card strong{

    font-size:22px;

    color:var(--text);

}

.module-grid{

    display:grid;

    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));

    gap:20px;

}

.module-card{

    text-decoration:none;

    background:var(--surface);

    border-radius:14px;

    padding:25px;

    box-shadow:var(--shadow);

    transition:.25s;

    color:var(--text);

    display:flex;

    flex-direction:column;

    align-items:center;

    justify-content:center;

    gap:10px;

    min-height:140px;

}

.module-card:hover{

    transform:translateY(-4px);

    box-shadow:0 10px 25px rgba(0,0,0,.18);

}

.module-card .icon{

    font-size:40px;

}

.module-card .title{

    font-size:16px;

    font-weight:600;

}

.module-card .action{

    font-size:13px;

    color:#2563eb;

}

.info-section{

    margin-top:40px;

    background:var(--surface);

    border-radius:12px;

    padding:25px;

    box-shadow:var(--shadow);

}

.info-grid{

    display:grid;

    grid-template-columns:repeat(2,1fr);

    gap:20px;

}

.info-item{

    padding:12px;

    border-bottom:1px solid var(--border);

}

.info-label{

    font-size:13px;

    color:var(--text-secondary);

    margin-bottom:5px;

}

.info-value{

    font-weight:600;

    color:var(--text);

}

.metric-good{

    color:#16a34a;

}

.metric-warning{

    color:#ca8a04;

}

.metric-danger{

    color:var(--danger);

}

/* ===========================
   TABLA
=========================== */

.table-dashboard{

    width:100%;

    border-collapse:collapse;

    background:var(--surface);

    margin:0 auto;

    text-align:center;

    color:var(--text);

}

.table-dashboard th{

    text-align:center;

    vertical-align:middle;

    background:var(--surface-2);

    color:var(--text);

    border-bottom:1px solid var(--border);

}

.table-dashboard td{

    text-align:center;

    vertical-align:middle;

    color:var(--text);

    border-bottom:1px solid var(--border);

}

.table-dashboard td:first-child,

.table-dashboard th:first-child{

    text-align:left;

}

.table-dashboard td:nth-child(2),

.table-dashboard td:nth-child(3),

.table-dashboard td:nth-child(4){

    text-align:center;

}

.table-dashboard tbody tr{

    transition:.2s;

}

.table-dashboard tbody tr:hover{

    background:var(--surface-2) !important;

    cursor:pointer;

}

.table-dashboard tbody tr:hover td:first-child{

    color:#2563eb;

    font-weight:700;

}

/* ===========================
   BOTÓN TEMA
=========================== */

.theme-btn{

    width:42px;

    height:42px;

    border:none;

    border-radius:50%;

    background:var(--surface);

    color:var(--text);

    cursor:pointer;

    font-size:20px;

    margin-right:15px;

    box-shadow:var(--shadow);

    transition:.25s;

}

.theme-btn:hover{

    transform:scale(1.08);

}

@media(max-width:768px){

    .investment-header{

        flex-direction:column;

        align-items:flex-start;

        gap:15px;

    }

    .info-grid{

        grid-template-columns:1fr;

    }

}


.btn-new {
    background: #497fcb;
    color: white;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-block;
    transition: .2s;
}

.btn-new:hover {
    background: #2d2c26;
}

.btn-secondary {
    background: #e5e7eb;
    color: #111827;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-block;
}

.btn-primary-custom {
    background: #2563eb;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: .2s;
}

.btn-primary-custom:hover {
    background: #1d4ed8;
}

.btn-new{
    background:#2563eb;
}

.btn-new:hover{
    background:#1d4ed8;
}

.card-info{

    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:15px;
    padding:10px;
    margin-bottom:10px;

    background:var(--surface-2);
    border:1px solid var(--border);
    border-radius:8px;


}


/* ===========================
   MODAL
=========================== */

.modal-overlay-custom{

    position:fixed;

    inset:0;

    display:none;

    justify-content:center;

    align-items:center;

    background:rgba(0,0,0,.45);

    z-index:9999;

    padding:25px;

}

.modal-overlay-custom.show{

    display:flex;

}

.modal-window-custom{

    width:95%;

    max-width:1200px;

    max-height:90vh;

    overflow:auto;

    background:var(--surface);

    color:var(--text);

    border-radius:12px;

    border:1px solid var(--border);

    box-shadow:var(--shadow);

}

.modal-header-custom{

    display:flex;

    justify-content:space-between;

    align-items:center;

    padding:18px 25px;

    border-bottom:1px solid var(--border);

}

.modal-body-custom{

    padding:25px;

}

.modal-close-custom{

    border:none;

    background:none;

    color:var(--text);

    cursor:pointer;

    font-size:22px;

}

input,
select,
textarea{

    padding:10px;

    border:1px solid var(--border);

    border-radius:8px;

    transition:.2s;

    width:100%;

    background:var(--surface);

    color:var(--text);

}

.form-select {

    width: 100%;
    height: 42px;

    padding: 0 12px;

    border: 1px solid var(--border);
    border-radius: 8px;

    background: var(--surface);
    color: var(--text);

    font-size: 14px;

    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%239ca3af' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 6.646a.5.5 0 0 1 .708 0L8 9.293l2.646-2.647a.5.5 0 0 1 .708.708L8.354 10.354a.5.5 0 0 1-.708 0L4.646 7.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");

    background-repeat: no-repeat;
    background-position: right 12px center;

    padding-right: 40px;

}


.configuration-header{

    display:flex;
    justify-content:space-between;
    align-items:center;

    margin-bottom:25px;
    padding:20px;

    background:var(--surface);
    border-radius:12px;

    box-shadow:var(--shadow);

}

.configuration-header h1{

    margin:0;
    color:var(--text);

}

.configuration-header small{

    color:var(--text-secondary);

}


.configuration-layout{

    display:grid;

    grid-template-columns:260px 1fr;

    gap:20px;

}


.configuration-menu{

    background:var(--surface);

    border-radius:12px;

    padding:15px;

    box-shadow:var(--shadow);

    height:max-content;

}


.configuration-menu-title{

    font-weight:700;

    color:var(--text);

    padding:10px;

    margin-bottom:5px;

}


.configuration-menu a{

    display:block;

    padding:12px;

    border-radius:8px;

    text-decoration:none;

    color:var(--text);

    margin-bottom:5px;

    transition:.2s;

}


.configuration-menu a:hover{

    background:var(--surface-2);

}


.configuration-menu a.active{

    background:var(--primary);

    color:white;

}


.configuration-content{

    background:var(--surface);

    border-radius:12px;

    padding:20px;

    box-shadow:var(--shadow);

}


.configuration-content h3{

    margin-top:0;

    color:var(--text);

}


.configuration-add{

    display:flex;

    gap:10px;

    margin-bottom:20px;

}


.configuration-add input{

    flex:1;

}


.configuration-item{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:15px;

    padding:12px;

    margin-bottom:10px;

    background:var(--surface-2);

    border:1px solid var(--border);

    border-radius:8px;

}


.configuration-item-name{

    color:var(--text);

    font-weight:600;

}


.configuration-actions{

    display:flex;

    align-items:center;

    gap:8px;

}


.configuration-status{

    font-size:12px;

    padding:4px 8px;

    border-radius:6px;

}


.configuration-status.active{

    background:#dcfce7;

    color:#166534;

}


.configuration-status.inactive{

    background:#fee2e2;

    color:#991b1b;

}


@media(max-width:768px){

    .configuration-layout{

        grid-template-columns:1fr;

    }

    .configuration-add{

        flex-direction:column;

    }

    .configuration-item{

        flex-direction:column;

        align-items:flex-start;

    }

    .configuration-actions{

        width:100%;

        flex-wrap:wrap;

    }

}
.permission-tabs {

    display:flex;

    gap:5px;

    border-bottom:

        1px solid var(--border-color, #555);

    margin-bottom:20px;

}

.permission-tab {

    appearance:none;

    border:none;

    background:transparent;

    color:var(--text-secondary);

    padding:12px 20px;

    cursor:pointer;

    font-weight:600;

    font-size:14px;

    border-bottom:

        3px solid transparent;

    transition:

        color .2s ease,

        border-color .2s ease,

        background .2s ease;

}

.permission-tab:hover {

    color:var(--text-primary);

}

.permission-tab.active {

    color:var(--text-primary);

    border-bottom-color:

        var(--accent-color, #3b82f6);

}



.menu-dropdown {
    position: relative;
    display: inline-block;
}

.menu-dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 190px;
    background: var(--card-bg, #ffffff);
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    z-index: 1000;
    padding: 6px;
}

.menu-dropdown:hover .menu-dropdown-content,
.menu-dropdown:focus-within .menu-dropdown-content {
    display: block;
}

.menu-dropdown-content a {
    display: block;
    padding: 10px 12px;
    border-radius: 5px;
    text-decoration: none;
    white-space: nowrap;
}

.menu-dropdown-content a:hover {
    background: var(--hover-bg, #f3f4f6);
}

/* Factura pendiente de revisión */
.btn-pending {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-pending:hover {
    background: #fde68a;
}

/* Factura revisada */
.btn-reviewed {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.btn-reviewed:hover {
    background: #bbf7d0;
}

</style>