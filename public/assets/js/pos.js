document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    // ── VAT Setup ─────────────────────────────────────────────────────────────
    // Philippine standard: selling prices are VAT-INCLUSIVE.
    // A product priced at ₱150 already contains VAT.
    // Back-calculate: VAT portion = total × 12/112, net = total × 100/112.
    const VAT_RATE    = 0.12;
    const VAT_DIVISOR = 1 + VAT_RATE; // 1.12

    const vatOf = (inclusive) => parseFloat((inclusive * VAT_RATE / VAT_DIVISOR).toFixed(2));
    const netOf = (inclusive) => parseFloat((inclusive / VAT_DIVISOR).toFixed(2));

    // ── State ─────────────────────────────────────────────────────────────────
    const cart = new Map();

    // ── Pagination state ──────────────────────────────────────────────────────
    const PAGE_SIZE    = 6;
    let   currentPage  = 1;

    // ── DOM refs ──────────────────────────────────────────────────────────────
    const search      = root.querySelector('[data-product-search]');
    const category    = root.querySelector('[data-category-filter]');
    const cards       = [...root.querySelectorAll('.product-card')];
    const grid        = root.querySelector('[data-product-grid]');
    const itemsEl     = root.querySelector('[data-cart-items]');
    const subtotalEl  = root.querySelector('[data-subtotal]');
    const taxEl       = root.querySelector('[data-tax]');
    const totalEl     = root.querySelector('[data-grand-total]');
    const prevBtn     = root.querySelector('[data-prev-page]');
    const nextBtn     = root.querySelector('[data-next-page]');
    const pageInfo    = root.querySelector('[data-page-info]');

    // Modal refs
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

    // ── Pagination ────────────────────────────────────────────────────────────

    // Returns only the cards that pass the current search + category filter
    function getVisibleCards() {
        const term     = search.value.trim().toLowerCase();
        const selected = category.value;
        return cards.filter(card => {
            const matchesTerm     = card.dataset.name.toLowerCase().includes(term);
            const matchesCategory = !selected || card.dataset.category === selected;
            return matchesTerm && matchesCategory;
        });
    }

    function renderPage() {
        const visible    = getVisibleCards();
        const totalPages = Math.max(1, Math.ceil(visible.length / PAGE_SIZE));

        // Clamp currentPage in case filters shrink the result set
        if (currentPage > totalPages) currentPage = totalPages;

        const start = (currentPage - 1) * PAGE_SIZE;
        const end   = start + PAGE_SIZE;

        // Show/hide each card based on whether it's in the current page slice
        cards.forEach(card => card.style.display = 'none');
        visible.slice(start, end).forEach(card => card.style.display = '');

        // Update pagination controls
        if (pageInfo) pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        if (prevBtn)  prevBtn.disabled = currentPage <= 1;
        if (nextBtn)  nextBtn.disabled = currentPage >= totalPages;
    }

    function filterProducts() {
        currentPage = 1; // reset to first page on every filter/search change
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
    cards.forEach((card) => {
        card.addEventListener('click', () => {
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
    category.addEventListener('change', filterProducts);

    if (prevBtn) prevBtn.addEventListener('click', () => { currentPage--; renderPage(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { currentPage++; renderPage(); });

    // ── Init ──────────────────────────────────────────────────────────────────
    renderPage();
    renderCart();
    toggleCashSection();
});