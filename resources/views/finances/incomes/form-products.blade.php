@extends('layouts.app')

@section('content')
@php
    $editing = isset($income) && $income;
    $initialItems = old('items');
    if ($initialItems === null) {
        $initialItems = $editing ? $income->items->map(fn($item) => [
            'comercial_id' => $item->comercial_id,
            'quantity' => $item->quantity,
            'unit_price_usd' => $item->unit_price_usd,
            'finance_tax_rate_id' => $item->finance_tax_rate_id,
            'saved_name' => $item->product_name_snapshot,
        ])->values()->all() : [];
    }
@endphp
<div class="finance-income-create" style="max-width:1150px;margin:24px auto;padding:0 16px;">
    <a href="{{ route('finances.incomes.index') }}">← Volver a ingresos</a>
    <h1 style="margin:16px 0 6px;">{{ $editing ? 'Editar ingreso' : 'Registrar ingreso' }}</h1>
    <p style="color:var(--text-secondary);">Agrega uno o varios productos de comercial</p>

    @if(!$exchangeRate)
        <div class="finance-warning">No hay tasa de cambio configurada para la fecha del ingreso. Configura la tasa mensual en Configuraciones → Fiscal antes de continuar.</div>
    @else
        <div class="finance-rate">Tasa de cambio aplicada al mes seleccionado: <strong>1 USD = L {{ number_format((float)$exchangeRate->rate, 4) }}</strong></div>
    @endif
    @if($errors->any())
        <div class="finance-errors">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif

    <form method="POST" action="{{ $editing ? route('finances.incomes.update', $income) : route('finances.incomes.store') }}" id="income-form" class="finance-form">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="finance-fields">
            <div><label for="business_customer_id">Cliente *</label><select class="form-control" id="business_customer_id" name="business_customer_id" required><option value="">Selecciona un cliente</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('business_customer_id', $income->business_customer_id ?? '') == $customer->id)>{{ $customer->nombre }}</option>@endforeach</select></div>
            <div><label for="income_date">Fecha del ingreso *</label><input class="form-control" id="income_date" type="date" name="income_date" value="{{ old('income_date', isset($income) ? $income->income_date->toDateString() : now()->toDateString()) }}" required></div>
            <div><label for="invoice_number">Número de factura (opcional)</label><input class="form-control" id="invoice_number" name="invoice_number" maxlength="100" value="{{ old('invoice_number', $income->invoice_number ?? '') }}" placeholder="Ej. FAC-000125"></div>
            <div><label for="inversion_id">Inversión *</label><select class="form-control" id="inversion_id" name="inversion_id" required><option value="">Selecciona una inversión</option>@foreach($investments as $investment)<option value="{{ $investment->id }}" @selected(old('inversion_id', $income->inversion_id ?? '') == $investment->id)>{{ $investment->clave ? $investment->clave.' — ' : '' }}{{ $investment->nombre }}</option>@endforeach</select></div>
        </div>

        <div style="margin-top:18px;"><label for="description">Descripción del ingreso *</label><input class="form-control" id="description" name="description" maxlength="255" value="{{ old('description', $income->description ?? '') }}" required placeholder="Descripción general del ingreso"></div>
        <div class="items-heading"><div><h2>Productos</h2><p></p></div><button type="button" class="btn-secondary" id="add-item">＋ Agregar producto</button></div>
        <div id="items-container"></div>
        <div id="no-products" class="finance-muted">Selecciona una inversión para cargar sus productos comerciales.</div>

        <div class="finance-totals">
            <div><span>Subtotal</span><strong id="total-subtotal">$ 0.00</strong></div>
            <div><span>Impuestos</span><strong id="total-tax">$ 0.00</strong></div>
            <div class="grand-total"><span>Total del ingreso</span><strong id="total-total">$ 0.00</strong></div>
          
        </div>
        <div style="margin-top:18px;"><label for="notes">Notas</label><textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $income->notes ?? '') }}</textarea></div>
        <div class="finance-actions"><a class="btn-secondary" href="{{ route('finances.incomes.index') }}">Cancelar</a><button class="btn-primary-custom" type="submit" @disabled(!$exchangeRate || $customers->isEmpty() || $investments->isEmpty() || $taxRates->isEmpty())>{{ $editing ? 'Guardar cambios' : 'Guardar ingreso' }}</button></div>
        @if($customers->isEmpty())<p class="finance-error-text">No hay clientes comerciales registrados.</p>@endif
        @if($investments->isEmpty())<p class="finance-error-text">No hay inversiones registradas.</p>@endif
        @if($taxRates->isEmpty())<p class="finance-error-text">No hay impuestos activos configurados.</p>@endif
    </form>
</div>
<style>
.finance-income-create label{display:block;margin-bottom:7px;font-weight:600;font-size:14px;color:var(--text,#111827)}
.finance-income-create .form-control{display:block;width:100%;min-height:44px;box-sizing:border-box;padding:10px 13px;border:1px solid var(--border,#d1d5db);border-radius:9px;background:var(--surface,#fff);color:var(--text,#111827);font:inherit}
.finance-income-create select.form-control{cursor:pointer}
.finance-income-create .form-control:focus{outline:none;border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.15)}
.finance-form{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:22px}
.finance-fields{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px}
.finance-warning,.finance-rate,.finance-errors{padding:13px 16px;border-radius:9px;margin:18px 0}
.finance-warning{background:#fef3c7;color:#92400e}.finance-rate{background:var(--surface);border:1px solid var(--border)}.finance-errors{background:#fee2e2;color:#991b1b}
.items-heading{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;margin:26px 0 14px}
.items-heading h2{margin:0 0 5px;font-size:20px}.items-heading p,.finance-muted{margin:0;color:var(--text-secondary);font-size:13px}
.income-item{border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:12px}
.income-item-grid{display:grid;grid-template-columns:minmax(210px,2fr) minmax(100px,.7fr) minmax(140px,1fr) minmax(180px,1.2fr) auto;gap:12px;align-items:end}
.item-subtotal{font-weight:700;white-space:nowrap}.remove-item{min-height:42px}
.finance-totals{border:1px solid var(--border);border-radius:10px;padding:17px;margin-top:18px}
.finance-totals>div{display:flex;justify-content:space-between;gap:16px;margin:7px 0}.finance-totals .grand-total{border-top:1px solid var(--border);padding-top:12px;margin-top:12px;font-size:19px}.finance-totals small{display:block;margin-top:12px;color:var(--text-secondary)}
.finance-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:22px}.finance-actions a{text-decoration:none;display:inline-flex;align-items:center;padding:0 16px}
.finance-error-text{color:#b91c1c}.finance-muted{padding:18px;border:1px dashed var(--border);border-radius:9px;text-align:center}
@media(max-width:850px){.income-item-grid{grid-template-columns:1fr 1fr}.income-item-grid .product-field{grid-column:1/-1}.income-item-grid .tax-field{grid-column:1/-1}}
@media(max-width:520px){.finance-form{padding:14px}.income-item-grid{grid-template-columns:1fr}.income-item-grid .product-field,.income-item-grid .tax-field{grid-column:auto}.finance-actions{flex-direction:column}.finance-actions>*{min-height:44px;justify-content:center}}
</style>
<script>
(function(){
    const investment=document.getElementById('inversion_id');
    const container=document.getElementById('items-container');
    const noProducts=document.getElementById('no-products');
    const taxRates=@json($taxRates->map(fn($tax)=>['id'=>$tax->id,'name'=>$tax->name,'percentage'=>(float)$tax->percentage])->values());
    const initialItems=@json($initialItems);
    let products=[];
    let rowIndex=0;

    function money(value){return '$ '+(Number(value)||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});}
    function escapeHtml(value){return String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}
    function updateTotals(){
        let subtotal=0,tax=0;
        container.querySelectorAll('.income-item').forEach(row=>{
            const qty=Number(row.querySelector('.quantity').value)||0;
            const price=Number(row.querySelector('.unit-price').value)||0;
            const selected=row.querySelector('.tax-rate');
            const pct=Number(selected.options[selected.selectedIndex]?.dataset.percentage)||0;
            const lineSub=Math.round((qty*price+Number.EPSILON)*100)/100;
            const lineTax=Math.round((lineSub*pct/100+Number.EPSILON)*100)/100;
            subtotal+=lineSub;tax+=lineTax;
            row.querySelector('.line-subtotal').textContent=money(lineSub);
            row.querySelector('.line-tax').textContent=money(lineTax);
        });
        document.getElementById('total-subtotal').textContent=money(subtotal);
        document.getElementById('total-tax').textContent=money(tax);
        document.getElementById('total-total').textContent=money(subtotal+tax);
        noProducts.style.display=container.querySelector('.income-item')?'none':'block';
    }
    function addRow(item={}){
        const idx=rowIndex++;
        const options=products.map(p=>'<option value="'+p.id+'" data-price="'+p.price+'" '+(String(item.comercial_id||'')===String(p.id)?'selected':'')+'>'+escapeHtml(p.name)+' — $ '+Number(p.price).toFixed(2)+'</option>').join('');
        const taxOptions=taxRates.map(t=>'<option value="'+t.id+'" data-percentage="'+t.percentage+'" '+(String(item.finance_tax_rate_id||'')===String(t.id)?'selected':'')+'>'+escapeHtml(t.name)+' ('+t.percentage+'%)</option>').join('');
        const row=document.createElement('div');row.className='income-item';
        row.innerHTML='<div class="income-item-grid">'+
          '<div class="product-field"><label>Producto comercial *</label><select class="form-control product-select" name="items['+idx+'][comercial_id]" required><option value="">Selecciona un producto</option>'+options+'</select></div>'+
          '<div><label>Cantidad *</label><input class="form-control quantity" type="number" min="0.01" step="0.01" name="items['+idx+'][quantity]" value="'+escapeHtml(item.quantity??1)+'" required></div>'+
          '<div><label>Precio unitario USD *</label><input class="form-control unit-price" type="number" min="0" step="0.01" name="items['+idx+'][unit_price_usd]" value="'+escapeHtml(item.unit_price_usd??'')+'" required><small style="color:var(--text-secondary)"></small></div>'+
          '<div class="tax-field"><label>Impuesto de este producto *</label><select class="form-control tax-rate" name="items['+idx+'][finance_tax_rate_id]" required><option value="">Selecciona impuesto</option>'+taxOptions+'</select><small style="color:var(--text-secondary)"> <span class="line-tax">$ 0.00</span></small></div>'+
          '<div><button type="button" class="btn-secondary remove-item">Quitar</button><div class="item-subtotal" style="margin-top:8px">Subtotal: <span class="line-subtotal">$ 0.00</span></div></div>'+
          '</div>';
        container.appendChild(row);
        const productSelect=row.querySelector('.product-select');
        productSelect.addEventListener('change',()=>{
            const opt=productSelect.options[productSelect.selectedIndex];
            if(opt.dataset.price!==undefined) row.querySelector('.unit-price').value=Number(opt.dataset.price).toFixed(2);
            updateTotals();
        });
        row.querySelector('.quantity').addEventListener('input',updateTotals);
        row.querySelector('.unit-price').addEventListener('input',updateTotals);
        row.querySelector('.tax-rate').addEventListener('change',updateTotals);
        row.querySelector('.remove-item').addEventListener('click',()=>{row.remove();updateTotals();});
        updateTotals();
    }
    async function loadProducts(selectedItems=[],keepRows=false){
        const id=investment.value;
        if(!id){products=[];container.innerHTML='';updateTotals();return;}
        noProducts.textContent='Cargando productos comerciales…';
        try{
            const url=@json(url('/finanzas/ingresos/inversiones')).replace(/\/$/,'')+'/'+encodeURIComponent(id)+'/productos';
            const response=await fetch(url,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            if(!response.ok) throw new Error('No se pudieron cargar los productos.');
            products=await response.json();
            if(!keepRows) container.innerHTML='';
            if(!container.querySelector('.income-item')){
                if(selectedItems.length) selectedItems.forEach(item=>addRow(item));
                else if(products.length) addRow();
            }
            noProducts.textContent=products.length?'Agrega los productos que forman parte de este ingreso.':'Esta inversión no tiene productos registrados en Comercial.';
            updateTotals();
        }catch(e){products=[];container.innerHTML='';noProducts.textContent='No se pudieron cargar los productos. Intenta nuevamente.';}
    }
    document.getElementById('add-item').addEventListener('click',()=>{if(products.length)addRow();});
    investment.addEventListener('change',()=>loadProducts([],false));
    const initialInvestment=investment.value;
    if(initialInvestment) loadProducts(initialItems,false); else updateTotals();
    document.getElementById('income-form').addEventListener('submit',e=>{
        if(!container.querySelector('.income-item')){e.preventDefault();alert('Agrega al menos un producto al ingreso.');}
    });
})();
</script>
@endsection
