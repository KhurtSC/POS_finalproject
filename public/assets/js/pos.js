document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    const VAT_RATE = 0.12;
    const VAT_DIVISOR = 1 + VAT_RATE;
    const PAGE_SIZE = 8;

    const cart = new Map();
    let allProducts = [];
    let currentPage = 1;

    const search = root.querySelector('[data-product-search]');
    const category = root.querySelector('[data-category-filter]');
    const scanInput = root.querySelector('[data-code-scan]');
    const scanButton = root.querySelector('[data-scan-submit]');
    const scanStatus = root.querySelector('[data-scan-status]');
    const grid = root.querySelector('[data-product-grid]');
    const itemsEl = root.querySelector('[data-cart-items]');
    const cartCount = root.querySelector('[data-cart-count]');
    const subtotalEl = root.querySelector('[data-subtotal]');
    const taxEl = root.querySelector('[data-tax]');
    const totalEl = root.querySelector('[data-grand-total]');
    const prevBtn = root.querySelector('[data-prev-page]');
    const nextBtn = root.querySelector('[data-next-page]');
    const pageInfo = root.querySelector('[data-page-info]');

    const modal = document.querySelector('[data-checkout-modal]');
    const modalSubtotal = modal.querySelector('[data-modal-subtotal]');
    const modalDiscount = modal.querySelector('[data-modal-discount]');
    const modalTotal = modal.querySelector('[data-modal-total]');
    const discountRow = modal.querySelector('[data-discount-row]');
    const discountInput = modal.querySelector('[data-discount-input]');
    const discountType = modal.querySelector('[data-discount-type]');
    const cashSection = modal.querySelector('[data-cash-section]');
    const tenderedInput = modal.querySelector('[data-tendered-input]');
    const changeDisplay = modal.querySelector('[data-change-display]');
    const cancelBtn = modal.querySelector('[data-modal-cancel]');
    const confirmBtn = modal.querySelector('[data-modal-confirm]');

    const money = (value) => `PHP ${Number(value).toFixed(2)}`;
    const vatOf = (inclusive) => parseFloat((inclusive * VAT_RATE / VAT_DIVISOR).toFixed(2));
    const netOf = (inclusive) => parseFloat((inclusive / VAT_DIVISOR).toFixed(2));
    const normalizeCode = (value) => String(value || '').trim().toLowerCase();
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));

    function selectedPaymentMethod() {
        return modal.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
    }

    function getGrossTotal() {
        let total = 0;
        cart.forEach((item) => total += item.price * item.qty);
        return total;
    }

    function getItemCount() {
        let count = 0;
        cart.forEach((item) => count += item.qty);
        return count;
    }

    function setScanStatus(message, tone = 'neutral') {
        if (!scanStatus) return;
        scanStatus.textContent = message;
        scanStatus.className = 'text-sm font-semibold ' + {
            success: 'text-teal-700',
            error: 'text-red-600',
            neutral: 'text-slate-500',
        }[tone];
    }

    function extractScannedCode(rawValue) {
        const value = String(rawValue || '').trim();
        if (!value) return '';

        try {
            const url = new URL(value);
            const fromQuery = url.searchParams.get('sku')
                || url.searchParams.get('code')
                || url.searchParams.get('barcode')
                || url.searchParams.get('product');

            if (fromQuery) return fromQuery.trim();

            const segments = url.pathname.split('/').filter(Boolean);
            return segments.at(-1) || value;
        } catch {
            return value;
        }
    }

    function findProductByCode(rawCode) {
        const code = normalizeCode(extractScannedCode(rawCode));
        if (!code) return null;

        return allProducts.find((product) => {
            return normalizeCode(product.sku) === code
                || normalizeCode(product.id) === code
                || normalizeCode(product.name) === code;
        }) || null;
    }

    function addProduct(product, source = 'tap') {
        if (!product) return false;

        const key = String(product.id);
        const existing = cart.get(key);
        const currentQty = existing?.qty || 0;

        if (currentQty >= Number(product.stock)) {
            setScanStatus(`${product.name} has no more available stock.`, 'error');
            return false;
        }

        cart.set(key, {
            product_id: Number(product.id),
            name: product.name,
            sku: product.sku || '',
            price: Number(product.price),
            stock: Number(product.stock),
            qty: currentQty + 1,
        });

        renderCart();
        if (source === 'scan') {
            setScanStatus(`Added ${product.name} from code ${product.sku || product.id}.`, 'success');
        }
        return true;
    }

    function computeDiscount(grossTotal) {
        const val = parseFloat(discountInput.value) || 0;
        const type = discountType.value;
        if (val <= 0) return { amount: 0, percent: 0 };
        if (type === 'percent') {
            return { percent: val, amount: parseFloat((grossTotal * val / 100).toFixed(2)) };
        }
        return { percent: 0, amount: val };
    }

    async function loadProducts() {
        grid.innerHTML = '<div class="col-span-full py-10 text-center text-slate-400 font-bold">Loading POS...</div>';

        try {
            const res = await fetch('/api/products');
            allProducts = await res.json();
            renderPage();
        } catch {
            grid.innerHTML = '<div class="col-span-full py-10 text-center text-red-500 font-bold">Failed to load products. Refresh the page.</div>';
        }
    }

    function getVisibleProducts() {
        const term = normalizeCode(search.value);
        const selected = category.value;

        return allProducts.filter((product) => {
            const matchesTerm = !term
                || normalizeCode(product.name).includes(term)
                || normalizeCode(product.sku).includes(term);
            const matchesCategory = !selected || String(product.category_id) === selected;
            return matchesTerm && matchesCategory;
        });
    }

    function renderPage() {
        const visible = getVisibleProducts();
        const totalPages = Math.max(1, Math.ceil(visible.length / PAGE_SIZE));

        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PAGE_SIZE;
        const pageProducts = visible.slice(start, start + PAGE_SIZE);

        grid.innerHTML = pageProducts.map((product) => {
            const image = product.image
                ? `<img src="/storage/${escapeHtml(product.image)}" alt="${escapeHtml(product.name)}" class="h-40 w-full object-cover" loading="lazy">`
                : '<div class="flex h-40 w-full items-center justify-center bg-slate-100 text-lg font-black text-slate-400">POS</div>';

            const lowStock = product.stock <= product.low_stock_threshold
                ? '<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-bold text-red-600">Low Stock</span>'
                : '';

            return `
                <button type="button"
                    class="product-card overflow-hidden rounded-lg border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md"
                    data-id="${escapeHtml(product.id)}">
                    ${image}
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="font-black text-slate-950">${escapeHtml(product.name)}</h2>
                                <p class="text-sm font-semibold text-slate-500">${escapeHtml(product.category?.name || 'Uncategorized')}</p>
                            </div>
                            <p class="font-black text-teal-600">${money(product.price)}</p>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">${escapeHtml(product.sku || 'No SKU')} / Stock ${escapeHtml(product.stock)}</p>
                            ${lowStock}
                        </div>
                    </div>
                </button>
            `;
        }).join('');

        if (!pageProducts.length) {
            grid.innerHTML = '<div class="col-span-full rounded-lg border border-dashed border-slate-300 bg-white p-10 text-center text-sm font-bold text-slate-400">No products match that search.</div>';
        }

        if (pageInfo) pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
    }

    function filterProducts() {
        currentPage = 1;
        renderPage();
    }

    function renderCart() {
        itemsEl.innerHTML = '';
        let grossTotal = 0;

        cart.forEach((item, key) => {
            grossTotal += item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'rounded-lg border border-slate-200 bg-white p-4 shadow-sm';
            row.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-black text-slate-950">${escapeHtml(item.name)}</p>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">${escapeHtml(item.sku || 'No SKU')}</p>
                        <p class="mt-1 text-sm font-semibold text-slate-500">${money(item.price)} each</p>
                    </div>
                    <button class="text-sm font-bold text-red-600" data-remove="${escapeHtml(key)}">Remove</button>
                </div>
                <div class="mt-3 flex items-center justify-between">
                    <div class="flex items-center rounded-md border border-slate-300 bg-slate-50">
                        <button class="px-3 py-1 font-black" data-decrease="${escapeHtml(key)}">-</button>
                        <span class="min-w-10 px-3 text-center text-sm font-black">${item.qty}</span>
                        <button class="px-3 py-1 font-black" data-increase="${escapeHtml(key)}">+</button>
                    </div>
                    <p class="font-black text-slate-950">${money(item.price * item.qty)}</p>
                </div>`;
            itemsEl.appendChild(row);
        });

        if (!cart.size) {
            itemsEl.innerHTML = '<div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-5 text-sm font-semibold text-slate-500">No items in the order yet. Tap a product or scan a SKU/barcode.</div>';
        }

        const vatAmount = vatOf(grossTotal);
        const netAmount = netOf(grossTotal);
        const count = getItemCount();

        if (cartCount) cartCount.textContent = `${count} item${count === 1 ? '' : 's'} selected`;
        subtotalEl.textContent = money(netAmount);
        if (taxEl) taxEl.textContent = money(vatAmount);
        totalEl.textContent = money(grossTotal);
    }

    function openModal() {
        const gross = getGrossTotal();
        const { amount } = computeDiscount(gross);
        const total = Math.max(0, gross - amount);

        modalSubtotal.textContent = money(gross);
        modalTotal.textContent = money(total);

        if (amount > 0) {
            discountRow.classList.remove('hidden');
            modalDiscount.textContent = `-${money(amount)}`;
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
        const gross = getGrossTotal();
        const { amount } = computeDiscount(gross);
        const total = Math.max(0, gross - amount);
        const tendered = parseFloat(tenderedInput.value) || 0;

        changeDisplay.textContent = money(Math.max(0, tendered - total));
        modalTotal.textContent = money(total);

        if (amount > 0) {
            discountRow.classList.remove('hidden');
            modalDiscount.textContent = `-${money(amount)}`;
        } else {
            discountRow.classList.add('hidden');
        }
    }

    function toggleCashSection() {
        cashSection.classList.toggle('hidden', selectedPaymentMethod() !== 'cash');
    }

    async function submitSale() {
        const gross = getGrossTotal();
        const { amount: discountAmount, percent: discountPercent } = computeDiscount(gross);
        const total = Math.max(0, gross - discountAmount);
        const paymentMethod = selectedPaymentMethod();
        const amountTendered = parseFloat(tenderedInput.value) || 0;

        if (paymentMethod === 'cash' && amountTendered < total) {
            alert(`Cash tendered (${money(amountTendered)}) is less than the total (${money(total)}).`);
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';

        const payload = {
            items: [...cart.values()].map((item) => ({
                product_id: item.product_id,
                quantity: item.qty,
            })),
            payment_method: paymentMethod,
            discount_percent: discountPercent,
            discount_amount: discountAmount,
            amount_tendered: amountTendered,
        };

        try {
            const response = await fetch('/api/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const err = await response.json().catch(() => ({}));
                alert(err.message || 'Checkout failed. Please try again.');
                confirmBtn.disabled = false;
                confirmBtn.textContent = 'Confirm Sale';
                return;
            }

            const data = await response.json();
            window.location.href = `/cashier/receipt/${data.sale_id}`;
        } catch {
            alert('Network error. Please check your connection and try again.');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Sale';
        }
    }

    function scanProduct() {
        const product = findProductByCode(scanInput.value);

        if (!product) {
            setScanStatus('No product matched that QR/barcode or SKU.', 'error');
            scanInput.select();
            return;
        }

        if (addProduct(product, 'scan')) {
            scanInput.value = '';
            scanInput.focus();
        }
    }

    grid.addEventListener('click', (event) => {
        const card = event.target.closest('.product-card');
        if (!card) return;

        const product = allProducts.find((item) => String(item.id) === String(card.dataset.id));
        addProduct(product);
    });

    itemsEl.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;

        const key = button.dataset.increase || button.dataset.decrease || button.dataset.remove;
        const item = cart.get(key);
        if (!item) return;

        if (button.dataset.increase) {
            if (item.qty >= item.stock) {
                setScanStatus(`${item.name} has no more available stock.`, 'error');
                return;
            }
            item.qty += 1;
        }

        if (button.dataset.decrease) item.qty -= 1;
        if (button.dataset.remove || item.qty <= 0) cart.delete(key);
        renderCart();
    });

    root.querySelector('[data-clear-cart]').addEventListener('click', () => {
        cart.clear();
        renderCart();
        setScanStatus('Order cleared. Ready for the next scan.', 'neutral');
    });

    root.querySelector('[data-checkout]').addEventListener('click', () => {
        if (!cart.size) {
            alert('Your cart is empty.');
            return;
        }
        openModal();
    });

    scanButton.addEventListener('click', scanProduct);
    scanInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            scanProduct();
        }
    });

    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) closeModal();
    });

    modal.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', () => {
            toggleCashSection();
            updateChange();
        });
    });

    tenderedInput.addEventListener('input', updateChange);
    discountInput.addEventListener('input', updateChange);
    discountType.addEventListener('change', updateChange);
    confirmBtn.addEventListener('click', submitSale);
    search.addEventListener('input', filterProducts);
    category.addEventListener('change', filterProducts);

    if (prevBtn) prevBtn.addEventListener('click', () => {
        currentPage--;
        renderPage();
    });

    if (nextBtn) nextBtn.addEventListener('click', () => {
        currentPage++;
        renderPage();
    });

    loadProducts();
    renderCart();
    toggleCashSection();
    scanInput.focus();
});
