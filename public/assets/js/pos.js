document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    // ── VAT Setup ─────────────────────────────────────────────────────────────
    const VAT_RATE    = 0.12;
    const VAT_DIVISOR = 1 + VAT_RATE;

    const vatOf = (inclusive) => parseFloat((inclusive * VAT_RATE / VAT_DIVISOR).toFixed(2));
    const netOf = (inclusive) => parseFloat((inclusive / VAT_DIVISOR).toFixed(2));

    // ── State ─────────────────────────────────────────────────────────────────
    const cart = new Map();
    let allProducts = []; // Will hold the JSON from the API

    // ── Pagination state ──────────────────────────────────────────────────────
    const PAGE_SIZE    = 6;
    let   currentPage  = 1;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const search      = root.querySelector('[data-product-search]');
    const category    = root.querySelector('[data-category-filter]');
    const grid        = root.querySelector('[data-product-grid]');
    const itemsEl     = root.querySelector('[data-cart-items]');
    const subtotalEl  = root.querySelector('[data-subtotal]');
    const taxEl       = root.querySelector('[data-tax]');
    const totalEl     = root.querySelector('[data-grand-total]');
    const prevBtn     = root.querySelector('[data-prev-page]');
    const nextBtn     = root.querySelector('[data-next-page]');
    const pageInfo    = root.querySelector('[data-page-info]');

    const modal         = document.querySelector('[data-checkout-modal]');
    const modalSubtotal = modal.querySelector('[data-modal-subtotal]');
    const modalDiscount = modal.querySelector('[data-modal-discount]');
    const modalTotal    = modal.querySelector('[data-modal-total]');
    const discountRow   = modal.querySelector('[data-discount-row]');
    const discountInput = modal.querySelector('[data-discount-input]');
    const discountType  = modal.querySelector('[data-discount-type]');
    const cashSection   = modal.querySelector('[data-cash-section]');
    const tenderedInput = modal.querySelector('[data-tendered-input]');
    const changeDisplay = modal.querySelector('[data-change-display]');
    const cancelBtn     = modal.querySelector('[data-modal-cancel]');
    const confirmBtn    = modal.querySelector('[data-modal-confirm]');

    // ── Helpers ───────────────────────────────────────────────────────────────
    const money = (v) => `₱${Number(v).toFixed(2)}`;

    const getGrossTotal = () => {
        let total = 0;
        cart.forEach(item => total += item.price * item.qty);
        return total;
    };

    function selectedPaymentMethod() {
        return modal.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
    }

    function computeDiscount(grossTotal) {
        const val  = parseFloat(discountInput.value) || 0;
        const type = discountType.value;
        if (val <= 0) return { amount: 0, percent: 0 };
        if (type === 'percent') {
            return { percent: val, amount: parseFloat((grossTotal * val / 100).toFixed(2)) };
        }
        return { percent: 0, amount: val };
    }

    // ── Fetch Data ────────────────────────────────────────────────────────────
    async function loadProducts() {
        grid.innerHTML = '<div class="col-span-full py-10 text-center text-slate-400 font-bold">Loading POS...</div>';
        try {
            const res = await fetch('/api/products');
            allProducts = await res.json();
            renderPage();
        } catch (err) {
            grid.innerHTML = '<div class="col-span-full py-10 text-center text-red-500 font-bold">Failed to load products. Refresh the page.</div>';
        }
    }

    // ── Pagination & Grid Render ──────────────────────────────────────────────
    function getVisibleProducts() {
        const term = search.value.trim().toLowerCase();
        const selected = category.value;
        return allProducts.filter(p => {
            const matchesTerm = p.name.toLowerCase().includes(term) || (p.sku && p.sku.toLowerCase().includes(term));
            const matchesCategory = !selected || String(p.category_id) === selected;
            return matchesTerm && matchesCategory;
        });
    }

    function renderPage() {
        const visible = getVisibleProducts();
        const totalPages = Math.max(1, Math.ceil(visible.length / PAGE_SIZE));
        
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PAGE_SIZE;
        const end = start + PAGE_SIZE;
        const pageProducts = visible.slice(start, end);

        grid.innerHTML = pageProducts.map(product => {
            const imgHtml = product.image 
                ? `<img src="/storage/${product.image}" alt="${product.name}" class="h-40 w-full object-cover" loading="lazy">`
                : `<div class="flex h-40 w-full items-center justify-center bg-slate-100 text-3xl">☕</div>`;
            
            const lowStockHtml = product.stock <= product.low_stock_threshold
                ? `<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-600">Low Stock</span>`
                : ``;

            return `
                <button type="button"
                    class="product-card overflow-hidden rounded-lg border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md"
                    data-id="${product.id}"
                    data-name="${product.name}"
                    data-price="${product.price}">
                    ${imgHtml}
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-black text-slate-950">${product.name}</h2>
                                <p class="text-sm font-semibold text-slate-500">${product.category?.name || 'Uncategorized'}</p>
                            </div>
                            <p class="font-black text-teal-600">₱${Number(product.price).toFixed(2)}</p>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Stock ${product.stock}</p>
                            ${lowStockHtml}
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        if (pageInfo) pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        if (prevBtn)  prevBtn.disabled = currentPage <= 1;
        if (nextBtn)  nextBtn.disabled = currentPage >= totalPages;
    }

    function filterProducts() {
        currentPage = 1;
        renderPage();
    }

    // ── Cart render ───────────────────────────────────────────────────────────
    function renderCart() {
        itemsEl.innerHTML = '';
        let grossTotal = 0;

        cart.forEach((item, key) => {
            grossTotal += item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'p-4';
            row.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-black text-slate-950">${item.name}</p>
                        <p class="text-sm font-semibold text-slate-500">${money(item.price)} each (VAT incl.)</p>
                    </div>
                    <button class="font-bold text-red-600" data-remove="${key}">Remove</button>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center rounded-md border border-slate-300">
                        <button class="px-3 py-1 font-black" data-decrease="${key}">-</button>
                        <span class="min-w-10 px-3 text-center text-sm font-black">${item.qty}</span>
                        <button class="px-3 py-1 font-black" data-increase="${key}">+</button>
                    </div>
                    <p class="font-black">${money(item.price * item.qty)}</p>
                </div>`;
            itemsEl.appendChild(row);
        });

        if (!cart.size) {
            itemsEl.innerHTML = '<div class="p-5 text-sm font-semibold text-slate-500">No items in cart yet.</div>';
        }

        const vatAmount = vatOf(grossTotal);
        const netAmount = netOf(grossTotal);

        subtotalEl.textContent = money(netAmount);
        if (taxEl) taxEl.textContent = money(vatAmount);
        totalEl.textContent    = money(grossTotal);
    }

    // ── Modal helpers ─────────────────────────────────────────────────────────
    function openModal() {
        const gross               = getGrossTotal();
        const { amount, percent } = computeDiscount(gross);
        const total               = Math.max(0, gross - amount);

        modalSubtotal.textContent = money(gross);
        modalTotal.textContent    = money(total);

        if (amount > 0) {
            discountRow.classList.remove('hidden');
            modalDiscount.textContent = `−${money(amount)}`;
        } else {
            discountRow.classList.add('hidden');
        }

        updateChange();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function updateChange() {
        const gross      = getGrossTotal();
        const { amount } = computeDiscount(gross);
        const total      = Math.max(0, gross - amount);
        const tendered   = parseFloat(tenderedInput.value) || 0;
        changeDisplay.textContent = money(Math.max(0, tendered - total));
        modalTotal.textContent    = money(total);
        if (amount > 0) {
            discountRow.classList.remove('hidden');
            modalDiscount.textContent = `−${money(amount)}`;
        } else {
            discountRow.classList.add('hidden');
        }
    }

    function toggleCashSection() {
        cashSection.classList.toggle('hidden', selectedPaymentMethod() !== 'cash');
    }

    // ── Checkout submit ───────────────────────────────────────────────────────
    async function submitSale() {
        const gross                                                = getGrossTotal();
        const { amount: discountAmount, percent: discountPercent } = computeDiscount(gross);
        const total          = Math.max(0, gross - discountAmount);
        const paymentMethod  = selectedPaymentMethod();
        const amountTendered = parseFloat(tenderedInput.value) || 0;

        if (paymentMethod === 'cash' && amountTendered < total) {
            alert(`Cash tendered (${money(amountTendered)}) is less than the total (${money(total)}).`);
            return;
        }

        confirmBtn.disabled    = true;
        confirmBtn.textContent = 'Processing…';

        const payload = {
            items: [...cart.values()].map(item => ({
                product_id: item.product_id,
                quantity:   item.qty,
            })),
            payment_method:   paymentMethod,
            discount_percent: discountPercent,
            discount_amount:  discountAmount,
            amount_tendered:  amountTendered,
        };

        try {
            const response = await fetch('/api/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept':       'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const err = await response.json().catch(() => ({}));
                alert(err.message || 'Checkout failed. Please try again.');
                confirmBtn.disabled    = false;
                confirmBtn.textContent = 'Confirm Sale';
                return;
            }

            const data = await response.json();
            window.location.href = `/cashier/receipt/${data.sale_id}`;

        } catch {
            alert('Network error. Please check your connection and try again.');
            confirmBtn.disabled    = false;
            confirmBtn.textContent = 'Confirm Sale';
        }
    }

    // ── Event listeners ───────────────────────────────────────────────────────
    grid.addEventListener('click', (event) => {
        const card = event.target.closest('.product-card');
        if (!card) return;

        const key  = card.dataset.name;
        const item = cart.get(key) || {
            name:       card.dataset.name,
            price:      Number(card.dataset.price),
            qty:        0,
            product_id: Number(card.dataset.id),
        };
        item.qty += 1;
        cart.set(key, item);
        renderCart();
    });

    itemsEl.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        const key  = button.dataset.increase || button.dataset.decrease || button.dataset.remove;
        const item = cart.get(key);
        if (!item) return;
        if (button.dataset.increase) item.qty += 1;
        if (button.dataset.decrease) item.qty -= 1;
        if (button.dataset.remove || item.qty <= 0) cart.delete(key);
        renderCart();
    });

    root.querySelector('[data-clear-cart]').addEventListener('click', () => {
        cart.clear();
        renderCart();
    });

    root.querySelector('[data-checkout]').addEventListener('click', () => {
        if (!cart.size) { alert('Your cart is empty.'); return; }
        openModal();
    });

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    modal.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', () => { toggleCashSection(); updateChange(); });
    });

    tenderedInput.addEventListener('input', updateChange);
    discountInput.addEventListener('input', updateChange);
    discountType.addEventListener('change',  updateChange);
    confirmBtn.addEventListener('click', submitSale);
    search.addEventListener('input', filterProducts);
    // Barcode Scanner Auto-Add Logic
    search.addEventListener('keydown', (event) => {
        // Barcode scanners automatically fire an "Enter" key after scanning
        if (event.key === 'Enter') {
            event.preventDefault(); 
            
            // Get whatever products are currently filtered on the screen
            const visibleProducts = getVisibleProducts();
            
            // If the barcode matched exactly ONE product, add it to the cart
            if (visibleProducts.length === 1) {
                const product = visibleProducts[0];
                const key = product.name;
                
                const item = cart.get(key) || {
                    name:       product.name,
                    price:      Number(product.price),
                    qty:        0,
                    product_id: Number(product.id),
                };
                
                item.qty += 1;
                cart.set(key, item);
                renderCart();
                
                // Clear the search bar instantly so they can scan the next item
                search.value = '';
                filterProducts();
            }
        }
    });
    category.addEventListener('change', filterProducts);

    if (prevBtn) prevBtn.addEventListener('click', () => { currentPage--; renderPage(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { currentPage++; renderPage(); });

    // ── Init ──────────────────────────────────────────────────────────────────
    loadProducts();
    renderCart();
    toggleCashSection();
});