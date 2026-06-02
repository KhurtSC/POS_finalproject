document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    // ── State ────────────────────────────────────────────────────────────────
    const cart    = new Map();
    const taxRate = 0.12;

    // ── DOM refs ─────────────────────────────────────────────────────────────
    const search      = root.querySelector('[data-product-search]');
    const category    = root.querySelector('[data-category-filter]');
    const cards       = [...root.querySelectorAll('.product-card')];
    const itemsEl     = root.querySelector('[data-cart-items]');
    const subtotalEl  = root.querySelector('[data-subtotal]');
    const taxEl       = root.querySelector('[data-tax]');
    const totalEl     = root.querySelector('[data-grand-total]');

    // Modal refs
    const modal           = document.querySelector('[data-checkout-modal]');
    const modalSubtotal   = modal.querySelector('[data-modal-subtotal]');
    const modalDiscount   = modal.querySelector('[data-modal-discount]');
    const modalTotal      = modal.querySelector('[data-modal-total]');
    const discountRow     = modal.querySelector('[data-discount-row]');
    const discountInput   = modal.querySelector('[data-discount-input]');
    const discountType    = modal.querySelector('[data-discount-type]');
    const cashSection     = modal.querySelector('[data-cash-section]');
    const tenderedInput   = modal.querySelector('[data-tendered-input]');
    const changeDisplay   = modal.querySelector('[data-change-display]');
    const cancelBtn       = modal.querySelector('[data-modal-cancel]');
    const confirmBtn      = modal.querySelector('[data-modal-confirm]');

    // ── Helpers ──────────────────────────────────────────────────────────────
    const money    = (v) => `₱${Number(v).toFixed(2)}`;
    const getTotal = () => {
        let subtotal = 0;
        cart.forEach(item => subtotal += item.price * item.qty);
        return subtotal;
    };

    function selectedPaymentMethod() {
        return modal.querySelector('input[name="payment_method"]:checked')?.value || 'cash';
    }

    function computeDiscount(subtotal) {
        const val  = parseFloat(discountInput.value) || 0;
        const type = discountType.value;
        if (val <= 0) return { amount: 0, percent: 0 };
        if (type === 'percent') {
            return { percent: val, amount: parseFloat((subtotal * val / 100).toFixed(2)) };
        }
        return { percent: 0, amount: val };
    }

    // ── Cart render ──────────────────────────────────────────────────────────
    function renderCart() {
        itemsEl.innerHTML = '';
        let subtotal = 0;

        cart.forEach((item, key) => {
            subtotal += item.price * item.qty;
            const row = document.createElement('div');
            row.className = 'p-4';
            row.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-black text-slate-950">${item.name}</p>
                        <p class="text-sm font-semibold text-slate-500">${money(item.price)} each</p>
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

        const tax = subtotal * taxRate;
        subtotalEl.textContent = money(subtotal);
        taxEl.textContent      = money(tax);
        totalEl.textContent    = money(subtotal + tax);
    }

    // ── Product filter ───────────────────────────────────────────────────────
    function filterProducts() {
        const term     = search.value.trim().toLowerCase();
        const selected = category.value;
        cards.forEach((card) => {
            const matchesTerm     = card.dataset.name.toLowerCase().includes(term);
            const matchesCategory = !selected || card.dataset.category === selected;
            card.classList.toggle('hidden', !(matchesTerm && matchesCategory));
        });
    }

    // ── Modal helpers ────────────────────────────────────────────────────────
    function openModal() {
        const subtotal         = getTotal();
        const { amount, percent } = computeDiscount(subtotal);
        const total            = Math.max(0, subtotal - amount);

        modalSubtotal.textContent = money(subtotal);
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
        const subtotal        = getTotal();
        const { amount }      = computeDiscount(subtotal);
        const total           = Math.max(0, subtotal - amount);
        const tendered        = parseFloat(tenderedInput.value) || 0;
        const change          = Math.max(0, tendered - total);
        changeDisplay.textContent = money(change);

        // Also refresh total in modal in case discount changed
        modalTotal.textContent    = money(total);
        if (amount > 0) {
            discountRow.classList.remove('hidden');
            modalDiscount.textContent = `−${money(amount)}`;
        } else {
            discountRow.classList.add('hidden');
        }
    }

    function toggleCashSection() {
        const isCash = selectedPaymentMethod() === 'cash';
        cashSection.classList.toggle('hidden', !isCash);
    }

    // ── Checkout submit ──────────────────────────────────────────────────────
    async function submitSale() {
        const subtotal           = getTotal();
        const { amount: discountAmount, percent: discountPercent } = computeDiscount(subtotal);
        const total              = Math.max(0, subtotal - discountAmount);
        const paymentMethod      = selectedPaymentMethod();
        const amountTendered     = parseFloat(tenderedInput.value) || 0;

        // Validate cash tendered
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

    // ── Event listeners ──────────────────────────────────────────────────────

    // Add product to cart
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

    // Cart item controls
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

    // Clear cart
    root.querySelector('[data-clear-cart]').addEventListener('click', () => {
        cart.clear();
        renderCart();
    });

    // Open modal on checkout click
    root.querySelector('[data-checkout]').addEventListener('click', () => {
        if (!cart.size) {
            alert('Your cart is empty.');
            return;
        }
        openModal();
    });

    // Cancel modal
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Toggle cash section on payment method change
    modal.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', () => {
            toggleCashSection();
            updateChange();
        });
    });

    // Update change on tendered input
    tenderedInput.addEventListener('input', updateChange);
    discountInput.addEventListener('input', updateChange);
    discountType.addEventListener('change', updateChange);

    // Confirm sale
    confirmBtn.addEventListener('click', submitSale);

    // Search and filter
    search.addEventListener('input', filterProducts);
    category.addEventListener('change', filterProducts);

    // Initial render
    renderCart();
    toggleCashSection();
});