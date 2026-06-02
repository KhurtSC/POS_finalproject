document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-pos]');
    if (!root) return;

    const cart = new Map();
    const search   = root.querySelector('[data-product-search]');
    const category = root.querySelector('[data-category-filter]');
    const cards    = [...root.querySelectorAll('.product-card')];
    const itemsEl  = root.querySelector('[data-cart-items]');
    const subtotalEl = root.querySelector('[data-subtotal]');
    const taxEl      = root.querySelector('[data-tax]');
    const totalEl    = root.querySelector('[data-grand-total]');
    const taxRate    = 0.12;

    const money = (value) => `\u20b1${value.toFixed(2)}`;

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

    function filterProducts() {
        const term     = search.value.trim().toLowerCase();
        const selected = category.value;
        cards.forEach((card) => {
            const matchesTerm     = card.dataset.name.toLowerCase().includes(term);
            const matchesCategory = !selected || card.dataset.category === selected;
            card.classList.toggle('hidden', !(matchesTerm && matchesCategory));
        });
    }

    cards.forEach((card) => {
        card.addEventListener('click', () => {
            const key  = card.dataset.name;
            const item = cart.get(key) || { name: key, price: Number(card.dataset.price), qty: 0, product_id: Number(card.dataset.id) };
            item.qty  += 1;
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

    root.querySelector('[data-checkout]').addEventListener('click', async () => {
        if (!cart.size) return;

        const checkoutBtn = root.querySelector('[data-checkout]');
        checkoutBtn.disabled    = true;
        checkoutBtn.textContent = 'Processing…';

        const payload = {
            items: [...cart.values()].map(item => ({
                product_id: item.product_id,
                quantity:   item.qty,
            })),
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
                checkoutBtn.disabled    = false;
                checkoutBtn.textContent = 'Checkout';
                return;
            }

            const data = await response.json();
            window.location.href = `/cashier/receipt/${data.sale_id}`;

        } catch {
            alert('Network error. Please check your connection and try again.');
            checkoutBtn.disabled    = false;
            checkoutBtn.textContent = 'Checkout';
        }
    });

    search.addEventListener('input', filterProducts);
    category.addEventListener('change', filterProducts);
    renderCart();
});